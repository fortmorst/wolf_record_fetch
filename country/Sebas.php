<?php
class Sebas extends SOW_MOD
{
  protected function fetch_days()
  {
    $days = $this->fetch->find('p.turnnavi',1);
    if($days === null)
    {
      //進行中の雑談村
      $this->insert_as_ruin();
      return false;
    }
    else
    {
      $days = $days->find('a',-1)->href;
      $days = preg_replace('/.+turn=(\d+)&amp.+/','\1',$days) -1;
      if($days === 1)
      {
        //開始しなかった廃村村
        $this->insert_as_ruin();
        return false;
      }
      else
      {
        $this->village->days = $days;
      }
    }
  }
  protected function check_ruin()
  {
    $info = 'div.info';
    $infosp = 'div.infosp';

    if(count($this->fetch->find($info)) <= 1 && count($this->fetch->find($infosp)) === 0)
    {
      return false;
    }
    else
    {
      return true;
    }
  }
  protected function fetch_rp()
  {
    $this->village->rp = trim($this->fetch->find('p.multicolumn_left',8)->plaintext);
    if(!isset($this->syswords[$this->village->rp]))
    {
      $this->fetch_sysword($this->village->rp);
    }
  }
  protected function fetch_policy()
  {
    $policy = $this->fetch->find('p.multicolumn_left',1)->plaintext;
    if($policy === "推理あり村")
    {
      $this->village->policy = true;
    }
    else
    {
      $this->village->policy = false;
    }
  }
  protected function fetch_date()
  {
    $date = $this->fetch->find('div.mes_date',0)->plaintext;
    $date = mb_substr(preg_replace('/ /','0',$date),mb_strpos($date,"2"),10);
    $this->village->date = preg_replace('/(\d{4})\/(\d{2})\/(\d{2})/','\1-\2-\3',$date);
  }
  protected function fetch_win_message()
  {
    $not_wtm = "/0に設定されました。|村の設定が変更|に変更します。/";

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
    $wtmid = preg_replace("/\A([^\r\n]+)(\r\n.+)?\z/ms", "$1", $wtmid);
    return $wtmid;
  }

  protected function fetch_users($person)
  {
    $this->fetch_persona($person);
    $this->fetch_player($person);
    $this->fetch_dtid($person);
    $this->fetch_role($person);

    if($this->user->dtid === Data::DES_ONLOOKER)
    {
      $this->insert_onlooker();
      return;
    }

    $this->fetch_sklid();
    $this->fetch_rltid_sow();
    $this->fetch_life();
  }
  protected function fetch_role($person)
  {
    $role = $person->find('td',3)->plaintext;
    $this->user->role = trim(mb_ereg_replace('\A([^\r\n]+)(\r\n.+|)','\1',$role,'m'));
  }
  protected function fetch_dtid($person)
  {
    $destiny = $person->find('td',2)->plaintext;
    $pattern = '/(\d+)日(目に|間を|目から)(.+)/';
    preg_match_all($pattern,$destiny,$matches);
    if($this->check_syswords($matches[3][0],'dtid'))
    {
      $this->user->dtid = $this->syswords[$this->village->rp]['dtid'][$matches[3][0]];
    }
    else
    {
      $this->user->dtid = null;
      $this->output_comment('undefined',__FUNCTION__,$destiny);
    }
    $this->user->end = (int)$matches[1][0];
  }
}
