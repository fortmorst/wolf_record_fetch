<?php

class Rinne extends SOW
{
  protected function make_rgl_detail($rgl)
  {
    $this->village->rglid = Data::RGL_RINNE;
    $this->village->rgl_detail = $rgl.',';
  }
}
