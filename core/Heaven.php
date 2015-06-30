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
  }
  protected function fetch_from_info()
  {
    $this->fetch->load_file($this->url.$this->village->vno."&date=0");

    $this->fetch_name();
    $this->fetch_days();
    $this->fetch_type();
    $this->fetch_policy();

    $this->fetch->clear();
  }
  protected function fetch_name()
  {
    $name = $this->fetch->find('h2',0)->plaintext;
    $this->village->name = mb_ereg_replace('\d+村 (.+)','\1',$name);
  }
  protected function fetch_days()
  {
    $days = $this->fetch->find('a',-3)->plaintext;
    $this->village->days = mb_substr($days,0,mb_strpos($days,'日')) -1;
    if($this->village->days < 2)
    {
      echo '>NOTICE: '.$this->village->vno.' has just '.$this->village->days.' days.'.PHP_EOL;
    }
  }
  protected function fetch_type()
  {
    $type = $this->fetch->find('dl.explain span',0)->plaintext;
    if($type === 'カード人狼形式 ')
    {
      $this->village->is_card = true;
      //echo $this->village->vno.' is card'.PHP_EOL;
      $this->village->days = $this->village->days +1;
    }
    else
    {
      $this->village->is_card = false;
    }
  }
  protected function fetch_policy()
  {
    $policy = $this->fetch->find('table table table tr td',13)->plaintext;
    if(mb_strpos($policy,"ガチ推理") !== false)
    {
      $this->village->policy = true;
    }
    else
    {
      $this->village->policy = false;
      $this->output_comment('rp');
    }
  }
  protected function fetch_from_pro()
  {
    $url = $this->url.$this->village->vno.'&date=1&start=1';
    $this->fetch->load_file($url);

    $this->fetch_date();
    $this->fetch->clear();
  }
  protected function fetch_date()
  {
    $date = $this->fetch->find('span.time',0)->plaintext;
    $date = mb_substr($date,0,10);
    $this->village->date = preg_replace('/(\d{4})\/(\d{2})\/(\d{2})/','\1-\2-\3',$date);
  }
  protected function fetch_from_epi()
  {
    if($this->village->is_card)
    {
      $day = $this->village->days;
      $log = 'log=all';
    }
    else
    {
      $day = $this->village->days+1;
      $log = 'start=1';
    }
    $url = $this->url.$this->village->vno.'&'.$log.'&date='.$day;
    $this->fetch->load_file($url);

    $this->fetch_wtmid();
    $this->make_cast();
  }
  protected function fetch_wtmid()
  {
    if($this->village->policy)
    {
      $wtmid = mb_substr($this->fetch->find('div.win',0)->plaintext,0,2);
      $this->village->wtmid = $this->TEAM[$wtmid];
    }
    else
    {
      $this->village->wtmid = Data::TM_RP;
    }
  }
  protected function make_cast()
  {
    $cast = $this->fetch->find('table.list tr');
    array_shift($cast);
    foreach($cast as $key=>$item)
    {
      $line = $item->plaintext;
      if(mb_ereg_match('^  ',$line))
      {
        unset($cast[$key]);
        continue;
      }

      $line = trim($line);
      switch(mb_substr($line,0,5))
      {
        case '[─] 処':
        case '[─] 観':
        case 'ハッシュタ':
          unset($cast[$key]);
          break;
        case '[─] 犠':
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

      $this->fetch_users($person);
      if($this->is_alive($key))
      {
        $this->insert_alive();
      }

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
      var_dump($user->get_vars());
      if(!$user->is_valid())
      {
        $this->output_comment('n_user');
      }
    }
  }
  protected function fetch_users($person)
  {
    $person = explode("\r\n",trim($person->plaintext));

    $this->user->persona = $person[0];
    $this->user->player = mb_substr($person[1],4);
    $this->user->role = $person[3];

    $this->fetch_sklid();
    if($this->user->role === '観戦者')
    {
      $this->insert_onlooker();
    }
    else
    {
      $this->fetch_rltid();
    }
  }
  protected function fetch_sklid()
  {
    $is_love = mb_strpos($this->user->role,'(');
    if($is_love === false)
    {
      $this->user->sklid = $this->SKILL[$this->user->role][0];
      $this->user->tmid = $this->SKILL[$this->user->role][1];
    }
    else
    {
      $role = mb_substr($this->user->role,0,$is_love-1);
      $this->user->sklid = $this->SKILL[$role][0];
      $this->user->tmid = Data::TM_LOVERS;
    }
  }
  protected function insert_onlooker()
  {
    $this->user->dtid  = Data::DES_ONLOOKER;
    $this->user->end   = 1;
    $this->user->life  = 0.000;
    $this->user->rltid = Data::RSL_ONLOOKER;
  }
  protected function fetch_rltid()
  {
    if($this->village->wtmid === 0)
    {
      $this->user->rltid = Data::RSL_JOIN;
      return;
    }
    if($this->user->tmid === $this->village->wtmid)
    {
      $this->user->rltid = Data::RSL_WIN;
    }
    else
    {
      $this->user->rltid = Data::RSL_LOSE;
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
  protected function fetch_from_daily($list)
  {
    if($this->village->is_card)
    {
      $days = $this->village->days;
      $start = 2;
      $log = 'log=all';
    }
    else
    {
      $days = $this->village->days +1;
      $start = 3;
      $log = 'start=1';
    }

    for($i=$start; $i<=$days; $i++)
    {
      $url = $this->url.$this->village->vno.'&'.$log.'&date='.$i;
      $this->fetch->load_file($url);
      $this->check_destiny($i,$list); //突然死もvote
      $this->fetch->clear();
    }
  }
  protected function check_destiny($i,$list)
  {
    $announce = $this->fetch->find('div.announce');

    foreach($announce as $item)
    {
      $destiny = trim($item->plaintext);
      $key = mb_substr($destiny,-7,7);
      if(!isset($this->DESTINY[$key]))
      {
        continue;
      }
      else
      {
        $persona = trim(mb_ereg_replace($this->DESTINY[$key][0],'\1',$destiny));
        $dtid = $this->DESTINY[$key][1];
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
