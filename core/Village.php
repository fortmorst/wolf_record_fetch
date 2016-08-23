<?php

class Village
{
  private  $vno
          ,$name
          ,$date
          ,$nop
          ,$rglid
          ,$days
          ,$wtmid
          ,$rgl_detail
          ,$evil_rgl //裏切り陣営の有無
          ,$rp //言い換え
          ,$policy //勝敗あり村かどうか
          ,$add_winner //SOWでの追加勝利陣営の有無
          ;

  use Properties;

  function __construct($vno)
  {
    $this->vno = $vno;
  }
  function get_vars()
  {
    $list = get_object_vars($this);
    unset($list['evil_rgl'],$list['rp'],$list['policy'],$list['add_winner']);
    return $list;
  }

  function is_valid()
  {
    $list = $this->get_vars();
    $result = true;
    foreach($list as $key=>$item)
    {
      switch($key)
      {
      case 'vno':
      case 'nop':
      case 'rglid':
      case 'days':
      case 'wtmid':
        if(!$this->is_int_value_valid($key,$item))
        {
          $result = false;
        }
        break;
      case 'name':
      case 'rgl_detail':
        if(!$this->is_string_value_valid($key,$item))
        {
          $result = false;
        }
        break;
      case 'date':
        if(empty($item) || !preg_match('/\d{2}-\d{1,2}-\d{1,2}/',$item))
        {
          $this->invalid_error($key,$item);
          $result =  false;
        }
        break;
      }
    }
    if($result === false)
    {
      return false;
    }
    return true;
  }
  private function is_int_value_valid($key,$item)
  {
    if($item === null || !is_int($item))
    {
      $this->invalid_error($key,$item);
      return false;
    }
    return true;
  }
  private function is_string_value_valid($key,$item)
  {
    if(empty($item) || !is_string($item) || !mb_check_encoding($item))
    {
      $this->invalid_error($key,$item);
      return false;
    }
    return true;
  }
  private function invalid_error($key,$item)
  {
    echo '>'.$key.' is invalid.->'.$item.PHP_EOL;
  }
}
