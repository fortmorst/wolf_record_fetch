<?php

class Crazy extends Giji_Old
{
  function __construct()
  {
    $cid = 17;
    $url_vil = 'http://crazy-crazy.sakura.ne.jp/crazy/sow.cgi?vid=';
    $url_log = 'http://crazy-crazy.sakura.ne.jp/crazy/sow.cgi?cmd=oldlog';
    parent::__construct($cid,$url_vil,$url_log);
    $this->policy = false;
  }
}
