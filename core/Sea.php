<?php

abstract class Sea extends Giji_Old
{
  use TRS_Sea;
  protected $RP_SP = [
    "RolePlay"=>'RP'
  ];

  function set_village_data()
  {
    $this->SKILL = $this->SKL_SEA;
    $this->TEAM = $this->TM_SEA;
    $this->WTM = $this->WTM_SEA;
  }
  protected function fetch_policy_detail()
  {
    $policy = $this->fetch->find('p.multicolumn_left',1)->plaintext;
    switch($policy)
    {
      case 'とくになし':
      case 'ガチ推理':
      case '推理&RP':
        $this->village->policy = true;
        break;
      default:
        $this->village->policy = false;
        $this->output_comment('rp',__function__);
        break;
    }
  }
}
