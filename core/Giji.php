<?php

abstract class Giji extends Country
{
  protected $base;
  protected $GIFT = [
     2=>'喪失'
    ,3=>'感染'
    ,5=>'光の輪'
    ,6=>'魔鏡'
    ,7=>'悪鬼'
    ,8=>'妖精の子'
    ,9=>'半端者'
    ,11=>'決定者'
    ,12=>'夢占師'
    ,13=>'酔払い'
    ];
  protected $BAND = [
     "love"=>"恋人"
    ,"hate"=>"邪気"
    ];

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
      $this->output_comment('ruin_midway',__function__);
    }
    //else
    //{
      //if($this->policy === null)
      //{
        //$this->fetch_policy();
      //}
      //$this->fetch_wtmid();
    //}

    //現在Cielのみ
    $this->village->wtmid = Data::TM_RP;

    $this->make_cast();
    $this->check_sprule();
    $this->village->rp = '新議事';
    $this->fetch_sysword($this->village->rp);
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
    //秘話村かどうか
    if(preg_match("/秘話/",$this->village->name))
    {
      $this->village->rglid = Data::RGL_SECRET;
      return;
    }
    $rule = preg_replace('/.+"game_name": "([^"]*)",.+/s',"$1",$this->base);
    switch($rule)
    {
      case 'ミラーズホロウ（死んだら負け）':
        $this->village->rglid = Data::RGL_MILL;
        break;
      case 'タブラの人狼（死んだら負け）':
        $this->village->rglid = Data::RGL_DEATH;
        break;
      default:
        $sql = "SELECT id FROM regulation where name='$rule'";
        $stmt = $this->db->query($sql);
        if($stmt === false)
        {
          $this->output_comment('undefined',__FUNCTION__,$rule);
        }
        else
        {
          $stmt = $stmt->fetch();
          $this->village->rglid = $stmt['id'];
        }
        break;
    }
  }
  protected function make_sysword_sql($rp)
  {
    return "select name,mes_sklid,mes_tmid,mes_dtid from sysword where name='$rp'";
  }
  protected function make_sysword_set($values,$table,$name)
  {
    if($table === 'mes_sklid')
    {
      $sql = "SELECT m.name,orgid,s.name orgname from mes_sklid m join skill s on orgid = s.id where m.id in ($values)";
    }
    else
    {
      $sql = "SELECT * from $table where id in ($values)";
    }

    $stmt = $this->db->query($sql);
    $list = [];

    if($table === 'mes_sklid')
    {
      //裏切り陣営考慮外
      foreach($stmt as $item)
      {
        $list[$item['name']] = ['sklid'=>(int)$item['orgid'],'orgname'=>$item['orgname']];
      }
    }
    else
    {
      foreach($stmt as $item)
      {
        $list[$item['name']] = (int)$item['orgid'];
      }
    }
    $GLOBALS['syswords'][$name]->{$table} = $list;
  }
  //現在Cielのみ
  protected function fetch_wtmid()
  {
    //if($this->policy || $this->village->policy)
    //{
      //$policy = preg_replace('/.+"rating": "([^"]*)".+/s',"$1",$this->base);
      //switch($policy)
      //{
        //case "とくになし":
        //case "[言] 殺伐、暴言あり":
        //case "[遖] あっぱれネタ風味":
        //case "[張] うっかりハリセン":
        //case "[全] 大人も子供も初心者も、みんな安心":
        //case "[危] 無茶ぶり上等":
          //$wtmid = preg_replace('/.+"winner": giji\.event\.winner\((\d+)\),.+/s',"$1",$this->base);
          //$this->village->wtmid = $this->fetch_from_sysword($wtmid,'wtmid');
          //break;
        //default:
          //$this->village->wtmid = Data::TM_RP;
          //$this->output_comment('rp',__function__,$policy);
          //break;
      //}
    //}
    //else
    //{
      //$this->village->wtmid = Data::TM_RP;
    //}
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
      var_dump($this->user->get_vars());
    }
    //Cielは裏切り陣営なし
    //if($this->is_evil === true && $this->village->evil_rgl !== true)
    //{
      //$this->change_evil_team();
    //}
  }
  protected function fetch_users($person)
  {
    $this->fetch_persona($person);
    $this->fetch_player($person);
    $this->fetch_tmid($person);
    $this->fetch_dtid($person);

    //Cielは裏切り陣営なし
    if($this->user->tmid  === Data::TM_EVIL)
    {
      $this->users[$key]->tmid = Data::TM_WOLF;
    }

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
    $dtid =preg_replace('/.+"live": "([^"]*)",.+/s',"$1",$person);
    $this->fetch_from_sysword($dtid,'dtid');
  }
  protected function fetch_tmid($person)
  {
    $tmid = preg_replace('/.+visible: "([^"]*)",.+/s',"$1",$person);
    $this->fetch_from_sysword($tmid,'tmid');

    //if($this->is_evil && $this->TEAM[$tmid][1])
    //{
      //$this->village->evil_rgl = true;
    //}
  }
  //Cielは裏切り陣営なし
  //protected function change_evil_team()
  //{
    //foreach($this->users as $key=>$user)
    //{
      //if($this->user->tmid  === Data::TM_EVIL)
      //{
        //$this->users[$key]->tmid = Data::TM_WOLF;
      //}
    //}
  //}
  protected function fetch_role($person)
  {
    $skill = preg_replace('/.+giji\.potof\.roles\((\d+), -?\d+\);.+/s',"$1",$person);
    $this->user->sklid = $GLOBALS['syswords'][$this->village->rp]->mes_sklid[$skill]['sklid'];

    //役職名を出力
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
      $this->user->role = $GLOBALS['syswords'][$this->village->rp]->mes_sklid[$skill]['orgname'].'、'.implode('、',$after_role);;
    }
    else
    {
      $this->user->role = $GLOBALS['syswords'][$this->village->rp]->mes_sklid[$skill]['orgname'];
    }
    //守護者と結社員に変更
    switch($this->user->role)
    {
      case "狩人":
        $this->user->role = "守護者";
        break;
      case "共有者":
        $this->user->role = "結社員";
        break;
      default:
        break;
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
