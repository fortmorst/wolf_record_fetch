<?php

class SOW extends Country
{
  private $cursewolf = [];

  function fetch_village()
  {
    $this->cursedwolf = [];
    $this->fetch_from_info();
    $this->fetch_from_pro();

    if($this->village->wtmid === Data::TM_RUIN)
    {
      return false;
    }
    $this->fetch_from_epi();
  }
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

    //判定方法を変える
    if(empty($this->RP_PRO))
    {
      $this->fetch_rp();
    }

    if($this->policy === null)
    {
      $this->fetch_policy();
    }

    $this->fetch->clear();
  }
  protected function fetch_name()
  {
    $this->village->name = $this->fetch->find('p.multicolumn_left',0)->plaintext;
  }
  protected function fetch_days()
  {
    $days = $this->fetch->find('p.turnnavi',0)->find('a',-4);
    //進行中(=雑談村)または開始しなかった廃村村
    if($days === null || $days->innertext === 'プロローグ')
    {
      $this->insert_as_ruin();
      return false;
    }
    $this->village->days = mb_substr($days->innertext,0,mb_strpos($days,'日')) +1;
  }
  protected function fetch_rp()
  {
    if(empty($this->RP_PRO))
    {
       $rp = trim($this->fetch->find('p.multicolumn_left',7)->plaintext);
    }
    else
    {
      $rp = mb_substr($this->fetch->find('p.info',0)->plaintext,1,5);
      //プロローグから取得する場合 書き直し
      //if(array_key_exists($rp,$this->RP_PRO))
      //{
        //$this->village->rp = $this->RP_PRO[$rp];
      //}
    }
    $this->village->rp = $rp;
    //言い換えリストに登録がなければ追加
    if(!isset($GLOBALS['syswords'][$rp]))
    {
      $this->fetch_sysword($rp);
    }
  }
  protected function fetch_from_pro()
  {
    $url = $this->url.'&turn=0&row=10&mode=all&move=page&pageno=1';
    $this->fetch->load_file($url);
    sleep(1);

    $this->fetch_date();
    if(!empty($this->RP_PRO))
    {
      $this->fetch_rp();
    }
    $this->fetch->clear();
  }
  protected function fetch_date()
  {
    $date = $this->fetch->find('div.mes_date',0)->plaintext;
    $date = mb_substr($date,mb_strpos($date,"2"),10);
    $this->village->date = preg_replace('/(\d{4})\/(\d{2})\/(\d{2})/','\1-\2-\3',$date);
  }
  protected function fetch_from_epi()
  {
    $url = $this->url.'&turn='.$this->village->days.'&row=40&mode=all&move=page&pageno=1';
    $this->fetch->load_file($url);
    sleep(1);

    //廃村なら非参加扱い
    if(!$this->check_ruin())
    {
      $this->village->wtmid = Data::TM_RP;
      $this->output_comment('ruin_midway',__function__);
    }
    else
    {
      $this->fetch_wtmid();
    }
    $this->make_cast();
  }
  protected function fetch_wtmid()
  {
    if($this->policy || $this->village->policy)
    {
      $wtmid = $this->fetch_win_message();
      if(array_key_exists($wtmid,$GLOBALS['syswords'][$this->village->rp]->mes_wtmid))
      {
        $this->village->wtmid = $GLOBALS['syswords'][$this->village->rp]->mes_wtmid[$wtmid];
      }
      else
      {
        $this->village->wtmid = Data::TM_RP;
        $this->output_comment('undefined',__FUNCTION__,$wtmid);
      }
    }
    else
    {
      $this->village->wtmid = Data::TM_RP;
    }
  }
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
    $wtmid = preg_replace("/\A([^\r\n]+)(\r\n.+)?\z/ms", "$1", $wtmid);
    return $wtmid;
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
      var_dump($user->get_vars());
      if(!$user->is_valid())
      {
        $this->output_comment('n_user',__function__);
      }
    }
  }
  protected function fetch_users($person)
  {
    $this->fetch_persona($person);
    $this->fetch_player($person);
    $this->fetch_role($person);

    $this->fetch_sklid();
    $this->fetch_rltid();

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
  protected function fetch_sklid()
  {
    if(array_key_exists($this->user->role,$GLOBALS['syswords'][$this->village->rp]->mes_sklid))
    {
      $this->user->sklid = $GLOBALS['syswords'][$this->village->rp]->mes_sklid[$this->user->role]['sklid'];
      $this->user->tmid = $GLOBALS['syswords'][$this->village->rp]->mes_sklid[$this->user->role]['tmid'];

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
    else
    {
      $this->user->sklid= null;
      $this->user->tmid= null;
      $this->output_comment('undefined',__FUNCTION__,$this->user->role);
    }
  }
  protected function fetch_persona($person)
  {
    $this->user->persona = trim($person->find("td",0)->plaintext);
  }
  protected function fetch_player($person)
  {
    $player =trim($person->find("td a",0)->plaintext);
    if(isset($this->{'d_'.get_class($this)}))
    {
      $this->user->player =$this->modify_player($player);
    }
    else
    {
      $this->user->player = $player;
    }
  }
  protected function fetch_role($person)
  {
    $role = $person->find('td',3)->plaintext;
    $this->user->role = mb_ereg_replace('\A(.+) \(.+を希望\)(.+|)','\1',$role,'m');
  }
  protected function fetch_rltid()
  {
    if($this->village->wtmid === 0)
    {
      $this->user->rltid = Data::RSL_JOIN;
      return;
    }

    if($this->user->tmid === $this->village->wtmid)
    {
      $this->user->rltid = Data::RSL_WIN;
    }
    else
    {
      $this->user->rltid = Data::RSL_LOSE;
    }
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

    if(array_key_exists($key,$GLOBALS['syswords'][$this->village->rp]->mes_dt_sys))
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
    $this->fetch_dtid($key_u,$dtid,$persona);
    return $key_u;
  }
  protected function fetch_dtid($key_u,$dtid,$persona)
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
