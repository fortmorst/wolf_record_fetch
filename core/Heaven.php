<?php

class Heaven extends Country
{
  use TRS_Heaven;
  private $key_alive;

  function fetch_village()
  {
    $this->fetch_from_info();
    $this->fetch_from_pro();
    $this->fetch_from_epi();
    //var_dump($this->village->get_vars());
  }
  protected function fetch_from_info()
  {
    $this->fetch->load_file($this->url.$this->village->vno."&date=0");

    $this->fetch_name();
    $this->fetch_days();
    $this->fetch_rglid();
    $this->fetch_type();

    $this->fetch->clear();
  }
  protected function fetch_name()
  {
    $name = $this->fetch->find('h2',0)->plaintext;
    $this->village->name = mb_ereg_replace('\d+村 (.+)','\1',$name);
  }
  protected function fetch_type()
  {
    $type = $this->fetch->find('table.vil_main table',1)->find('td',5)->plaintext;
    if($type === 'カード人狼形式')
    {
      $this->village->is_card = true;
      echo $this->village->vno.' is card'.PHP_EOL;
      $this->village->days = $this->village->days +2;
    }
    else
    {
      $this->village->is_card = false;
    }
  }
  protected function fetch_nop()
  {
  }
  protected function fetch_rglid()
  {
    $rgl = $this->fetch->find('table',3)->find('td',5)->plaintext;
    if($rgl === '？')
    {
      $this->village->nop = 16;
    }
    else
    {
      $this->village->nop = mb_strlen($rgl);
    }

    if(array_key_exists($rgl,$this->rgl))
    {
      $this->village->rglid = $this->rgl[$rgl];
    }
    else
    {
      $this->village->rglid = Data::RGL_ETC;
    }
  }
  protected function fetch_days()
  {
    $days = trim($this->fetch->find('a',-4)->plaintext);
    if($days === '全')
    {
      $days = trim($this->fetch->find('table.vil_main a',-7)->plaintext);
    }
    $this->village->days = mb_substr($days,0,mb_strpos($days,'日')) -1;
    if($this->village->days < 2)
    {
      echo 'NOTICE: '.$this->village->vno.' has just '.$this->village->days.' days.'.PHP_EOL;
    }
  }
  protected function fetch_from_pro()
  {
    $url = $this->url.$this->village->vno.'_1.html';
    $this->fetch->load_file($url);

    $this->fetch_date();
    $this->fetch->clear();
  }
  protected function fetch_date()
  {
    $date = $this->fetch->find('span.time',1)->plaintext;
    $date = mb_substr($date,0,10);
    $this->village->date = preg_replace('/(\d{4})\/(\d{2})\/(\d{2})/','\1-\2-\3',$date);
  }
  protected function fetch_from_epi()
  {
    if($this->village->is_card)
    {
      $day = $this->village->days-1;
    }
    else
    {
      $day = $this->village->days+1;
    }
    $url = $this->url.$this->village->vno.'_'.$day.'.html';
    $this->fetch->load_file($url);

    $this->fetch_wtmid();
    $this->make_cast();
  }
  protected function fetch_wtmid()
  {
    $wtmid = mb_substr(trim($this->fetch->find('div.win',0)->plaintext),0,2);
    $this->village->wtmid = $this->team[$wtmid];
  }
  protected function make_cast()
  {
    $cast = $this->fetch->find('table.member tr td');
    foreach($cast as $key=>$item)
    {
      $line = trim($item->plaintext);
      switch(mb_substr($line,0,2))
      {
        case '':
        case '☆生':
        case '☆突':
        case '☆処':
          unset($cast[$key]);
          break;
        case '☆犠':
          $this->key_alive = $key;
          unset($cast[$key]);
          break;
        default:
          break;
      }
    }
    $this->cast = $cast;
  }
  protected function insert_users()
  {
    $list = [];
    $this->users = [];
    foreach($this->cast as $key=>$person)
    {
      $this->user = new User();
      $this->fetch_users($person,$key);
      $this->users[] = $this->user;
      //生存者を除く名前リストを作る
      $list[] = $this->user->persona;
      if($this->user->end !== null)
      {
        unset($list[$key]);
      }
    }
    $this->fetch->clear();
    $this->fetch_from_daily($list);

    foreach($this->users as $user)
    {
      //var_dump($user->get_vars());
      if(!$user->is_valid())
      {
        $this->output_comment('n_user');
      }
    }
  }
  protected function fetch_users($person,$key)
  {
    $person = explode("\r\n",trim($person->plaintext));

    $this->user->persona = $person[0];
    $this->user->player = mb_substr($person[1],4);
    $this->user->role = $person[2];

    $this->fetch_sklid();
    $this->fetch_rltid();
    if($this->is_alive($key))
    {
      $this->insert_alive();
    }
  }
  protected function is_alive($key)
  {
    if($this->key_alive > $key)
    {
      return true;
    }
    else
    {
      return false;
    }
  }
  protected function insert_alive()
  {
    $this->user->dtid = Data::DES_ALIVE;
    $this->user->end = $this->village->days;
    $this->user->life = 1.000;
  }
  protected function fetch_sklid()
  {
    $is_love = mb_strpos($this->user->role,'（');
    if($is_love === false)
    {
      $this->user->sklid = $this->skill[$this->user->role][0];
      $this->user->tmid = $this->skill[$this->user->role][1];
    }
    else
    {
      $role = mb_substr($this->user->role,0,$is_love);
      $this->user->sklid = $this->skill[$role][0];
      $this->user->tmid = Data::TM_LOVERS;
    }
  }
  protected function fetch_rltid()
  {
    if($this->user->tmid === $this->village->wtmid)
    {
      $this->user->rltid = Data::RSL_WIN;
    }
    else
    {
      $this->user->rltid = Data::RSL_LOSE;
    }
  }
  protected function fetch_from_daily($list)
  {
    if($this->village->is_card)
    {
      $days = $this->village->days-1;
      $start = 2;
    }
    else
    {
      $days = $this->village->days +1;
      $start = 3;
    }

    for($i=$start; $i<=$days; $i++)
    {
      $url = $this->url.$this->village->vno.'_'.$i.'.html';
      $this->fetch->load_file($url);
      $system = $this->check_destiny($i,'system',$list);
      $kill = $this->check_destiny($i,'kill',$list);
      $this->fetch->clear();
    }
  }
  protected function check_destiny($i,$find,$list)
  {
    $announce = $this->fetch->find('div.'.$find);

    foreach($announce as $item)
    {
      $destiny = trim($item->plaintext);
      $key = mb_substr($destiny,-7,7);
      if(!isset($this->destiny[$key]))
      {
        continue;
      }
      else
      {
        $persona = trim(mb_ereg_replace($this->destiny[$key][0],'\1',$destiny));
        $dtid = $this->destiny[$key][1];
      }

      $key_u = array_search($persona,$list);
      if($this->users[$key_u]->dtid === Data::DES_RETIRED)
      {
        continue;
      }

      $this->fetch_dtid($key_u,$dtid,$persona);
      if($this->village->is_card)
      {
        if($this->users[$key_u]->dtid === Data::DES_HANGED)
        {
          $this->users[$key_u]->end = $i +1;
        }
        else
        {
          $this->users[$key_u]->end = $i;
        }
      }
      else
      {
        $this->users[$key_u]->end = $i -1;
      }
      $this->users[$key_u]->life = round(($this->users[$key_u]->end-1) / $this->village->days,3);
    }
  }
  protected function fetch_dtid($key_u,$dtid,$persona)
  {
      //妖魔陣営の無残死は呪殺死にする
      if($this->users[$key_u]->tmid === Data::TM_FAIRY && $dtid === Data::DES_EATEN)
      {
        $this->users[$key_u]->dtid = Data::DES_CURSED;
      }
      else
      {
        $this->users[$key_u]->dtid = $dtid;
      }
  }
}
