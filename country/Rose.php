<?php
class Rose extends SOW
{
  protected function fetch_rp()
  {
    $this->check_sprule();

    $rp = trim($this->fetch->find('p.multicolumn_left',7)->plaintext);
    $this->village->rp = $rp.$this->sysword;
    //言い換えリストに登録がなければ追加
    if(!isset($GLOBALS['syswords'][$this->village->rp]))
    {
      $this->fetch_sysword($this->village->rp);
    }
  }
  protected function check_sprule()
  {
    if(preg_match("/秘話/",$this->village->name))
    {
      $this->village->rglid = Data::RGL_SECRET;
    }
  }
  protected function make_sysword_sql($rp)
  {
    return "select name,mes_sklid,mes_tmid,mes_dtid,mes_dt_sys,mes_wtmid from sysword where name='$rp'";
  }
  protected function make_sysword_set($values,$table,$name)
  {
    $sql = "SELECT * from $table where id in ($values)";
    $stmt = $this->db->query($sql);
    $list = [];

    if($table === 'mes_dt_sys')
    {
      foreach($stmt as $item)
      {
        $list[$item['name']] = ['regex'=>$item['regex'],'dtid'=>(int)$item['orgid']];
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

  protected function fetch_policy_detail()
  {
    $policy = $this->fetch->find('p.multicolumn_left',11)->plaintext;
    if(preg_match('/一般|初心者歓迎/',$policy))
    {
      $this->village->policy = true;
    }
    else
    {
      $this->village->policy = false;
    }
  }
  protected function fetch_wtmid()
  {
    if($this->village->policy)
    {
      $wtmid = $this->fetch_win_message();
      if($this->check_syswords($wtmid,'wtmid'))
      {
        $this->village->wtmid = $GLOBALS['syswords'][$this->village->rp]->mes_wtmid[$wtmid];
        //奴隷勝利の場合追加勝利扱いにする
        if($this->village->wtmid === Data::TM_SLAVE)
        {
          $this->village->wtmid = Data::TM_VILLAGER;
          $this->village->add_winner = Data::TM_SLAVE;
        }
      }
      else
      {
        $this->village->wtmid = Data::TM_RP;
        $this->output_comment('undefined',__FUNCTION__,$wtmid);
      }
    }
    else
    {
      $this->village->wtmid = Data::TM_RP;
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
      //生存者を除く名前リストを作る
      $list[] = $this->user->persona;
      if($this->user->end !== null)
      {
        unset($list[$key]);
      }
      if($this->user->sklid  === Data::SKL_BAPTIST && $this->user->dtid === Data::DES_MARTYR)
      {
        $martyr = true;
      }
    }
    $this->fetch_from_daily($list);
    if(isset($martyr))
    {
      $this->insert_baptist($list);
    }

    foreach($this->users as $user)
    {
      //var_dump($user->get_vars());
      if(!$user->is_valid())
      {
        $this->output_comment('n_user',__FUNCTION__,$user->persona);
      }
    }
  }
  protected function fetch_users($person)
  {
    $this->fetch_persona($person);
    $this->fetch_player($person);
    $this->fetch_role($person);

    $list = $this->make_list_using_sysword($person);
    array_walk($list,[$this,'fetch_from_sysword']);

    //見物人
    if($this->user->dtid === Data::DES_ONLOOKER)
    {
      $this->insert_onlooker();
      return;
    }
    //生存者
    if($this->user->dtid === Data::DES_ALIVE)
    {
      $this->insert_alive();
    }
    $this->fetch_rltid_sow();
  }
  protected function make_list_using_sysword($person)
  {
    return ['dtid'=>$person->find('td',2)->plaintext,'tmid'=>$person->find('td',3)->plaintext,'sklid'=>$this->user->role];
  }
  protected function fetch_from_sysword($value,$column)
  {
    if(array_key_exists($value,$GLOBALS['syswords'][$this->village->rp]->{'mes_'.$column}))
    {
      $this->user->{$column} = $GLOBALS['syswords'][$this->village->rp]->{'mes_'.$column}[$value];
    }
    else
    {
      $this->user->{$column} = null;
      $this->output_comment('undefined',__FUNCTION__,$value);
    }
  }
  protected function fetch_role($person)
  {
    $role = $person->find('td',4)->plaintext;
    $this->user->role = mb_ereg_replace('\A(.+) \(.+を希望\)(.+|)','\1',$role,'m');
    //死神陣営の勝利者は、役職の後ろに「☆勝利」がつく
    if($this->village->policy && mb_strstr($role,'☆勝利'))
    {
      $this->user->rltid = Data::RSL_WIN;
    }
  }
  protected function fetch_rltid_sow()
  {
    if(!empty($this->user->rltid))
    {
      //勝利した死神陣営または見物人
      return;
    }

    if($this->village->wtmid === Data::TM_RP)
    {
      $this->user->rltid = Data::RSL_JOIN;
    }
    else if($this->user->tmid === $this->village->wtmid || $this->user->tmid === $this->village->add_winner)
    {
      if($this->user->tmid === Data::TM_EFB)
      {
        //死神陣営で勝利判定が埋まっていない者は敗北扱い
        $this->user->rltid = Data::RSL_LOSE;
      }
      else
      {
        $this->user->rltid = Data::RSL_WIN;
      }
    }
    else
    {
      $this->user->rltid = Data::RSL_LOSE;
    }
  }
  protected function fetch_key_u($list,$item)
  {
    $destiny = trim(preg_replace("/\r\n/",'',$item->plaintext));
    $key = mb_substr(trim($item->plaintext),-8,8);

    if($this->check_syswords($key,'dt_sys'))
    {
      $regex = $GLOBALS['syswords'][$this->village->rp]->mes_dt_sys[$key]['regex'];
    }
    else
    {
      return false;
    }

    //適当系の被襲撃者はスキップ
    if($regex === null)
    {
      $this->output_comment('fool',__FUNCTION__);
      return false;
    }

    $persona = trim(mb_ereg_replace($regex,'\2',$destiny,'m'));

    $key_u = array_search($persona,$list);
    if($key_u === false)
    {
      return false;
    }
    return $key_u;
  }
  protected function insert_baptist($list)
  {
    $days = $this->village->days;
    $find = 'p.infosp';
    for($i=4; $i<=$days; $i++)
    {
      $announce = $this->fetch_daily_url($i,$find);
      foreach($announce as $item)
      {
        $destiny = trim($item->plaintext);
        $key = mb_substr($destiny,-6,6);
        $persona = trim(mb_ereg_replace("(.+) は、.+ を命を引き換えに復活させた。",'\1',$destiny));
        $key_u = array_search($persona,$list);
        if($key_u)
        {
          $this->users[$key_u]->end = $i;
          $this->users[$key_u]->life = round(($i-1) / $this->village->days,3);
        }
      }
      $this->fetch->clear();
    }
  }
}
