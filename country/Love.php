<?php

class Love extends Giji_Old
{
  function __construct()
  {
    $cid = 68;
    $url_vil = 'http://www.lovesick.rossa.cc/cabala/sow.cgi?vid=';
    $url_log = 'http://www.lovesick.rossa.cc/cabala/sow.cgi?cmd=oldlog';
    parent::__construct($cid,$url_vil,$url_log);
    $this->policy = false;
    $this->is_evil = true;
  }
}
