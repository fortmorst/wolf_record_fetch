<?php

class Chitose extends SOW
{
  use TRS_SOW;
  protected $RP_SP = [
     'ようちえん'=>'KIDS'
    ,'メトロポリスβ'=>'METRO'
  ];
  protected $WTM_KIDS= [
     '。めでたしめでたし。'=>Data::TM_VILLAGER
    ,'て去って行きました。'=>Data::TM_WOLF
    ,'が残っていたのです。'=>Data::TM_FAIRY
  ];
  protected $WTM_METRO = [
     '人狼に勝利したのだ！'=>Data::TM_VILLAGER
    ,'めて去って行った……'=>Data::TM_WOLF
    ,'くことはなかった……'=>Data::TM_FAIRY
    ,'すすべがなかった……'=>Data::TM_FAIRY
  ];
  protected $DT_KIDS = [//処刑と突然死は区別するために8文字取得
     'り眠りについた。'=>['.+(\(ランダム投票\)|投票した。)(.+) は子ども達の手により眠りについた。',Data::DES_HANGED]
    ,'然眠りについた。'=>['^( ?)(.+) は、突然眠りについた。',Data::DES_RETIRED]
    ,'発見された。'=>['(.+)朝、 ?(.+) が、むざん.+',Data::DES_EATEN]
    ,'後を追った。'=>['^( ?)(.+) は(絆に引きずられるように|哀しみに暮れて) .+ の後を追った。',Data::DES_SUICIDE]
  ];

  function __construct()
  {
    $data = $this->set_village_data();
    parent::__construct($data['cid'],$data['url_vil'],$data['url_log']);
    $this->RP_LIST = array_merge($this->RP_LIST,$this->RP_SP);
  }
  function set_village_data()
  {
    $this->policy = true;
    $cid = 32;
    $url_vil = "http://chitose-azure.sakura.ne.jp/alf-laylah/sow/sow.cgi?vid=";
    $url_log = "http://chitose-azure.sakura.ne.jp/alf-laylah/sow/sow.cgi?cmd=oldlog";
    return ['cid'=>$cid,'url_vil'=>$url_vil,'url_log'=>$url_log];
  }
  protected function fetch_key_u($list,$rp,$item)
  {
      $destiny = trim(preg_replace("/\r\n/",'',$item->plaintext));
      $key= mb_substr(trim($item->plaintext),-6,6);

      if($key === "りについた。")
      {
        $key= mb_substr(trim($item->plaintext),-8,8);
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
