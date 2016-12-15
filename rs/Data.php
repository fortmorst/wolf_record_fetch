<?php

class Data
{
  //陣営
  const TM_RP        =  0; //勝敗なし
  const TM_RUIN      = 97; //廃村
  const TM_VILLAGER  =  1; //村人陣営
  const TM_WOLF      =  2; //人狼陣営
  const TM_FAIRY     =  3; //妖魔陣営
  const TM_LOVERS    =  4; //恋人陣営
  const TM_EFB       =  8; //邪気陣営
  const TM_EVIL      =  9; //裏切りの陣営
  const TM_SLAVE     = 14; //奴隷陣営
  const TM_MARTYR    = 22; //殉教者陣営
  const TM_ONLOOKER  = 98; //見物人
  const SKL_OWNER    = 77; //支配人 進行中に全てのログが読める

  //役職
  const SKL_SEER            =  2; //占い師 人狼かそれ以外かを判定、妖魔陣営を呪殺
  const SKL_FM              =  5; //共有者 互いに共有者であることを知る
  const SKL_FM_WIS          =  8; //共鳴者 囁ける共有
  const SKL_STIGMA          =  9; //聖痕者 独自の聖痕を持つ
  const SKL_BAPTIST         = 78; //洗礼者 一人を蘇生できるが、自分が代わりに死ぬ
  const SKL_LUNATIC         =  6; //狂人 能力なし
  const SKL_WHISPER         = 12; //囁き狂人 赤ログに参加出来る
  const SKL_FANATIC         = 11; //狂信者 狼を知る
  const SKL_LUNA_WIS        = 13; //叫迷狂人 叫迷同士で囁ける
  const SKL_EVIL            =365; //裏切り狂人
  const SKL_EVL_KNOW_WF     =366; //滅殺者
  const SKL_EVL_SEER_ROLE   =367; //愛好家
  const SKL_EVL_MIMIC       =373; //不穏分子/裏切りの陣営
  const SKL_WOLF            =  7; //人狼 毎日一人を襲撃できる
  const SKL_WOLF_CURSED     = 43; //呪狼 逆呪殺する
  const SKL_FAIRY           = 14; //妖魔 襲撃耐性、被占で呪詛死する
  const SKL_QP              = 15; //恋愛天使 2dに二人に指定し、その二人が生き残れば勝利
  const SKL_QP_SELF         = 17; //求愛者 自撃ち恋愛天使
  const SKL_QP_SELF_MELON   = 16; //求婚者 秘密窓+相手が断れる求愛者
  const SKL_ONLOOKER        = 10; //見物人

  //結末 destiny
  const DES_ALIVE   = 1;  //生存
  const DES_RETIRED = 2;  //突然死
  const DES_EATEN   = 4;  //襲撃死
  const DES_CURSED  = 5;  //呪詛死
  const DES_ONLOOKER=10;  //見物

  //編成
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
  const RGL_RUIN = 30;      //廃村

  //結果
  const RSL_WIN      = 1;
  const RSL_LOSE     = 2;
  const RSL_JOIN     = 3;   //参加(非ガチ村)
  const RSL_INVALID  = 4;   //無効(一部の国での突然死)
  const RSL_ONLOOKER = 5;   //見物

  //言い換え
  const RP_DEFAULT    = "人狼物語";
  const RP_DEFAULT_ID = 58;
}
