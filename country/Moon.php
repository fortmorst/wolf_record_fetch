<?php

class Moon extends SOW
{
  use TRS_SOW;
  protected $SKL_SP = [
     '辻占狂人'=>[Data::SKL_LUNA_SEER_MELON,Data::TM_WOLF]
    ,'叫迷狂人'=>[Data::SKL_LUNA_WIS,Data::TM_WOLF]
    ,'首無騎士'=>[Data::SKL_HEADLESS,Data::TM_WOLF]
    ,'碧狼'=>[Data::SKL_HEADLESS_NOTALK,Data::TM_WOLF]
    ,'賢者'=>[Data::SKL_SEER_ROLE,Data::TM_VILLAGER]
    ,'霊媒師'=>[Data::SKL_MEDI_ROLE,Data::TM_VILLAGER]
    ,'神託者'=>[Data::SKL_MEDI_EATEN,Data::TM_VILLAGER]
  ];
  protected $RP_SP = [
     '月狼'=>'MOON'
    ,'人狼署'=>'POLICE'
    ,'月狼学園'=>'SCHOOL'
  ];
  protected $WTM_POLICE= [
     '平和は守られたのだ！'=>Data::TM_VILLAGER
    ,'の遠吠えが響くのみ。'=>Data::TM_WOLF
    //,'が残っていたのです。'=>Data::TM_FAIRY
  ];
  protected $WTM_MOON= [
     'は終わったのだ――！'=>Data::TM_VILLAGER
    ,'達の楽園なのだ――！'=>Data::TM_WOLF
    ,'の始まりである――。'=>Data::TM_FAIRY
  ];
  protected $WTM_SCHOOL= [
     'は終わったのだ――！'=>Data::TM_VILLAGER
    ,'う学舎ではない――。'=>Data::TM_WOLF
    ,'の始まりである――。'=>Data::TM_FAIRY
  ];
  protected $DT_MOON = [
     '儚く散った。'=>['.+(\(ランダム投票\)|置いた。)(.+) の命が儚く散った。',Data::DES_HANGED]
    ,'突然死した。'=>['^( ?)(.+) は、突然死した。',Data::DES_RETIRED]
    ,'ていた……。'=>['(.+)朝、(.+) の姿が消.+',Data::DES_EATEN]
    ,'後を追った。'=>['^( ?)(.+) は(絆に引きずられるように|哀しみに暮れて) .+ の後を追った。',Data::DES_SUICIDE]
  ];
  protected $DESTINY = [
     "突然死"=>Data::DES_RETIRED
    ,"処刑死"=>Data::DES_HANGED
    ,"襲撃死"=>Data::DES_EATEN
    ,"呪殺"=>Data::DES_CURSED
    ,"後追死"=>Data::DES_SUICIDE
    ];
  function __construct()
  {
    $cid = 56;
    $url_vil = "http://managarmr.sakura.ne.jp/sow.cgi?vid=";
    $url_log = "http://managarmr.sakura.ne.jp/sow.cgi?cmd=oldlog";
    parent::__construct($cid,$url_vil,$url_log);
    $this->RP_LIST = array_merge($this->RP_LIST,$this->RP_SP);
    $this->SKILL = array_merge($this->SKILL,$this->SKL_SP);
  }
  protected function fetch_policy()
  {
    $policy= mb_strstr($this->fetch->find('p.multicolumn_left',-1)->plaintext,'推理');
    if($policy !== false)
    {
      $this->village->policy = true;
    }
    else
    {
      $this->village->policy = false;
      $this->output_comment('rp');
    }
  }
  protected function fetch_rp()
  {
    $rp = trim($this->fetch->find('div.paragraph',2)->find('p.multicolumn_left',3)->plaintext);
    if(array_key_exists($rp,$this->RP_LIST))
    {
      $this->village->rp = $this->RP_LIST[$rp];
    }
    else
    {
      $this->village->rp = 'SOW';
      $this->output_comment('undefined',$rp);
    }
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
        $this->output_comment('n_user');
      }
      $this->users[] = $this->user;
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
      $this->fetch_end($person);
    }
  }
  protected function fetch_role($person)
  {
    $role = $person->find('td',4)->plaintext;
    $this->user->role = mb_ereg_replace('\A(.+) \(.+を希望\)(.+|)','\1',$role,'m');
  }
  protected function fetch_end($person)
  {
    $destiny = trim($person->find('td',2)->plaintext);
    if($destiny === '生存')
    {
      $this->user->dtid = Data::DES_ALIVE;
      $this->user->end = $this->village->days;
      $this->user->life = 1.000;
    }
    else
    {
      $this->user->dtid = $this->DESTINY[mb_ereg_replace('\d+日(.+)','\1',$destiny)];
      $this->user->end = (int)mb_ereg_replace('(\d+)日.+','\1',$destiny);
      $this->user->life = round(($this->user->end-1) / $this->village->days,3);
    }
  }
}
