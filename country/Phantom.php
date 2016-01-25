<?php
class Phantom extends SOW
{
  protected function fetch_days()
  {
    $days = trim($this->fetch->find('p.turnnavi',0)->find('a',-4)->innertext);
    $this->village->days = mb_substr($days,0,mb_strpos($days,'日')) +1;
  }
  protected function fetch_from_epi()
  {
    $url = $this->url.'&turn='.$this->village->days.'&row=40&mode=all&move=page&pageno=1';
    $this->fetch->load_file($url);
    sleep(1);

    $this->village->wtmid = Data::TM_RP;
    $this->make_cast();
  }

  protected function make_sysword_sql($rp)
  {
    return "SELECT name,mes_sklid,mes_dt_sys FROM sysword WHERE name='$rp'";
  }
  protected function insert_users()
  {
    $list = [];
    $this->users = [];

    $is_not_ruined = $this->check_ruin();
    foreach($this->cast as $key=>$person)
    {
      $this->user = new User();
      $this->fetch_users($person);
      $this->users[] = $this->user;
      if(!$is_not_ruined)
      {
        //廃村村はリストを作らない
        continue;
      }
      //生存者を除く名前リストを作る
      $list[] = $this->user->persona;
      if($this->user->end !== null)
      {
        unset($list[$key]);
      }
    }
    if($is_not_ruined === true)
    {
      $this->fetch_from_daily($list);
    }

    foreach($this->users as $user)
    {
      if(!$user->is_valid())
      {
        $this->output_comment('n_user',__function__);
      }
    }
  }
  protected function fetch_role($person)
  {
    $role = $person->find('td',3)->plaintext;
    if(mb_ereg_match("\A.+を希望\z",$role))
    {
      $this->user->role = '--';
    }
    else
    {
      $this->user->role = mb_ereg_replace('\A(.+) \(.+\)(.+|)','\1',$role,'m');
    }
  }
  protected function modify_cursed_seer($persona,$key_u)
  {
    if($this->village->rp === 'ゆめまぼろし')
    {
      $dialog = 'みました。';
      $pattern = '　 ?(.+) は、(.+) を詠みました。';
    }
    else
    {
      $dialog = 'を占った。';
      $pattern = ' ?(.+) は、(.+) を占った。';
    }

    $announce = $this->fetch->find('p.infosp');
    foreach($announce as $item)
    {
      $info = trim($item->plaintext);
      $key= mb_substr($info,-5,5);
      if($key === $dialog)
      {
        $seer = trim(mb_ereg_replace($pattern,'\1',$info,'m'));
        $wolf = trim(mb_ereg_replace($pattern,'\2',$info,'m'));
        if($seer === $persona && in_array($wolf,$this->cursewolf))
        {
          return true;
        }
        else
        {
          continue;
        }
      }
    }
    return false;
  }
}
