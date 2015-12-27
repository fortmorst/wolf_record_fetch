<?php
class BW extends SOW
{
  use TRS_SOW;
  protected $RP_PRO = [
     '影が忍び寄'=>'BW'
    ,'平和な日々'=>'FOREST'
    ,'大きな時計'=>'CLOCK'
    ];
  protected $WTM_BW= [
     'のを感じていた……。'=>Data::TM_VILLAGER
    ,'墟だけが残っていた。'=>Data::TM_WOLF
    ,'いていなかった……。'=>Data::TM_FAIRY
  ];
  protected $WTM_FOREST= [
     'る日々は去ったのだ。'=>Data::TM_VILLAGER
    ,'残して去っていった。'=>Data::TM_WOLF
    ,'いていなかった……。'=>Data::TM_FAIRY
  ];
  protected $WTM_CLOCK= [
     'らず持ち主の傍らに。'=>Data::TM_VILLAGER
    ,'い取りましょうか…？'=>Data::TM_WOLF
    ,'いていなかった……。'=>Data::TM_FAIRY
  ];
  protected $SKL_CLOCK = [
     "『時計』の主"=>[Data::SKL_VILLAGER,Data::TM_VILLAGER]
    ,"時間泥棒"=>[Data::SKL_WOLF,Data::TM_WOLF]
    ,"時詠み"=>[Data::SKL_SEER,Data::TM_VILLAGER]
    ,"時計鑑定士"=>[Data::SKL_MEDIUM,Data::TM_VILLAGER]
    ,"時間泥棒を呼んだ者"=>[Data::SKL_LUNATIC,Data::TM_WOLF]
    ,"贋作師"=>[Data::SKL_GUARD,Data::TM_VILLAGER]
    ,"『クロノス』ギルド員"=>[Data::SKL_FM,Data::TM_VILLAGER]
    ,"時の精霊"=>[Data::SKL_FAIRY,Data::TM_FAIRY]
    ,"共犯者"=>[Data::SKL_WHISPER,Data::TM_WOLF]
    ,"『クロノス』ギルド員見習い"=>[Data::SKL_STIGMA,Data::TM_VILLAGER]
    ,"時間泥棒の協力者"=>[Data::SKL_FANATIC,Data::TM_WOLF]
    ,"『クロノス』上級ギルド員"=>[Data::SKL_FM_WIS,Data::TM_VILLAGER]
    ,"同じ日に生まれた時の精霊"=>[Data::SKL_FRY_WIS,Data::TM_FAIRY]
    ,"時詠み返し"=>[Data::SKL_WOLF_CURSED,Data::TM_WOLF]
    ,"時計蒐集家"=>[Data::SKL_WISEWOLF,Data::TM_WOLF]
    ,"時結び"=>[Data::SKL_PIXY,Data::TM_FAIRY]
    ];
  protected $DESTINY = [
     "突死"=>Data::DES_RETIRED
    ,"処刑"=>Data::DES_HANGED
    ,"襲撃"=>Data::DES_EATEN
    ,"呪殺"=>Data::DES_CURSED
    ,"逆呪"=>Data::DES_CURSED
    ,"連死"=>Data::DES_SUICIDE
    ];
  protected function fetch_policy()
  {
    parent::fetch_policy();
    if($this->village->policy === true)
    {
      $policy = $this->fetch->find('p.multicolumn_left',8)->plaintext;
      if(preg_match('/物語/',$policy))
      {
        $this->village->policy = false;
        $this->output_comment('rp');
      }
    }
  }
  protected function fetch_date()
  {
    $date = $this->fetch->find('td.time_info span',0)->plaintext;
    $date = mb_substr($date,0,10);
    $this->village->date = preg_replace('/(\d{4})\/(\d{2})\/(\d{2})/','\1-\2-\3',$date);
  }

  protected function make_cast()
  {
    $cast = $this->fetch->find('table.castlist tbody tr');
    array_shift($cast);
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
        $this->output_comment('n_user');
      }
      $this->users[] = $this->user;
    }
  }
  protected function fetch_users($person)
  {
    $this->user->persona = trim($person->find('td',0)->plaintext);
    $this->fetch_player($person);
    $this->fetch_role($person);
    $this->fetch_end($person);
    $this->fetch_sklid();
    $this->fetch_rltid();
  }
  protected function fetch_role($person)
  {
    $role = $person->find('td',4)->plaintext;
    $this->user->role = mb_ereg_replace('\A(.+) \(.+\)(.+|)','\1',$role,'m');
  }
  protected function fetch_end($person)
  {
    $destiny = trim($person->find('td',3)->plaintext);
    if($destiny === '生存')
    {
      $this->user->dtid = Data::DES_ALIVE;
      $this->user->end = $this->village->days;
      $this->user->life = 1.000;
    }
    else
    {
      $this->user->dtid = $this->DESTINY[mb_ereg_replace('\d+d(.+)','\1',$destiny)];
      $this->user->end = (int)mb_ereg_replace('(\d+)d.+','\1',$destiny);
      $this->user->life = round(($this->user->end-1) / $this->village->days,3);
    }
  }
}
