<?php

class Chitose_RP extends Chitose
{
  function set_village_data()
  {
    $this->policy = false;
    $cid = 33;
    $url_vil = "http://1000nacht.sakura.ne.jp/story/sow/sow.cgi?vid=";
    $url_log = "http://1000nacht.sakura.ne.jp/story/sow/sow.cgi?cmd=oldlog";
    return ['cid'=>$cid,'url_vil'=>$url_vil,'url_log'=>$url_log];
  }
}
