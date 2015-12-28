<?php

class Ivory extends Giji_Old
{
  use TRS_Ivory;
  protected $RP_SP = [
     "象牙の塔"=>'IVORY'
    ,"ミラーズホロウ"=>'MILLERS'
    ,"マフィア"=>'MAFIA'
  ];

  protected function fetch_name()
  {
    $name = $this->fetch->find('p.multicolumn_left',0)->plaintext;
    $this->village->name = mb_ereg_replace("(.+)\r\n.+","\\1",$name);
  }
  protected function check_sprule()
  {
    $rule= trim($this->fetch->find('dl.mes_text_report dt',1)->plaintext);
    if(array_key_exists($rule,$this->RGL_IVORY))
    {
      $this->village->rglid = $this->RGL_IVORY[$rule];
    }
  }
  protected function fetch_rp()
  {
    $rp = trim($this->fetch->find('dl.mes_text_report dt',0)->plaintext);
    $rp = mb_ereg_replace('文章セット：「(.+)」','\\1',$rp);
    if(array_key_exists($rp,$this->RP_SP))
    {
      $this->village->rp = $this->RP_SP[$rp]; 
    }
    else
    {
      $this->village->rp = 'NORMAL'; 
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
    if($this->village->rp !== 'NORMAL')
    {
      $this->user->sklid = $this->{'SKL_'.$this->village->rp}[$sklid];
    }
    else
    {
      $this->user->sklid = $this->SKILL[$sklid];
    }
  }
  protected function fetch_tmid($result)
  {
    $tmid = mb_substr($result,0,2);
    if($this->village->rp !== 'NORMAL')
    {
      $this->user->tmid = $this->{'TM_'.$this->village->rp}[$tmid];
    }
    else
    {
      $this->user->tmid = $this->TEAM[$tmid][0];
    }
  }
}
