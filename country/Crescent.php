<?php

class Crescent extends Giji_Old
{
  protected function fetch_date()
  {
    $date = $this->fetch->find('div.mes_date',0)->plaintext;
    $date = mb_substr($date,mb_strpos($date,"2"),10);
    $this->village->date = preg_replace("/(\d{4})\/(\d{2})\/(\d{2})/","$1-$2-$3",$date);
  }
  protected function fetch_policy()
  {
    $policy = $this->fetch->find('p.multicolumn_left',1)->plaintext;
    if(mb_strpos($policy,"真剣勝負") !== false)
    {
      $this->village->policy = true;
    }
    else
    {
      $this->village->policy = false;
    }
  }
  protected function check_ruin()
  {
    $info = "div.info";
    $infosp = "div.infosp";

    if(count($this->fetch->find($info)) <= 1 && count($this->fetch->find($infosp)) === 0)
    {
      return false;
    }
    else
    {
      return true;
    }
  }
  protected function fetch_win_message()
  {
    $not_wtm = "/延長されました。|村の設定が変更されました。/";

    $wtmid = trim($this->fetch->find('div.info',-1)->plaintext);
    if(preg_match($not_wtm,$wtmid))
    {
      $do_i = -2;
      do
      {
        $wtmid = trim($this->fetch->find('div.info',$do_i)->plaintext);
        $do_i--;
      } while(preg_match($not_wtm,$wtmid));
    }

    //照・据え膳勝利メッセージがあったら削除
    $wtmid = preg_replace("/そして、.+|ブヒィ.+|追加勝利.+|ｗｗｗ.+|明日の遠足.+|ああそうだ.+|/s","",$wtmid);
    //改行を削除
    $wtmid = preg_replace("/\r\n/","",$wtmid);
    return $wtmid;
  }
  protected function fetch_users($person)
  {
    $this->fetch_persona($person);
    $this->fetch_player($person);

    $role = trim($person->find('td',4)->plaintext);
    if(mb_substr($role,-2) === "居た")
    {
      $this->user->role = "見物人";
      $this->insert_onlooker();
    }
    else
    {
      $this->fetch_role($role);
      $this->fetch_tmid($role);
      $this->fetch_sklid();
      $this->fetch_dtid($person->find('td',2)->plaintext);
      $this->fetch_rltid(trim($person->find('td',3)->plaintext));
      $this->fetch_life();
    }
  }
  protected function fetch_role($role)
  {
    $this->user->role = preg_replace("/.+：([^\r\n]+)\r\n　　.+/s","$1",$role);
  }
  protected function fetch_dtid($dtid)
  {
    if(!preg_match("/.+\r\n\((\d+)d\)/",$dtid))
    {
      $this->user->end = $this->village->days;
      $this->user->dtid = Data::DES_ALIVE;
    }
    else
    {
      $this->user->end = (int)preg_replace("/.+\((\d+)d\)/s","$1",$dtid);
      $dtid = preg_replace("/(.+)\r\n\(\d+d\)/s","$1",$dtid);
      $this->fetch_from_sysword($dtid,"dtid");
    }
  }
}
