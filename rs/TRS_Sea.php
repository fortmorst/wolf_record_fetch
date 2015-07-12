<?php

trait TRS_Sea
{
  protected $SKL_SEA = [
    //村
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
    ,"容疑者"=>Data::SKL_REALIZED
    ,"獣化病"=>Data::SKL_COUNTER
    ,"陰陽師"=>Data::SKL_ONMYO
    ,"暗殺者"=>Data::SKL_ASSASSIN
    ,"見習い占い師"=>Data::SKL_SEER_FUZZ
    ,"傾奇者"=>Data::SKL_RANDOM_EATEN
    ,"中身占い師"=>Data::SKL_SEER_ID
    ,"巫者"=>Data::SKL_REALIZE_WOLF
    ,"狼少年"=>Data::SKL_MISTAKE_SEER
    ,"猫又"=>Data::SKL_CAT
    ,"運命の子"=>Data::SKL_RANDOM_TOLD
    ,"星詠み"=>Data::SKL_SEER_BAND
    ,"月酔"=>Data::SKL_MISTAKE_MEDI
    ,"月族"=>Data::SKL_MOON
    ,"解呪師"=>Data::SKL_DISPEL
    ,"交信者"=>Data::SKL_CONTACT
    ,"霊話師"=>Data::SKL_READ_G_C
    ,"審問官"=>Data::SKL_SEER_SKILL
    ,"血呪者"=>Data::SKL_HALF_VMP
    ,"妖血族"=>Data::SKL_IRON_FRY
    ,"鑑定士"=>Data::SKL_SEER_GIFT
    ,"交易商"=>Data::SKL_EXCHANGE_GIFT
    ,"修道者"=>Data::SKL_FM_REALIZE_WOLFRY
    ,"黒薔薇"=>Data::SKL_TEMPT
    ,"恋狼"=>Data::SKL_GRD_READ_W
    ,"隠者"=>Data::SKL_SEER_TWICE
    ,"御子"=>Data::SKL_FM_BAP_5DAY
    ,"見習い騎士"=>Data::SKL_GRD_NOT_STRAIGHT
    ,"パン屋"=>Data::SKL_BAKERY
    ,"占い師の弟子"=>Data::SKL_CHANGE_SEER
    ,"独裁者"=>Data::SKL_DICTATOR
    ,"狐化呪者"=>Data::SKL_CHANGE_FRY
    ,"狼化呪者"=>Data::SKL_CHANGE_WOLF
    ,"潜在意識"=>Data::SKL_ASS_MISTAKE
    ,"空色"=>Data::SKL_AZURE
    //裏切り
    ,"邪魔之民"=>Data::SKL_JAMMER
    ,"宿借之民"=>Data::SKL_SNATCH
    ,"念波之民"=>Data::SKL_LUNA_WIS
    ,"座敷童"=>Data::SKL_LUNA_MIMIC_FM
    ,"囮人形"=>Data::SKL_LUNA_BLACK
    ,"狂人"=>Data::SKL_LUNATIC
    ,"狂信者"=>Data::SKL_FANATIC
    ,"人形使い"=>Data::SKL_MUPPETER
    ,"囁き狂人"=>Data::SKL_WHISPER
    ,"半狼"=>Data::SKL_HALFWOLF
    ,"魔神官"=>Data::SKL_LUNA_MEDI
    ,"魔術師"=>Data::SKL_LUNA_SEER_ROLE
    ,"悟られ狂人"=>Data::SKL_REALIZED
    ,"感応狂人"=>Data::SKL_LUNA_READ_W
    ,"瘴気狂人"=>Data::SKL_NONE
    ,"誘惑者"=>Data::SKL_LUNA_TEMPT_SEA
    ,"狂科学者"=>Data::SKL_MAD
    ,"呪術師"=>Data::SKL_LUNA_ADD_FRY
    ,"月従者"=>Data::SKL_FANATIC_MOON
    ,"邪教徒"=>Data::SKL_LUNA_WIS_TEMPT
    ,"狂化狂人"=>Data::SKL_LUNA_ASS_ONCE
    ,"サイコパス"=>Data::SKL_PSYCHOPATH
    ,"月下美人"=>Data::SKL_LUNA_BAND
    ,"死操術師"=>Data::SKL_LUNA_WITCH_2DIE
    //狼
    ,"首無騎士"=>Data::SKL_HEADLESS
    ,"人狼"=>Data::SKL_WOLF
    ,"智狼"=>Data::SKL_WISEWOLF
    ,"呪狼"=>Data::SKL_WOLF_CURSED
    ,"白狼"=>Data::SKL_WHITEWOLF
    ,"仔狼"=>Data::SKL_CHILDWOLF
    ,"衰狼"=>Data::SKL_WOLF_DYING
    ,"黙狼"=>Data::SKL_WOLF_NOTALK
    ,"賢狼"=>Data::SKL_WOLF_MEDI_ROLE
    ,"霊狼"=>Data::SKL_WOLF_MR_READ_G
    ,"大狼"=>Data::SKL_WWOLF_BLACK_G
    ,"群狼"=>Data::SKL_WOLF_PRINCE_MANY
    ,"月狼"=>Data::SKL_WOLF_MOON
    ,"夢魔"=>Data::SKL_WOLF_TMP_WOLF
    ,"怨狼"=>Data::SKL_WOLF_DELAY_2
    ,"魂狼"=>Data::SKL_WOLF_READ_C
    ,"妖狼"=>Data::SKL_WOLF_READ_D
    ,"桜狼"=>Data::SKL_WOLF_CHERRY
    ,"崇狼"=>Data::SKL_WOLF_PRINCE
    ,"霧狼"=>Data::SKL_WOLF_FOG
    //妖魔
    ,"ハムスター人間"=>Data::SKL_FAIRY
    ,"九尾"=>Data::SKL_FRY_ASS_COUNTER
    ,"呪狐"=>Data::SKL_FRY_CURSED
    ,"半妖"=>Data::SKL_FRY_DYING_HALF
    ,"仙狐"=>Data::SKL_FRY_MEDI_ROLE
    ,"擬狼妖精"=>Data::SKL_FRY_MIMIC_W
    ,"風花妖精"=>Data::SKL_FRY_DYING
    ,"悪戯妖精"=>Data::SKL_PIXY
    ,"サトリ"=>Data::SKL_FRY_SEER_ROLE
    ,"瘴狐"=>Data::SKL_FRY_SEAL
    ,"惑狐"=>Data::SKL_FRY_TEMPT
    ,"夜叉"=>Data::SKL_YASHA
    ,'犬神'=>Data::SKL_FRY_CAT
    ,"コウモリ人間"=>Data::SKL_FRY_WIS
    ,'鏡狐'=>Data::SKL_FRY_ADD_FRY
    ,'月夜霊'=>Data::SKL_FRY_MOON
    ,'夜夢'=>Data::SKL_FRY_CHANGELING
    ,'天邪鬼'=>Data::SKL_FRY_PIXY_RANDOM
    ,'憑狐'=>Data::SKL_FRY_MISTAKE
    ,'天狐'=>Data::SKL_FRY_VOTE_TWICE
    ,'夕霧'=>Data::SKL_FRY_FOG
    ,'天眼'=>Data::SKL_FRY_FNT_READ_ALL
    //一匹狼
    ,"一匹狼"=>Data::SKL_LONEWOLF
    //笛吹き
    ,"笛吹き"=>Data::SKL_PIPER
    //邪気陣営
    ,"邪気悪魔"=>Data::SKL_EFB
    //恋陣営
    ,"恋愛天使"=>Data::SKL_QP
    ,"片想い"=>Data::SKL_PASSION
    ,"遊び人"=>Data::SKL_BITCH
    ,"純恋者"=>Data::SKL_QP_SELF
    //吸血鬼
    ,"吸血鬼"=>Data::SKL_VAMPIRE_SEA
    ,"眷属"=>Data::SKL_SERVANT
    ,"偽神官"=>Data::SKL_SRV_SEER_ROLE
    ,"殺戮者"=>Data::SKL_SRV_ASS
    ,"純血種"=>Data::SKL_VMP_PURE
    ,"覚醒種"=>Data::SKL_VMP_HALF
    //深海団
    ,"コレクター"=>Data::SKL_SEA_WILD
    ,"ビブロフィリア"=>Data::SKL_SEA_NONE
    ,"ギャンブラー"=>Data::SKL_SEA_EXPECT_ALIVE
    ,"グリムリーパー"=>Data::SKL_SEA_EXPECT_DEAD
    ,"ヴァルキュリア"=>Data::SKL_SEA_EXPECT_WIN
    ,"カロン"=>Data::SKL_SEA_WITCH
    ,"シビュラ"=>Data::SKL_SEA_SIBYL
    ,"セイレーン"=>Data::SKL_SEA_SIREN
    ,"工作員"=>Data::SKL_SEA
    //妖魔族
    ,"古妖魔"=>Data::SKL_DEVIL
    ,"響鳴種"=>Data::SKL_DVL_MIMIC_FM
    ,"妖姫"=>Data::SKL_DVL_TEMPT
    ,"闇妖魔"=>Data::SKL_DVL_READ_W
    ,"悟心種"=>Data::SKL_DVL_FANATIC
    ,"妖魔導師"=>Data::SKL_DVL_GRD_CURSE
    ,"変化種"=>Data::SKL_DVL_AZURE
    ,"樹霊妖魔"=>Data::SKL_DVL_CURSED
    //照坊主
    ,"悔罪人"=>Data::SKL_TERU
    //据え膳
    ,"鱗魚人"=>Data::SKL_FISH
    //陣営無し
    ,"迷える子羊"=>Data::SKL_SHEEP
    ,"ジョーカー"=>Data::SKL_JOKER
    ];
  protected $TM_SEA = [
     "村人"=>[Data::TM_VILLAGER,false]
    ,"人狼"=>[Data::TM_WOLF,false]
    ,"妖精"=>[Data::TM_FAIRY,true]
    ,"恋人"=>[Data::TM_LOVERS,false]
    ,"一匹"=>[Data::TM_LWOLF,true]
    ,"笛吹"=>[Data::TM_PIPER,true]
    ,"邪気"=>[Data::TM_EFB,true]
    ,"裏切"=>[Data::TM_EVIL,false]
    ,"据え"=>[Data::TM_FISH,false]
    ,"--"  =>[Data::TM_NONE,false] //2d突然死の片想い
    ,"勝利"=>[Data::TM_NONE,false]
    ,"贖罪"=>[Data::TM_TERU,false]
    ,"吸血"=>[Data::TM_VAMPIRE,true]
    ,"深海"=>[Data::TM_SEA,true]
    ,"妖魔"=>[Data::TM_DEVIL,true]
    ];
  public $WTM_SEA = [
     "の人物が消え失せた時、其処"=>Data::TM_NONE
    ,"の人物が消え失せた時、そこ"=>Data::TM_NONE
    ,"の人狼を退治した……。人狼"=>Data::TM_VILLAGER
    ,"らかな光が降り注ぐ。全ての"=>Data::TM_VILLAGER
    ,"の勝利やわらかな光が降り注"=>Data::TM_VILLAGER
    ,"全ての希望を染めつくした。"=>Data::TM_WOLF
    ,"の勝利闇が全ての希望を染め"=>Data::TM_WOLF
    ,"達は自らの過ちに気付いた。"=>Data::TM_WOLF
    ,"の人狼を退治した……。だが"=>Data::TM_FAIRY
    ,"の勝利全ての人狼を退治した"=>Data::TM_FAIRY
    ,"時、人狼は勝利を確信し、そ"=>Data::TM_FAIRY
    ,"の勝利その時、人狼は勝利を"=>Data::TM_FAIRY
    ,"も、人狼も、妖孤でさえも、"=>Data::TM_LOVERS//誤字
    ,"達の勝利村人も、人狼も、妖"=>Data::TM_LOVERS
    ,"も、人狼も、妖精でさえも、"=>Data::TM_LOVERS
    ,"達は、そして人狼達も自らの"=>Data::TM_LWOLF
    ,"狼の勝利村人達は、そして人"=>Data::TM_LWOLF
    ,"達は気付いてしまった。もう"=>Data::TM_PIPER
    ,"き勝利村人達は気付いてしま"=>Data::TM_PIPER
    ,"はたった独りだけを選んだ。"=>Data::TM_EFB
    ,"の勝利運命はたった独りだけ"=>Data::TM_EFB
    ];
  protected   $WTM_RP = [
     "の人物が消え失せた時、其処"=>Data::TM_NONE
    ,"の魔が消滅し舞台はついに終"=>Data::TM_VILLAGER
    ,"侵食は進行しついに舞台の全"=>Data::TM_WOLF
    ,"して全ての魔は滅された。た"=>Data::TM_FAIRY
    ,"して魔は舞台の全てを覆い尽"=>Data::TM_FAIRY
    ,"な困難の果てに勝ち残ったの"=>Data::TM_LOVERS
    ,"して舞台は終焉に近づく。誰"=>Data::TM_LWOLF
    ,"の静寂が舞台を包む。立場の"=>Data::TM_PIPER
    ,"はたった独りだけを選んだ。"=>Data::TM_EFB
  ];
}
