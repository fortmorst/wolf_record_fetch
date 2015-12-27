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
      echo '☆'.$item['class'].'->'.PHP_EOL;
      $this->village_pending = [];
      //キューの確認
      $this->check_queue($item['id']);
      //新規村の確認
      $vno_max_db = $this->check_new_fetch($item['id'],$item['class'],$item['url_log'],$item['ruin']);
      //village_pendingリストから更新確認
      if(!empty($this->village_pending) && $this->check_village_pending($item['id'],$item['class'],$item['url'],$vno_max_db))
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
  private function check_new_fetch($cid,$class,$url_log,$ruin)
  {
    $vno_max_vlist = $this->check_vlist_latest($url_log,$class);
    $vno_max_db = $this->check_db_latest_vno($cid);
    //後で廃村してるか否かは国で確認するようにする
    $vno_ruin = $this->check_db_latest_ruin($cid);

    //廃村が連続している国は最新村番号をチェック
    //将来的に削除
    if($vno_ruin !== 0)
    {
      if($vno_ruin === $vno_max_vlist)
      {
        return $vno_max_db;
      }
      else
      {
        echo '▼ruin clear.'.PHP_EOL;
      }
    }

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
  private function check_db_latest_ruin($cid)
  {
    //廃村が連続している国はDBに村番号がある 
    $sql = "SELECT ruin FROM country WHERE id=".$cid;
    $stmt = $this->db->query($sql);
    $vno_ruin= $stmt->fetch(PDO::FETCH_NUM);

    return (int)$vno_ruin[0];
  }

  private function check_vlist_latest($url_log,$class)
  {
    $this->html->load_file($url_log);
    sleep(1);
    switch($class)
    {
      case 'Ning':
        $list_vno = $this->html->find('a',1)->plaintext;
        $list_vno =(int) preg_replace('/G(\d+) .+/','$1',$list_vno);
        break;
      case 'Morphe':
      case 'Xebec':
      case 'Crazy':
      case 'Guta':
      case 'Sea_Red':
      case 'Sea_Blue':
      case 'Sea_Old':
      case 'Ivory':
      case 'Crescent':
      case 'Love':
      case 'Plot':
      case 'Perjury':
        $list_vno = (int)$this->html->find('tr.i_hover td',0)->plaintext;
        break;
      case 'Ciel':
        $list_vno = $this->html->find('tr',1)->find('td',0)->innertext;
        $list_vno = (int)preg_replace("/^(\d+) <a.+/","$1",$list_vno);
        break;
      case 'Melon':
      case 'Rose':
      case 'Cherry':
      case 'Real':
      case 'Moon':
      case 'Chitose':
      case 'Chitose_RP':
      case 'Rinne':
      case 'Phantom':
      case 'Dark':
      case 'BW':
      case 'Dance':
        $list_vno = (int)preg_replace('/^(\d+) .+/','\1',$this->html->find('tbody td a',0)->plaintext);
        break;
      case 'Sebas':
        $list_vno = (int)preg_replace('/^(\d+) .+/','\1',$this->html->find('tbody td a',1)->plaintext);
        break;
      case 'Silence':
        $list_vno = (int)preg_replace('/^(\d+) .+/','\1',$this->html->find('td a',0)->plaintext);
        break;
      case 'Reason':
        $list_vno = $this->html->find('a',3)->plaintext;
        $list_vno =(int) mb_ereg_replace('A(\d+) .+','\\1',$list_vno);
        break;
    }
    $this->html->clear();
    return $list_vno;
  }

  private function check_village_pending($id,$class,$url,$vno_max_db)
  {
    foreach($this->village_pending as $key=>$vno)
    {
      $url_vil = mb_ereg_replace('%n',$vno,$url);
      $is_end = $this->check_end($class,$url_vil);
      echo 'vno: '.$vno.PHP_EOL;
      //廃村判定は後で消す
      if($is_end)
      {
        //queueに入っている(DBの最大村番号よりも小さい)なら削除
        if($vno < $vno_max_db)
        {
          $sql = 'DELETE FROM village_queue where cid='.$id.' AND vno='.$vno;
          $stmt = $this->db->query($sql);
          echo '◎'.$vno.' in queue was deleted.'.PHP_EOL;
        }

        //廃村か否か
        if($this->check_not_ruined($class,$url_vil))
        {
          //両方trueならcontinue;
          continue;
        }
        else
        {
          //is_endがtrue&ruinedがfalse(=ruinである)ならruinedコメント
          //後で削除
          unset($this->village_pending[$key]);
          echo '※'.$vno.'>> ruined.'.PHP_EOL;
        }
      }
      else
      {
        //is_endがfalseでruinedもfalseならキューに書く
        unset($this->village_pending[$key]);
        //終了していない村は一旦村番号をメモ
        $sql = 'INSERT INTO village_queue VALUES ('.$id.','.$vno.')';
        $stmt = $this->db->query($sql);

        echo '●'.$vno.'was written into DB.'.PHP_EOL;
      }
    }
    return (!empty($this->village_pending))? true:false;
  }
  private function check_end($class,$url)
  {

    $this->html->load_file($url);
    sleep(1);
    switch($class)
    {
      case 'Ning':
        $last_page = trim($this->html->find('span.time',0)->plaintext);
        break;
      case 'Reason':
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

  private function check_not_ruined($class,$url)
  {
    $not_ruin = ['Ning','Phantom','Reason'];

    if(array_search($class,$not_ruin) !== false)
    {
      return true;
    }
    else if($class === 'Rinne')
    {
      $this->html->load_file($url.'&cmd=vinfo');
      sleep(1);
      $day = $this->html->find('p.multicolumn_role',0)->plaintext;
      if($day === '短期')
      {
        return false;
      }
      else
      {
        return true;
      }
      $this->html->clear();
    }

    $this->html->load_file($url);
      sleep(1);
    switch($class)
    {
      case 'Melon':
        $epi = $this->html->find('link[rel=Prev]',0)->href;
        $epi = mb_ereg_replace('.+;t=(\d+)','\\1',$epi);
        $this->html->clear();
        $this->html->load_file($url.'&t='.$epi.'&r=5&m=a&o=a&mv=p&n=1');
      sleep(1);
        if(count($this->html->find('p.info')) <= 1 && count($this->html->find('p.infosp')) === 0)
        {
          return false;
        }
        else
        {
          return true;
        }
        break;
        break;
      case 'Plot':
      case 'Ciel':
      case 'Perjury':
        $scrap = $this->html->find('script',-2)->innertext;
        $scrap = mb_ereg_replace('.+"is_scrap":     \(0 !== (\d)\),.+',"\\1",$scrap,'m');
        $this->html->clear();
        if($scrap === '1')
        {
          return false;
        }
        else
        {
          return true;
        }
        break;
      default:
        switch($class)
        {
          case 'Sebas':
          case 'Crescent':
            $info = 'div.info';
            $infosp = 'div.infosp';
            break;
          case 'Silence':
            $info = 'div.announce';
            $infosp = 'div.extra';
            break;
          default:
            $info = 'p.info';
            $infosp = 'p.infosp';
            break;
        }
        $epi = $this->html->find('link[rel=Prev]',0)->href;
        $epi = mb_ereg_replace('.+;turn=(\d+)','\\1',$epi);
        $this->html->clear();
        $this->html->load_file($url.'&turn='.$epi.'&mode=all&row=5&move=page&pageno=1');
      sleep(1);
        if(count($this->html->find($info)) <= 1 && count($this->html->find($infosp)) === 0)
        {
          return false;
        }
        else
        {
          return true;
        }
        break;
    }
  }
}
