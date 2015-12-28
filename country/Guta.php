<?php

class Guta extends Giji_Old
{
  use TRS_Guta;
  protected $RP_SP = [
     "ミラーズホロウ"=>'MILLERS'
    ,"昏き宵闇の琥珀"=>'AMBER'
  ];

  protected function fetch_policy()
  {
    $policy = $this->fetch->find('p.multicolumn_left',1)->plaintext;
    switch($policy)
    {
      case "ガチ推理（陣営勝敗最優先）":
      case "推理＆RP（勝負しながらキャラプレイも楽しむ）":
        $this->village->policy = true;
        break;
      default:
        $this->village->policy = false;
        $this->output_comment('rp');
        break;
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
    if($this->village->rp === "AMBER")
    {
      $this->user->tmid = $this->TM_AMBER[$tmid][0];
      $is_evil_team = $this->TM_AMBER[$tmid][1];
    }
    else
    {
      $this->user->tmid = $this->TEAM[$tmid][0];
      $is_evil_team = $this->TEAM[$tmid][1];
    }
    if($this->is_evil && $is_evil_team)
    {
      $this->village->evil_rgl = true;
    }
  }
  protected function fetch_dtid($result)
  {
    if($this->village->rp === "AMBER")
    {
      $this->user->dtid = $this->DES_AMBER[$result];
    }
    else
    {
      $this->user->dtid = $this->DESTINY[$result];
    }
  }

}
