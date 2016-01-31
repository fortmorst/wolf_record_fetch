<?php

class Moon extends SOW_MOD
{
  protected $EVIL_ROLE = [Data::SKL_EVIL,Data::SKL_EVL_KNOW_WF,Data::SKL_EVL_SEER_ROLE];

  protected function fetch_policy()
  {
    $policy= mb_strstr($this->fetch->find('p.multicolumn_left',-1)->plaintext,'推理');
    if($policy !== false)
    {
      $this->village->policy = true;
    }
    else
    {
      $this->village->policy = false;
      $this->output_comment('rp',__function__);
    }
  }
  protected function fetch_rp()
  {
    $rp = trim($this->fetch->find('div.paragraph',2)->find('p.multicolumn_left',3)->plaintext);
    $this->village->rp = $rp.'_月狼';
    if(!isset($GLOBALS['syswords'][$this->village->rp]))
    {
      $this->fetch_sysword($this->village->rp);
    }
  }
  protected function fetch_role($person)
  {
    $role = $person->find('td',4);
    if(!empty($role))
    {
      $role = $role->plaintext;
      $this->user->role = mb_ereg_replace('\A(.+) \(.+を希望\)(.+|)','\1',$role,'m');
    }
    else
    {
      $this->user->role = '見物人';
    }
  }
  protected function modify_from_sklid()
  {
    //狂人を人狼陣営にする
    if($this->user->tmid === Data::TM_EVIL && !array_key_exists($this->user->sklid,$this->EVIL_ROLE))
    {
      $this->user->tmid = Data::TM_WOLF;
    }
  }
}
