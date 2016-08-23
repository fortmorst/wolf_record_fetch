<?php

abstract class Giji_Old extends Country
{
  function fetch_village()
  {
    $this->fetch_from_info();
    $this->fetch_from_pro();

    if($this->village->wtmid === Data::TM_RUIN)
    {
      return false;
    }
    
    $this->fetch_from_epi();
  }

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

  protected function fetch_name()
  {
    $this->village->name = $this->fetch->find('p.multicolumn_left',0)->plaintext;
  }
  protected function check_sprule()
  {
    //タブラの人狼以外ならDBから引く
    $rule= trim($this->fetch->find('dl.mes_text_report dt',1)->plaintext);
    if(strpos($rule,'タブラの人狼') === false)
    {
      $sql = "SELECT id FROM regulation where name='$rule'";
      $stmt = $this->db->query($sql);
      if($stmt === false)
      {
        $this->output_comment('undefined',__FUNCTION__,$rule);
      }
      else
      {
        $stmt = $stmt->fetch();
        $this->village->rglid = (int)$stmt['id'];
      }
    }
    else if(preg_match("/秘話/",$this->village->name))
    {
      $this->village->rglid = Data::RGL_SECRET;
    }
  }
  protected function fetch_days()
  {
    $days = $this->fetch->find('p.turnnavi',0)->find('a',-4);
    //進行中(=雑談村)または開始しなかった廃村村
    if($days === null || $days->innertext === 'プロローグ')
    {
      $this->insert_as_ruin();
      return false;
    }
    $this->village->days = mb_substr($days->innertext,0,mb_strpos($days,'日')) +1;
  }
  protected function fetch_rp()
  {
    $this->check_sprule();
    if($this->sysword === null)
    {
      $rp = trim($this->fetch->find('dl.mes_text_report dt',0)->plaintext);
    }
    else
    {
      //固定
      $rp = $this->sysword;
    }

    $this->village->rp = $rp;
    if(!isset($GLOBALS['syswords'][$rp]))
    {
      $this->fetch_sysword($rp);
    }
  }
  protected function make_sysword_sql($rp)
  {
    return "select name,mes_sklid,mes_tmid,mes_dtid,mes_wtmid from sysword where name='$rp'";
  }
  protected function make_sysword_set($values,$table,$name)
  {
    $sql = "SELECT * from $table where id in ($values)";
    $stmt = $this->db->query($sql);
    $list = [];

    if($table === 'mes_tmid')
    {
      $sql = "SELECT m.name,orgid,evil_flg FROM mes_tmid m JOIN team t ON orgid = t.id WHERE m.id IN ($values)";
      $stmt = $this->db->query($sql);
      foreach($stmt as $item)
      {
        $list[$item['name']] = ['tmid'=>(int)$item['orgid'],'evil_flg'=>(bool)$item['evil_flg']];
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
  protected function fetch_policy()
  {
    parent::fetch_policy();
    if($this->village->policy === true)
    {
      $this->fetch_policy_detail();
    }
  }
  protected function fetch_policy_detail()
  {
    $policy = $this->fetch->find('p.multicolumn_left',1)->plaintext;
    switch($policy)
    {
      case "とくになし":
      case "[言] 殺伐、暴言あり":
      case "[遖] あっぱれネタ風味":
      case "[張] うっかりハリセン":
      case "[暢] のんびり雑談":
      case "[全] 大人も子供も初心者も、みんな安心":
        $this->village->policy = true;
        break;
      default:
        $this->village->policy = false;
        break;
    }
  }
  protected function fetch_from_pro()
  {
    $url = $this->url.'&turn=0&row=10&mode=all&move=page&pageno=1';
    $this->fetch->load_file($url);
    sleep(1);

    $this->fetch_date();
    $this->fetch->clear();
  }
  protected function fetch_date()
  {
    $date = $this->fetch->find('p.mes_date',0)->plaintext;
    $date = mb_substr($date,mb_strpos($date,"2"),10);
    $this->village->date = preg_replace('/(\d{4})\/(\d{2})\/(\d{2})/','\1-\2-\3',$date);
  }
  protected function fetch_from_epi()
  {
    $url = $this->url.'&turn='.$this->village->days.'&row=40&mode=all&move=page&pageno=1';
    $this->fetch->load_file($url);
    sleep(1);
    //廃村なら非参加扱い
    if(!$this->check_ruin())
    {
      $this->village->wtmid = Data::TM_RP;
      $this->output_comment('ruin_midway',__function__);
    }
    else
    {
      $this->fetch_wtmid();
    }

    $this->make_cast();
  }
  protected function fetch_win_message()
  {
    $not_wtm = '/延長されました。|村の設定が変更されました。/';

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
  protected function fetch_wtmid()
  {
    if($this->policy || $this->village->policy)
    {
      $wtmid = $this->fetch_win_message();
      if($this->check_syswords($wtmid,'wtmid'))
      {
        $this->village->wtmid = $GLOBALS['syswords'][$this->village->rp]->mes_wtmid[$wtmid];
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
  protected function make_cast()
  {
    $this->cast = $this->fetch->find('tbody tr.i_active');
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
    if($this->is_evil === true && $this->village->evil_rgl !== true)
    {
      $this->change_evil_team();
    }
    foreach($this->users as $user)
    {
      //var_dump($user->get_vars());
    }
  }
  protected function change_evil_team()
  {
    foreach($this->users as $key=>$user)
    {
      if($user->tmid === Data::TM_EVIL)
      {
        $this->users[$key]->tmid = Data::TM_WOLF;
      }
    }
  }
  protected function fetch_users($person)
  {
    $this->fetch_persona($person);
    $this->fetch_player($person);

    $result = $person->find("td",3)->plaintext;
    $result = mb_substr($result,0,mb_strpos($result,"\n")-1);
    $result = explode(' ',$result);

    $this->fetch_role($result[2]);
    if(mb_substr($this->user->role,-2) === "居た")
    {
      $this->user->role  = '見物人';
      $this->insert_onlooker();
    }
    else
    {
      $this->fetch_rltid($result[1]);
      $this->fetch_sklid();
      $this->fetch_from_sysword($result[0],'dtid');
      $this->fetch_end($person);
      $this->fetch_tmid($result[2]);
      $this->fetch_life();
    }
  }
  protected function fetch_persona($person)
  {
    $this->user->persona =trim($person->find("td",0)->plaintext);
  }
  protected function fetch_player($person)
  {
    $player =trim($person->find("td",1)->plaintext);
    $this->user->player =$this->modify_player($player);
  }
  protected function fetch_role($role)
  {
    $this->user->role = mb_substr($role,mb_strpos($role,'：')+1);
  }
  protected function fetch_end($person)
  {
    if($this->user->dtid === Data::DES_ALIVE)
    {
      $this->user->end = $this->village->days;
    }
    else
    {
      $this->user->end = (int)preg_replace("/(.+)日/","$1",$person->find("td",2)->plaintext);
    }
  }
  protected function fetch_sklid()
  {
    $role = $this->user->role;
    if(mb_strpos($role,"、") === false)
    {
      $sklid = $role;
    }
    else
    {
      //役職欄に絆などついている場合
      $sklid = mb_substr($role,0,mb_strpos($role,"、"));
    }
    $this->fetch_from_sysword($sklid,'sklid');
  }
  protected function fetch_tmid($result)
  {
    $tmid = mb_substr($result,0,mb_strpos($result,'：'));
    if($this->check_syswords($tmid,'tmid'))
    {
      $this->user->tmid = $GLOBALS['syswords'][$this->village->rp]->mes_tmid[$tmid]['tmid'];
      if($this->is_evil && $GLOBALS['syswords'][$this->village->rp]->mes_tmid[$tmid]['evil_flg'])
      {
        $this->village->evil_rgl = true;
      }
    }
    else
    {
      $this->user->tmid = null;
      $this->output_comment('undefined',__FUNCTION__,$tmid);
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
      switch($result)
      {
        case '勝利':
          $this->user->rltid = Data::RSL_WIN;
          break;
        case '敗北':
          $this->user->rltid = Data::RSL_LOSE;
          break;
        case '': //突然死
        case '--':
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
