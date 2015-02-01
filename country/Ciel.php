<?php

class Ciel extends Giji
{
  function __construct()
  {
    $cid = 18;
    $url_vil = 'http://ciel.moo.jp/cheat/sow.cgi?vid=';
    $url_log = 'http://ciel.moo.jp/cheat/sow.cgi?cmd=oldlog';
    parent::__construct($cid,$url_vil,$url_log);
    $this->policy = false;
  }
}
