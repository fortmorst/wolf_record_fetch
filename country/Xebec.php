<?php

class Xebec extends Giji_Old
{
  function __construct()
  {
    $cid = 16;
    $url_vil = 'http://xebec.x0.to/xebec/sow.cgi?vid=';
    $url_log = 'http://xebec.x0.to/xebec/sow.cgi?cmd=oldlog';
    parent::__construct($cid,$url_vil,$url_log);
    $this->policy = false;
  }
}
