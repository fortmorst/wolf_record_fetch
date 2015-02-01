<?php

class Dance extends SOW
{
  use TRS_SOW;
  function __construct()
  {
    $cid = 53;
    $url_vil = "http://hamyoron.s262.xrea.com/sow/sow.cgi?vid=";
    $url_log = "http://hamyoron.s262.xrea.com/sow/sow.cgi?cmd=oldlog";
    parent::__construct($cid,$url_vil,$url_log);
    $this->policy = true;
  }
}
