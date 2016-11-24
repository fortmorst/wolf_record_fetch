<?php

abstract class Sea extends Giji_Old
{
  protected function fetch_policy_detail()
  {
    $policy = $this->fetch->find('p.multicolumn_left',1)->plaintext;
    switch($policy)
    {
      case "とくになし":
      case "ガチ推理":
      case "推理&RP":
        $this->village->policy = true;
        break;
      default:
        $this->village->policy = false;
        break;
    }
  }
}
