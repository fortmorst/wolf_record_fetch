<?php

class Mikan extends SOW
{
  use TRS_SOW;
  protected $RP_SP = [
     'RP村用'=>'RP'
    ,'はぴたん王国'=>'HAPI'
    ];
  protected $SKL_HAPI = [
     "食いしん坊"=>[Data::SKL_VILLAGER,Data::TM_VILLAGER]
    ,"お菓子族"=>[Data::SKL_WOLF,Data::TM_WOLF]
    ,"電波ティシエ"=>[Data::SKL_FM_WIS,Data::TM_VILLAGER]
    ,"コウモリ"=>[Data::SKL_PIXY,Data::TM_FAIRY]
    ,"マイフレ"=>[Data::SKL_WHISPER,Data::TM_WOLF]
    ,"ハムスター"=>[Data::SKL_FRY_WIS,Data::TM_FAIRY]
    ,"ひそひそパティシエ"=>[Data::SKL_FRY_WIS,Data::TM_FAIRY]
    ,"お菓子族儲"=>[Data::SKL_WOLF_CURSED,Data::TM_WOLF]
    ];

  protected $DT_RP = [
     ' は消えた。'=>['.+(\(ランダム投票\)|投票した。)(.+) は消えた。',Data::DES_HANGED]
    ,'突然死した。'=>['^( ?)(.+) は、突然死した。',Data::DES_RETIRED]
    ,' が消えた。'=>['()(.+) が消えた。',Data::DES_EATEN]
    ,'後を追った。'=>['^( ?)(.+) は(絆に引きずられるように|哀しみに暮れて) .+ の後を追った。',Data::DES_SUICIDE]
  ];
  protected $DT_HAPI = [
     'しくペロリ！'=>['.+(\(ランダム投票\)|投票した。)(.+) は食いしん.+',Data::DES_HANGED]
    ,'突然死した。'=>['^( ?)(.+) は、突然死した。',Data::DES_RETIRED]
    ,'リ！された。'=>['(.+)朝、 ?(.+) が美味しく.+',Data::DES_EATEN]
    ,'後を追った。'=>['^( ?)(.+) は(絆に引きずられるように|哀しみに暮れて) .+ の後を追った。',Data::DES_SUICIDE]
  ];

  function __construct()
  {
    $cid = 55;
    $url_vil = "http://mecan.nazo.cc/sow/sow.cgi?vid=";
    $url_log = "http://mecan.nazo.cc/sow/sow.cgi?cmd=oldlog";
    parent::__construct($cid,$url_vil,$url_log);
    $this->policy = false;
    $this->RP_LIST = array_merge($this->RP_LIST,$this->RP_SP);
  }
  protected function fetch_key_u($list,$rp,$item)
  {
      $destiny = trim(preg_replace("/\r\n/",'',$item->plaintext));
      $key= mb_substr(trim($item->plaintext),-6,6);

      if(mb_ereg_match('.+ 消えた。\z',$key))
      {
        $key = '  消えた。';
      }

      if(!isset($this->{'DT_'.$rp}[$key]))
      {
        return false;
      }
      else
      {
        $persona = trim(mb_ereg_replace($this->{'DT_'.$rp}[$key][0],'\2',$destiny,'m'));
        $dtid = $this->{'DT_'.$rp}[$key][1];
      }

      $key_u = array_search($persona,$list);
      if($key_u === false)
      {
        return false;
      }
      $this->fetch_dtid($key_u,$dtid,$persona);
      return $key_u;
  }
}
