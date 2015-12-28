<?php

class Rinne extends SOW
{
  use TRS_Rinne;
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
