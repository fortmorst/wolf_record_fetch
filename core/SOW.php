<?php

class SOW extends SOW_MOD
{
  protected $cursewolf = [];

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

    if($this->sysword !== 'pro')
    {
      $this->fetch_rp();
    }

    if($this->policy === null)
    {
      $this->fetch_policy();
    }

    $this->fetch->clear();
  }
  protected function fetch_rp()
  {
    if($this->sysword === 'pro')
    {
      //プロローグから取得する
      $rp = mb_substr($this->fetch->find('p.info',0)->plaintext,1,5);
      $sql = "SELECT name FROM sysword WHERE prologue='$rp'";
      $stmt = $this->db->query($sql);
      if($stmt === false)
      {
        $this->output_comment('undefined',__FUNCTION__,$rp);
        $rp = "人狼物語";
      }
      else
      {
        $stmt = $stmt->fetch();
        $rp = $stmt['name'];
      }
    }
    else if($this->sysword === null)
    {
      //情報欄から取得する
       $rp = trim($this->fetch->find('p.multicolumn_left',7)->plaintext);
    }
    else
    {
      //固定
      $rp = $this->sysword;
    }
    $this->village->rp = $rp;
    //言い換えリストに登録がなければ追加
    if(!isset($GLOBALS['syswords'][$rp]))
    {
      $this->fetch_sysword($rp);
    }
  }
  protected function make_sysword_sql($rp)
  {
    return "SELECT name,mes_sklid,mes_dt_sys,mes_wtmid FROM sysword WHERE name='$rp'";
  }
  protected function make_sysword_set($values,$table,$name)
  {
    if($table === 'mes_sklid')
    {
      $sql = "SELECT m.name,orgid,tmid from mes_sklid m join skill s on orgid = s.id where m.id in ($values)";
    }
    else
    {
      $sql = "SELECT * from $table where id in ($values)";
    }
    $stmt = $this->db->query($sql);
    $list = [];
    switch($table)
    {
      case 'mes_sklid':
        foreach($stmt as $item)
        {
          $list[$item['name']] = ['sklid'=>(int)$item['orgid'],'tmid'=>(int)$item['tmid']];
        }
        break;
      case 'mes_dt_sys':
        foreach($stmt as $item)
        {
          $list[$item['name']] = ['regex'=>$item['regex'],'dtid'=>(int)$item['orgid']];
        }
        break;
      case 'mes_wtmid':
        foreach($stmt as $item)
        {
          $list[$item['name']] = (int)$item['orgid'];
        }
        break;
    }
    $GLOBALS['syswords'][$name]->{$table} = $list;
  }
  protected function fetch_from_pro()
  {
    $url = $this->url.'&turn=0&row=10&mode=all&move=page&pageno=1';
    $this->fetch->load_file($url);
    sleep(1);

    $this->fetch_date();
    if($this->sysword === 'pro')
    {
      $this->fetch_rp();
    }
    $this->fetch->clear();
  }
  protected function make_cast()
  {
    $cast = $this->fetch->find('tbody tr');
    array_shift($cast);
    $this->cast = $cast;
  }
  protected function insert_users()
  {
    $list = [];
    $this->users = [];
    foreach($this->cast as $key=>$person)
    {
      $this->user = new User();
      $this->fetch_users($person);
      $this->users[] = $this->user;
      //生存者を除く名前リストを作る
      $list[] = $this->user->persona;
      if($this->user->end !== null)
      {
        unset($list[$key]);
      }
    }
    $this->fetch_from_daily($list);

    foreach($this->users as $user)
    {
      //var_dump($user->get_vars());
      if(!$user->is_valid())
      {
        $this->output_comment('n_user',__function__,$user->persona);
      }
    }
  }
  protected function fetch_users($person)
  {
    $this->fetch_persona($person);
    $this->fetch_player($person);
    $this->fetch_role($person);

    $this->fetch_sklid();
    $this->fetch_rltid_sow();

    //見物人
    if($this->user->tmid === Data::TM_ONLOOKER)
    {
      $this->insert_onlooker();
      return;
    }

    if($this->is_alive($person))
    {
      $this->insert_alive();
    }
  }
  protected function is_alive($person)
  {
    $status = $person->find('td',2)->plaintext;
    if($status === '生存')
    {
      return true;
    }
    else
    {
      return false;
    }
  }
  protected function modify_from_sklid()
  {
    //狂人を人狼陣営にする
    if($this->user->tmid === Data::TM_EVIL)
    {
      $this->user->tmid = Data::TM_WOLF;
    }

    //呪狼の名前をメモ
    if($this->user->sklid === Data::SKL_WOLF_CURSED)
    {
      $this->cursewolf[] = $this->user->persona;
    }
  }
  protected function fetch_role($person)
  {
    $role = $person->find('td',3)->plaintext;
    $this->user->role = mb_ereg_replace('\A(.+) \(.+を希望\)(.+|)','\1',$role,'m');
  }
  protected function fetch_daily_url($i,$find)
  {
    $row = 40;
    $url = $this->url.'&turn='.$i.'&mode=all&move=page&pageno=1&row='.$row;
    $this->fetch->load_file($url);
    sleep(1);
    $announce = $this->fetch->find($find);
    //処刑以降が取れてなさそうな場合はログ件数を増やす
    if(count($announce) <= 1 && $find !== 'p.infosp')
    {
      do
      {
        $row += 10;
        $url = $this->url.'&turn='.$i.'&mode=all&move=page&pageno=1&row='.$row;
        $this->fetch->load_file($url);
      sleep(1);
        $announce = $this->fetch->find($find);
        if($row >= 70)
        {
          echo '>NOTICE: too deep row in fetch_daily_url'.PHP_EOL;
          break;
        }
      } while (count($announce) <= 1);
    }
    return $announce;
  }
  protected function fetch_from_daily($list)
  {
    $days = $this->village->days;
    $find = 'p.info';

    for($i=2; $i<=$days; $i++)
    {
      $announce = $this->fetch_daily_url($i,$find);
      foreach($announce as $item)
      {
        $key_u = $this->fetch_key_u($list,$item);
        if($key_u === false)
        {
          continue;
        }
        $this->users[$key_u]->end = $i;
        $this->users[$key_u]->life = round(($i-1) / $this->village->days,3);
      }
      $this->fetch->clear();
    }
  }
  protected function fetch_key_u($list,$item)
  {
    $destiny = trim(preg_replace("/\r\n/",'',$item->plaintext));
    $key= mb_substr(trim($item->plaintext),-8,8);

    if($this->check_syswords($key,'dt_sys'))
    {
      $regex = $GLOBALS['syswords'][$this->village->rp]->mes_dt_sys[$key]['regex'];
      $dtid  = $GLOBALS['syswords'][$this->village->rp]->mes_dt_sys[$key]['dtid']; 
    }
    else
    {
      return false;
    }

    //適当系の被襲撃者はスキップ
    if($regex === null)
    {
      $this->output_comment('fool',__FUNCTION__);
      return false;
    }

    $persona = trim(mb_ereg_replace($regex,'\2',$destiny,'m'));

    $key_u = array_search($persona,$list);
    if($key_u === false)
    {
      return false;
    }
    $this->fetch_dtid_sow($key_u,$dtid,$persona);
    return $key_u;
  }
  protected function fetch_dtid_sow($key_u,$dtid,$persona)
  {
    //妖魔陣営の無残死は呪殺死にする
    if($this->users[$key_u]->tmid === Data::TM_FAIRY && $dtid === Data::DES_EATEN)
    {
      $this->users[$key_u]->dtid = Data::DES_CURSED;
    }
    //呪狼が存在する編成で、占い師が襲撃された場合別途チェック
    else if(!empty($this->cursewolf) && $this->users[$key_u]->sklid === Data::SKL_SEER && $dtid === Data::DES_EATEN && $this->modify_cursed_seer($persona,$key_u))
    {
      $this->users[$key_u]->dtid = Data::DES_CURSED;
    }
    else
    {
      $this->users[$key_u]->dtid = $dtid;
    }
  }
  protected function modify_cursed_seer($persona,$key_u)
  {
    $dialog = 'を占った。';
    $pattern = ' ?(.+) は、(.+) を占った。';
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
