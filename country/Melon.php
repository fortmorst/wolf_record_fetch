<?php

class Melon extends SOW_MOD
{
  protected $WTM_SKIP = [
     '/村の設定が変更されました。/','/遺言状が公開されました。/','/遺言メモが公開/','/おさかな、美味しかったね！/','/魚人はとても美味/','/人魚は/','/とってもきれいだね！/','/自殺志願者/','/運動会は晴天です！/','/見物しに/','/がやってきたよ/'
  ];
  protected $ONLOOKER = [];
  protected function fetch_rp()
  {
    $this->village->rp = $this->fetch->find('p.multicolumn_left',9)->plaintext;
    if(!isset($this->syswords[$this->village->rp]))
    {
      $this->fetch_sysword($this->village->rp);
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
      $this->output_comment('ruin_midway',__function__);
    }
    else
    {
      $this->fetch_wtmid();
    }
    $this->make_cast();
  }
  protected function fetch_win_message()
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
    $wtmid = preg_replace("/\A([^\r\n]+)(\r\n.+)?\z/ms", "$1", $wtmid);
    return $wtmid;
  }
  protected function make_cast()
  {
    $cast = $this->fetch->find('table tr');
    array_shift($cast);
    //「見物人」見出しを削除する
    //「見物人」の言い換えを取得する
    foreach($cast as $key=>$item)
    {
      $line = $item->find('td',2);
      if(empty($line))
      {
        //見物人か支配人か
        $this->check_onlooker($item->find('th',0)->plaintext);
        unset($cast[$key]);
      }
    }
    $this->cast = $cast;
  }
  protected function check_onlooker($onlooker)
  {
    $onlooker = mb_substr($onlooker,0,-2);
    if($this->check_syswords($onlooker,'sklid'))
    {
      $this->ONLOOKER[$this->syswords[$this->village->rp]['sklid'][$onlooker]['sklid']] = $onlooker;
    }
    else
    {
      $this->output_comment('undefined',__FUNCTION__,$onlooker);
      $this->ONLOOKER[] = [Data::SKL_ONLOOKER=>$onlooker];
    }
  }
  protected function fetch_users($person)
  {
    $this->fetch_persona($person);
    $this->fetch_player($person);
    $this->fetch_role($person);
    if($this->user->tmid !== Data::TM_ONLOOKER)
    {
      $this->fetch_dtid($person);
      $this->fetch_rltid($person->find('td',2)->plaintext);
    }
  }
  protected function fetch_role($person)
  {
    $role = $person->find("td",4)->plaintext;
    $dtid = $person->find("td",3)->plaintext;
    //見物人の場合
    if($role === '--')
    {
      if($dtid === "--")
      {
        $this->user->role = $this->ONLOOKER[Data::SKL_OWNER];
        $this->insert_owner();
      }
      else
      {
        $this->user->role = $this->ONLOOKER[Data::SKL_ONLOOKER];
        $this->insert_onlooker();
      }
      return;
    }

    $this->user->role = preg_replace('/\r\n.+/s','',$role);

    if(preg_match("/\A.+\(.+\)/",$this->user->role))
    {
      //婚約者は元の役職扱いにする
      $engaged = preg_replace('/^.+\((.+)\)/','\1',$this->user->role);
      if($this->check_syswords($engaged,'sklid'))
      {
        $this->user->sklid = $this->syswords[$this->village->rp]['sklid'][$engaged]['sklid'];
        $this->user->tmid = Data::TM_LOVERS;
      }
      else
      {
        $this->user->sklid= null;
        $this->user->tmid = Data::TM_LOVERS;
        $this->output_comment('undefined',__FUNCTION__,$engaged);
      }
      return;
    }
    else if($this->check_syswords($this->user->role,'sklid'))
    {
      //通常の役職挿入
      $this->user->sklid = $this->syswords[$this->village->rp]['sklid'][$this->user->role]['sklid'];
      $this->user->tmid = $this->syswords[$this->village->rp]['sklid'][$this->user->role]['tmid'];
    }
    else
    {
      $this->user->sklid= null;
      $this->user->tmid= null;
      $this->output_comment('undefined',__FUNCTION__,$this->user->role);
    }

    //失恋済の求婚者は村人陣営に修正
    if($this->user->sklid === Data::SKL_QP_SELF_MELON && preg_match('/(失恋|片思い|孤独)★/',$role))
    {
      $this->user->tmid = Data::TM_VILLAGER;
    }
    //裏切り陣営を人狼陣営に変える
    $this->modify_from_sklid();
  }
  protected function insert_owner()
  {
    $this->user->sklid = Data::SKL_OWNER;
    $this->user->tmid = Data::TM_ONLOOKER;
    $this->user->dtid  = Data::DES_ONLOOKER;
    $this->user->end   = 1;
    $this->user->life  = 0.000;
    $this->user->rltid = Data::RSL_ONLOOKER;
  }
  protected function fetch_dtid($person)
  {
    $destiny = $person->find("td",3)->plaintext;
    if($destiny === '生存')
    {
      $this->user->dtid = Data::DES_ALIVE;
      $this->user->end = $this->village->days;
      $this->user->life = 1.000;
    }
    else
    {
      $dtid = mb_substr($destiny,mb_strpos($destiny,'d')+1);
      $this->fetch_from_sysword($dtid,'dtid');
      $this->user->end = (int)mb_substr($destiny,0,mb_strpos($destiny,'d'));
      $this->user->life = round(($this->user->end-1) / $this->village->days,3);
    }
  }
  protected function fetch_rltid($person)
  {
    if($this->user->player !== "master" && $this->user->dtid === Data::DES_EATEN && $this->user->end === 2)
    {
      //喋るダミー(IDがmasterではない)は参加扱いにする
      $this->user->rltid = Data::RSL_JOIN;
    }
    else
    {
      parent::fetch_rltid($person);
    }
  }
}
