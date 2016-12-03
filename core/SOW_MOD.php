<?php

class SOW_MOD extends Giji_Old
{
  protected function fetch_policy_detail()
  {
    //議事用の設定を削除
  }
  protected function make_sysword_set($rp,$sysid)
  {
    foreach(["sklid","dtid","wtmid"] as $table)
    {
      switch($table)
      {
        case "sklid":
          $list = [];
          $sql = "SELECT `m`.`name`,`orgid`,`tmid` FROM `mes_sklid` `m` JOIN `skill` `s` ON `orgid` = `s`.`id` JOIN `mes_sklid_sysword` `ms` ON `ms`.`msid` = `m`.`id` WHERE `ms`.`sysid`={$sysid}";
          $stmt = $this->db->query($sql);
          foreach($stmt as $item)
          {
            $list[$item['name']] = ['sklid'=>(int)$item['orgid'],'tmid'=>(int)$item['tmid']];
          }
          break;
        default:
          $list = $this->make_sysword_name_orgid_set($table,$sysid);
          break;
      }
      $this->syswords[$rp][$table] = $list;
    }
  }
  protected function fetch_date()
  {
    $date = $this->fetch->find('div.mes_date',0)->plaintext;
    $date = mb_substr($date,mb_strpos($date,"2"),10);
    $this->village->date = preg_replace("/(\d{4})\/(\d{2})\/(\d{2})/","$1-$2-$3",$date);
  }

  protected function make_cast()
  {
    $cast = $this->fetch->find('tbody tr');
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
      var_dump($this->user->get_vars());
      if(!$this->user->is_valid())
      {
        $this->output_comment("n_user",__function__,$this->user->persona);
      }
      $this->users[] = $this->user;
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
    $this->fetch_rltid_sow();
  }
  protected function fetch_dtid($person)
  {
    $destiny = trim($person->find('td',2)->plaintext);
    $destiny =  mb_ereg_replace('\d+日(.+)','\1',$destiny);
    $this->fetch_from_sysword($destiny,"dtid");
  }
  protected function fetch_role($person)
  {
    $role = $person->find('td',4)->plaintext;
    $this->user->role = mb_ereg_replace('\A(.+) \(.+を希望\)(.+|)','\1',$role,'m');
  }
  protected function fetch_end($person)
  {
    $end = trim($person->find('td',2)->plaintext);
    $this->user->end = (int)mb_ereg_replace('(\d+)日.+','\1',$end);
    $this->user->life = round(($this->user->end-1) / $this->village->days,3);
  }
  protected function fetch_sklid()
  {
    if($this->check_syswords($this->user->role,"sklid"))
    {
      $this->user->sklid = $this->syswords[$this->village->rp]['sklid'][$this->user->role]['sklid'];
      $this->user->tmid = $this->syswords[$this->village->rp]['sklid'][$this->user->role]['tmid'];

      $this->modify_from_sklid();
    }
    else
    {
      $this->user->sklid= null;
      $this->user->tmid= null;
      $this->output_comment("undefined",__FUNCTION__,$this->user->role);
    }
  }
  protected function modify_from_sklid()
  {
    //狂人を人狼陣営にする
    if($this->user->tmid === Data::TM_EVIL)
    {
      $this->user->tmid = Data::TM_WOLF;
    }
  }
  protected function fetch_rltid_sow()
  {
    if($this->village->wtmid === 0)
    {
      $this->user->rltid = Data::RSL_JOIN;
      return;
    }

    if($this->user->tmid === $this->village->wtmid)
    {
      $this->user->rltid = Data::RSL_WIN;
    }
    else
    {
      $this->user->rltid = Data::RSL_LOSE;
    }
  }
}
