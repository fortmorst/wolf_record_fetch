<?php
class Phantom extends SOW
{
  use TRS_SOW;
  private  $is_ruined;
  protected $RP_PRO = [
     'この村にも'=>'SOW'
    ,'なんか人狼'=>'FOOL'
    ,'　村は数十'=>'JUNA'
    ,'昼間は人間'=>'WBBS'
    ,'呼び寄せた'=>'PHANTOM'
    ,'　それはま'=>'DREAM'
    ];
  protected $WTM_RUINED = [
     'SOW'    =>'もう人影はない……。'
    ,'FOOL'   =>'て誰もいなくなった。'
    ,'JUNA'   =>'が忽然と姿を消した。'
    ,'WBBS'   =>'が忽然と姿を消した。'
    ,'PHANTOM'=>'らぬ静けさのみ……。'
    ,'DREAM'  =>'、静かな朝です……。'
    ];
  protected $SKL_SP = [
     "妖狐"=>[Data::SKL_FAIRY,Data::TM_FAIRY]
    ,"天狐"=>[Data::SKL_FRY_WIS,Data::TM_FAIRY]
    ,"冥狐"=>[Data::SKL_PIXY,Data::TM_FAIRY]
    ,"幻魔"=>[Data::SKL_PIXY,Data::TM_FAIRY]
    ,"--"=>[Data::SKL_NULL,Data::TM_NONE]
    ];
  protected $DT_DREAM = [
     'のです……。'=>['.+(\(ランダム投票\)|指差しました。)(.+) は人々の意思により処断されたのです……。',Data::DES_HANGED]
    ,'突然死した。'=>['^( ?)(.+) は、突然死した。',Data::DES_RETIRED]
    ,'されました。'=>['(.+)朝、 ?(.+) が無残.+',Data::DES_EATEN]
    ,'後を追った。'=>['^( ?)(.+) は(絆に引きずられるように) .+ の後を追った。',Data::DES_SUICIDE]
  ];
  function __construct()
  {
    $cid = 47;
    $url_vil = "http://schicksal.sakura.ne.jp/sow/sow.cgi?vid=";
    $url_log = "http://schicksal.sakura.ne.jp/sow/sow.cgi?cmd=oldlog";
    parent::__construct($cid,$url_vil,$url_log);
    $this->SKILL = array_merge($this->SKILL,$this->SKL_SP);
    $this->policy = false;
    $this->is_ruined = false;
  }
  protected function fetch_wtmid()
  {
    $wtmid = $this->fetch_win_message();
    if($wtmid === $this->WTM_RUINED[$this->village->rp])
    {
      $this->is_ruined = true;
    }
    $this->village->wtmid = Data::TM_RP;
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
      if($this->is_ruined)
      {
        //廃村村はリストを作らない
        continue;
      }
      //生存者を除く名前リストを作る
      $list[] = $this->user->persona;
      if($this->user->end !== null)
      {
        unset($list[$key]);
      }
    }
    if($this->is_ruined === false)
    {
      $this->fetch_from_daily($list);
    }

    foreach($this->users as $user)
    {
      if(!$user->is_valid())
      {
        $this->output_comment('n_user');
      }
    }
  }
  protected function fetch_role($person)
  {
    $role = $person->find('td',3)->plaintext;
    if(mb_ereg_match("\A.+を希望\z",$role))
    {
      $this->user->role = '--';
    }
    else
    {
      $this->user->role = mb_ereg_replace('\A(.+) \(.+\)(.+|)','\1',$role,'m');
    }
  }
  protected function modify_cursed_seer($persona,$key_u)
  {
    if($this->village->rp === 'DREAM')
    {
      $dialog = 'みました。';
      $pattern = '　 ?(.+) は、(.+) を詠みました。';
    }
    else
    {
      $dialog = 'を占った。';
      $pattern = ' ?(.+) は、(.+) を占った。';
    }

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
