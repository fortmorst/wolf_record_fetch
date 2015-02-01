<?php

class Sea_Blue extends Sea
{
  function set_village_data()
  {
    $cid = 67;
    $url_vil = "http://chaos-circle.jp/abyss/sow.cgi?vid=";
    $url_log = "http://chaos-circle.jp/abyss/sow.cgi?cmd=oldlog";
    return ['cid'=>$cid,'url_vil'=>$url_vil,'url_log'=>$url_log];
  }
}
