<?php

class Silence extends SOW
{
  use TRS_SOW;
  protected $RP_PRO = [
     'この村にも'=>'SOW'
    ,'昼間は人間'=>'WBBS'
    ,'あいさつす'=>'PO'
    ];
  protected $WTM_PO = [
     'ーんはぽぽぽぽーん！'=>Data::TM_VILLAGER
    ,'CMを去っていった。'=>Data::TM_WOLF
    ,'がおはよウナギ……。'=>Data::TM_FAIRY
    ,'の村を去っていった。'=>Data::TM_LOVERS
  ];
  protected $SKL_PO = [
     "たのしいなかま"=>[Data::SKL_VILLAGER,Data::TM_VILLAGER]
    ,"ぽぽぽぽーん"=>[Data::SKL_WOLF,Data::TM_WOLF]
    ,"おやすみなサイ"=>[Data::SKL_SEER,Data::TM_VILLAGER]
    ,"ただいマンボウ"=>[Data::SKL_MEDIUM,Data::TM_VILLAGER]
    ,"あいさつ坊や"=>[Data::SKL_LUNATIC,Data::TM_WOLF]
    ,"スタッフ"=>[Data::SKL_GUARD,Data::TM_VILLAGER]
    ,"AC"=>[Data::SKL_FM,Data::TM_VILLAGER]
    ,"いただきマウス"=>[Data::SKL_FAIRY,Data::TM_FAIRY]
    ,"あいさつガール"=>[Data::SKL_WHISPER,Data::TM_WOLF]
    ,"ごちそうさマウス"=>[Data::SKL_PIXY,Data::TM_FAIRY]
    ,"ありがとウサギ"=>[Data::SKL_QP,Data::TM_LOVERS]
  ];
  protected $DT_PO = [
     '挨殺された。'=>['.+(\(ランダムあいさつ\)|あいさつした。)(.+) はたのしいなかま達に挨殺された。',Data::DES_HANGED]
    ,'突然死した。'=>['\A( ?)(.+) は、突然死した。',Data::DES_RETIRED]
    ,'ち亡くすね。'=>['(.+)朝、(.+) の首がぽぽぽ.+',Data::DES_EATEN]
    ,'後を追った。'=>['\A( ?)(.+) は(民間の広告ネットワークに引きずられるように|感謝の気持ちを込めて) .+ の後を追った。',Data::DES_SUICIDE]
  ];
  function __construct()
  {
    $cid = 35;
    $url_vil = "http://silence.hotcom-web.com/cgi-bin/sow/sow.cgi?vid=";
    $url_log = "http://silence.hotcom-web.com/cgi-bin/sow/sow.cgi?cmd=oldlog";
    parent::__construct($cid,$url_vil,$url_log);
    $this->policy = true;
  }
  protected function fetch_name()
  {
    $this->village->name = $this->fetch->find('table.list tr td',1)->plaintext;
  }
  protected function fetch_days()
  {
    $days = trim($this->fetch->find('p',0)->find('a',-4)->innertext);
    $this->village->days = mb_substr($days,0,mb_strpos($days,'日')) +1;
  }
  protected function fetch_rp()
  {
    $rp = mb_substr($this->fetch->find('div.announce',0)->plaintext,0,5);
    if(array_key_exists($rp,$this->RP_PRO))
    {
      $this->village->rp = $this->RP_PRO[$rp];
    }
    else
    {
      $this->village->rp = 'SOW';
      $this->output_comment('undefined',$rp);
    }
  }
  protected function fetch_win_message()
  {
    $wtmid = trim($this->fetch->find('div.announce',-1)->plaintext);
    if(preg_match("/村の更新日が延長|村の設定が変更/",$wtmid))
    {
      $do_i = -2;
      do
      {
        $wtmid = trim($this->fetch->find('div.announce',$do_i)->plaintext);
        $do_i--;
      } while(preg_match("/村の更新日が延長|村の設定が変更/",$wtmid));
    }
    return mb_substr(preg_replace("/\r\n/","",$wtmid),-10);
  }
  protected function make_cast()
  {
    $cast = $this->fetch->find('table tr');
    array_shift($cast);
    $this->cast = $cast;
  }
  protected function fetch_sklid()
  {
    if(!empty($this->{'SKL_'.$this->village->rp}))
    {
      $this->user->sklid = $this->{'SKL_'.$this->village->rp}[$this->user->role][0];
      $this->user->tmid = $this->{'SKL_'.$this->village->rp}[$this->user->role][1];
    }
    else
    {
      $this->user->sklid = $this->SKILL[$this->user->role][0];
      $this->user->tmid = $this->SKILL[$this->user->role][1];
    }
    if(preg_match('/恋人/',$this->user->role))
    {
      $this->user->tmid = Data::TM_LOVERS;
    }
  }
  protected function fetch_from_daily($list)
  {
    $days = $this->village->days;
    $find = 'div.announce';

    //言い換えの有無
    if(!empty($this->{'DT_'.$this->village->rp}))
    {
      $rp = $this->village->rp;
    }
    else
    {
      $rp = 'NORMAL';
    }

    for($i=2; $i<=$days; $i++)
    {
      $announce = $this->fetch_daily_url($i,$find);
      foreach($announce as $item)
      {
        $key_u = $this->fetch_key_u($list,$rp,$item);
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
}
