<?php

class Raaru extends Heaven
{
  protected $SKL_SP = [
      "妖狐"  =>[Data::SKL_FAIRY,Data::TM_FAIRY]
     ,"背徳者"=>[Data::SKL_FRY_FANATIC,Data::TM_FAIRY]
     ,"契約者"=>[Data::SKL_QP,Data::TM_LOVERS]
     ,"共犯者"=>[Data::SKL_QP_SELF,Data::TM_LOVERS]
     ,"観戦者"=>[Data::SKL_ONLOOKER,Data::TM_ONLOOKER]
  ];
  function __construct()
  {
    $cid = 69;
    $url_vil = "http://ranukiwolf.sakura.ne.jp/sam_ten/index.cgi?vid=";
    $url_log = "http://ranukiwolf.sakura.ne.jp/sam_ten/index.cgi";
    parent::__construct($cid,$url_vil,$url_log);
    $this->SKILL = array_merge($this->SKILL,$this->SKL_SP);
  }

}
