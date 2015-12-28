<?php

class Crescent extends Giji_Old
{
  use TRS_Crescent;
  protected $RP_SP = 
  [
     "蒼い三日月"=>'BM'
    ,"変態BBS"=>'HENTAI'
    ,"ＧＭイラットのクロサー"=>'CLOSED'
  ];

  protected function fetch_date()
  {
    $date = $this->fetch->find('div.mes_date',0)->plaintext;
    $date = mb_substr($date,mb_strpos($date,"2"),10);
    $this->village->date = preg_replace('/(\d{4})\/(\d{2})\/(\d{2})/','\1-\2-\3',$date);
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
      $this->output_comment('rp');
    }
  }
  protected function fetch_wtmid()
  {
    if($this->village->policy)
    {
      $not_wtm = '/村の更新日が延長されました。|村の設定が変更されました。/';
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
      $wtmid = mb_ereg_replace('そして、天に.+|そして、お日.+|明日の遠足.+|そして、死の.+','',$wtmid,'m');
      //特定の言い換えだけ取得文字部分を変更
      if($this->village->rp === 'BM' || $this->village->rp === 'CLOSED')
      {
        $wtmid = mb_substr(preg_replace("/\r\n/","",$wtmid),-6);
      }
      else
      {
        $wtmid = mb_substr(preg_replace("/\r\n/","",$wtmid),2,13);
      }

      if($this->village->rp !== 'NORMAL')
      {
        $this->village->wtmid = $this->{'WTM_'.$this->village->rp}[$wtmid];
      }
      else
      {
        $this->village->wtmid = $this->WTM[$wtmid];
      }
    }
    else
    {
      $this->village->wtmid = Data::TM_RP;
    }
  }
  protected function fetch_users($person)
  {
    $this->fetch_persona($person);
    $this->fetch_player($person);

    $role = trim($person->find('td',4)->plaintext);
    if(mb_substr($role,-2) === '居た')
    {
      $this->insert_onlooker();
    }
    else
    {
      $this->fetch_role($role);
      $this->fetch_sklid();
      $this->fetch_dtid(trim($person->find('td',2)->plaintext));
      $this->fetch_rltid(trim($person->find('td',3)->plaintext));
      $this->fetch_life();
    }
  }
  protected function fetch_role($role)
  {
    $this->user->role = mb_ereg_replace('.+：([^\r\n]+)\r\n　　.+','\\1',$role,'m');
    $this->fetch_tmid(mb_substr($role,0,2));
  }
  protected function fetch_tmid($tmid)
  {
    if($this->village->rp !== 'NORMAL')
    {
      $this->user->tmid = $this->{'TM_'.$this->village->rp}[$tmid][0];
      $is_evil_team = $this->{'TM_'.$this->village->rp}[$tmid][1];
    }
    else
    {
      $this->user->tmid = $this->TEAM[$tmid][0];
      $is_evil_team = $this->TEAM[$tmid][1];
    }

    if($this->is_evil && $is_evil_team)
    {
      $this->village->evil_rgl = true;
    }
  }
  protected function fetch_dtid($dtid)
  {
    if($dtid === '生存者' || $dtid === '健常者')
    {
      $this->user->end = $this->village->days;
      $this->user->dtid = Data::DES_ALIVE;
    }
    else
    {
      $this->user->end = (int)mb_ereg_replace(".+\((\d+)d\)","\\1",$dtid,'m');
      if($this->village->rp === 'HENTAI')
      {
        $this->user->dtid = $this->DES_HENTAI[mb_substr($dtid,0,mb_strpos($dtid,"\n")-1)];
      }
      else
      {
        $this->user->dtid = $this->DESTINY[mb_substr($dtid,0,mb_strpos($dtid,"\n")-1)];
      }
    }
  }

  protected function fetch_sklid()
  {
    $role = $this->user->role;
    if(mb_strpos($role,"、") === false)
    {
      $sklid = $role;
    }
    else
    {
      //役職欄に絆などついている場合
      $sklid = mb_substr($role,0,mb_strpos($role,"、"));
    }
    if($this->village->rp !== 'NORMAL')
    {
      $this->user->sklid = $this->{'SKL_'.$this->village->rp}[$sklid];
    }
    else
    {
      $this->user->sklid = $this->SKILL[$sklid];
    }
  }
}
