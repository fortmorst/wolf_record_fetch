<?php
class Rose extends SOW
{
  protected $syswords = [];
  use TRS_Rose;

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
    exit;

    if($this->policy === null)
    {
      $this->fetch_policy();
    }

    $this->fetch->clear();
  }
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
  protected function fetch_sysword($rp)
  {
    $sql = 'SELECT name,mes_skill,mes_team,mes_dt,mes_wtm FROM sysword WHERE name="'.$rp.'"';
    $stmt = $this->db->query($sql);
    $stmt = $stmt->fetchAll();
    $name = $stmt[0]['name'];
    unset($stmt[0]['name']);
    $this->syswords[$name] = new Sysword();
    array_walk($stmt[0],[$this,'make_sysword_set'],$name);
    var_dump($this->syswords[$name]->get_vars());
  }
  protected function make_sysword_set($values,$table,$name)
  {
    $sql = "SELECT * from $table where id in ($values)";
    $stmt = $this->db->query($sql);
    //$stmt = $stmt->fetchAll();
    $list = [];
    if($table === 'mes_dt')
    {
      foreach($stmt as $item)
      {
        $list[$item['name']] = ['regex'=>$item['regex'],'dtid'=>(int)$item['orgid']];
      }
      $this->syswords[$name]->mes_dt = $list;
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
  //可能ならSOW全体に移植
  protected function fetch_win_message()
  {
    $not_wtm = '/村の更新日が延長されました。|村の設定が変更されました。/';
    $wtmid = trim($this->fetch->find('p.info',-1)->plaintext);
    //とりあえず一行だけ
//$wtmid = preg_replace("/^ ([^\r\n]+)(\r\n)?(.+ )?$/ms", "$1", $wtmid);
    if(preg_match($not_wtm,$wtmid))
    {
      $do_i = -2;
      do
      {
        $wtmid = trim($this->fetch->find('p.info',$do_i)->plaintext);
        $do_i--;
      } while(preg_match($not_wtm,$wtmid));
    }
    return mb_substr(preg_replace("/\r\n/","",$wtmid),-10);
  }
  protected function fetch_wtmid()
  {
    if(!$this->village->policy)
    {
      $this->village->wtmid = Data::TM_RP;
    }
    else
    {
      $wtmid = $this->fetch_win_message();
      if(array_key_exists($wtmid,$this->{'WTM_'.$this->village->rp}))
      {
        $this->village->wtmid = $this->{'WTM_'.$this->village->rp}[$wtmid];
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
      if(!$user->is_valid())
      {
        $this->output_comment('n_user');
      }
    }
  }
  protected function fetch_users($person)
  {
    $this->fetch_persona($person);
    $this->fetch_player($person);
    $this->fetch_role($person);
    $this->user->tmid = $this->TEAM[$person->find('td',3)->plaintext];

    if(mb_ereg_match('見物人|やじうま',$this->user->role))
    {
      $this->insert_onlooker();
    }
    else
    {
      $this->user->dtid = $this->DESTINY[$person->find('td',2)->plaintext];
      if($this->user->dtid === Data::DES_ALIVE)
      {
        $this->insert_alive();
      }
      $this->fetch_sklid();
      $this->fetch_rltid();
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
  protected function fetch_sklid()
  {
    if(!empty($this->{'SKL_'.$this->village->rp}))
    {
      $this->user->sklid = $this->{'SKL_'.$this->village->rp}[$this->user->role];
    }
    else
    {
      $this->user->sklid = $this->SKILL[$this->user->role];
    }
  }
  protected function fetch_rltid()
  {
    if($this->user->rltid)
    {
      return;
    }

    if(!$this->village->policy)
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
  protected function insert_alive()
  {
    $this->user->end = $this->village->days;
    $this->user->life = 1.000;
  }
  protected function fetch_key_u($list,$rp,$item)
  {
      $destiny = trim(preg_replace("/\r\n/",'',$item->plaintext));
      $key= mb_substr(trim($item->plaintext),-6,6);
      if(!isset($this->{'DT_'.$rp}[$key]))
      {
        return false;
      }
      else
      {
        $persona = trim(mb_ereg_replace($this->{'DT_'.$rp}[$key][0],'\2',$destiny,'m'));
      }

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
