<?php

class Sysword
{
  private  $mes_sklid = []
          ,$mes_tmid = []
          ,$mes_dtid = []
          ,$mes_dt_sys = []
          ,$mes_wtmid = []
          ;
  use Properties;
  function get_vars()
  {
    return get_object_vars($this);
  }
}
