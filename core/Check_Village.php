<?php

class Check_Village
{
  private  $cid
          ,$url_log
          ,$url_vil
          ,$queue
          ,$queue_del = []
          ,$village = []
          ,$html
          ,$fp
          ;

  function __construct($cid,$url_vil,$url_log)
  {
    $this->cid     = $cid;
    $this->url_vil = $url_vil;
    $this->url_log = $url_log;

    $this->html = new simple_html_dom();
  }

  function get_village()
  {
    $this->check_queue();
    $this->check_new_fetch();
    $this->close_queue();
    if($this->village)
    {
      sort($this->village);
    }
    return $this->village;
  }

  function remove_queue($vno=false)
  {
    //空なら操作しない
    if(empty($this->queue_del))
    {
      return;
    }

    $queue = $this->open_queue();
    echo 'queue_del:';
    var_dump($this->queue_del);
    echo 'remove_queue/vno:'.$vno.', queue: '.$queue.PHP_EOL;

    //村番号の指定があるならそれだけを除外する
    if($vno !== false)
    {
      $queue = mb_ereg_replace($this->cid.'_'.$vno.',',"",$queue);
    }
    else
    {
      foreach($this->queue_del as $vno)
      {
        $queue = preg_replace('/'.$this->cid.'_'.$vno.',/',"",$queue); 
      }
    }
    ftruncate($this->fp,0);
    fseek($this->fp, 0);
    fwrite($this->fp,$queue);
    $this->close_queue();
  }

  private function open_queue()
  {
    $fname = __DIR__.'/../rs/queue.txt';
    if(is_writable($fname))
    {
      $this->fp = fopen($fname,'a+');
      flock($this->fp,LOCK_EX);
      $line = fgets($this->fp);
      return $line;
    }
    else
    {
      return false;
    }
  }
  private function close_queue()
  {
    if($this->fp)
    {
      fflush($this->fp);
      flock($this->fp,LOCK_UN);
      fclose($this->fp);
    }
  }

  private function check_queue()
  {
    $line = $this->open_queue();
    if($line && mb_strstr($line,$this->cid.'_'))
    {
      $this->queue = $line;
      $queue_array = explode(',',$line);
      array_pop($queue_array);
      foreach($queue_array as $item)
      {
        if(!mb_strstr($item,$this->cid.'_'))
        {
          continue;
        }
        $vno = preg_replace('/'.$this->cid.'_/','',$item);
        $is_end = $this->check_end($vno);
        if($is_end && $this->check_not_ruined($vno))
        {
          $this->village[] = (int)$vno;
          $this->queue_del[] = (int)$vno;
        }
        else if($is_end)
        {
          $this->queue_del[] = (int)$vno;
          echo '※'.$vno.'>> ruined.'.PHP_EOL;
        }
      }
    }
    else
    {
      return false;
    }
  }

  private function check_end($vno)
  {
    $this->html->load_file($this->url_vil.$vno);
    switch($this->cid)
    {
      case Cnt::Ning:
        $last_page = trim($this->html->find('span.time',0)->plaintext);
        break;
      case Cnt::Reason:
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
  private function check_not_ruined($vno)
  {
    if($this->cid === Cnt::Ning || $this->cid === Cnt::Phantom || $this->cid === Cnt::Reason)
    {
      return true;
    }
    else if($this->cid === Cnt::Rinne)
    {
      $this->html->load_file($this->url_vil.$vno.'&cmd=vinfo');
      $day = $this->html->find('p.multicolumn_role',0)->plaintext;
      if($day === '短期')
      {
        return false;
      }
      $this->html->clear();
    }

    $this->html->load_file($this->url_vil.$vno);
    switch($this->cid)
    {
      case Cnt::Melon:
        $epi = $this->html->find('link[rel=Prev]',0)->href;
        $epi = mb_ereg_replace('.+;t=(\d+)','\\1',$epi);
        $this->html->clear();
        $this->html->load_file($this->url_vil.$vno.'&t='.$epi.'&r=5&m=a&o=a&mv=p&n=1');
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
      case Cnt::Plot:
      case Cnt::Ciel:
      case Cnt::Perjury:
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
        switch($this->cid)
        {
          case Cnt::Sebas:
          case Cnt::Crescent:
            $info = 'div.info';
            $infosp = 'div.infosp';
            break;
          case Cnt::Silence:
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
        $this->html->load_file($this->url_vil.$vno.'&turn='.$epi.'&mode=all&row=5&move=page&pageno=1');
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

  private function check_new_fetch()
  {
    $list_vno = $this->check_endlist();
    $db_vno = $this->check_db();
    echo 'list_vno: '.$list_vno;
    echo 'db_vno: ';
    var_dump($db_vno);

    //廃村が連続している国は最新村番号をチェック
    if($db_vno['ruin'] !== 0)
    {
      if($db_vno['ruin'] === $list_vno)
      {
        return;
      }
      else
      {
        echo '▼ruin clear.'.PHP_EOL;
      }
    }

    if($list_vno > $db_vno['max'])
    {
      $fetch_n  = $list_vno - $db_vno['max'];
      for ($i=1;$i<=$fetch_n;$i++)
      {
        $vno = 0;
        $vno = $db_vno['max'] + $i;
        $is_end = $this->check_end($vno);
        echo '$vno: '.$vno.PHP_EOL;

        if($is_end && $this->check_not_ruined($vno))
        {
          $this->village[] = (int)$vno;
        }
        else if($is_end)
        {
          echo '※'.$vno.'>> ruined.'.PHP_EOL;
        }
        else
        {
          //終了していない村は一旦村番号をメモ
          var_dump(mb_strstr($this->queue,$this->cid.'_'.$vno));
          if(!mb_strstr($this->queue,$this->cid.'_'.$vno))
          {
            echo 'fwrite:'.fwrite($this->fp,$this->cid.'_'.$vno.',').PHP_EOL;
          }
        }
      }
    }
  }

  private function check_endlist()
  {
    $this->html->load_file($this->url_log);
    switch($this->cid)
    {
      case Cnt::Ning:
        $list_vno = $this->html->find('a',1)->plaintext;
        $list_vno =(int) preg_replace('/G(\d+) .+/','$1',$list_vno);
        break;
      case Cnt::Morphe:
      case Cnt::Xebec:
      case Cnt::Crazy:
      case Cnt::Guta:
      case Cnt::Sea_Red:
      case Cnt::Sea_Blue:
      case Cnt::Sea_Old:
      case Cnt::Ivory:
      case Cnt::Crescent:
      case Cnt::Love:
        $list_vno = (int)$this->html->find('tr.i_hover td',0)->plaintext;
        break;
      case Cnt::Plot:
      case Cnt::Ciel:
      case Cnt::Perjury:
        $list_vno = $this->html->find('tr',1)->find('td',0)->innertext;
        $list_vno = (int)preg_replace("/^(\d+) <a.+/","$1",$list_vno);
        break;
      case Cnt::Melon:
      case Cnt::Rose:
      case Cnt::Cherry:
      case Cnt::Real:
      case Cnt::Moon:
      case Cnt::Chitose:
      case Cnt::Chitose_RP:
      case Cnt::Rinne:
      case Cnt::Phantom:
      case Cnt::Mikan:
      case Cnt::Dark:
      case Cnt::BW:
      case Cnt::Dance:
        $list_vno = (int)preg_replace('/^(\d+) .+/','\1',$this->html->find('tbody td a',0)->plaintext);
        break;
      case Cnt::Sebas:
        $list_vno = (int)preg_replace('/^(\d+) .+/','\1',$this->html->find('tbody td a',1)->plaintext);
        break;
      case Cnt::Silence:
        $list_vno = (int)preg_replace('/^(\d+) .+/','\1',$this->html->find('td a',0)->plaintext);
        break;
      case Cnt::Reason:
        $list_vno = $this->html->find('a',3)->plaintext;
        $list_vno =(int) mb_ereg_replace('A(\d+) .+','\\1',$list_vno);
        break;
    }
    $this->html->clear();
    return $list_vno;
  }
  private function check_db()
  {
    //DB接続
    try{
      $pdo = new DBAdapter();
    } catch (pdoexception $e){
      var_dump($e->getMessage());
      exit;
    }
    //DBから一番最後に取得した村番号を取得
    $sql = "SELECT MAX(vno) FROM village where cid=".$this->cid;
    $stmt = $pdo->query($sql);
    $vno_max= $stmt->fetch(PDO::FETCH_NUM);

    //廃村が連続している国はDBに村番号がある 
    $sql = "SELECT ruin FROM country WHERE id=".$this->cid;
    $stmt = $pdo->query($sql);
    $vno_ruin= $stmt->fetch(PDO::FETCH_NUM);

    //DB切断
    $pdo = null;
    return ['max'=>(int)$vno_max[0],'ruin'=>(int)$vno_ruin[0]];
  }
}
