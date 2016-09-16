<?php

class Moon extends SOW_MOD
{
  protected $EVIL_ROLE = [Data::SKL_EVIL,Data::SKL_EVL_KNOW_WF,Data::SKL_EVL_SEER_ROLE,Data::SKL_EVL_MIMIC];

  protected function fetch_policy()
  {
    $policy= mb_strstr($this->fetch->find('div.paragraph',2)->find('p.multicolumn_left',19)->plaintext,'推理');
    if($policy !== false)
    {
      $this->village->policy = true;
    }
    else
    {
      $this->village->policy = false;
    }
  }
  protected function fetch_rp()
  {
    $rp = trim($this->fetch->find('div.paragraph',2)->find('p.multicolumn_left',3)->plaintext);
    $this->village->rp = $rp.$this->sysword;
    if(!isset($GLOBALS['syswords'][$this->village->rp]))
    {
      $this->fetch_sysword($this->village->rp);
    }
  }
  protected function make_sysword_sql($rp)
  {
    return "select name,mes_sklid,mes_dtid,mes_wtmid from sysword where name='$rp'";
  }
  protected function make_sysword_set($values,$table,$name)
  {
    $sql = "SELECT * from $table where id in ($values)";
    $stmt = $this->db->query($sql);
    $list = [];
    if($table === 'mes_sklid')
    {
      $sql = "SELECT m.name,orgid,tmid,evil_flg FROM mes_sklid m JOIN skill s ON m.orgid = s.id JOIN team t ON s.tmid = t.id WHERE m.id IN ($values)";
      $stmt = $this->db->query($sql);
      foreach($stmt as $item)
      {
        $list[$item['name']] = ['sklid'=>(int)$item['orgid'],'tmid'=>(int)$item['tmid'],'evil_flg'=>(bool)$item['evil_flg']];
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
  protected function fetch_win_message()
  {
    $wtmid = trim($this->fetch->find('p.info',-1)->plaintext);
    $not_wtm  = '/見物に/';
    //遅刻見物人のシスメなどを除外
    if(preg_match($not_wtm,$wtmid))
    {
      $do_i = -2;
      do
      {
        $wtmid = trim($this->fetch->find('p.info',$do_i)->plaintext);
        $do_i--;
      } while(preg_match($not_wtm,$wtmid));
    }
    $wtmid = preg_replace("/\A([^\r\n]+)(\r\n.+)?\z/ms", "$1", $wtmid);
    return $wtmid;
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
        $this->output_comment('n_user',__function__,$this->user->persona);
      }
      $this->users[] = $this->user;
    }
    foreach($this->users as $key=>$user)
    {
      if($user->tmid === Data::TM_EVIL)
      {
        $this->change_evil_team_moon($key,$user);
      }
      //var_dump($user->get_vars());
    }
  }
  protected function fetch_users($person)
  {
    $this->fetch_persona($person);
    $this->fetch_player($person);

    $this->fetch_role($person);
    $this->fetch_dtid($person);

    if($this->user->dtid === Data::DES_ONLOOKER)
    {
      $this->insert_onlooker();
      return;
    }

    if($this->user->dtid === Data::DES_ALIVE)
    {
      $this->insert_alive();
    }
    else
    {
      $this->fetch_end($person);
    }

    $this->fetch_sklid();
    //_SOWにしない
    $this->fetch_rltid($person);
  }
  protected function fetch_role($person)
  {
    $role = $person->find('td',4);
    if(!empty($role))
    {
      $role = $role->plaintext;

      if(mb_strpos($role,"人真似師") !== false)
      {
        $this->user->role = mb_ereg_replace('\A人真似師/(.+) \(.+を希望\)(.+|)','\1',$role,'m');
      }
      else
      {
        $this->user->role = mb_ereg_replace('\A(.+) \(.+を希望\)(.+|)','\1',$role,'m');
      }
    }
    else
    {
      $this->user->role = '見物人';
    }
  }
  protected function fetch_sklid()
  {
    if($this->check_syswords($this->user->role,"sklid"))
    {
      $this->user->sklid = $GLOBALS['syswords'][$this->village->rp]->mes_sklid[$this->user->role]['sklid'];
      $this->user->tmid = $GLOBALS['syswords'][$this->village->rp]->mes_sklid[$this->user->role]['tmid'];
      if($this->is_evil && $GLOBALS['syswords'][$this->village->rp]->mes_sklid[$this->user->role]['evil_flg'])
      {
        $this->village->evil_rgl = true;
      }
    }
    else
    {
      $this->user->sklid= null;
      $this->user->tmid= null;
      $this->output_comment('undefined',__FUNCTION__,$this->user->role);
    }
  }
  protected function change_evil_team_moon($key,$user)
  {
  if($this->village->evil_rgl !== true || ($this->village->evil_rgl === true && array_search($this->users[$key]->sklid,$this->EVIL_ROLE) === false))
    {
      $this->users[$key]->tmid = Data::TM_WOLF;
    }
  }
  protected function fetch_rltid($result)
  {
    if($this->village->wtmid === Data::TM_RP)
    {
      $this->user->rltid = Data::RSL_JOIN;
    }
    else
    {
      switch($result->find('td',3)->plaintext)
      {
        case '勝利':
          $this->user->rltid = Data::RSL_WIN;
          break;
        case '敗北':
          $this->user->rltid = Data::RSL_LOSE;
          break;
        case '': //突然死
          $this->user->rltid = Data::RSL_INVALID;
          break;
        default:
          $this->output_comment('undefined',__FUNCTION__,$result);
          $this->user->rltid = Data::RSL_JOIN;
          break;
      }
    }
  }
}
