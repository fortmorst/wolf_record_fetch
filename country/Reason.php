<?php

class Reason extends Country
{
  private $url_epi;

  protected function fetch_village()
  {
    $this->fetch_from_pro();
    $this->fetch_from_epi();
  }

  protected function fetch_from_pro()
  {
    $this->fetch->load_file($this->url);
      sleep(1);

    $this->fetch_name();
    $this->fetch_date();
    $this->fetch_days();
    $this->village->rp = $this->sysword;
    $this->fetch_sysword($this->village->rp);

    $this->fetch->clear();
  }
  protected function fetch_name()
  {
    $this->village->name = trim($this->fetch->find('span#village_name',0)->plaintext);
  }
  protected function fetch_date()
  {
    $date = $this->fetch->find('span.character_name',0)->id;
    $this->village->date = date("Y-m-d",mb_ereg_replace('mes(.+)c1',"\\1",$date));
  }
  protected function fetch_days()
  {
    $url = $this->fetch->find('div#NaviDay a',-1)->href;
    $this->village->days = (int)mb_ereg_replace(".+view_kako/\d+/(\d+)/.+", "\\1", $url);
    $this->url_epi = $this->url.'/'.$this->village->days;
  }

  protected function fetch_from_epi()
  {
    $this->fetch->load_file($this->url_epi);
    sleep(1);
    $this->make_cast();

    $this->fetch_wtmid();

    $this->fetch->clear();
  }

  protected function make_cast()
  {
    $cast = $this->fetch->find('div.systemmessage p',-1)->plaintext;
    $cast = explode("だった。\r",$cast);
    array_pop($cast);
    $this->cast = $cast;
  }
  protected function fetch_wtmid()
  {
    $wtmid = trim($this->fetch->find('div.systemmessage p',-2)->plaintext);
    $wtmid = preg_replace("/\A([^\r\n]+)(\r\n.+)?\z/ms", "$1", $wtmid);
    if($this->check_syswords($wtmid,'wtmid'))
    {
      $this->village->wtmid = $GLOBALS['syswords'][$this->village->rp]->mes_wtmid[$wtmid];
    }
    else
    {
      $this->village->wtmid = Data::TM_RP;
      $this->output_comment('undefined',__FUNCTION__,$wtmid);
    }
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
      $list[] = $this->user->persona;
      if($this->user->dtid === Data::DES_ALIVE)
      {
        unset($list[$key]);
      }
    }
    $this->fetch_from_daily($list);

    foreach($this->users as $user)
    {
      //var_dump($user->get_vars());
      if(!$user->is_valid())
      {
        $this->output_comment('n_user',__function__,$user->persona);
      }
    }
  }
  protected function fetch_users($person)
  {
    $life = $this->fetch_person(trim($person));

    $this->fetch_sklid();
    $this->fetch_rltid();

    if($life === '生存')
    {
      $this->insert_alive();
    }
  }
  protected function fetch_person($person)
  {
    $pattern = "([^（]+)（(.+)）、(生存|死亡)。(.+)$";

    $player = mb_ereg_replace($pattern,'\\2',$person);
    $this->user->player =$this->modify_player($player);

    $this->user->persona = mb_ereg_replace($pattern,'\\1',$person);
    $this->user->role = mb_ereg_replace($pattern,'\\4',$person);

    return mb_ereg_replace($pattern,'\\3',$person);;
  }
  protected function fetch_sklid()
  {
    if($this->check_syswords($this->user->role,"sklid"))
    {
      $this->user->sklid = $GLOBALS['syswords'][$this->village->rp]->mes_sklid[$this->user->role]['sklid'];
      if($this->user->sklid === Data::SKL_LUNATIC)
      {
        $this->user->tmid = Data::TM_WOLF;
      }
      else
      {
        $this->user->tmid = $GLOBALS['syswords'][$this->village->rp]->mes_sklid[$this->user->role]['tmid'];
      }
    }
    else
    {
      $this->user->sklid= null;
      $this->user->tmid= null;
      $this->output_comment('undefined',__FUNCTION__,$this->user->role);
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
    $days = $this->village->days;
    for($i=2; $i<=$days; $i++)
    {
      $this->fetch->load_file($this->url.'/'.$i);
      sleep(1);
      $announce = $this->fetch->find('div.systemmessage_white');
      foreach($announce as $item)
      {
        $key_u = $this->fetch_key_u($list,$item);
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
  protected function fetch_key_u($list,$item)
  {
    $destiny = trim(preg_replace("/\r\n/",'',$item->plaintext));

    //突然死メッセージが6文字
    $is_retired = mb_strpos($destiny,'は突然死');
    if($is_retired !== false)
    {
      $dtid = Data::DES_RETIRED;
      $persona = mb_substr($destiny,0,$is_retired);
    }
    else
    {
      $key = mb_substr(trim($item->plaintext),-8,8);
      if($this->check_syswords($key,'dt_sys'))
      {
        $regex = $GLOBALS['syswords'][$this->village->rp]->mes_dt_sys[$key]['regex'];
        $dtid  = $GLOBALS['syswords'][$this->village->rp]->mes_dt_sys[$key]['dtid']; 
      }
      else
      {
        return false;
      }

      $persona = trim(mb_ereg_replace($regex,'\2',$destiny,'m'));
    }

    $key_u = array_search($persona,$list);
    if($key_u === false)
    {
      return false;
    }
    $this->users[$key_u]->dtid = $dtid;
    return $key_u;
  }
}
