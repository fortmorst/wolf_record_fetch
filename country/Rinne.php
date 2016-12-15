<?php

class Rinne extends SOW
{
  const SYSWORD = "人狼輪廻";
  protected function make_rgl_detail($rgl)
  {
    $this->village->rglid = Data::RGL_RINNE;
    $this->village->rgl_detail = $rgl.',';
  }
}
