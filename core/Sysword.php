<?php

class Sysword
{
  private  $mes_skill = []
          ,$mes_team = []
          ,$mes_dt = []
          ,$mes_wtm = []
          ;
  use Properties;
  function get_vars()
  {
    return get_object_vars($this);
  }
}
