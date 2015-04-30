<?php

class Sea_Old extends Sea
{
  function set_village_data()
  {
    $cid = 34;
    $url_vil = "http://chaos-circle.versus.jp/wolf/abyss/sow.cgi?vid=";
    $url_log = "http://chaos-circle.versus.jp/wolf/abyss/sow.cgi?cmd=oldlog";
    return ['cid'=>$cid,'url_vil'=>$url_vil,'url_log'=>$url_log];
  }
}
