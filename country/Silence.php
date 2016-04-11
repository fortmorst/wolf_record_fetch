<?php

class Silence extends SOW
{
  protected function fetch_name()
  {
    $this->village->name = $this->fetch->find('table.list tr td',1)->plaintext;
  }
  protected function fetch_days()
  {
    $days = trim($this->fetch->find('p',0)->find('a',-4)->innertext);
    if($days === 'プロローグ')
    {
      $this->insert_as_ruin();
      return false;
    }
    $this->village->days = mb_substr($days,0,mb_strpos($days,'日')) +1;
  }
  protected function fetch_rp()
  {
    $rp = mb_substr($this->fetch->find('div.announce',0)->plaintext,0,5);
    $sql = "SELECT name FROM sysword WHERE prologue='$rp'";
    $stmt = $this->db->query($sql);
    if($stmt === false)
    {
      $this->output_comment('undefined',__FUNCTION__,$rp);
      $rp = "人狼物語";
    }
    else
    {
      $stmt = $stmt->fetch();
      $rp = $stmt['name'];
    }
    $this->village->rp = $rp;
    //言い換えリストに登録がなければ追加
    if(!isset($GLOBALS['syswords'][$rp]))
    {
      $this->fetch_sysword($rp);
    }
  }
  protected function check_ruin()
  {
    $info = 'div.announce';
    $infosp = 'div.extra';

    if(count($this->fetch->find($info)) <= 1 && count($this->fetch->find($infosp)) === 0)
    {
      return false;
    }
    else
    {
      return true;
    }
  }
  protected function fetch_wtmid()
  {
    $wtmid = trim($this->fetch->find('div.announce',-1)->plaintext);
    $wtmid = preg_replace("/\A([^\r\n]+)(\r\n.+)?\z/ms", "$1", $wtmid);

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
  protected function make_cast()
  {
    $cast = $this->fetch->find('table tr');
    array_shift($cast);
    $this->cast = $cast;
  }
  protected function fetch_users($person)
  {
    $this->fetch_persona($person);
    $this->fetch_player($person);
    $this->fetch_role($person);

    $this->fetch_sklid();
    $this->fetch_rltid_sow();

    if($this->is_alive($person))
    {
      $this->insert_alive();
    }
  }
  protected function fetch_role($person)
  {
    $role = $person->find('td',3)->plaintext;
    if(preg_match('/恋人/',$role))
    {
      $this->user->tmid = Data::TM_LOVERS;
    }
    $this->user->role = mb_ereg_replace('\A(.+) \(.+を希望\)(.+|)','\1',$role,'m');
  }
  protected function fetch_sklid()
  {
    if($this->check_syswords($this->user->role,'sklid'))
    {
      $this->user->sklid = $GLOBALS['syswords'][$this->village->rp]->mes_sklid[$this->user->role]['sklid'];
      //既に恋人陣営指定がある場合はスキップ
      if($this->user->tmid === null)
      {
        $this->user->tmid = $GLOBALS['syswords'][$this->village->rp]->mes_sklid[$this->user->role]['tmid'];
      }

      $this->modify_from_sklid();
    }
    else
    {
      $this->user->sklid= null;
      $this->user->tmid= null;
      $this->output_comment('undefined',__FUNCTION__,$this->user->role);
    }
  }
  protected function fetch_from_daily($list)
  {
    $days = $this->village->days;
    $find = 'div.announce';

    for($i=2; $i<=$days; $i++)
    {
      $announce = $this->fetch_daily_url($i,$find);
      foreach($announce as $item)
      {
        $key_u = $this->fetch_key_u($list,$item);
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
}
