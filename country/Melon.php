<?php

class Melon extends SOW
{
  use TRS_Melon;

  protected function fetch_rp()
  {
    $rp = $this->fetch->find('p.multicolumn_left',9)->plaintext;
    if(array_key_exists($rp,$this->RP_LIST))
    {
      $this->village->rp = $this->RP_LIST[$rp];
    }
    else
    {
      $this->village->rp = 'SOW';
      $this->output_comment('undefined',$rp);
    }
  }
  protected function fetch_policy()
  {
    $policy= mb_strstr($this->fetch->find('p.multicolumn_left',-2)->plaintext,'推理');
    if($policy !== false)
    {
      $this->village->policy = true;
    }
    else
    {
      $this->village->policy = false;
      $this->output_comment('rp');
    }
  }
  protected function fetch_from_pro()
  {
    $url = $this->url.'&t=0&r=10&o=a&mv=p&n=1';
    $this->fetch->load_file($url);
      sleep(1);

    $this->fetch_date();
    $this->fetch->clear();
  }
  protected function fetch_from_epi()
  {
    $url = $this->url.'&t='.$this->village->days.'&row=40&o=a&mv=p&n=1';
    $this->fetch->load_file($url);
    sleep(1);

    //廃村なら非参加扱い
    if(!$this->check_ruin())
    {
      $this->village->wtmid = Data::TM_RP;
      $this->output_comment('ruin_midway');
    }
    else
    {
      $this->fetch_wtmid();
    }
    $this->make_cast();
  }
  protected function fetch_wtmid()
  {
    if(!$this->village->policy)
    {
      $this->village->wtmid = Data::TM_RP;
    }
    else
    {
      $wtmid = trim($this->fetch->find('p.info',-1)->plaintext);
      //遅刻見物人のシスメなどを除外
      $count_replace = 0;
      preg_replace($this->WTM_SKIP,'',$wtmid,1,$count_replace);
      if($count_replace)
      {
        $do_i = -2;
        do
        {
          $wtmid = trim($this->fetch->find('p.info',$do_i)->plaintext);
          $do_i--;
          preg_replace($this->WTM_SKIP,'',$wtmid,1,$count_replace);
        } while($count_replace);
      }
      $wtmid = preg_replace('/\r\n/','',$wtmid);
      //人狼劇場言い換えのみ、先頭12文字で取得する
      if($this->village->rp === 'THEATER')
      {
        $wtmid = mb_substr($wtmid,0,12);
      }
      else
      {
        $wtmid = mb_substr($wtmid,-10);
      }
      if(array_key_exists($wtmid,$this->{'WTM_'.$this->village->rp}))
      {
        $this->village->wtmid = $this->{'WTM_'.$this->village->rp}[$wtmid];
      }
      else
      {
        $this->village->wtmid = Data::TM_RP;
        $this->output_comment('undefined',$wtmid);
      }
    }
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
  protected function insert_users()
  {
    $this->users = [];
    foreach($this->cast as $person)
    {
      $this->user = new User();
      $this->fetch_users($person);
      if(!$this->user->is_valid())
      {
        $this->output_comment('n_user');
      }
      //エラーでも歯抜けが起きないように入れる
      $this->users[] = $this->user;
    }
  }
  protected function fetch_users($person)
  {
    $this->fetch_persona($person);
    $this->fetch_player($person);
    $this->fetch_role($person);
  }
  protected function fetch_player($person)
  {
    $player = trim($person->find("td",1)->plaintext);
    $this->user->player =$this->modify_player($player);
  }

  protected function fetch_role($person)
  {
    $role = $person->find("td",4)->plaintext;
    $dtid = $person->find("td",3)->plaintext;
    //役職の改行以降をカットする
    $this->user->role = preg_replace('/\r\n.+/s','',$role);
    if($role === '--')
    {
      $this->insert_onlooker();
      $this->modify_onlooker($dtid);
    }
    else
    {
      if(!empty($this->{'SKL_'.$this->village->rp}))
      {
        $rp = $this->village->rp;
      }
      else
      {
        $rp = 'SOW';
      }
      //婚約者は元の役職扱いにする
      if(mb_strstr($this->user->role,$this->{'SKL_'.$rp}[25]))
      {
        $sklid = preg_replace('/^.+\((.+)\)/','\1',$this->user->role);
        $this->user->tmid = Data::TM_LOVERS;
      }
      else
      {
        $sklid = preg_replace('/\(.+/','',$this->user->role);
      }
      //能力が登録済かチェック
      $skl_key = array_search($sklid,$this->{'SKL_'.$rp});
      if($skl_key !== false)
      {
        $this->user->sklid = $this->SKILL[$skl_key][0];
        if($this->user->tmid !== Data::TM_LOVERS)
        {
          $this->user->tmid = $this->SKILL[$skl_key][1];
          if($this->user->sklid === Data::SKL_QP_SELF_MELON && preg_match('/(失恋|片思い|孤独)★/',$role))
          {
            $this->user->tmid = Data::TM_VILLAGER;
          }
        }
      }
      else if(mb_strstr($sklid,$this->{'SKL_'.$rp}[6]))
      {
        //聖痕者
        $this->user->sklid = $this->SKILL[6][0];
        $this->user->tmid = $this->SKILL[6][1];
      }
      else
      {
        //未定義の役職
        $this->user->sklid = $this->SKILL[0][0];
        $this->user->tmid = $this->SKILL[0][1];
        $this->output_comment('undefined',$sklid);
      }
      $this->fetch_end($dtid);
      $this->fetch_rltid_m($person);
    }
  }
  protected function modify_onlooker($dtid)
  {
    if($dtid === '--')
    {
      $this->user->sklid = Data::SKL_OWNER;
      switch($this->village->rp)
      {
        case 'GB':
          $this->user->role = '旧校舎の主';
          break;
        default:
          $this->user->role = '支配人';
          break;
      }
    }
    else
    {
      switch($this->village->rp)
      {
        case 'MELON':
          $this->user->role = 'やじうま';
          break;
        case 'GB':
          $this->user->role = '観客';
          break;
        case 'MOON':
          $this->user->role = 'お客様';
          break;
        default:
          $this->user->role = '見物人';
          break;
      }
    }
  }
  protected function fetch_end($dtid)
  {
    if($dtid === '生存')
    {
      $this->user->dtid = Data::DES_ALIVE;
      $this->user->end = $this->village->days;
      $this->user->life = 1.000;
    }
    else
    {
      $this->user->dtid = $this->DESTINY[mb_substr($dtid,mb_strpos($dtid,'d')+1)];
      $this->user->end = (int)mb_substr($dtid,0,mb_strpos($dtid,'d'));
      $this->user->life = round(($this->user->end-1) / $this->village->days,3);
    }
  }
  protected function fetch_rltid_m($person)
  {
    if($this->village->wtmid === Data::TM_RP)
    {
      $this->user->rltid = Data::RSL_JOIN;
    }
    else if($this->user->player !== "master" && $this->user->dtid === Data::DES_EATEN && $this->user->end === 2)
    {
      //喋るダミー(IDがmasterではない)は参加扱いにする
      $this->user->rltid = Data::RSL_JOIN;
    }
    else
    {
      $this->user->rltid = $this->RSL[$person->find("td",2)->plaintext];
    }
  }
}
