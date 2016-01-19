<?php
class Rose extends SOW
{
  //SOWに上書き
  protected $syswords = [];
  //use TRS_Rose;

  protected function fetch_from_info()
  {
    $this->fetch->load_file($this->url."&cmd=vinfo");
    sleep(1);

    $this->fetch_name();
    if($this->fetch_days() === false)
    {
      $this->fetch->clear();
      return;
    }

    $this->fetch_rp();

    if($this->policy === null)
    {
      $this->fetch_policy();
    }

    $this->fetch->clear();
  }

  //SOWに上書き
  protected function fetch_rp()
  {
    if(empty($this->RP_PRO))
    {
       $rp = trim($this->fetch->find('p.multicolumn_left',7)->plaintext);
    }
    else
    {
      $rp = mb_substr($this->fetch->find('p.info',0)->plaintext,1,5);
      //プロローグから取得する場合 書き直し
      //if(array_key_exists($rp,$this->RP_PRO))
      //{
        //$this->village->rp = $this->RP_PRO[$rp];
      //}
    }
    //言い換えリストに登録がなければ追加
    if(!isset($this->syswords[$rp]))
    {
      $this->fetch_sysword($rp);
    }
    $this->village->rp = $rp;
  }
  //SOWに上書き
  protected function fetch_sysword($rp)
  {
    $sql = "SELECT name,mes_sklid,mes_tmid,mes_dtid,mes_dt_sys,mes_wtmid FROM sysword WHERE name='$rp'";
    $stmt = $this->db->query($sql);
    $stmt = $stmt->fetchAll();
    $name = $stmt[0]['name'];
    unset($stmt[0]['name']);
    $this->syswords[$name] = new Sysword();
    array_walk($stmt[0],[$this,'make_sysword_set'],$name);
    //var_dump($this->syswords[$name]->get_vars());
  }
  //SOWに上書き
  protected function make_sysword_set($values,$table,$name)
  {
    $sql = "SELECT * from $table where id in ($values)";
    $stmt = $this->db->query($sql);
    //$stmt = $stmt->fetchAll();
      $list = [];
      if($table === 'mes_dt_sys')
      {
        foreach($stmt as $item)
        {
          $list[$item['name']] = ['regex'=>$item['regex'],'dtid'=>(int)$item['orgid']];
        }
        $this->syswords[$name]->mes_dt_sys = $list;
    }
    else
    {
      foreach($stmt as $item)
      {
        $list[$item['name']] = (int)$item['orgid'];
      }
      $this->syswords[$name]->{$table} = $list;
    }
  }
  protected function fetch_policy()
  {
    parent::fetch_policy();
    if($this->village->policy === true)
    {
      $policy = $this->fetch->find('p.multicolumn_left',11)->plaintext;
      if(preg_match('/一般|初心者歓迎/',$policy))
      {
        $this->village->policy = true;
      }
      else
      {
        $this->village->policy = false;
        $this->output_comment('rp');
      }
    }
  }
  //SOWに上書き
  protected function fetch_win_message()
  {
    $not_wtm = '/村の更新日が延長されました。|村の設定が変更されました。/';

    $wtmid = trim($this->fetch->find('p.info',-1)->plaintext);
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
  //奴隷周り以外SOWに上書き
  protected function fetch_wtmid()
  {
    if(!$this->village->policy)
    {
      $this->village->wtmid = Data::TM_RP;
    }
    else
    {
      $wtmid = $this->fetch_win_message();
      if(array_key_exists($wtmid,$this->syswords[$this->village->rp]->mes_wtmid))
      {
        $this->village->wtmid = $this->syswords[$this->village->rp]->mes_wtmid[$wtmid];
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
        $this->output_comment('undefined',$wtmid);
      }
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
      var_dump($user->get_vars());
      if(!$user->is_valid())
      {
        $this->output_comment('n_user',$user->persona);
      }
    }
  }
  //一部SOWに上書き
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
    $this->fetch_rltid();
  }
  //SOWに上書き
  protected function make_list_using_sysword($person)
  {
    return ['dtid'=>$person->find('td',2)->plaintext,'tmid'=>$person->find('td',3)->plaintext,'sklid'=>$this->user->role];
  }
  //SOWに上書き
  protected function fetch_from_sysword($value,$column)
  {
    if(array_key_exists($value,$this->syswords[$this->village->rp]->{'mes_'.$column}))
    {
      $this->user->{$column} = $this->syswords[$this->village->rp]->{'mes_'.$column}[$value];
    }
    else
    {
      $this->user->{$column} = null;
      $this->output_comment('undefined',$value);
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
  protected function fetch_rltid()
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
  //SOWに上書き
  protected function fetch_from_daily($list)
  {
    $days = $this->village->days;
    $find = 'p.info';

    //言い換えの有無
    //if(!empty($this->{'DT_'.$this->village->rp}))
    //{
      //$rp = $this->village->rp;
    //}
    //else
    //{
      //$rp = 'NORMAL';
    //}

    for($i=2; $i<=$days; $i++)
    {
      $announce = $this->fetch_daily_url($i,$find);
      foreach($announce as $item)
      {
        $key_u = $this->fetch_key_u($list,$find,$item);
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
  //一部SOWに上書き
  protected function fetch_key_u($list,$rp,$item)
  {
    $destiny = trim(preg_replace("/\r\n/",'',$item->plaintext));
    $key = mb_substr(trim($item->plaintext),-8,8);

    if(array_key_exists($key,$this->syswords[$this->village->rp]->mes_dt_sys))
    {
      $regex = $this->syswords[$this->village->rp]->mes_dt_sys[$key]['regex'];
    }
    else
    {
      //適当系の場合警告を出す
      //$this->output_comment('undefined',$destiny);
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
