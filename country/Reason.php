<?php

class Reason extends Country
{
  private $url_epi;
  private $skill =
    [
        "村人"  =>Data::SKL_VILLAGER
       ,"占い師"=>Data::SKL_SEER
       ,"霊能者"=>Data::SKL_MEDIUM
       ,"狩人"  =>Data::SKL_GUARD
       ,"共有者"=>Data::SKL_FM
       ,"人狼"  =>Data::SKL_WOLF
       ,"狂人"  =>Data::SKL_LUNATIC
    ]; 

  function __construct()
  {
    $cid = 59;
    $url_vil = "http://sui.sib.jp/pc/view_kako/";
    $url_log = "http://sui.sib.jp/pc/index_kako/";
    parent::__construct($cid,$url_vil,$url_log);
  }

  protected function fetch_village()
  {
    $this->fetch_from_pro();
    $this->fetch_from_epi();
    //var_dump($this->village->get_vars());
  }

  protected function fetch_from_pro()
  {
    $this->fetch->load_file($this->url.$this->village->vno);
      sleep(1);

    $this->fetch_name();
    $this->fetch_date();
    $this->fetch_days();

    $this->fetch->clear();
  }
  protected function fetch_name()
  {
    $this->village->name = trim($this->fetch->find('span#village_name',0)->plaintext);
  }
  protected function fetch_date()
  {
    $date = $this->fetch->find('span.character_name',0)->id;
    $this->village->date = date("y-m-d",mb_ereg_replace('mes(.+)c1',"\\1",$date));
  }
  protected function fetch_days()
  {
    $url = $this->fetch->find('div#NaviDay a',-1)->href;
    $this->village->days = (int)mb_ereg_replace(".+view_kako/\d+/(\d+)/.+", "\\1", $url);
    $this->url_epi = $this->url.$this->village->vno.'/'.$this->village->days;
  }

  protected function fetch_from_epi()
  {
    $this->fetch->load_file($this->url_epi);
      sleep(1);
    $this->make_cast();

    //$this->fetch_nop();
    //$this->fetch_rglid();
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
    switch(mb_substr($wtmid,0,3))
    {
      case '全ての': //村勝利
        $this->village->wtmid = Data::TM_VILLAGER;
        break;
      case 'もう人': //狼勝利
        $this->village->wtmid = Data::TM_WOLF;
        break;
      default:
        $this->output_comment('undefined',$wtmid);
        break;
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
        $this->output_comment('n_user');
      }
    }
  }
  protected function fetch_users($person)
  {
    $life = $this->fetch_person(trim($person));

    $this->fetch_sklid();
    $this->fetch_tmid();
    $this->fetch_rltid();

    if($life === '生存')
    {
      $this->user->dtid = Data::DES_ALIVE;
      $this->user->end = $this->village->days;
      $this->user->life = 1.000;
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

  protected function fetch_from_daily($list)
  {
    $days = $this->village->days;
    for($i=2; $i<=$days; $i++)
    {
      $this->fetch->load_file($this->url.$this->village->vno.'/'.$i);
      sleep(1);
      $announce = $this->fetch->find('div.systemmessage_white');
      foreach($announce as $item)
      {
        $destiny = trim(preg_replace("/\r\n/",'',$item->plaintext));
        switch(mb_substr($destiny,-6,6))
        {
          case "突然死した。":
            $persona = mb_ereg_replace("^(.+)は突然死した。", "\\1", $destiny);
            $key = array_search($persona,$list);
            $this->users[$key]->dtid = Data::DES_RETIRED;
            break;
          case "処刑された。":
            $persona = mb_ereg_replace(".+投票した。(.+) は 村.+", "\\1", $destiny,'m');
            $key = array_search($persona,$list);
            $this->users[$key]->dtid = Data::DES_HANGED;
            break;
          case "発見された。":
            $persona = mb_ereg_replace("翌朝、(.+)が無残.+", "\\1", $destiny);
            $key = array_search($persona,$list);
            $this->users[$key]->dtid = Data::DES_EATEN;
            break;
          default:
            continue;
        }   
        $this->users[$key]->end = $i;
        $this->users[$key]->life = round(($i-1) / $this->village->days,3);
      }
      $this->fetch->clear();
    }
  }
  protected function fetch_sklid()
  {
    $this->user->sklid = $this->skill[$this->user->role];
  }
  protected function fetch_tmid()
  {
    if($this->user->role === "人狼" || $this->user->role === "狂人")
    {
      $this->user->tmid = Data::TM_WOLF;
    }
    else
    {
      $this->user->tmid = Data::TM_VILLAGER;
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
}
