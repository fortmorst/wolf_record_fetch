<?php
class Dark extends SOW
{
  const SYSWORD = "暗黒審問";
  protected function fetch_win_message()
  {
    $not_wtm = '/村の更新日が延長されました。|村の設定が変更されました。/';

    $wtmid = trim($this->fetch->find('p.info',-1)->plaintext);
    if(preg_match($not_wtm,$wtmid))
    {
      $do_i = -2;
      do
      {
        $wtmid = trim($this->fetch->find('p.info',$do_i)->plaintext);
        $do_i--;
      } while(preg_match($not_wtm,$wtmid));
    }
    $wtmid = preg_replace("/\r\n/", "", $wtmid);
    return $wtmid;
  }

  protected function fetch_persona($person)
  {
    $persona = trim($person->find('td',0)->plaintext);
    if(preg_match('/\(勝利\)$/',$persona))
    {
      $this->user->persona = mb_ereg_replace('\(勝利\)','',$persona);
    }
    else
    {
      $this->user->persona = $persona;
    }
  }
}
