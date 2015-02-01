<?php

class Data
{
  //陣営 team
  const TM_RP        =  0; //勝敗なし
  const TM_VILLAGER  =  1; //村人陣営
  const TM_WOLF      =  2; //人狼陣営
  const TM_FAIRY     =  3; //妖魔陣営
  const TM_LOVERS    =  4; //恋人陣営
  const TM_LWOLF     =  6; //一匹狼陣営
  const TM_PIPER     =  7; //笛吹き陣営
  const TM_EFB       =  8; //邪気陣営
  const TM_VAMPIRE   = 15; //吸血鬼陣営
  const TM_SEA       = 17; //深海団
  const TM_BMOON     = 11; //蒼月教会
  //追加勝利
  const TM_EVIL      =  9; //裏切りの陣営
  const TM_FISH      = 10; //据え膳
  const TM_TERU      = 13; //照坊主
  const TM_SLAVE     = 14; //奴隷陣営
  const TM_YANDERE   = 16; //悪霊陣営
  const TM_MARTYR    = 22; //殉教者陣営
  //その他
  const TM_NONE      = 99; //陣営なし
  const TM_ONLOOKER  = 98; //見物人  
  //輪廻
  const TM_R_VIL     = 18; //村人
  const TM_R_SEER    = 19; //占い師
  const TM_R_MED     = 20; //霊能者
  const TM_R_WOLF    = 21; //人狼
    

  //編成 regulation
  const RGL_C    =  1;      //C編成
  const RGL_F    =  2;      //F編成
  const RGL_G    =  3;      //G編成
  const RGL_E    =  6;      //妖魔入り
  const RGL_S_1  = 28;      //少人数狼1
  const RGL_S_2  =  4;      //少人数狼2
  const RGL_S_3  =  5;      //少人数狼3
  const RGL_S_C2 =  7;      //少人数狼2C
  const RGL_S_C3 =  8;      //少人数狼3C
  const RGL_C_ST = 25;      //聖痕入りC
  const RGL_G_ST = 17;      //聖痕入りG
  const RGL_TES1 = 12;      //試験壱
  const RGL_TES2 = 13;      //試験弐
  const RGL_TES3 = 31;      //試験参
  const RGL_LOVE = 15;      //恋人入り
  const RGL_HERO = 22;      //占い師なし
  const RGL_SECRET=27;      //秘話村
  const RGL_MILL = 18;      //ミラーズホロウ
  const RGL_DEATH= 19;      //死んだら負け
  const RGL_LOSE = 32;      //負けたら勝ち
  const RGL_TA   = 20;      //Trouble☆Aliens
  const RGL_MIST = 14;      //深い霧の夜
  const RGL_PLOT = 29;      //陰謀に集う胡蝶
  const RGL_RINNE= 33;      //輪廻
  const RGL_ETC  = 24;      //特殊


  //結果 result
  const RSL_WIN      = 1;
  const RSL_LOSE     = 2;
  const RSL_JOIN     = 3;   //参加(非ガチ村)
  const RSL_INVALID  = 4;   //無効(一部の国での突然死)
  const RSL_ONLOOKER = 5;   //見物


  //結末 destiny
  const DES_ALIVE   = 1;  //生存
  const DES_RETIRED = 2;  //突然死
  const DES_HANGED  = 3;  //処刑死
  const DES_EATEN   = 4;  //襲撃死
  const DES_CURSED  = 5;  //呪詛死
  const DES_DROOP   = 7;  //衰退死
  const DES_SUICIDE = 8;  //後追死
  const DES_FEARED  = 9;  //恐怖死 
  const DES_MARTYR  = 11; //殉教
  const DES_ONLOOKER= 10; //見物
  

  //能力 skill
  //村人陣営
  const SKL_VILLAGER        =  1; //村人
  const SKL_SEER            =  2; //占い師
  const SKL_SEER_TM         = 35; //信仰占師
  const SKL_SEER_TM_FUZZ    = 84; //審判者 おおまかな陣営占い
  const SKL_SEER_AURA       = 36; //気占師
  const SKL_SEER_ROLE       = 52; //賢者
  const SKL_SEER_BAND       =115; //運命読み 絆の有無と種類を占う
  const SKL_SEER_ID         = 85; //中身占い師(魂魄師)
  const SKL_SEER_ONCE       = 66; //夢占師
  const SKL_SEER_FUZZ       = 96; //見習い占い師 たまに結果が見えない占師
  const SKL_SEER_ROLETM     =184; //天啓者 一日遅れで役職と陣営を占う
  const SKL_MEDIUM          =  3; //霊能者
  const SKL_MEDI_TM         = 67; //信仰霊能者
  const SKL_MEDI_ROLE       =133; //導師
  const SKL_MEDI_ROLETM     =200; //骸糾問 役職と陣営の霊能判定
  const SKL_MEDI_READ_G     = 18; //降霊者
  const SKL_MEDI_ID         =188; //霊媒師 中身霊能者
  const SKL_WILD            =109; //野生児 残狼人数が分かる
  const SKL_GUARD           =  4; //狩人
  const SKL_GRD_BLACK       = 54; //守護獣
  const SKL_GRD_ALCH        = 79; //狙撃手
  const SKL_GRD_G           =112; //守護霊 死んでから護衛できる
  const SKL_GRD_TWICE       =201; //二兎追 二人護衛できるが60%の確率で失敗
  const SKL_FM              =  5; //共有者
  const SKL_FM_WIS          =  8; //共鳴者
  const SKL_STIGMA          =  9; //聖痕者
  const SKL_FOLLOWER        = 19; //追従者
  const SKL_LEONARD         =187; //決定者
  const SKL_AGITATOR        = 20; //扇動者
  const SKL_HUNTER          = 21; //賞金稼
  const SKL_DOG             = 22; //人犬
  const SKL_PRINCE          = 23; //王子様
  const SKL_LINEAGE         = 24; //狼血族
  const SKL_SUSPECT         = 92; //容疑者 占判定は黒、霊判定は白、自覚あり
  const SKL_SUS_LINEAGE     =152; //狼気纏 霊判定では白が出る狼血族
  const SKL_DOCTOR          = 25; //医師 とらエリルールの感染を解除
  const SKL_DISPEL          =190; //解呪師 各種呪いと誘惑状態の解除
  const SKL_CURSED          = 26; //呪人
  const SKL_DYING           = 27; //預言者
  const SKL_SICK            = 28; //病人
  const SKL_ALCHEMIST       = 29; //錬金術師
  const SKL_SG              = 32; //生贄
  const SKL_NOTARY          = 68; //公証人
  const SKL_BAPTIST         = 78; //洗礼者
  const SKL_CONTACT         = 87; //交信者 初日に一人と交信ログで会話可能にする
  const SKL_NOBLE           = 90; //貴族 襲撃を受けると身代わりに奴隷全員が死ぬ
  const SKL_WITCH           = 30; //魔女
  const SKL_COUNTER         = 93; //獣化病 常時発動錬金術師
  const SKL_ONMYO           = 94; //陰陽師 妖魔系か暗殺者を呪殺
  const SKL_DEMO            =111; //陽動者 襲撃の身代わりになれる
  const SKL_CAT             =154; //猫又 錬金術師+処刑されるとランダム道連れ
  const SKL_ASSASSIN        = 95; //暗殺者 襲撃行使、占われると溶ける
  const SKL_ASS_CRESCENT    =114; //暗殺者(三日月) 襲撃行使、狼や妖魔も殺害可
  const SKL_READ_W          = 31; //少女
  const SKL_READ_G          =113; //霊感少年 霊話師(深海) 墓ログ閲覧
  const SKL_IRON            =110; //鉄人 襲撃を受けない
  const SKL_IRON_ONCE_SICK  = 33; //長老
  const SKL_MISTAKE_GRD     = 69; //闇狩人
  const SKL_MISTAKE_FRY     =116; //狐好き 思い込み狐
  const SKL_MISTAKE_LOVE    =117; //妄想家 思い込み求愛者
  const SKL_MISTAKE_SEER    =138; //狼少年 でたらめな占結果が出る。呪殺は可能
  const SKL_MISTAKE_MEDI    =179; //月酔 でたらめな霊結果が出る
  const SKL_MOON            =189; //月族 被占被護衛で行使者を裏切り陣営にする
  const SKL_RANDOM_EATEN    = 97; //傾奇者 被襲撃でランダムに役職変化
  const SKL_RANDOM_DEAD     =118; //転生者 死後三日後ランダムな役職で復活
  const SKL_RANDOM_TOLD     =155; //運命の子 被占いでランダムに役職変化
  //裏切りの陣営
  const SKL_LUNATIC         =  6; //狂人
  const SKL_WHISPER         = 12; //囁き狂人
  const SKL_LUNA_WIS        = 13; //叫迷狂人
  const SKL_LUNA_WIS_FRY    =202; //深林之民 叫迷と蝙蝠発言可
  const SKL_LUNA_WIS_TEMPT  = 88; //誘惑者 囁き狂人+初日に一人を隷従者(恩恵)にする
  const SKL_LUNA_WIS_LINEAGE=159; //胡蝶 襲撃されると囁き狂人に変化
  const SKL_JAMMER          = 34; //邪魔之民
  const SKL_FANATIC         = 11; //狂信者
  const SKL_FANATIC_MOON    =191; //月従者 月系役職を把握する
  const SKL_MUPPETER        = 37; //人形使い
  const SKL_HALFWOLF        = 38; //半狼
  const SKL_SNATCH          = 60; //宿借之民
  const SKL_SEAL            = 71; //封印狂人
  const SKL_SLEEPER         = 89; //睡狼 役職自覚がない。被襲撃で人狼になる
  const SKL_REALIZED        = 99; //悟られ狂人 逆狂信者
  const SKL_DAZZLE          =121; //幻惑者 絆の内容を逆にする
  const SKL_PERVERT         =122; //倒錯者 占霊判定を逆にする
  const SKL_LUNA_BLACK      = 98; //囮人形 占われると黒判定が出る
  const SKL_LUNA_SEER       = 55; //狂神官 占い師
  const SKL_LUNA_SEER_ROLE  = 40; //魔術師
  const SKL_LUNA_SEER_MELON = 72; //辻占狂人 占い師 呪殺能力なし
  const SKL_LUNA_SEER_ID    = 86; //呪魂者 中身占い師 占われると呪殺できる
  const SKL_LUNA_SEER_ROLETM=185; //祈祷師 一日遅れで役職と陣営を占う
  const SKL_LUNA_MEDI       = 39; //魔神官
  const SKL_LUNA_READ_W     = 62; //感応狂人 赤ログを覗けるが発言不可
  const SKL_LUNA_MIMIC_FM   = 63; //狂鳴者 共鳴ログに紛れ込む
  const SKL_LUNA_TEMPT      = 81; //冒涜者 狂信者+初日に一人を背信者にする
  const SKL_LUNA_TEMPTED    = 82; //背信者 背信者、冒涜者同士で会話可能
  const SKL_LUNA_TEMPT_SEA  =156; //誘惑者 初日に一人を半端者にする
  const SKL_LUNA_SICK_EXE   =119; //怨嗟狂人 吊られると能力者を無能にする
  const SKL_LUNA_EXE_G      =120; //騒霊 墓下で投票できる
  const SKL_LUNA_ADD_FRY    =163; //呪術師 一人に襲撃無効・被呪殺能力を付与
  const SKL_NONE            =139; //瘴気狂人 2Dに一人を無能状態にする
  const SKL_MAD             =153; //狂学者 2Dに一人を人狼にする
  const SKL_LUNA_WITCH      =183; //南瓜提灯 毒殺+蘇生薬
  //人狼陣営
  const SKL_WOLF            =  7; //人狼
  const SKL_HEADLESS        = 41; //首無騎士
  const SKL_WISEWOLF        = 42; //智狼
  const SKL_WHITEWOLF       = 44; //白狼
  const SKL_WWOLF_BLACK_G   =126; //擬狼 大狼 死ぬと真判定になる白狼
  const SKL_CHILDWOLF       = 45; //仔狼
  const SKL_RECKLESS        =127; //蛮狼 自分を犠牲に護衛貫通襲撃が可能
  const SKL_SLEEPER_BLACK   =124; //忘狼 占いでも覚醒する睡狼
  const SKL_WOLF_DYING      = 46; //衰狼
  const SKL_WOLF_NOTALK     = 47; //黙狼
  const SKL_WOLF_CURSED     = 43; //呪狼
  const SKL_WOLF_SNATCH     = 70; //憑狼
  const SKL_WOLF_TEMPT      = 80; //瘴狼 初日に一人を隷従者(恩恵)にする
  const SKL_WOLF_MEDI_ROLE  =100; //賢狼 処刑者の役職が分かる
  const SKL_WOLF_MR_READ_G  =101; //霊狼 上記+墓下ログが読める
  const SKL_WOLF_HUNGRY     =123; //餓狼 二日連続襲撃できないと死ぬ
  const SKL_WOLF_FAN        =125; //嗅狼 半狼や狼血族が分かる
  const SKL_WOLF_DEPEND     =160; //従狼 人カウントも狼カウントもしない
  const SKL_WOLF_ELDER      =151; //古狼 襲撃耐性を貫通するが、6割の確率で襲撃失敗する
  const SKL_WOLF_DELAY      =165; //蠱狼 襲撃した一日後に対象が死ぬ。襲撃返り討ち耐性を無効化
  const SKL_WOLF_PRINCE     =180; //群狼 LWにならない限り処刑されない
  const SKL_WOLF_MOON       =192; //月狼 被占被護衛で行使者を裏切り陣営にする
  const SKL_WOLF_MMC_FM_WIS =199; //愚狼 共鳴ログに紛れ込む
  const SKL_WOLF_NOBAND     =203; //鈍狼 あらゆる絆がつかない
  //妖魔陣営
  const SKL_FAIRY           = 14; //妖魔
  const SKL_PIXY            = 50; //悪戯妖精
  const SKL_VAMPIRE         = 74; //吸血鬼 瓜科
  const SKL_YASHA           =162; //夜叉 50%の確率でランダムに襲撃
  const SKL_FRY_MIMIC_W     = 48; //擬狼妖精
  const SKL_FRY_DYING       = 49; //風花妖精
  const SKL_FRY_DYING_HALF  =104; //半妖 風花妖精+襲撃を受けると仙狐になる
  const SKL_FRY_WIS         = 61; //蝙蝠人間
  const SKL_FRY_JAMMER      = 64; //邪魔妖精
  const SKL_FRY_SNATCH      = 65; //宿借妖精
  const SKL_FRY_READ_W      = 75; //夜兎 赤窓閲覧
  const SKL_FRY_READ_A      =173; //妖兎 全秘密ログ閲覧
  const SKL_FRY_READ_A_DOG  =174; //月兎 妖兎+処刑突然死以外の死因を一度だけ防ぐ
  const SKL_FRY_READ_ALL_P  =130; //九尾 三日月 毒殺+全秘密ログ閲覧
  const SKL_FRY_SEER_BAND   = 83; //夢魔 絆の有無を占う(呪殺不可)
  const SKL_FRY_SEER_ROLE   =140; //サトリ 役職占いができる妖魔
  const SKL_FRY_ASS_COUNTER =102; //九尾 呪殺されない、襲撃行使、被襲撃で相手を道連れ
  const SKL_FRY_CURSED      =103; //呪狐 被呪殺時相手を道連れ
  const SKL_FRY_MEDI_ROLE   =105; //仙狐 処刑者の役職が分かる
  const SKL_FRY_POISON      =129; //野狐 毒薬行使
  const SKL_FRY_GRD         =131; //謀狐 護衛行使
  const SKL_FRY_SEAL        =132; //雪女 特殊能力封印
  const SKL_FRY_NONE        =157; //瘴狐 2Dに一人を無能状態にする
  const SKL_FRY_COUNTER     =164; //木霊 被呪殺時一人を道連れ
  const SKL_FRY_ADD_FRY     =166; //鏡狐 一人に襲撃無効・被呪殺能力を付与
  const SKL_FRY_CAT         =170; //祟狐 被呪殺・襲撃・処刑時に相手を道連れ
  const SKL_FRY_TEMPT       =171; //惑狐 2Dに一人を妖精の子にする
  const SKL_FRY_ADD_SICK    =182; //管狐 能力被行使で無能化する恩恵を付与
  const SKL_FRY_ADD_MRT     =186; //妖花 2dに一人を殉教者陣営にする
  const SKL_FRY_MOON        =193; //月夜霊 被占被護衛で行使者を裏切り陣営にする
  //一匹狼陣営
  const SKL_LONEWOLF        = 56; //一匹狼
  const SKL_LONE_TWICE      =169; //人虎 襲撃を受けると、二回襲撃可能になる
  //笛吹き陣営
  const SKL_PIPER           = 57; //笛吹き
  //恋陣営
  const SKL_QP              = 15; //恋愛天使
  const SKL_QP_SELF         = 17; //求愛者
  const SKL_QP_SELF_MELON   = 16; //求婚者
  const SKL_PASSION         = 53; //片想い
  const SKL_BITCH           = 59; //遊び人
  const SKL_MISTAKE_QP      =134; //狂愛者 絆を結んだと思い込む。無自覚かつランダムに襲撃する
  const SKL_MISTAKE_PSS     =196; //天然誑 能力を受けると相手に片恋絆を付ける
  //邪気陣営
  const SKL_EFB             = 51; //邪気悪魔
  const SKL_EFB_SELF        =136; //決闘者 自撃ち邪気悪魔
  const SKL_EFB_KILL_BAND   =137; //般若 恋陣営が誰かを知る。全恋絆死亡+自分生存で勝利
  //吸血鬼陣営
  const SKL_VAMPIRE_SEA     =106; //吸血鬼 深海 2Dに二人を眷属に変える
  const SKL_SERVANT         =107; //眷属 眷属同士で会話可能
  //深海団
  const SKL_SEE_WILD        =141; //コレクター 終了時狼+妖=1
  const SKL_SEA_NONE        =142; //ビブロフィリア 毎日一人を無能状態にする+終了時全員無能
  const SKL_SEA_EXPECT_DEAD =158; //グリムリーパー 指定した二人死亡
  const SKL_SEA_EXPECT_ALIVE=143; //ギャンブラー 指定した二人が生存
  const SKL_SEA_EXPECT_WIN  =167; //ヴァルキュリア 村か狼陣営を指定、指定した陣営勝利
  const SKL_SEA_ADD_FRY     =168; //パナギア 指定した二人に襲撃無効・被呪殺付与、二人呪詛死
  const SKL_SEA_WITCH       =172; //カロン 3Dと5Dに毒殺・蘇生できる。終了時四人で勝利
  //殉教者陣営
  const SKL_MRT_WOLF        =175; //盲信者 人狼が勝てば勝利、人狼全員が死ぬと後追死する
  const SKL_MRT_FRY         =176; //背徳者 妖精が勝てば勝利、妖精全員が死ぬと後追死する
  const SKL_MRT_LOVE        =177; //月下氷人 恋人が勝てば勝利、恋人全員が死ぬと後追死する
  const SKL_MRT_EFB         =178; //介在人 邪気が勝てば勝利、邪気全員が死ぬと後追死する
  //蒼月教会
  const SKL_BMN_SAINT       =194; //聖女 毎日一人蒼月教会陣営に引き入れる
  const SKL_BMN_SEER        =195; //祭司 占い師
  const SKL_BMN_ASS         =197; //執行者 毎日一人殺害できる
  const SKL_BMN_SNATCH      =198; //影武者 役職聖女と自分を入れ替える
  //据え膳
  const SKL_FISH            = 58; //鱗魚人
  const SKL_FISH_DOG        =181; //大魚人 襲撃されても一日だけ生き長らえる魚
  //照坊主
  const SKL_TERU            = 76; //照坊主
  //奴隷陣営
  const SKL_SLAVE           = 91; //奴隷
  //悪霊陣営
  const SKL_YANDERE         =135; //恋未練 指定先と自分が死ねば追加勝利、墓下投票可
  //輪廻
  const SKL_R_VILLAGER      =144; //村人
  const SKL_R_GUARD         =145; //狩人
  const SKL_R_SEER          =146; //占い師
  const SKL_R_SEER_UNSKILL  =147; //見習い占い師
  const SKL_R_MEDIUM        =148; //霊能者
  const SKL_R_WOLF          =149; //智狼
  const SKL_R_WOLF_WISE     =150; //智狼
  //陣営なし
  const SKL_NULL            =108; //なし(廃村雑談村など)
  const SKL_SUCKER          = 73; //血人 陣営なし
  const SKL_PUPIL           =128; //弟子 黒幕見物人によって2D前に死んでいる場合、能力発動しない
  const SKL_THIEF           =161; //盗賊 黒幕見物人によって2D前に死んでいる場合、能力発動しない
  //見物人
  const SKL_ONLOOKER        = 10; //見物人
  const SKL_OWNER           = 77; //支配人
}
