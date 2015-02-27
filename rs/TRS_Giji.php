<?php

trait TRS_Giji
{
  protected $RGL_SP = [
     'ミラーズホロウ' =>Data::RGL_MILL
    ,'ミラーズホロウ（死んだら負け）'=>Data::RGL_MILL
    ,'タブラの人狼（死んだら負け）'=>Data::RGL_DEATH
    ,'Trouble☆Aliens'=>Data::RGL_TA
    ,'深い霧の夜'     =>Data::RGL_MIST
    ,'陰謀に集う胡蝶'=>Data::RGL_PLOT
    ];
  protected $WTM = [
     1=>Data::TM_VILLAGER
    ,2=>Data::TM_WOLF
    ,3=>Data::TM_PIPER
    ,4=>Data::TM_FAIRY
    ,5=>Data::TM_FAIRY
    ,6=>Data::TM_LWOLF
    ,7=>Data::TM_LOVERS
    ,8=>Data::TM_EFB
    ,9=>Data::TM_NONE
    ];
  protected $TEAM = [
     "WIN_HUMAN"=>[Data::TM_VILLAGER,false]
    ,"WIN_WOLF"=>[Data::TM_WOLF,false]
    ,"WIN_PIXI"=>[Data::TM_FAIRY,true]
    ,"WIN_LOVER"=>[Data::TM_LOVERS,false]
    ,"WIN_LONEWOLF"=>[Data::TM_LWOLF,true]
    ,"WIN_GURU"=>[Data::TM_PIPER,true]
    ,"WIN_HATER"=>[Data::TM_EFB,true]
    ,"WIN_EVIL"=>[Data::TM_EVIL,false]
    ,"WIN_DISH"=>[Data::TM_FISH,false]
    ,"WIN_NONE"=>[Data::TM_ONLOOKER,false]
    ];
  protected $RSL = [
     "勝利"=>Data::RSL_WIN
    ,"敗北"=>Data::RSL_LOSE
    ,""=>Data::RSL_INVALID //突然死
    ];
  protected $SKILL = [
     1=>[Data::SKL_VILLAGER,'村人']
    ,2=>[Data::SKL_STIGMA,'聖痕者']
    ,3=>[Data::SKL_FM,'結社員']
    ,4=>[Data::SKL_FM_WIS,'共鳴者']
    ,5=>[Data::SKL_SEER,'占い師']
    ,6=>[Data::SKL_SEER_TM,'信仰占師']
    ,7=>[Data::SKL_SEER_AURA,'気占師']
    ,8=>[Data::SKL_SEER_ROLE,'賢者']
    ,9=>[Data::SKL_GUARD,'守護者']
    ,10=>[Data::SKL_MEDIUM,'霊能者']
    ,11=>[Data::SKL_MEDI_TM,'信仰霊能者']
    ,12=>[Data::SKL_MEDI_ROLE,'導師']
    ,13=>[Data::SKL_MEDI_READ_G,'降霊者']
    ,14=>[Data::SKL_FOLLOWER,'追従者']
    ,15=>[Data::SKL_AGITATOR,'煽動者']
    ,16=>[Data::SKL_HUNTER,'賞金稼']
    ,17=>[Data::SKL_DOG,'人犬']
    ,18=>[Data::SKL_PRINCE,'王子様']
    ,19=>[Data::SKL_LINEAGE,'狼血族']
    ,20=>[Data::SKL_DOCTOR,'医師']
    ,21=>[Data::SKL_CURSED,'呪人']
    ,22=>[Data::SKL_DYING,'預言者']
    ,23=>[Data::SKL_SICK,'病人']
    ,24=>[Data::SKL_ALCHEMIST,'錬金術師']
    ,25=>[Data::SKL_WITCH,'魔女']
    ,26=>[Data::SKL_GIRL,'少女']
    ,27=>[Data::SKL_SG,'生贄']
    ,28=>[Data::SKL_IRON_ONCE_SICK,'長老']
    ,31=>[Data::SKL_JAMMER,'邪魔之民']
    ,32=>[Data::SKL_SNATCH,'宿借之民']
    ,33=>[Data::SKL_LUNA_WIS,'念波之民']
    ,41=>[Data::SKL_LUNATIC,'狂人']
    ,42=>[Data::SKL_FANATIC,'狂信者']
    ,43=>[Data::SKL_MUPPETER,'人形使い']
    ,44=>[Data::SKL_WHISPER,'囁き狂人']
    ,45=>[Data::SKL_HALFWOLF,'半狼']
    ,47=>[Data::SKL_LUNA_MEDI,'魔神官']
    ,48=>[Data::SKL_LUNA_SEER_ROLE,'魔術師']
    ,52=>[Data::SKL_HEADLESS,'首無騎士']
    ,61=>[Data::SKL_WOLF,'人狼']
    ,63=>[Data::SKL_WISEWOLF,'智狼']
    ,64=>[Data::SKL_WOLF_CURSED,'呪狼']
    ,65=>[Data::SKL_WHITEWOLF,'白狼']
    ,66=>[Data::SKL_CHILDWOLF,'仔狼']
    ,67=>[Data::SKL_WOLF_DYING,'衰狼']
    ,68=>[Data::SKL_WOLF_NOTALK,'黙狼']
    ,81=>[Data::SKL_FAIRY,'栗鼠妖精']
    ,86=>[Data::SKL_FRY_MIMIC_W,'擬狼妖精']
    ,88=>[Data::SKL_FRY_DYING,'風花妖精']
    ,89=>[Data::SKL_PIXY,'悪戯妖精']
    ,90=>[Data::SKL_EFB,'邪気悪魔']
    ,91=>[Data::SKL_QP,'恋愛天使']
    ,92=>[Data::SKL_PASSION,'片想い']
    ,93=>[Data::SKL_PUPIL,'弟子']
    ,94=>[Data::SKL_THIEF,'盗賊']
    ,96=>[Data::SKL_LONEWOLF,'一匹狼']
    ,97=>[Data::SKL_PIPER,'笛吹き']
    ,98=>[Data::SKL_FISH,'鱗魚人']
    ,101=>[Data::SKL_BITCH,'遊び人']
    ,999=>[Data::SKL_ONLOOKER,'見物人']
    ];
  protected $GIFT = [
     2=>'喪失'
    ,3=>'感染'
    ,5=>'光の輪'
    ,6=>'魔鏡'
    ,7=>'悪鬼'
    ,8=>'妖精の子'
    ,9=>'半端者'
    ,11=>'決定者'
    ,12=>'夢占師'
    ,13=>'酔払い'
    ];
  protected $BAND = [
     "love"=>"恋人"
    ,"hate"=>"邪気"
    ];
  protected $DESTINY = [
     "live"=>Data::DES_ALIVE
    ,"suddendead"=>Data::DES_RETIRED
    ,"executed"=>Data::DES_HANGED
    ,"victim"=>Data::DES_EATEN
    ,"cursed"=>Data::DES_CURSED
    ,"droop"=>Data::DES_DROOP
    ,"suicide"=>Data::DES_SUICIDE
    ,"feared"=>Data::DES_FEARED
    ,"mob"=>Data::DES_ONLOOKER
    ];
}
