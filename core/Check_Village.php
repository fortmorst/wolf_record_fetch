<?php

class Check_Village
{
  private  $html
          ,$stmt
          ,$db
          ,$village_pending
          ;

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
        echo '※ERROR: '.$item['name'].' 取得中にエラーが発生しました。この国をスキップします。->'.$e->getMessage().PHP_EOL;
        unset($this->stmt[$stmt_key]);
        continue;
      }
    }
    $this->db->disconnect();
    return $this->stmt;
  }

  private function check_queue($cid)
  {
    $sql = "select vno from village_queue where cid=".$cid;
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
    $sql = "SELECT MAX(vno) FROM village where cid=".$cid;
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
      case 'giji_old':
        $list_vno = (int)$this->html->find('tr.i_hover td',0)->plaintext;
        break;
      case 'sow':
        $list_vno = (int)preg_replace('/^(\d+) .+/','\1',$this->html->find('tbody td a',0)->plaintext);
        break;
      case 'bbs':
        $list_vno = $this->html->find('a',1)->plaintext;
        $list_vno =(int) preg_replace('/G(\d+) .+/','$1',$list_vno);
        break;
      case 'sow_sebas':
        $list_vno = (int)preg_replace('/^(\d+) .+/','\1',$this->html->find('tbody td a',1)->plaintext);
        break;
      case 'giji':
        $list_vno = $this->html->find('tr',1)->find('td',0)->innertext;
        $list_vno = (int)preg_replace("/^(\d+) <a.+/","$1",$list_vno);
        break;
      case 'sow_silence':
        $list_vno = (int)preg_replace('/^(\d+) .+/','\1',$this->html->find('td a',0)->plaintext);
        break;
      case 'bbs_reason':
        $list_vno = $this->html->find('a',3)->plaintext;
        $list_vno =(int) mb_ereg_replace('A(\d+) .+','\\1',$list_vno);
        break;
    }
    $this->html->clear();
    return $list_vno;
  }

  private function check_village_pending($id,$type,$url,$talk,$vno_max_db)
  {
    foreach($this->village_pending as $key=>$vno)
    {
      $url_vil = mb_ereg_replace('%n',$vno,$url);
      $is_end = $this->check_end($type,$url_vil);

      //echo $class.': vno= '.$vno.PHP_EOL;

      //村番号が存在しない場合
      if(!$this->is_not_found($type,$url_vil))
      {
        unset($this->village_pending[$key]);
        $this->insert_empty_village($id,$vno);
        echo '⚠️NOTICE->'.$vno.' は存在しません。穴埋めだけ行います。'.PHP_EOL;
        continue;
      }
      //雑談村がある国の場合
      if(!$this->is_not_talk_village($url_vil,$talk))
      {
        echo '⚠️NOTICE->'.$vno.' は雑談村です。'.PHP_EOL;
        continue;
      }

      if($is_end)
      {
        //queueに入っている(DBの最大村番号よりも小さい)なら削除
        if($vno < $vno_max_db)
        {
          $sql = 'DELETE FROM village_queue where cid='.$id.' AND vno='.$vno;
          $this->db->query($sql);
          //echo '◎'.$vno.' in queue was deleted.'.PHP_EOL;
        }
      }
      else
      {
        //is_endがfalseならキューに書く
        unset($this->village_pending[$key]);
        if($vno > $vno_max_db)
        {
          //キューにまだ入っておらず、終了していない村は一旦村番号をメモ
          $sql = 'INSERT INTO village_queue VALUES ('.$id.','.$vno.')';
          $this->db->query($sql);

         // echo '●'.$vno.'was written into DB.'.PHP_EOL;
        }
      }
    }
    return (!empty($this->village_pending))? true:false;
  }
  private function is_not_talk_village($url,$talk)
  {
    $this->html->load_file($url);
    $title = $this->html->find('title',0)->plaintext;
    $this->html->clear();
    if($talk !== null && mb_strpos($title,$talk) !== false)
    {
      return false;
    }
    else
    {
      return true;
    }
  }
  private function is_not_found($type,$url)
  {
    $this->html->load_file($url);
    if($type === 'sow_silence')
    {
      $tag = 'div.inframe';
    }
    else
    {
      $tag = 'div.paragraph';
    }
    $paragraph = $this->html->find($tag.' p',0);
    if($paragraph && $paragraph->plaintext === '村データ が見つかりません。')
    {
      $this->html->clear();
      return false;
    }
    else
    {
      $this->html->clear();
      return true;
    }
  }
  private function insert_empty_village($cid,$vno)
  {
    $sql = "INSERT INTO village(cid,vno,name,date,nop,rglid,days,wtmid) VALUES (".$cid.",".$vno.",'###vil not found###','0000-00-00',1,30,1,97)";
    $this->db->query($sql);
  }
  private function check_end($type,$url)
  {

    $this->html->load_file($url);
    sleep(1);
    switch($type)
    {
      case 'bbs':
        $last_page = trim($this->html->find('span.time',0)->plaintext);
        break;
      case 'bbs_reason':
        $last_page = $this->html->find('a',0)->plaintext;
        if($last_page === '')
        {
          $last_page = '終了';
        }
        break;
      default:
        $last_page = mb_substr($this->html->find('title',0)->plaintext,0,2);
        break;
    }
    $this->html->clear();

    if($last_page === "終了")
    {
      return true;
    }
    else
    {
      return false;
    }
  }

}
