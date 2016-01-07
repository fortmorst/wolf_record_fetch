<?php
class Sebas extends SOW
{
  use TRS_SOW;
  protected $RP_SP = [
     '鬼ごっこ'=>'ONI'
    ,'無茶振り人狼'=>'JUNA'
    ,'ガチっているフリ'=>'FOOL'
  ];
  protected $WTM_ONI = [
     'した……！ぜぇはぁ。'=>Data::TM_VILLAGER
    ,'テープを切りました。'=>Data::TM_WOLF
    ,'時代が到来しました。'=>Data::TM_FAIRY
  ];
  protected $SKL_SP = [
     "鬼（人狼）"=>[Data::SKL_WOLF,Data::TM_WOLF]
    ,"狐"=>[Data::SKL_FAIRY,Data::TM_FAIRY]
    ,"天狗"=>[Data::SKL_FRY_WIS,Data::TM_FAIRY]
    ,"呪鬼"=>[Data::SKL_WOLF_CURSED,Data::TM_WOLF]
    ,"智鬼"=>[Data::SKL_WISEWOLF,Data::TM_WOLF]
    ,"悪戯っ子"=>[Data::SKL_PIXY,Data::TM_FAIRY]
  ];
  protected $DT_SP = [
     '生き'=>Data::DES_ALIVE
    ,"突然"=>Data::DES_RETIRED
    ,"処刑"=>Data::DES_HANGED
    ,"襲撃"=>Data::DES_EATEN
    ,"呪殺"=>Data::DES_CURSED
    ,"後追"=>Data::DES_SUICIDE
  ];

  function set_village_data()
  {
    $this->RP_LIST = array_merge($this->RP_LIST,$this->RP_SP);
    $this->SKILL = array_merge($this->SKILL,$this->SKL_SP);
  }

  protected function fetch_days()
  {
    $days = trim($this->fetch->find('p.turnnavi',1)->find('a',-1)->href);
    $days = preg_replace('/.+turn=(\d+)&amp.+/','\1',$days) -1;
    if($days === 1)
    {
      $this->insert_as_ruin();
      return false;
    }
    else
    {
      $this->village->days = $days;
    }
  }
  protected function check_ruin()
  {
    $info = 'div.info';
    $infosp = 'div.infosp';

    if(count($this->fetch->find($info)) <= 1 && count($this->fetch->find($infosp)) === 0)
    {
      return false;
    }
    else
    {
      return true;
    }
  }
  protected function fetch_rp()
  {
    $rp = trim($this->fetch->find('p.multicolumn_left',8)->plaintext);
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
  protected function fetch_policy()
  {
    $policy = $this->fetch->find('p.multicolumn_left',1)->plaintext;
    if($policy === "推理あり村")
    {
      $this->village->policy = true;
    }
    else
    {
      $this->village->policy = false;
      $this->output_comment('rp');
    }
  }
  protected function fetch_date()
  {
    $date = $this->fetch->find('div.mes_date',0)->plaintext;
    $date = mb_substr(preg_replace('/ /','0',$date),mb_strpos($date,"2"),10);
    $this->village->date = preg_replace('/(\d{4})\/(\d{2})\/(\d{2})/','\1-\2-\3',$date);
  }
  protected function fetch_win_message()
  {
    $not_wtm = "/0に設定されました。|村の設定が変更|に変更します。/";
    $wtmid = trim($this->fetch->find('div.info',-1)->plaintext);
    if(preg_match($not_wtm,$wtmid))
    {
      $do_i = -2;
      do
      {
        $wtmid = trim($this->fetch->find('div.info',$do_i)->plaintext);
        $do_i--;
      } while(preg_match($not_wtm,$wtmid));
    }
    return mb_substr(preg_replace("/\r\n/","",$wtmid),-10);
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
      //エラーでも歯抜けが起きないように入れる
      $this->users[] = $this->user;
    }
  }
  protected function fetch_users($person)
  {
    $this->fetch_persona($person);
    $this->fetch_player($person);
    $this->fetch_role($person);

    if($this->user->role === '参観者' || $this->user->role === '観てるだけ')
    {
      $this->insert_onlooker();
    }
    else
    {
      $this->fetch_sklid();
      $this->fetch_destiny($person);
      $this->fetch_rltid();
      $this->fetch_life();
    }
  }
  protected function fetch_role($person)
  {
    $role = $person->find('td',3)->plaintext;
    $this->user->role = trim(mb_ereg_replace('\A([^\r\n]+)(\r\n.+|)','\1',$role,'m'));
  }
  protected function fetch_destiny($person)
  {
    $destiny = $person->find('td',2)->plaintext;
    $pattern = '/(\d+)日(目に|間を)(.{6}).+/';
    preg_match_all($pattern,$destiny,$matches);
    $this->user->dtid = $this->DT_SP[$matches[3][0]];
    $this->user->end = (int)$matches[1][0];
  }
  protected function fetch_life()
  {
    if($this->user->dtid === Data::DES_ALIVE)
    {
      $this->user->life = 1.000;
    }
    else
    {
      $this->user->life = round(($this->user->end-1) / $this->village->days,3);
    }
  }
}
