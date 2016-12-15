<?php

class Cherry extends SOW
{
  const SYSWORD = "人狼物語";

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
