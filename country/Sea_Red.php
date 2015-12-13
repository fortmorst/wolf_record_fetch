<?php

class Sea_Red extends Sea
{
  function set_village_data()
  {
    $cid = 45;
    $url_vil = "http://red-abyss.net/abyss/sow.cgi?vid=";
    $url_log = "http://red-abyss.net/abyss/sow.cgi?cmd=oldlog";
    return ['cid'=>$cid,'url_vil'=>$url_vil,'url_log'=>$url_log];
  }
}
