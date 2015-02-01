<?php

class Rinne extends SOW
{
  use TRS_Rinne;
  function __construct()
  {
    $cid = 57;
    $url_vil = "http://monooki.sakura.ne.jp/sow/sow.cgi?vid=";
    $url_log = "http://monooki.sakura.ne.jp/sow/sow.cgi?cmd=oldlog";
    parent::__construct($cid,$url_vil,$url_log);
    $this->policy = true;
  }
  protected function make_rgl_detail($rgl)
  {
    $this->village->rglid = Data::RGL_RINNE;
    $this->village->rgl_detail = $rgl.',';
    echo '>'.$this->village->vno.': rgl=>'.$rgl;
  }
  protected function fetch_rp()
  {
    $this->village->rp = 'SOW';
  }
}
