<?php

class Guta extends Giji_Old
{
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
        $this->output_comment('rp',__function__);
        break;
    }
  }
}
