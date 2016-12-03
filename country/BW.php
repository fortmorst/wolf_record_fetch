<?php
class BW extends SOW_MOD
{
  protected function fetch_rp()
  {
    //情報欄から取得する
    $rp = trim($this->fetch->find('p.multicolumn_left',4)->plaintext);
    $this->village->rp = $rp;
    //言い換えリストに登録がなければ追加
    if(!isset($this->syswords[$rp]))
    {
      $this->fetch_sysword($rp);
    }
  }
  protected function fetch_policy_detail()
  {
    $policy = $this->fetch->find('p.multicolumn_left',8)->plaintext;
    if(preg_match('/物語/',$policy))
    {
      $this->village->policy = false;
    }
  }
  protected function fetch_date()
  {
    $date = $this->fetch->find('td.time_info span',0)->plaintext;
    $date = mb_substr($date,0,10);
    $this->village->date = preg_replace('/(\d{4})\/(\d{2})\/(\d{2})/','\1-\2-\3',$date);
  }

  protected function make_cast()
  {
    $cast = $this->fetch->find('table.castlist tbody tr');
    array_shift($cast);
    $this->cast = $cast;
  }
  protected function fetch_dtid($person)
  {
    $destiny = trim($person->find('td',3)->plaintext);
    $destiny = mb_ereg_replace('\d+d(.+)','\1',$destiny);
    $this->fetch_from_sysword($destiny,'dtid');
  }
  protected function fetch_end($person)
  {
    $end = trim($person->find('td',3)->plaintext);
    $this->user->end = (int)mb_ereg_replace('(\d+)d.+','\1',$end);
    $this->user->life = round(($this->user->end-1) / $this->village->days,3);
  }
}
