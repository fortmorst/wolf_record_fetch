<?php

trait TRS_Heaven
{
  protected $SKILL =
    [
      "村人"    =>[Data::SKL_VILLAGER,Data::TM_VILLAGER]
     ,"占い師"  =>[Data::SKL_SEER,Data::TM_VILLAGER]
     ,"中身占い師"=>[Data::SKL_SEER_ID,Data::TM_VILLAGER]
     ,"霊能者"  =>[Data::SKL_MEDIUM,Data::TM_VILLAGER]
     ,"狩人"    =>[Data::SKL_GUARD,Data::TM_VILLAGER]
     ,"共有者"  =>[Data::SKL_FM,Data::TM_VILLAGER]
     ,"聖痕者"  =>[Data::SKL_STIGMA,Data::TM_VILLAGER]
     ,"烙印者"  =>[Data::SKL_BRAND,Data::TM_VILLAGER]
     ,"風来狩人"=>[Data::SKL_GRD_NOT_TWICE,Data::TM_VILLAGER]
     ,"共鳴者"  =>[Data::SKL_FM_WIS,Data::TM_VILLAGER]
     ,"猫又"    =>[Data::SKL_CAT,Data::TM_VILLAGER]
     ,"人狼"    =>[Data::SKL_WOLF,Data::TM_WOLF]
     ,"狂人"    =>[Data::SKL_LUNATIC,Data::TM_WOLF]
     ,"Ｃ国狂人"=>[Data::SKL_WHISPER,Data::TM_WOLF]
     ,"狂信者"  =>[Data::SKL_FANATIC,Data::TM_WOLF]
     ,"邪魔狂人"=>[Data::SKL_JAMMER_TO_SKL,Data::TM_WOLF]
     ,"妖魔"    =>[Data::SKL_FAIRY,Data::TM_FAIRY]
     ,"キューピッド"=>[Data::SKL_QP,Data::TM_LOVERS]
     ,"求愛者"  =>[Data::SKL_QP_SELF,Data::TM_LOVERS]
    ]; 
  protected $TEAM =
    [
        "村人"=>Data::TM_VILLAGER
       ,"人狼"=>Data::TM_WOLF
       ,"妖狐"=>Data::TM_FAIRY
       ,"裏切"=>Data::TM_LOVERS
    ]; 
  protected $DESTINY = [
     '刑されました。'=>['投票の結果、(.+) が処刑されました。',Data::DES_HANGED]
    ,'然死しました。'=>['^(.+) が突然死しました。',Data::DES_RETIRED]
    ,'見されました。'=>['^(.+) が無残な姿で発見されました。',Data::DES_EATEN]
    ,'を追いました。'=>['^(.+) は悲しみに暮れて .+ の後を追いました。',Data::DES_SUICIDE]
  ];
}
