<?php

abstract class Giji extends Country
{
  use TRS_Giji;
  protected $base;

  function fetch_village()
  {
    $this->fetch->load_file($this->url."#mode=info_open_player");
    sleep(1);
    $this->base = $this->fetch->find('script',-2)->innertext;

    $this->fetch_name();
    $this->fetch_date();

    $is_not_scrap = $this->check_ruin();
    $is_days_not_empty = $this->fetch_days();
    if(!$is_not_scrap && $is_days_not_empty === false)
    {
      //開始前廃村
      $this->fetch->clear();
      return;
    }
    else if(!$is_not_scrap)
    {
      //進行中廃村
      $this->village->wtmid = Data::TM_RP;
      $this->output_comment('ruin_midway',__function__);
    }
    else
    {
      if($this->policy === null)
      {
        $this->fetch_policy();
      }
      $this->fetch_wtmid();
    }

    $this->make_cast();
    $this->check_sprule();
    
  }
  protected function fetch_name()
  {
    $this->village->name = preg_replace('/.*?\),.*?"name":    "([^"]*)",.+/s',"$1",$this->base);
  }
  protected function fetch_date()
  {
    $date = preg_replace('/.+"updateddt":    new Date\(1000 \* (\d+)\),.+/s',"$1",$this->base);
    $this->village->date = date('Y-m-d',$date);
  }
  protected function check_ruin()
  {
    $scrap = mb_ereg_replace('.+"is_scrap":     \(0 !== (\d)\),.+',"\\1",$this->base,'m');
    return ($scrap !== '1') ? true:false;
  }
  protected function fetch_days()
  {
    $days = (int)preg_replace('/.+"turn": (\d+).+/s',"$1",$this->base);
    if($days === 1)
    {
      $this->insert_as_ruin();
      return false;
    }
    $this->village->days = $days;
  }
  protected function check_sprule()
  {
    $rule = preg_replace('/.+"game_name": "([^"]*)",.+/s',"$1",$this->base);
    if(array_key_exists($rule,$this->RGL_SP))
    {
      $this->village->rglid = $this->RGL_SP[$rule];
    }
    else if(preg_match("/秘話/",$this->village->name))
    {
      $this->village->rglid = Data::RGL_SECRET;
    }
  }
  protected function fetch_wtmid()
  {
    if($this->policy || $this->village->policy)
    {
      $policy = preg_replace('/.+"rating": "([^"]*)".+/s',"$1",$this->base);
      switch($policy)
      {
        case "とくになし":
        case "[言] 殺伐、暴言あり":
        case "[遖] あっぱれネタ風味":
        case "[張] うっかりハリセン":
        case "[全] 大人も子供も初心者も、みんな安心":
        case "[危] 無茶ぶり上等":
          $this->village->wtmid = $this->WTM[preg_replace('/.+"winner": giji\.event\.winner\((\d+)\),.+/s',"$1",$this->base)];
          break;
        default:
          $this->village->wtmid = Data::TM_RP;
          $this->output_comment('rp',__function__);
          break;
      }
    }
    else
    {
      $this->village->wtmid = Data::TM_RP;
    }
  }
  protected function make_cast()
  {
    $cast = explode("gon.potofs",$this->base);
    array_shift($cast);
    array_pop($cast);
    $this->cast = $cast;
  }

  protected function insert_users()
  {
    $this->users = [];
    foreach($this->cast as $person)
    {
      $this->user = new User();
      $this->fetch_users($person);
      if(!$this->user->is_valid())
      {
        $this->output_comment('n_user',__function__);
      }
      $this->users[] = $this->user;
    }
    if($this->is_evil === true && $this->village->evil_rgl !== true)
    {
      $this->change_evil_team();
    }
  }
  protected function fetch_users($person)
  {
    $this->fetch_persona($person);
    $this->fetch_player($person);
    $this->fetch_dtid($person);
    $this->fetch_tmid($person);

    $this->fetch_role($person);
    $this->fetch_end($person);
    $this->fetch_rltid($person);
    $this->fetch_life($person);
  }
  protected function fetch_persona($person)
  {
    $this->user->persona = preg_replace('/.+"longname": "([^"]*)",.+/s',"$1",$person);
  }
  protected function fetch_player($person)
  {
    $player = preg_replace('/.+sow_auth_id = "([^"]*)".+/s',"$1",$person);
    $this->user->player =$this->modify_player($player);
  }
  protected function fetch_dtid($person)
  {
    $this->user->dtid =$this->DESTINY[preg_replace('/.+"live": "([^"]*)",.+/s',"$1",$person)];
  }
  protected function fetch_tmid($person)
  {
    $tmid = preg_replace('/.+visible: "([^"]*)",.+/s',"$1",$person);
    $this->user->tmid =$this->TEAM[$tmid][0];

    if($this->is_evil && $this->TEAM[$tmid][1])
    {
      $this->village->evil_rgl = true;
    }
  }
  protected function change_evil_team()
  {
    foreach($this->users as $key=>$user)
    {
      if($this->user->tmid  === Data::TM_EVIL)
      {
        $this->users[$key]->tmid = Data::TM_WOLF;
      }
    }
  }
  protected function fetch_role($person)
  {
    $sklid = preg_replace('/.+giji\.potof\.roles\((\d+), -?\d+\);.+/s',"$1",$person);
    $this->user->sklid =$this->SKILL[$sklid][0];

    $gift = (int)preg_replace('/.+giji\.potof\.roles\(\d+, (-?\d+)\);.+/s',"$1",$person);
    $love = preg_replace('/.+pl\.love = "([^"]*)".+/s',"$1",$person);
    //恩恵か恋邪気絆があれば追加
    if($gift >= 2 || $love !== '')
    {
      $after_role = [];
      if($gift >= 2)
      {
        $after_role[] = $this->GIFT[$gift];
      }
      if($love !== '')
      {
        $after_role[] = $this->BAND[$love];
      }
      $this->user->role = $this->SKILL[$sklid][1].'、'.implode('、',$after_role);
    }
    else
    {
      $this->user->role = $this->SKILL[$sklid][1];
    }
  }
  protected function fetch_end($person)
  {
    $end = (int)preg_replace('/.+"deathday": (-*\d+),.+/s',"$1",$person);
    switch($end)
    {
      case -2: //見物人
        $this->user->end = 1;
        break;
      case -1: //生存者
        $this->user->end = $this->village->days;
        break;
      default:
        $this->user->end = $end;
        break; 
    }
  }
  protected function fetch_rltid($person)
  {
    if($this->user->sklid === Data::SKL_ONLOOKER)
    {
      $this->user->rltid = Data::RSL_ONLOOKER;
    }
    else if($this->village->wtmid === Data::TM_RP)
    {
      $this->user->rltid = Data::RSL_JOIN;
    }
    else
    {
      $rltid = preg_replace('/.+result:  "([^"]*)".+/s',"$1",$person);
      $this->user->rltid = $this->RSL[$rltid];
    }
  }
}
