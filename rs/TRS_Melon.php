<?php

trait TRS_Melon
{
  protected $RP_LIST = [
     '人狼物語'=>'SOW'
    ,'まったり'=>'MELON'
    ,'適当系'=>'FOOL'
    ,'妖怪物語'=>'SOW_Y'
    ,'人狼審問'=>'JUNA'
    ,'人狼BBS'=>'WBBS'
    ,'ビジネスオフィス'=>'OFFICE'
    ,'人狼劇場'=>'THEATER'
    ,'アリスのお茶会'=>'ALICE'
    ,'魔神村'=>'MAJIN'
    ,'騎士団領への旅路'=>'TOUR'
    ,'宵闇の琥珀'=>'KOHAKU'
    ,'国史学園'=>'KOKUSI'
    ,'旧校舎の怪談'=>'GB'
    ,'煌びやかな賭博場'=>'CASINO'
    ,'F2077再戦企画'=>'F2077'
    ,'月見村の狼(限定)'=>'MOON'
    ,'裏切りのゲーム盤（限定）'=>'BETRAY'
    ,'ネヴァジスタ(限定)'=>'NEVER'
    ,'VO8(限定)'=>'VO8'
    ,'商店街BBS(限定)'=>'MARKET'
  ];

  protected $WTM_SKIP = [
     '/村の設定が変更されました。/','/遺言状が公開されました。/','/遺言メモが公開/','/おさかな、美味しかったね！/','/魚人はとても美味/','/人魚は/','/とってもきれいだね！/','/自殺志願者/','/運動会は晴天です！/'
    ,'/見物しに/','/がやってきたよ/'
  ];

  protected $WTM_SOW = [
     '人狼を退治したのだ！'=>Data::TM_VILLAGER
    ,'を立ち去っていった。'=>Data::TM_WOLF
    ,'いていなかった……。'=>Data::TM_FAIRY
    ,'にも無力なのだ……。'=>Data::TM_LOVERS
    ,'領域だったのだ……。'=>Data::TM_LOVERS
  ];
  protected $WTM_MELON = [
     'かいけつ！やったね！'=>Data::TM_VILLAGER
    ,'かいけつならずだよ！'=>Data::TM_WOLF
    ,'つ！…したっぽいよ？'=>Data::TM_FAIRY
    ,'がひろがったみたい？'=>Data::TM_FAIRY
    ,'ゅ〜ん&#9825;'=>Data::TM_LOVERS
    ,'きどうなっちゃうの？'=>Data::TM_LOVERS
  ];
  protected $WTM_FOOL = [
     'が勝ちやがりました。'=>Data::TM_VILLAGER
    ,'ようだ。おめでとう。'=>Data::TM_WOLF
    ,'けている（らしい）。'=>Data::TM_FAIRY
    ,'んだよ！（意味不明）'=>Data::TM_FAIRY
    ,'は世界を救うんだよ！'=>Data::TM_LOVERS
    ,'狼だって救うんだよ！'=>Data::TM_LOVERS
  ];
  protected $WTM_SOW_Y = [
     '戻ってくると思うよ。'=>Data::TM_VILLAGER
    ,'なくなっちゃったよ。'=>Data::TM_WOLF
    ,'ちを見ているよ……。'=>Data::TM_FAIRY
  ];
  protected $WTM_JUNA = [
     '人狼に勝利したのだ！'=>Data::TM_VILLAGER
    ,'めて去って行った……'=>Data::TM_WOLF
    ,'くことはなかった……'=>Data::TM_FAIRY
    ,'すすべがなかった……'=>Data::TM_FAIRY
    ,'真の愛に目覚めた……'=>Data::TM_LOVERS
  ];
  protected $WTM_WBBS = [
     'る日々は去ったのだ！'=>Data::TM_VILLAGER
    ,'の村を去っていった。'=>Data::TM_WOLF
    ,'生き残っていた……。'=>Data::TM_FAIRY
    ,'に世界はあるの……。'=>Data::TM_LOVERS
  ];
  protected $WTM_OFFICE = [
     'る支社に戻りました！'=>Data::TM_VILLAGER
    ,'ってしまいました…。'=>Data::TM_WOLF
    ,'てしまったようです。'=>Data::TM_FAIRY
  ];
  protected $WTM_THEATER = [
     '素敵狼さんを全員捕まえち'=>Data::TM_VILLAGER
    ,'もう狼さんの魅力に抵抗出'=>Data::TM_WOLF
    ,'素敵狼さんを全員捕まえた'=>Data::TM_LOVERS
  ];
  protected $WTM_ALICE = [
     'いお茶会の再開です！'=>Data::TM_VILLAGER
    ,'ありませんでした…。'=>Data::TM_WOLF
    ,'んか要らないのです。'=>Data::TM_LOVERS
  ];
  protected $WTM_MAJIN = [
    '年の間閉ざされる……'=>Data::TM_WOLF
  ];
  protected $WTM_TOUR = [
     '。村人側の勝利です！'=>Data::TM_VILLAGER
    ,'。人狼側の勝利です！'=>Data::TM_WOLF
    ,'にも無力なのだ……。'=>Data::TM_LOVERS
  ];
  protected $WTM_KOHAKU = [
     '平和な日々が訪れる。'=>Data::TM_VILLAGER
    ,'まで愛でるとしよう。'=>Data::TM_WOLF
  ];
  protected $WTM_KOKUSI = [
     '人狼を退治したのだ！'=>Data::TM_VILLAGER
    ,'を立ち去っていった。'=>Data::TM_WOLF
  ];
  protected $WTM_GB = [
     '悪霊を始末したのだ！'=>Data::TM_VILLAGER
    ,'いていなかった……。'=>Data::TM_FAIRY
  ];
  protected $WTM_CASINO = [
    'る村人はいないのだ！'=>Data::TM_WOLF
    ,'にも無力なのだ……。'=>Data::TM_LOVERS
  ];
  protected $WTM_F2077 = [
    'めでとうございます！'=>Data::TM_VILLAGER
  ];
  protected $WTM_MOON = [
    'を取り戻したのです！'=>Data::TM_VILLAGER
  ];
  protected $WTM_BETRAY = [
    'の＞を捕らえたのだ！'=>Data::TM_VILLAGER
  ];
  protected $WTM_NEVER = [
    'と戻っていきました。'=>Data::TM_WOLF
  ];
  protected $WTM_VO8 = [
    'ーの姿が！ハム勝利！'=>Data::TM_FAIRY
  ];
  protected $WTM_MARKET = [
    '生き残っていた……。'=>Data::TM_FAIRY
  ];

  //結末
  protected $DESTINY = [
     "処刑"=>Data::DES_HANGED
    ,"突然死"=>Data::DES_RETIRED
    ,"襲撃"=>Data::DES_EATEN
    ,"呪殺"=>Data::DES_CURSED
    ,"後追"=>Data::DES_SUICIDE
  ];
  //勝敗
  protected $RSL = [
     "勝利"=>Data::RSL_WIN
    ,"敗北"=>Data::RSL_LOSE
    ,"--"=>Data::RSL_INVALID //無効(突然死)
  ];

  //能力、陣営
  protected $SKILL = [
     [Data::SKL_VILLAGER,Data::TM_VILLAGER]
    ,[Data::SKL_SEER,Data::TM_VILLAGER]
    ,[Data::SKL_MEDIUM,Data::TM_VILLAGER]
    ,[Data::SKL_GUARD,Data::TM_VILLAGER]
    ,[Data::SKL_FM,Data::TM_VILLAGER]
    ,[Data::SKL_FM_WIS,Data::TM_VILLAGER]
    ,[Data::SKL_STIGMA,Data::TM_VILLAGER]
    ,[Data::SKL_NOTARY,Data::TM_VILLAGER]
    ,[Data::SKL_MISTAKE_GRD,Data::TM_VILLAGER]
    ,[Data::SKL_WOLF,Data::TM_WOLF]
    ,[Data::SKL_WOLF_CURSED,Data::TM_WOLF]
    ,[Data::SKL_WISEWOLF,Data::TM_WOLF]
    ,[Data::SKL_WOLF_SNATCH,Data::TM_WOLF]
    ,[Data::SKL_LUNATIC,Data::TM_WOLF]
    ,[Data::SKL_FANATIC,Data::TM_WOLF]
    ,[Data::SKL_WHISPER,Data::TM_WOLF]
    ,[Data::SKL_LUNA_WIS,Data::TM_WOLF]
    ,[Data::SKL_SEAL,Data::TM_WOLF]
    ,[Data::SKL_LUNA_SEER_MELON,Data::TM_WOLF]
    ,[Data::SKL_FAIRY,Data::TM_FAIRY]
    ,[Data::SKL_FRY_WIS,Data::TM_FAIRY]
    ,[Data::SKL_PIXY,Data::TM_FAIRY]
    ,[Data::SKL_SUCKER,Data::TM_NONE]
    ,[Data::SKL_VAMPIRE,Data::TM_FAIRY]
    ,[Data::SKL_QP_SELF_MELON,Data::TM_LOVERS]
    ,[]//婚約者
    ,[Data::SKL_FISH,Data::TM_FISH]
    ,[Data::SKL_TERU,Data::TM_TERU]
  ];
  protected $SKL_SOW = [
     '村人','占い師','霊能者','狩人','共有者','共鳴者','聖痕者','公証人','闇狩人'
     ,'人狼','呪狼','智狼','憑狼','狂人','狂信者','Ｃ国狂人','叫迷狂人','封印狂人','辻占狂人'
     ,'ハムスター人間','蝙蝠人間','小悪魔','血人','吸血鬼'
     ,'求婚者','婚約者','魚人','照坊主'
  ];

  protected $SKL_MELON = [
     'むらびと','うらないし','れいのー','しゅご','けっしゃ','きょーめいしゃ','ホクロもち','こーしょーにん','やみしゅご'
    ,'じんろー','じゅろー','ちろー','ひょうろー','きょーじん','きょーしんしゃ','ヒソヒソきょーじん','おたけびきょーじん','ふーいんきょーじん','つじうらきょーじん'
    ,'よーま','てんま','こあくま','ちびと','きゅーけつき'
    ,'きゅーこんしゃ','こんやくしゃ','さかなびと','てるぼーず'
  ];
  protected $SKL_FOOL = [
     'ただの人','エスパー','イタコ','ストーカー','夫婦','おしどり夫婦','痣もち','公証人','闇ストーカー'
    ,'おおかみ','逆恨み狼','グルメ','憑狼','人狼スキー','人狼教信者','人狼教神官','叫迷狂人','封印狂人','辻占狂人'
    ,'ハム','コウモリ','イタズラっ子','血人','吸血鬼'
    ,'求婚者','婚約者','魚人','照坊主'
  ];
  protected $SKL_SOW_Y = [
      'ただの妖怪','鏡持ち','口寄せ','銭投げ','仙人','山彦','聖獣','公証人','闇銭'
     ,'貧乏神','死神','疫病神','付喪神','狂鬼','信鬼','囁鬼','叫迷狂人','封印狂人','辻占狂人'
     ,'飯綱','鳴家','生霊','血人','吸血鬼'
     ,'求婚者','婚約者','魚人','照坊主'
  ];
  protected $SKL_JUNA = [
      '村人','占い師','霊能者','守護者','結社員','共鳴者','聖痕者','公証人','闇守護'
     ,'人狼','呪狼','智狼','憑狼','狂人','狂信者','囁き狂人','叫迷狂人','封印狂人','辻占狂人'
     ,'妖魔','天魔','悪戯妖精','血人','吸血鬼'
     ,'求婚者','婚約者','魚人','照坊主'
  ];
  protected $SKL_OFFICE = [
     '支社社員','監査役','筆頭株主','保守派','労組幹部','秘匿恋愛者','名誉会長','公証人','闇保守派'
    ,'本社人事','本社人事課長','本社人事次長','本社人事部長','人事補佐Ａ','人事補佐Ｂ','人事補佐Ｃ','叫迷狂人','封印狂人','辻占狂人'
    ,'産業スパイ梅','産業スパイ桃','産業スパイ桜','血人','吸血鬼'
    ,'求婚者','婚約者','魚人','照坊主'
  ];
  protected $SKL_ALICE = [
    '参加者','証人','医者','守衛','裁判官','共鳴者','聖痕者','公証人','闇守衛'
    ,'犯人','弁の立つ犯人','観察力のある犯人','憑狼','狂言者','狂信者','囁き狂言者','叫迷狂人','封印狂人','辻占狂人'
    ,'ちゃっかり屋','しっかり屋','うっかり屋','血人','吸血鬼'
    ,'求婚者','婚約者','魚人','照坊主'
  ];
  protected $SKL_MAJIN = [
      '村人','占い師','霊能者','守護者','結社員','共鳴者','聖痕者','公証人','闇守護'
     ,'人狼','呪狼','智狼','憑狼','狂人','狂信者','囁き狂人','叫迷狂人','封印狂人','辻占狂人'
     ,'妖魔','天魔','悪戯妖精','血人','吸血鬼'
     ,'求婚者','婚約者','魚人','照坊主'
  ];
  protected $SKL_KOHAKU = [
     '町人','真名探り','好事家','護符職人','刑事','警部','不在証明アリ','公証人','落ち零れ護符職人'
     ,'魔術師','呪術師','秘術師','憑狼','悪徳琥珀商人','魔術師を目撃した者','魔術師の愛弟子','念話術士','封印狂人','辻占狂人'
     ,'琥珀妖精','蝙蝠人間','悪意ある琥珀妖精','尊き琥珀妖精の血族','高貴なる琥珀妖精'
     ,'求婚者','婚約者','人魚','自殺志願者'
  ];
  protected $SKL_KOKUSI = [
     '一般人','占い師','霊能者','狩人','共有者','共鳴者','聖痕者','公証人','闇狩人'
     ,'人狼','呪狼','智狼','憑狼','狂人','狂信者','Ｃ国狂人','叫迷狂人','封印狂人','辻占狂人'
     ,'ハムスター人間','蝙蝠人間','小悪魔','血人','吸血鬼'
     ,'求婚者','婚約者','魚人','照坊主'
  ];
  protected $SKL_GB = [
    '学生','占い師','霊能者','退魔師','共有者','共鳴者','聖痕者','公証人','エセ退魔師','悪霊','呪霊','智霊','憑霊','狂人','狂信者','Ｃ国狂人','叫迷狂人','封印狂人','辻占狂人','座敷童','蝙蝠人間','小悪魔','血人','吸血鬼','求婚者','婚約者','魚人','照坊主'
  ];
  protected $SKL_CASINO = [
     '村人','占い師','霊能者','狩人','共有者','共鳴者','聖痕者','公証人','闇狩人'
     ,'狼','呪狼','智狼','憑狼','狂人','狂信者','Ｃ国狂人','叫迷狂人','封印狂人','辻占狂人'
     ,'妖狐','念話狐','小悪魔','血人','吸血鬼'
     ,'求婚者','婚約者','魚人','照坊主'
  ];
  protected $SKL_MOON = [
     '村人役','占い師役','霊能者役','狩人役','共有者役','共鳴者','聖痕者','公証人','闇狩人'
     ,'人狼役','呪狼','智狼','憑狼','狂人役','狂信者','Ｃ国狂人','叫迷狂人','封印狂人','辻占狂人'
     ,'妖狐役','蝙蝠人間','小悪魔','血人','吸血鬼'
     ,'求婚者','婚約者','魚人','照坊主'
  ];
  protected $SKL_BETRAY = [
     'AGNメンバー','覗き屋','情報屋','ガーディアン','共有者','魂の通話者','聖痕者','公証人','闇狩人'
     ,'満月に吼えるもの','呪狼','満月を知るもの','憑狼','狂人','狂信者','内通者','遺伝子の通話者','封じる人','辻占狂人'
     ,'ハムスター人間','蝙蝠人間','小悪魔','神経質な人','吸血鬼'
     ,'求める人','婚約者','魚人','照坊主'
  ];
}
