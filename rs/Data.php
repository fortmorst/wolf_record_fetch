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
  const TM_DEVIL     = 12; //妖魔族
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
  const SKL_VILLAGER        =  1; //村人 能力なし
    //能動判定系
    const SKL_SEER            =  2; //占い師 人狼かそれ以外かを判定、妖魔陣営を呪殺
    const SKL_SEER_TM         = 35; //信仰占師 陣営を判定
    const SKL_SEER_TM_FUZZ    = 84; //審判者 おおまかな陣営占い
    const SKL_SEER_AURA       = 36; //気占師 能力者か否かを判定
    const SKL_SEER_ROLE       = 52; //賢者 役職を判定する
    const SKL_SEER_BAND       =115; //運命読み 絆の有無と種類を占う
    const SKL_SEER_ID         = 85; //中身占い師(魂魄師)
    const SKL_SEER_ONCE       = 66; //夢占師 一度だけ占える
    const SKL_SEER_SKILL      =209; //審問官 村人か否かを占う
    const SKL_SEER_FUZZ       = 96; //見習い占い師 たまに結果が見えない占師
    const SKL_SEER_ROLETM     =184; //天啓者 一日遅れで役職と陣営を占う
    const SKL_SEER_GIFT       =217; //鑑定士 所持恩恵を判定、呪殺/覚醒しない
    const SKL_SEER_TWICE      =249; //隠者 二人一度に占う。恋人を呪殺出来る
    const SKL_DOCTOR          = 25; //医師 とらエリルールの感染を解除
    const SKL_DISPEL          =190; //解呪師 各種呪いと誘惑状態の解除
    const SKL_EXCHANGE_GIFT   =216; //交易商 対象にランダムな恩恵を付与、所持済の場合はその恩恵を貰う
    const SKL_SEER_CURSED     =224; //審神者 呪殺/被呪殺のみの占い師
    const SKL_TEMPT           =243; //黒薔薇 一人を村人陣営にする。黒薔薇全員が死んだら元の陣営に戻る
    //処刑判定系
    const SKL_MEDIUM          =  3; //霊能者 処刑者が人狼かそれ以外かを判定
    const SKL_MEDI_TM         = 67; //信仰霊能者 処刑者の陣営を判定
    const SKL_MEDI_ROLE       =133; //導師 処刑者の役職を判定
    const SKL_MEDI_ROLETM     =200; //骸糾問 役職と陣営の霊能判定
    const SKL_MEDI_READ_G     = 18; //降霊者 墓ログを読める
    const SKL_MEDI_ID         =188; //霊媒師 中身霊能者
    const SKL_READ_G          =113; //霊感少年 墓ログ閲覧
    const SKL_REALIZE_WOLF    =109; //野生児 残狼人数が分かる
    const SKL_REALIZE_FRY     =233; //陰陽生 残妖精人数が分かる
    //護衛系
    const SKL_GUARD           =  4; //狩人 一人を襲撃から守る
    const SKL_GRD_BLACK       = 54; //守護獣 判定で黒が出る+自分を守れる狩人
    const SKL_GRD_READ_W      =239; //恋狼 判定出黒が出る+狼ログをひらがなで読める
    const SKL_GRD_ALCH        = 79; //狙撃手 護衛成功時狼を返り討ちに出来る可能性がある
    const SKL_GRD_G           =112; //守護霊 死んでから護衛できる
    const SKL_GRD_TWICE       =201; //二兎追 二人護衛できるが60%の確率で失敗
    //被襲撃防衛系
    const SKL_HUNTER          = 21; //賞金稼 死亡時指定した相手を道連れにする
    const SKL_SICK            = 28; //病人 襲撃相手を無能にする
    const SKL_DOG             = 22; //人犬 襲撃されても一日だけ生き長らえる
    const SKL_IRON            =110; //鉄人 襲撃を受けない
    const SKL_IRON_ONCE_SICK  = 33; //長老 襲撃を一回無効、二回目は死ぬが相手を無能にする
    const SKL_ALCHEMIST       = 29; //錬金術師 薬を飲んだ日に襲撃されると道連れにする
    const SKL_COUNTER         = 93; //獣化病 常時発動錬金術師
    const SKL_CAT             =154; //猫又 錬金術師+処刑されるとランダム道連れ
    const SKL_NOBLE           = 90; //貴族 襲撃を受けると身代わりに奴隷全員が死ぬ
    const SKL_DEMO            =111; //陽動者 襲撃の身代わりになれる
    //共有系
    const SKL_FM              =  5; //共有者 互いに共有者であることを知る
    const SKL_FM_WIS          =  8; //共鳴者 囁ける共有
    const SKL_FM_WIS_LNG      =212; //黒鳴者 黒判定が出る共鳴
    const SKL_FM_REALIZE_WOLFRY=241;//修道者 共鳴+人狼と妖精合計の残り人数が分かる
    const SKL_FM_BAP_5DAY     =251; //御子(深海) 共鳴ログ+5dに一人を蘇生出来る
    const SKL_STIGMA          =  9; //聖痕者 独自の聖痕を持つ
    const SKL_CONTACT         = 87; //交信者 初日に一人と交信ログで会話可能にする
    const SKL_READ_G_C        =208; //霊話師 墓ログと交信ログを読める
    const SKL_PSY             =226; //巫女 神通会話に参加
    const SKL_PSY_IRON_FRY    =225; //稲荷狐 襲撃耐性、呪殺される/自覚あり、神通会話に参加
    //投票関係
    const SKL_FOLLOWER        = 19; //追従者 委任しか出来ない
    const SKL_LEONARD         =187; //決定者 二票持つ
    const SKL_AGITATOR        = 20; //扇動者 死んだ翌日の処刑が二人になる
    const SKL_PRINCE          = 23; //王子様 処刑を一度だけ取り消す
    const SKL_SG              = 32; //生贄 処刑票が同数になると死ぬが、翌日の処刑相手を指定できる
    //被占で何かが起こる
    const SKL_LINEAGE         = 24; //狼血族 占われると黒判定が出る/占われるまで自覚がない
    const SKL_CURSED          = 26; //呪人 占われると逆呪殺する
    const SKL_CURSED_LINEAGE  =252; //隠呪者 自覚なし呪人
    const SKL_IRON_FRY        =215; //妖血族 襲撃耐性、呪殺される/自覚なし
    const SKL_SUSPECT         = 92; //容疑者 占判定は黒、霊判定は白、自覚あり
    const SKL_SUS_LINEAGE     =152; //狼気纏 霊判定では白が出る狼血族
    const SKL_MOON            =189; //月族 被占被護衛で行使者を裏切り陣営にする
    const SKL_HALF_VMP        =210; //血呪者 被占で覚醒種になり、ランダムで一人を眷属にする
    //殺害or蘇生
    const SKL_BAPTIST         = 78; //洗礼者 一人を蘇生できるが、自分が代わりに死ぬ
    const SKL_BAP_CRESCENT    =213; //反魂師 一人を蘇生出来る
    const SKL_WITCH           = 30; //魔女 任意の一人を殺害/蘇生できる
    const SKL_ONMYO           = 94; //陰陽師 妖魔系か暗殺者を呪殺
    const SKL_ASSASSIN        = 95; //暗殺者 襲撃行使、占われると溶ける
    const SKL_ASS_CRESCENT    =114; //暗殺者(三日月) 襲撃行使、狼や妖魔も殺害可
    //思い込み
    const SKL_MISTAKE_GRD     = 69; //闇狩人 思い込み狩人
    const SKL_MISTAKE_FRY     =116; //狐好き 思い込み狐
    const SKL_MISTAKE_LOVE    =117; //妄想家 思い込み求愛者
    const SKL_MISTAKE_SEER    =138; //狼少年 でたらめな占結果が出る。呪殺は可能
    const SKL_MISTAKE_SEER_NOCURSE=230; //猪突妄信 でたらめな占結果が出る(狼数は正確)。呪殺不可能
    const SKL_MISTAKE_MEDI    =179; //月酔 でたらめな霊結果が出る
    //ランダム変化
    const SKL_RANDOM_EATEN    = 97; //傾奇者 被襲撃でランダムに役職変化
    const SKL_RANDOM_DEAD     =118; //転生者 死後三日後ランダムな役職で復活
    const SKL_RANDOM_TOLD     =155; //運命の子 被占いでランダムに役職変化
    //他
    const SKL_DYING           = 27; //預言者 生存狼+2日目に死ぬ
    const SKL_NOTARY          = 68; //公証人 死ぬと遅延メモが公開される
    const SKL_GIRL            = 31; //少女 赤ログ閲覧 襲撃対象になると死ぬ
    const SKL_CHANGE_SAINT    =231; //御子 聖女が死んだ翌日に聖女になる
    const SKL_BAKERY          =235; //パン屋 生存中はパンを焼くメッセージが出る
  //裏切りの陣営
  const SKL_LUNATIC         =  6; //狂人 能力なし
    //囁き系
    const SKL_WHISPER         = 12; //囁き狂人 赤ログに参加出来る
    const SKL_LUNA_WIS        = 13; //叫迷狂人 叫迷同士で囁ける
    const SKL_LUNA_WIS_FRY    =202; //深林之民 叫迷と蝙蝠発言可
    const SKL_LUNA_READ_W     = 62; //感応狂人 赤ログを覗けるが発言不可
    const SKL_LUNA_MIMIC_FM   = 63; //狂鳴者 共鳴ログに紛れ込む
    const SKL_LUNA_GIRL       =214; //鼠人 赤ログ閲覧 襲撃対象になると死ぬ
    //把握系
    const SKL_FANATIC         = 11; //狂信者 狼を知る
    const SKL_REALIZED        = 99; //悟られ狂人 逆狂信者
    const SKL_FANATIC_MOON    =191; //月従者 月系役職を把握する
    //占い師系
    const SKL_LUNA_SEER       = 55; //狂神官 占い師
    const SKL_LUNA_SEER_MELON = 72; //辻占狂人 占い師 呪殺能力なし
    const SKL_LUNA_SEER_ROLE  = 40; //魔術師 賢者
    const SKL_LUNA_SEER_ID    = 86; //呪魂者 中身占い師 占われると呪殺できる
    const SKL_LUNA_SEER_ROLETM=185; //祈祷師 一日遅れで役職と陣営を占う
    //誘惑・変質系
    const SKL_LUNA_WIS_TEMPT  = 88; //誘惑者 邪教徒(深海) 囁き狂人+初日に一人を隷従者(恩恵)にする
    const SKL_LUNA_TEMPT      = 81; //冒涜者 狂信者+初日に一人を背信者にする
    const SKL_LUNA_TEMPTED    = 82; //背信者 背信者、冒涜者同士で会話可能
    const SKL_LUNA_TEMPT_SEA  =156; //誘惑者 初日に一人を半端者にする
    const SKL_LUNA_ADD_FRY    =163; //呪術師 一人に襲撃無効・被呪殺能力を付与
    const SKL_JAMMER          = 34; //邪魔之民 対象を占い能力から保護する
    const SKL_DAZZLE          =121; //幻惑者 絆の内容を逆にする
    const SKL_PERVERT         =122; //倒錯者 生存中に占霊判定を逆にする
    const SKL_SEAL            = 71; //封印狂人 任意の相手の能力を一日だけ封じる
    const SKL_SNATCH          = 60; //宿借之民 姿を入れ替える
    const SKL_LUNA_WITCH      =183; //南瓜提灯 毒殺+蘇生薬
    const SKL_NONE            =139; //瘴気狂人 2Dに一人を無能状態にする
    const SKL_MAD             =153; //狂学者 2Dに一人を人狼にする
    //霊能者系
    const SKL_LUNA_MEDI       = 39; //魔神官 導師
    //襲撃で変化
    const SKL_LUNA_WIS_LINEAGE=159; //胡蝶 自覚なし。襲撃されると囁き狂人に変化
    const SKL_SLEEPER         = 89; //睡狼 自覚なし。被襲撃で人狼になる
    const SKL_HALFWOLF        = 38; //半狼 自覚あり。非襲撃で人狼になる
    //他
    const SKL_MUPPETER        = 37; //人形使い ダミーで喋る
    const SKL_LUNA_EXE_G      =120; //騒霊 墓下で投票できる
    const SKL_LUNA_BLACK      = 98; //囮人形 占われると黒判定が出る
    const SKL_LUNA_SICK_EXE   =119; //怨嗟狂人 吊られると任意の一人を無能にする
    const SKL_LUNA_CHANGE_ROLE=221; //四不象 初日に存在する役職に擬態して判定を変えられる
  //人狼陣営
  const SKL_WOLF            =  7; //人狼 毎日一人を襲撃できる
    //特殊襲撃
    const SKL_HEADLESS        = 41; //首無騎士 人狼も襲撃可能
    const SKL_HEADLESS_NOTALK =234; //碧狼 囁けない首無騎士
    const SKL_WISEWOLF        = 42; //智狼 襲撃した人間の役職が分かる
    const SKL_WISEWOLF_SENSE  =229; //悟狼 自分が襲撃した人間の役職が分かる/襲撃に失敗しても判明する
    const SKL_CHILDWOLF       = 45; //仔狼 死んだ翌日の襲撃が二回になる
    const SKL_RECKLESS        =127; //蛮狼 自分を犠牲に護衛貫通襲撃が可能
    const SKL_WOLF_SNATCH     = 70; //憑狼 襲撃者の身体を乗っ取る
    const SKL_WOLF_HUNGRY     =123; //餓狼 二日連続襲撃できないと死ぬ
    const SKL_WOLF_ELDER      =151; //古狼 襲撃耐性を貫通するが、6割の確率で襲撃失敗する
    const SKL_WOLF_DELAY      =165; //蠱狼 襲撃した一日後に対象が死ぬ。襲撃返り討ち耐性を無効化
    const SKL_WOLF_DELAY_2    =219; //怨狼 襲撃した二日後に対象が死ぬ
    const SKL_WOLF_TMP_WOLF   =211; //夢魔(深海) 襲撃相手を人狼にする
    const SKL_WOLF_ADD_MRT    =168; //外狼 襲撃した相手を殉教者にする
    const SKL_WOLF_ELDER_DYING=223; //焔狼 襲撃耐性を貫通するが、生存狼+2日目に死ぬ 
    //被占で変化
    const SKL_WOLF_CURSED     = 43; //呪狼 逆呪殺する
    const SKL_WHITEWOLF       = 44; //白狼 占判定が白
    const SKL_WWOLF_BLACK_G   =126; //擬狼 大狼 死ぬと真判定になる白狼
    const SKL_SLEEPER_BLACK   =124; //忘狼 占いでも覚醒する睡狼
    const SKL_WOLF_MOON       =192; //月狼 被占被護衛で行使者に月狂病(恩恵/眷属ログ)を付与
    const SKL_WOLF_CHERRY     =250; //桜狼 被占被護衛で行使者に狂鳴者(恩恵/囁きログ)を付与
    //囁き系
    const SKL_WOLF_READ_C     =238; //魂狼 交信ログを読める
    const SKL_WOLF_MR_READ_G  =101; //霊狼 導師+墓下ログが読める
    const SKL_WOLF_MMC_FM_WIS =199; //愚狼 共鳴ログに紛れ込む
    const SKL_WOLF_READ_D     =242; //妖狼 妖魔族ログに紛れ込む
    //特殊能力系
    const SKL_WOLF_TEMPT      = 80; //瘴狼 初日に一人を隷従者(恩恵)にする
    const SKL_WOLF_MEDI_ROLE  =100; //賢狼 導師
    const SKL_WOLF_FAN        =125; //嗅狼 半狼や狼血族が分かる
    //特殊性質
    const SKL_WOLF_DEPEND     =160; //従狼 人カウントも狼カウントもしない
    const SKL_WOLF_PRINCE     =180; //群狼 LWにならない限り処刑されない
    const SKL_WOLF_NOBAND     =203; //鈍狼 あらゆる絆がつかない
    const SKL_WOLF_DYING      = 46; //衰狼 生存狼+2日目に死ぬ
    const SKL_WOLF_NOTALK     = 47; //黙狼 囁けない
  //妖魔陣営
  const SKL_FAIRY           = 14; //妖魔 襲撃耐性、被占で呪詛死する
    //特殊能力
    const SKL_PIXY            = 50; //悪戯妖精 道連れ絆を撃つ
    const SKL_FRY_PIXY_RANDOM =247; //天邪鬼 効果の分からない絆を撃つ
    const SKL_FRY_SEER_BAND   = 83; //夢魔 絆の有無を占う(呪殺不可)
    const SKL_FRY_JAMMER      = 64; //邪魔妖精 対象を占い能力から保護する
    const SKL_FRY_SEER_ROLE   =140; //サトリ 役職占いができる妖魔
    const SKL_FRY_MEDI_ROLE   =105; //仙狐 処刑者の役職が分かる
    const SKL_YASHA           =162; //夜叉 50%の確率でランダムに襲撃
    const SKL_FRY_READ_ALL_P  =130; //九尾 三日月 毒殺+全秘密ログ閲覧
    const SKL_FRY_POISON      =129; //野狐 毒薬行使
    const SKL_FRY_GRD         =131; //謀狐 護衛行使
    const SKL_FRY_SEER_FUZZ   =228; //幼狐 50%の確率で失敗する占い師、妖精が誰かを知る、襲撃耐性なし呪殺されない
    const SKL_FRY_CHANGELING  =245; //夜夢 最多処刑者を予想出来た場合、指定の人物と被処刑者と入れ替える
    //相手を変化
    const SKL_FRY_SNATCH      = 65; //宿借妖精 姿を入れ替える
    const SKL_FRY_TEMPT       =171; //惑狐 2Dに一人を妖精の子にする
    const SKL_FRY_ADD_SICK    =182; //管狐 能力被行使で無能化する恩恵を付与
    const SKL_FRY_ADD_MRT     =186; //妖花 2dに一人を殉教者陣営にする
    const SKL_VAMPIRE         = 74; //吸血鬼 瓜科 人間を血人に変化させる
    const SKL_FRY_SEAL        =132; //雪女 特殊能力封印
    const SKL_FRY_SEAL_ONCE   =227; //妖鳥 一日だけ特殊能力封印
    const SKL_FRY_NONE        =157; //瘴狐 2Dに一人を無能状態にする
    const SKL_FRY_ADD_FRY     =166; //鏡狐 一人に襲撃無効・被呪殺能力を付与
    //囁き系
    const SKL_FRY_WIS         = 61; //蝙蝠人間 蝙蝠人間同士で囁ける
    const SKL_FRY_WIS_LUNA    =220; //念波妖狐 裏切り陣営の窓で囁ける
    const SKL_FRY_MIMIC_W     = 48; //擬狼妖精 赤ログに紛れ込む
    const SKL_FRY_READ_W      = 75; //夜兎 赤窓閲覧
    const SKL_FRY_READ_A      =173; //妖兎 全秘密ログ閲覧
    const SKL_FRY_MIMIC_PSY   =222; //欺狐 蝙蝠人間窓+神通力窓に参加
    //被能力行使で特殊能力
    const SKL_FRY_READ_A_DOG  =174; //月兎 妖兎+処刑突然死以外の死因を一度だけ防ぐ
    const SKL_FRY_DYING_HALF  =104; //半妖 風花妖精+襲撃を受けると仙狐になる
    const SKL_FRY_MOON        =193; //月夜霊 被占被護衛で行使者を裏切り陣営にする
    const SKL_FRY_ASS_COUNTER =102; //九尾 呪殺されない、襲撃行使、被襲撃で相手を道連れ
    const SKL_FRY_CAT         =170; //祟狐 被呪殺・襲撃・処刑時に相手を道連れ
    const SKL_FRY_CURSED      =103; //呪狐 被呪殺時相手を道連れ
    const SKL_FRY_CURSED_ALL  =218; //七歩蛇 被能力対象時に相手を殺害
    const SKL_FRY_MISTAKE     =164; //憑狐 村人思い込み妖魔
    //他
    const SKL_FRY_DYING       = 49; //風花妖精 生存狼+2日目に死ぬ 
  //一匹狼陣営
  const SKL_LONEWOLF        = 56; //一匹狼 人狼とは別に襲撃する
  const SKL_LONE_TWICE      =169; //人虎 襲撃を受けると、二回襲撃可能になる
  //笛吹き陣営
  const SKL_PIPER           = 57; //笛吹き 毎日二人を踊らせる/自分を除く生存者全員が踊ったら勝利
  //恋陣営
  const SKL_QP              = 15; //恋愛天使 2dに二人に指定し、その二人が生き残れば勝利
  const SKL_QP_SELF         = 17; //求愛者 自撃ち恋愛天使
  const SKL_QP_SELF_MELON   = 16; //求婚者 秘密窓+相手が断れる求愛者
  const SKL_PASSION         = 53; //片想い 相手が気付かない片恋絆を撃つ
  const SKL_BITCH           = 59; //遊び人 自撃ち+偽の絆も撃つ
  const SKL_MISTAKE_QP      =134; //狂愛者 絆を結んだと思い込む。無自覚かつランダムに襲撃する
  const SKL_MISTAKE_PSS     =196; //天然誑 能力を受けると相手に片恋絆を付ける
  //邪気陣営
  const SKL_EFB             = 51; //邪気悪魔 二人に邪気絆を撃ち、どちらかだけが生き残れば勝ち
  const SKL_EFB_SELF        =136; //決闘者 自撃ち邪気悪魔
  const SKL_EFB_KILL_BAND   =137; //般若 恋陣営が誰かを知る。全恋絆死亡+自分生存で勝利
  const SKL_EFB_TRIPLE      =232; //三つ巴 自分+二人に邪気絆を撃つ
  //吸血鬼陣営
  const SKL_VAMPIRE_SEA     =106; //吸血鬼 深海 2Dに二人を眷属に変える
  const SKL_SERVANT         =107; //眷属 眷属同士で会話可能
  const SKL_SRV_SEER_ROLE   =204; //偽神官 眷属、賢者
  const SKL_SRV_ASS         =205; //殺戮者 襲撃行使
  const SKL_VMP_PURE        =206; //純血種 毎日一人を吸血鬼陣営の役職に変える
  const SKL_VMP_HALF        =207; //覚醒種 特定役職が占を受けて吸血鬼陣営になった役職
  //深海団
  const SKL_SEA_WILD        =141; //コレクター 終了時狼+妖=1
  const SKL_SEA_NONE        =142; //ビブロフィリア 毎日一人を無能状態にする+終了時全員無能
  const SKL_SEA_EXPECT_DEAD =158; //グリムリーパー 指定した二人死亡
  const SKL_SEA_EXPECT_ALIVE=143; //ギャンブラー 指定した二人が生存
  const SKL_SEA_EXPECT_WIN  =167; //ヴァルキュリア 村か狼陣営を指定、指定した陣営勝利
  const SKL_SEA_WITCH       =172; //カロン 3Dと5Dに毒殺・蘇生できる。終了時四人で勝利
  const SKL_SEA_SIBYL       =240; //シビュラ 2d以降ランダムに指定された事件を起こせる
  const SKL_SEA_SIREN       =244; //セイレーン 2d以降処刑突然衰退死以外の死者を蘇らせる
  //蒼月教会
  const SKL_BMN_SAINT       =194; //聖女 毎日一人蒼月教会陣営に引き入れる
  const SKL_BMN_SEER        =195; //祭司 占い師
  const SKL_BMN_ASS         =197; //執行者 毎日一人殺害できる
  const SKL_BMN_SNATCH      =198; //影武者 役職聖女と自分を入れ替える
  //殉教者陣営
  const SKL_MRT_WOLF        =175; //盲信者 人狼が勝てば勝利、人狼全員が死ぬと後追死する
  const SKL_MRT_FRY         =176; //背徳者 妖精が勝てば勝利、妖精全員が死ぬと後追死する
  const SKL_MRT_LOVE        =177; //月下氷人 恋人が勝てば勝利、恋人全員が死ぬと後追死する
  const SKL_MRT_EFB         =178; //介在人 邪気が勝てば勝利、邪気全員が死ぬと後追死する
  //据え膳
  const SKL_FISH            = 58; //鱗魚人 襲撃されたら勝ち
  const SKL_FISH_DOG        =181; //大魚人 襲撃されても一日だけ生き長らえる魚
  //照坊主
  const SKL_TERU            = 76; //照坊主 処刑されたら勝ち
  //奴隷陣営
  const SKL_SLAVE           = 91; //奴隷 貴族が死んでいれば勝ち
  //悪霊陣営
  const SKL_YANDERE         =135; //恋未練 指定先と自分が死ねば追加勝利、墓下投票可
  //妖魔族
  const SKL_DEVIL           =248; //古妖魔 妖魔族基本、独自窓を持つ
  const SKL_DVL_MIMIC_FM    =236; //響鳴種 共鳴窓に潜り込む
  const SKL_DVL_TEMPT       =237; //妖姫 一人を役職恩恵そのままに妖魔族にする
  const SKL_DVL_READ_W      =246; //闇妖魔 囁きログに潜り込む
  //輪廻
  const SKL_R_VILLAGER      =144; //村人 村陣営 全体発言が使える
  const SKL_R_GUARD         =145; //狩人 村陣営 護衛できる
  const SKL_R_SEER          =146; //占い師 占い師陣営 占える
  const SKL_R_SEER_UNSKILL  =147; //見習い占い師 占い師陣営 能力なし
  const SKL_R_MEDIUM        =148; //霊能者 霊能者陣営 処刑された人が人狼か否か分かる
  const SKL_R_WOLF          =149; //人狼 人狼陣営 襲撃出来る
  const SKL_R_WOLF_WISE     =150; //智狼 人狼陣営 襲撃した相手の役職が分かる 
  //陣営なし
  const SKL_NULL            =108; //なし(廃村雑談村など)
  const SKL_SUCKER          = 73; //血人 陣営なし
  const SKL_PUPIL           =128; //弟子 黒幕見物人によって2D前に死んでいる場合、能力発動しない
  const SKL_THIEF           =161; //盗賊 黒幕見物人によって2D前に死んでいる場合、能力発動しない
  //見物人
  const SKL_ONLOOKER        = 10; //見物人
  const SKL_OWNER           = 77; //支配人 進行中に全てのログが読める
}
