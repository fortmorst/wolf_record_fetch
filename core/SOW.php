<?php

class SOW extends Country
{
  private $cursewolf = [];

  function fetch_village()
  {
    $this->cursedwolf = [];
    $this->fetch_from_info();
    $this->fetch_from_pro();
    $this->fetch_from_epi();
    var_dump($this->village->get_vars());
  }
  protected function fetch_from_info()
  {
    $this->fetch->load_file($this->url."&cmd=vinfo");
      sleep(1);

    $this->fetch_name();
    $this->fetch_days();
    if(empty($this->RP_PRO))
    {
      $this->fetch_rp();
    }
    if($this->policy === null)
    {
      $this->fetch_policy();
    }

    $this->fetch->clear();
  }
  protected function fetch_name()
  {
    $this->village->name = $this->fetch->find('p.multicolumn_left',0)->plaintext;
  }
  protected function fetch_days()
  {
    $days = trim($this->fetch->find('p.turnnavi',0)->find('a',-4)->innertext);
    $this->village->days = mb_substr($days,0,mb_strpos($days,'日')) +1;
  }
  protected function fetch_rp()
  {
    if(empty($this->RP_PRO))
    {
      $rp = trim($this->fetch->find('p.multicolumn_left',7)->plaintext);
      if(array_key_exists($rp,$this->RP_LIST))
      {
        $this->village->rp = $this->RP_LIST[$rp];
        return;
      }
    }
    else
    {
      $rp = mb_substr($this->fetch->find('p.info',0)->plaintext,1,5);
      if(array_key_exists($rp,$this->RP_PRO))
      {
        $this->village->rp = $this->RP_PRO[$rp];
        return;
      }
    }
    $this->village->rp = 'SOW';
  }
  protected function fetch_from_pro()
  {
    $url = $this->url.'&turn=0&row=10&mode=all&move=page&pageno=1';
    $this->fetch->load_file($url);
      sleep(1);

    $this->fetch_date();
    if(!empty($this->RP_PRO))
    {
      $this->fetch_rp();
    }
    $this->fetch->clear();
  }
  protected function fetch_date()
  {
    $date = $this->fetch->find('div.mes_date',0)->plaintext;
    $date = mb_substr($date,mb_strpos($date,"2"),10);
    $this->village->date = preg_replace('/(\d{4})\/(\d{2})\/(\d{2})/','\1-\2-\3',$date);
  }
  protected function fetch_from_epi()
  {
    $url = $this->url.'&turn='.$this->village->days.'&row=40&mode=all&move=page&pageno=1';
    $this->fetch->load_file($url);
      sleep(1);

    $this->fetch_wtmid();
    $this->make_cast();
  }
  protected function fetch_wtmid()
  {
    if($this->policy || $this->village->policy)
    {
      $wtmid = $this->fetch_win_message();
      if(array_key_exists($wtmid,$this->{'WTM_'.$this->village->rp}))
      {
        $this->village->wtmid = $this->{'WTM_'.$this->village->rp}[$wtmid];
      }
      else
      {
        $this->village->wtmid = Data::TM_RP;
        $this->output_comment('undefined',$wtmid);
      }
    }
    else
    {
      $this->village->wtmid = Data::TM_RP;
    }
  }
  protected function fetch_win_message()
  {
    $not_wtm = '/村の更新日が延長されました。|村の設定が変更されました。/';
    $wtmid = trim($this->fetch->find('p.info',-1)->plaintext);
    if(preg_match($not_wtm,$wtmid))
    {
      $do_i = -2;
      do
      {
        $wtmid = trim($this->fetch->find('p.info',$do_i)->plaintext);
        $do_i--;
      } while(preg_match($not_wtm,$wtmid));
    }
    return mb_substr(preg_replace("/\r\n/","",$wtmid),-10);
  }
  protected function make_cast()
  {
    $cast = $this->fetch->find('tbody tr');
    array_shift($cast);
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
      $this->users[] = $this->user;
      //生存者を除く名前リストを作る
      $list[] = $this->user->persona;
      if($this->user->end !== null)
      {
        unset($list[$key]);
      }
    }
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
    $this->fetch_persona($person);
    $this->fetch_player($person);
    $this->fetch_role($person);

    if($this->user->role === '見物人')
    {
      $this->insert_onlooker();
    }
    else
    {
      $this->fetch_sklid();
      $this->fetch_rltid();
      if($this->is_alive($person))
      {
        $this->insert_alive();
      }
    }
  }
  protected function fetch_persona($person)
  {
    $this->user->persona = trim($person->find("td",0)->plaintext);
  }
  protected function fetch_player($person)
  {
    $player =trim($person->find("td a",0)->plaintext);
    if(isset($this->{'d_'.get_class($this)}))
    {
      $this->user->player =$this->modify_player($player);
    }
    else
    {
      $this->user->player = $player;
    }
  }
  protected function is_alive($person)
  {
    $status = $person->find('td',2)->plaintext;
    if($status === '生存')
    {
      return true;
    }
    else
    {
      return false;
    }
  }
  protected function insert_onlooker()
  {
    $this->user->sklid  = Data::SKL_ONLOOKER;
    $this->user->tmid = Data::TM_ONLOOKER;
    $this->user->dtid  = Data::DES_ONLOOKER;
    $this->user->end   = 1;
    $this->user->life  = 0.000;
    $this->user->rltid = Data::RSL_ONLOOKER;
  }
  protected function insert_alive()
  {
    $this->user->dtid = Data::DES_ALIVE;
    $this->user->end = $this->village->days;
    $this->user->life = 1.000;
  }
  protected function fetch_role($person)
  {
    $role = $person->find('td',3)->plaintext;
    $this->user->role = mb_ereg_replace('\A(.+) \(.+を希望\)(.+|)','\1',$role,'m');
  }
  protected function fetch_sklid()
  {
    if(!empty($this->{'SKL_'.$this->village->rp}))
    {
      $this->user->sklid = $this->{'SKL_'.$this->village->rp}[$this->user->role][0];
      $this->user->tmid = $this->{'SKL_'.$this->village->rp}[$this->user->role][1];
    }
    else
    {
      $this->user->sklid = $this->SKILL[$this->user->role][0];
      $this->user->tmid = $this->SKILL[$this->user->role][1];
    }
    //呪狼の名前をメモ
    if($this->user->sklid === Data::SKL_WOLF_CURSED)
    {
      $this->cursewolf[] = $this->user->persona;
    }
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
  protected function fetch_daily_url($i,$find)
  {
    $row = 40;
    $url = $this->url.'&turn='.$i.'&mode=all&move=page&pageno=1&row='.$row;
    $this->fetch->load_file($url);
      sleep(1);
    $announce = $this->fetch->find($find);
    //処刑以降が取れてなさそうな場合はログ件数を増やす
    if(count($announce) <= 1 && $find !== 'p.infosp')
    {
      do
      {
        $row += 10;
        $url = $this->url.'&turn='.$i.'&mode=all&move=page&pageno=1&row='.$row;
        $this->fetch->load_file($url);
      sleep(1);
        $announce = $this->fetch->find($find);
        if($row >= 70)
        {
          echo '>NOTICE: too deep row in fetch_daily_url'.PHP_EOL;
          break;
        }
      } while (count($announce) <= 1);
    }
    return $announce;
  }
  protected function fetch_from_daily($list)
  {
    $days = $this->village->days;
    $find = 'p.info';

    //言い換えの有無
    if(!empty($this->{'DT_'.$this->village->rp}))
    {
      $rp = $this->village->rp;
    }
    else
    {
      $rp = 'NORMAL';
    }

    for($i=2; $i<=$days; $i++)
    {
      $announce = $this->fetch_daily_url($i,$find);
      foreach($announce as $item)
      {
        $key_u = $this->fetch_key_u($list,$rp,$item);
        if($key_u === false)
        {
          continue;
        }
        $this->users[$key_u]->end = $i;
        $this->users[$key_u]->life = round(($i-1) / $this->village->days,3);
      }
      $this->fetch->clear();
    }
  }
  protected function fetch_key_u($list,$rp,$item)
  {
      $destiny = trim(preg_replace("/\r\n/",'',$item->plaintext));
      $key= mb_substr(trim($item->plaintext),-6,6);
      if(!isset($this->{'DT_'.$rp}[$key]))
      {
        return false;
      }
      else
      {
        $persona = trim(mb_ereg_replace($this->{'DT_'.$rp}[$key][0],'\2',$destiny,'m'));
        $dtid = $this->{'DT_'.$rp}[$key][1];
      }

      $key_u = array_search($persona,$list);
      if($key_u === false)
      {
        return false;
      }
      $this->fetch_dtid($key_u,$dtid,$persona);
      return $key_u;
  }
  protected function fetch_dtid($key_u,$dtid,$persona)
  {
      //妖魔陣営の無残死は呪殺死にする
      if($this->users[$key_u]->tmid === Data::TM_FAIRY && $dtid === Data::DES_EATEN)
      {
        $this->users[$key_u]->dtid = Data::DES_CURSED;
      }
      //呪狼が存在する編成で、占い師が襲撃された場合別途チェック
      else if(!empty($this->cursewolf) && $this->users[$key_u]->sklid === Data::SKL_SEER && $dtid === Data::DES_EATEN && $this->modify_cursed_seer($persona,$key_u))
      {
        $this->users[$key_u]->dtid = Data::DES_CURSED;
      }
      else
      {
        $this->users[$key_u]->dtid = $dtid;
      }
  }
  protected function modify_cursed_seer($persona,$key_u)
  {
    $dialog = 'を占った。';
    $pattern = ' ?(.+) は、(.+) を占った。';
    $announce = $this->fetch->find('p.infosp');
    foreach($announce as $item)
    {
      $info = trim($item->plaintext);
      $key= mb_substr($info,-5,5);
      if($key === $dialog)
      {
        $seer = trim(mb_ereg_replace($pattern,'\1',$info,'m'));
        $wolf = trim(mb_ereg_replace($pattern,'\2',$info,'m'));
        if($seer === $persona && in_array($wolf,$this->cursewolf))
        {
          return true;
        }
        else
        {
          continue;
        }
      }
    }
    return false;
  }
}
