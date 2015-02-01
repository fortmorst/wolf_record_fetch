<?php

class Cherry extends SOW
{
  use TRS_SOW;
  function __construct()
  {
    $cid = 30;
    $url_vil = "http://mirage.sakuratan.com/sow.cgi?vid=";
    $url_log = "http://mirage.sakuratan.com/sow.cgi?cmd=oldlog";
    parent::__construct($cid,$url_vil,$url_log);
  }
  protected function fetch_rp()
  {
    $this->village->rp = 'SOW';
  }
  protected function make_cast()
  {
    $cast = $this->fetch->find('table tr');
    array_shift($cast);
    //「見物人」見出しを削除する
    foreach($cast as $key=>$item)
    {
      $line = $item->find('td',2);
      if(empty($line))
      {
        unset($cast[$key]);
      }
    }
    $this->cast = $cast;
  }
  protected function is_alive($person)
  {
    $status = $person->find('td',3)->plaintext;
    if($status === '生存')
    {
      return true;
    }
    else
    {
      return false;
    }
  }
  protected function fetch_role($person)
  {
    $role = $person->find('td',4)->plaintext;
    $this->user->role = mb_ereg_replace('\A(.+) \(.+を希望\)(.+|)','\1',$role,'m');
  }
}
