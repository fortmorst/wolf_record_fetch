<?php

class Check_Village
{
  private  $html
          ,$stmt
          ,$db
          ,$village_pending
          ;

  const VILLAGE_TALK = 1;
  const VILLAGE_NULL = 2;
  const VILLAGE_END  = 3;
  const VILLAGE_NOT_END = -1;

  function __construct($stmt)
  {
    $this->stmt = $stmt;
    $this->db = new Connect_DB();

    $this->html = new simple_html_dom();
  }

  function check()
  {
    $this->db->connect();

    foreach($this->stmt as $stmt_key=>$item)
    {
      $this->village_pending = [];
      //キューの確認
      $this->check_queue($item['id']);
      try
      {
        //新規村の確認
        $vno_max_db = $this->check_new_fetch($item['id'],$item['check_type'],$item['url_log']);
        //village_pendingリストから更新確認
        if(!empty($this->village_pending) && $this->check_village_pending($item['id'],$item['check_type'],$item['url'],$item['talk_title'],$vno_max_db))
        {
          //stmtに書き込む
          $this->stmt[$stmt_key]['queue'] = $this->village_pending;
        }
        else
        {
          //更新村がゼロの場合、stmtを削除
          unset($this->stmt[$stmt_key]);
        }
      }
      catch(Exception $e)
      {
        echo "🚫 {$item['name']} チェック中にエラーが発生しました。->{$e->getMessage()}".PHP_EOL;
        unset($this->stmt[$stmt_key]);
        continue;
      }
    }
    $this->db->disconnect();
    return $this->stmt;
  }

  private function check_queue($cid)
  {
    $sql = "select `vno` from `village_queue` where `cid`={$cid}";
    $stmt = $this->db->query($sql);
    $result = $stmt->fetchAll();

    //キューに村番号がない場合
    if(empty($result))
    {
      return;
    }

    foreach($result as $item)
    {
      $this->village_pending[] = (int)$item['vno'];
    }
  }
  private function check_new_fetch($cid,$type,$url_log)
  {
    $vno_max_vlist = $this->check_vlist_latest($url_log,$type);
    $vno_max_db = $this->check_db_latest_vno($cid);

    //dbの最大村番号よりも、村リストの最大村番号が大きければ差分を確認リストに入れる
    if($vno_max_vlist > $vno_max_db)
    {
      $fetch_n  = $vno_max_vlist - $vno_max_db;

      for ($i=1;$i<=$fetch_n;$i++)
      {
        $this->village_pending[] = $vno_max_db + $i;
      }
    }

    return $vno_max_db;
  }
  private function check_db_latest_vno($cid)
  {
    //DBから一番最後に取得した村番号を取得
    $sql = "SELECT MAX(`vno`) FROM `village` where `cid`={$cid}";
    $stmt = $this->db->query($sql);
    $vno_max= $stmt->fetch(PDO::FETCH_NUM);

    return (int)$vno_max[0];
  }

  private function check_vlist_latest($url_log,$type)
  {
    $this->html->load_file($url_log);
    sleep(1);
    switch($type)
    {
      case "giji_old":
        $list_vno = (int)$this->html->find('tr.i_hover td',0)->plaintext;
        break;
      case "sow":
        $list_vno = (int)preg_replace('/^(\d+) .+/','\1',$this->html->find('tbody td a',0)->plaintext);
        break;
      case "bbs":
        $list_vno = $this->html->find('a',1)->plaintext;
        $list_vno =(int) preg_replace('/G(\d+) .+/','$1',$list_vno);
        break;
      case "sow_sebas":
        $list_vno = (int)preg_replace('/^(\d+) .+/','\1',$this->html->find('tbody td a',1)->plaintext);
        break;
      case "giji":
        $list_vno = $this->html->find('tr',1)->find('td',0)->innertext;
        $list_vno = (int)preg_replace("/^(\d+) <a.+/","$1",$list_vno);
        break;
      case "bbs_reason":
        $list_vno = $this->html->find('a',3)->plaintext;
        $list_vno =(int) mb_ereg_replace('A(\d+) .+','\\1',$list_vno);
        break;
    }
    $this->html->clear();
    return $list_vno;
  }

  private function check_village_pending($cid,$type,$url,$talk,$vno_max_db)
  {
    foreach($this->village_pending as $key=>$vno)
    {
      $url_vil = str_replace("%n",$vno,$url);
      $this->html->load_file($url_vil);
      sleep(1);

      switch($type)
      {
        case "bbs":
          $village = $this->check_bbs();
          break;
        case "bbs_reason":
          $village = $this->check_reason($url);
          break;
        default:
          $village = $this->check_from_title($talk);
          break;
      }

      switch($village)
      {
        case self::VILLAGE_NOT_END: //進行中の村
          unset($this->village_pending[$key]);
          if(!$this->db->check_vno_in_queue($cid,$vno))
          {
            //キューにまだ入っておらず、終了していない村は一旦村番号をメモ
            $sql = "INSERT INTO `village_queue` VALUES ({$cid},{$vno})";
            $this->db->query($sql);
          }
          break;
        case self::VILLAGE_END: //終了済の村
          break;
        case self::VILLAGE_TALK:  //雑談村
          unset($this->village_pending[$key]);
          $this->insert_talk_village($cid,$vno);
          echo "✅ {$vno}は雑談村です。穴埋めだけ行います。".PHP_EOL;
          break;
        case self::VILLAGE_NULL:  //欠番の村
          unset($this->village_pending[$key]);
          $this->insert_empty_village($cid,$vno);
          echo "✅ {$vno}は存在しません。穴埋めだけ行います。".PHP_EOL;
          break;
      }
    }
    //取得予定村が残ったらtrue
    return (!empty($this->village_pending))? true:false;
  }
  private function check_from_title($talk)
  {
    $title = $this->html->find('title',0)->plaintext;

    //雑談村かどうか
    if($talk !== null && mb_strpos($title,$talk) !== false)
    {
      return self::VILLAGE_TALK;
    }

    switch(mb_substr($title,0,2))
    {
      case "終了":  //終了済の村
        return self::VILLAGE_END;
        break;
      case "村デ":  //欠番の村
        return self::VILLAGE_NULL;
        break;
      default:      //進行中の村
        return self::VILLAGE_NOT_END;
        break;
    }
  }
  private function check_bbs()
  {
    $last_page = $this->html->find('span.time',0);

    if($last_page === NULL)
    {
      return self::VILLAGE_NULL;
    }

    //TODO: Goutte取得時は最後のスペースがない
    if($last_page->plaintext === "終了 ")
    {
      return self::VILLAGE_END;
    }
    else
    {
      return self::VILLAGE_NOT_END;
    }
  }
  private function check_reason($url)
  {
    $title = $this->html->find('title',0)->plaintext;
    if($title !== '')
    {
      return self::VILLAGE_END;
    }
    else
    {
      //進行中URLが存在するかどうか
      $url = str_replace("_kako","",$url);
      $this->html->load_file($url);
      sleep(1);
      $title = $this->html->find('title',0)->plaintext;
      if($title !== '')
      {
        return self::VILLAGE_NOT_END;
      }
      else
      {
        return self::VILLAGE_NULL;
      }
    }
  }
  private function insert_empty_village($cid,$vno)
  {
    $sql = "INSERT INTO `village`(`cid`,`vno`,`name`,`date`,`nop`,`rglid`,`days`,`wtmid`,`rgl_detail`) VALUES ({$cid},{$vno},'###vil not found###','0000-00-00',1,30,0,97,'1,')";
    $this->db->query($sql);
  }
  private function insert_talk_village($cid,$vno)
  {
    $title = $this->html->find('title',0)->plaintext;
    $sql = "INSERT INTO `village`(`cid`,`vno`,`name`,`date`,`nop`,`rglid`,`days`,`wtmid`,`rgl_detail`) VALUES ({$cid},{$vno},'{$title}','0000-00-00',1,30,0,97,'1,')";
    $this->db->query($sql);
  }
}
