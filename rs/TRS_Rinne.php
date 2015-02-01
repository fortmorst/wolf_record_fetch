<?php

trait TRS_Rinne
{
  protected $RP_LIST = [
     '人狼物語'=>'SOW'
  ];
  protected $WTM_SOW= [
     'が頂点に立ったのだ！'=>Data::TM_R_VIL
    ,'いたのかもしれない。'=>Data::TM_R_SEER
    ,'雄叫びが響き渡った。'=>Data::TM_R_MED
    ,'なり得なかった……。'=>Data::TM_R_WOLF
  ];
  protected $SKILL = [
     "村人"=>[Data::SKL_R_VILLAGER,Data::TM_R_VIL]
    ,"狩人"=>[Data::SKL_R_GUARD,Data::TM_R_VIL]
    ,"占い師"=>[Data::SKL_R_SEER,Data::TM_R_SEER]
    ,"見習い占い師"=>[Data::SKL_R_SEER_UNSKILL,Data::TM_R_SEER]
    ,"霊能者"=>[Data::SKL_R_MEDIUM,Data::TM_R_MED]
    ,"人狼"=>[Data::SKL_R_WOLF,Data::TM_R_WOLF]
    ,"智狼"=>[Data::SKL_R_WOLF_WISE,Data::TM_R_WOLF]
    ];
  protected $DT_NORMAL = [
     '処刑された。'=>['.+(\(ランダム投票\)|投票できた。)(.+) は村人達の手により処刑された。',Data::DES_HANGED]
    ,'突然死した。'=>['^( ?)(.+) は、突然死した。',Data::DES_RETIRED]
    ,'発見された。'=>['(.+)朝、 ?(.+) が無残.+',Data::DES_EATEN]
  ];
}
