<?php

trait TRS_Giji_Old
{
  public $TEAM = [
     "村人"=>[Data::TM_VILLAGER,false]
    ,"人狼"=>[Data::TM_WOLF,false]
    ,"妖精"=>[Data::TM_FAIRY,true]
    ,"恋人"=>[Data::TM_LOVERS,false]
    ,"一匹"=>[Data::TM_LWOLF,true]
    ,"笛吹"=>[Data::TM_PIPER,true]
    ,"邪気"=>[Data::TM_EFB,true]
    ,"裏切"=>[Data::TM_EVIL,false]
    ,"据え"=>[Data::TM_FISH,false]
    ,"テル"=>[Data::TM_TERU,false] //三日月のオールスター
    ,"--"  =>[Data::TM_NONE,false] //2d突然死の片想い
    ,"勝利"=>[Data::TM_NONE,false]
    ];
  public $SKILL = [
     "村人"=>Data::SKL_VILLAGER
    ,"聖痕者"=>Data::SKL_STIGMA
    ,"結社員"=>Data::SKL_FM
    ,"共鳴者"=>Data::SKL_FM_WIS
    ,"占い師"=>Data::SKL_SEER
    ,"信仰占師"=>Data::SKL_SEER_TM
    ,"気占師"=>Data::SKL_SEER_AURA
    ,"賢者"=>Data::SKL_SEER_ROLE
    ,"守護者"=>Data::SKL_GUARD
    ,"霊能者"=>Data::SKL_MEDIUM
    ,"信仰霊能者"=>Data::SKL_MEDI_TM
    ,"導師"=>Data::SKL_MEDI_ROLE
    ,"降霊者"=>Data::SKL_MEDI_READ_G
    ,"追従者"=>Data::SKL_FOLLOWER
    ,"煽動者"=>Data::SKL_AGITATOR
    ,"賞金稼"=>Data::SKL_HUNTER
    ,"人犬"=>Data::SKL_DOG
    ,"王子様"=>Data::SKL_PRINCE
    ,"狼血族"=>Data::SKL_LINEAGE
    ,"医師"=>Data::SKL_DOCTOR
    ,"呪人"=>Data::SKL_CURSED
    ,"預言者"=>Data::SKL_DYING
    ,"病人"=>Data::SKL_SICK
    ,"錬金術師"=>Data::SKL_ALCHEMIST
    ,"魔女"=>Data::SKL_WITCH
    ,"少女"=>Data::SKL_GIRL
    ,"生贄"=>Data::SKL_SG
    ,"長老"=>Data::SKL_IRON_ONCE_SICK
    ,"邪魔之民"=>Data::SKL_JAMMER
    ,"宿借之民"=>Data::SKL_SNATCH
    ,"念波之民"=>Data::SKL_LUNA_WIS
    ,"狂人"=>Data::SKL_LUNATIC
    ,"狂信者"=>Data::SKL_FANATIC
    ,"人形使い"=>Data::SKL_MUPPETER
    ,"囁き狂人"=>Data::SKL_WHISPER
    ,"半狼"=>Data::SKL_HALFWOLF
    ,"魔神官"=>Data::SKL_LUNA_MEDI
    ,"魔術師"=>Data::SKL_LUNA_SEER_ROLE
    ,"首無騎士"=>Data::SKL_HEADLESS
    ,"人狼"=>Data::SKL_WOLF
    ,"智狼"=>Data::SKL_WISEWOLF
    ,"呪狼"=>Data::SKL_WOLF_CURSED
    ,"白狼"=>Data::SKL_WHITEWOLF
    ,"仔狼"=>Data::SKL_CHILDWOLF
    ,"衰狼"=>Data::SKL_WOLF_DYING
    ,"黙狼"=>Data::SKL_WOLF_NOTALK
    ,"栗鼠妖精"=>Data::SKL_FAIRY
    ,"擬狼妖精"=>Data::SKL_FRY_MIMIC_W
    ,"風花妖精"=>Data::SKL_FRY_DYING
    ,"悪戯妖精"=>Data::SKL_PIXY
    ,"邪気悪魔"=>Data::SKL_EFB
    ,"恋愛天使"=>Data::SKL_QP
    ,"片想い"=>Data::SKL_PASSION
    ,"一匹狼"=>Data::SKL_LONEWOLF
    ,"笛吹き"=>Data::SKL_PIPER
    ,"鱗魚人"=>Data::SKL_FISH
    ,"遊び人"=>Data::SKL_BITCH
    //人狼物語用言い換え
    ,"共有者"=>Data::SKL_FM
    ,"狩人"=>Data::SKL_GUARD
    ,"コウモリ人間"=>Data::SKL_LUNA_WIS
    ,"Ｃ国狂人"=>Data::SKL_WHISPER
    ,"首なし騎士"=>Data::SKL_HEADLESS
    ,"ハムスター人間"=>Data::SKL_FAIRY
    ,"ピクシー"=>Data::SKL_PIXY
    ,"キューピッド"=>Data::SKL_QP
    //三日月のオールスター
    ,"テルテル"=>Data::SKL_TERU
    ,"求愛者"=>Data::SKL_QP_SELF
    ];
  public $RGL_SP = [
     'ミラーズホロウ' =>Data::RGL_MILL
    ,'死んだら負け'   =>Data::RGL_DEATH
    ,'死んだら負け(ミラーズホロウ)'=>Data::RGL_MILL
    ,'Trouble☆Aliens'=>Data::RGL_TA
    ,'深い霧の夜'     =>Data::RGL_MIST
    ];
  //Orbital☆Starでは村人→乗客なので最初の二文字を削る 13文字
  public $WTM = [
     "の人物が消え失せた時、其処"=>Data::TM_NONE
    ,"の人狼を退治した……。人狼"=>Data::TM_VILLAGER
    ,"が去り、まぶしい光が降り注"=>Data::TM_VILLAGER
    ,"達は自らの過ちに気付いた。"=>Data::TM_WOLF
    ,"村を覆い、村人達は自らの過"=>Data::TM_WOLF
    ,"人狼に抵抗できるほど村人は"=>Data::TM_WOLF
    ,"の人狼を退治した……。だが"=>Data::TM_FAIRY
    ,"時、人狼は勝利を確信し、そ"=>Data::TM_FAIRY
    ,"じ風が舞い、村人達は凱歌を"=>Data::TM_FAIRY
    ,"じ風が舞い、村中に人狼達の"=>Data::TM_FAIRY
    ,"も、人狼も、妖精でさえも、"=>Data::TM_LOVERS
    ,"達は、そして人狼達も自らの"=>Data::TM_LWOLF
    ,"達は気付いてしまった。もう"=>Data::TM_PIPER
    ,"はたった独りだけを選んだ。"=>Data::TM_EFB
    ];
  public $RSL = [
     "勝利"=>Data::RSL_WIN
    ,"敗北"=>Data::RSL_LOSE
    ,""=>Data::RSL_INVALID //突然死
    ];
  public $DESTINY = [
     "生存者"=>Data::DES_ALIVE
    ,"突然死"=>Data::DES_RETIRED
    ,"処刑死"=>Data::DES_HANGED
    ,"襲撃死"=>Data::DES_EATEN
    ,"呪詛死"=>Data::DES_CURSED
    ,"衰退死"=>Data::DES_DROOP
    ,"後追死"=>Data::DES_SUICIDE
    ,"恐怖死"=>Data::DES_FEARED
    ,"飢餓死"=>Data::DES_STARVE //三日月
    ];
  public $WTM_MILLERS = [
     "の人物が消え失せた時、其処"=>Data::TM_NONE
    ,"の人狼を退治した……。人狼"=>Data::TM_VILLAGER
    ,"人狼に抵抗できるほど村人は"=>Data::TM_WOLF
    ,"の人狼を退治した……。だが"=>Data::TM_FAIRY
    ,"時、人狼は勝利を確信し、そ"=>Data::TM_FAIRY
    ,"も、人狼も、妖精でさえも、"=>Data::TM_LOVERS
    ,"達は、そして人狼達も自らの"=>Data::TM_LWOLF
    ,"達は気付いてしまった。もう"=>Data::TM_PIPER
    ,"はたった独りだけを選んだ。"=>Data::TM_EFB
    ];
  public $SKL_MILLERS = [//ミラーズホロウ
     "村人"=>Data::SKL_VILLAGER
    ,"預言者"=>Data::SKL_SEER_ROLE
    ,"守護者"=>Data::SKL_GUARD
    ,"狩人"=>Data::SKL_HUNTER
    ,"魔女"=>Data::SKL_WITCH
    ,"少女"=>Data::SKL_GIRL
    ,"スケープゴート"=>Data::SKL_SG
    ,"長老"=>Data::SKL_IRON_ONCE_SICK
    ,"人狼"=>Data::SKL_WISEWOLF
    ,"キューピッド"=>Data::SKL_QP
    ];
}
