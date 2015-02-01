<?php

class Morphe extends Giji_Old
{
  function __construct()
  {
    $cid = 13;
    $url_vil = "http://morphe.sakura.ne.jp/morphe/sow.cgi?vid=";
    $url_log = "http://morphe.sakura.ne.jp/morphe/sow.cgi?cmd=oldlog";
    $this->is_evil = true;
    parent::__construct($cid,$url_vil,$url_log);
  }
}
