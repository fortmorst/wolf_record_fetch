<?php
class Phantom extends SOW
{
  const SYS_PRO = [
    '呼び寄せた'=>"人狼物語　幻夢",
    '昼間は人間'=>"人狼BBS",
    '　村は数十'=>"人狼審問",
    '　それはま'=>"ゆめまぼろし",
    'なんか人狼'=>"適当系",
   ];

  protected function fetch_from_info()
  {
    $this->fetch->load_file($this->url."&cmd=vinfo");
    sleep(1);

    $this->fetch_name();
    if($this->fetch_days() === false)
    {
      $this->fetch->clear();
      return;
    }

    $this->fetch->clear();
  }
  protected function fetch_from_pro()
  {
    $url = $this->url.'&turn=0&row=10&mode=all&move=page&pageno=1';
    $this->fetch->load_file($url);
    sleep(1);

    $this->fetch_date();
    $this->fetch_rp();
    $this->fetch->clear();
  }
  protected function fetch_rp()
  {
    $rp = mb_substr($this->fetch->find('p.info',0)->plaintext,1,5);
    $this->village->rp = self::SYS_PRO[$rp];
    //言い換えリストに登録がなければ追加
    if(!isset($this->syswords[$this->village->rp]))
    {
      $this->fetch_sysword($this->village->rp);
    }
  }
  protected function make_sysword_set($rp,$sysid)
  {
    foreach(["sklid","dt_sys"] as $table)
    {
      switch($table)
      {
        case "sklid":
          $list = $this->make_sysword_name_sklid_tmid_set($sysid);
          break;
        case "dt_sys":
          $list = $this->make_sysword_dtsys_set($sysid);
          break;
      }
      $this->syswords[$rp][$table] = $list;
    }
  }
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
      // var_dump($user->get_vars());
      if(!$user->is_valid())
      {
        $this->output_comment('n_user',__function__,$user->persona);
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
