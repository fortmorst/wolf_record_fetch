<?php

trait TRS_SOW
{
  protected $RP_LIST = [
     '人狼物語'=>'SOW'
    ,'適当系'=>'FOOL'
    ,'人狼審問'=>'JUNA'
    ,'人狼BBS'=>'WBBS'
  ];
  protected $WTM_SOW= [
     '人狼を退治したのだ！'=>Data::TM_VILLAGER
    ,'を立ち去っていった。'=>Data::TM_WOLF
    ,'いていなかった……。'=>Data::TM_FAIRY
    ,'の村を去っていった。'=>Data::TM_LOVERS
  ];
  protected $WTM_FOOL = [
     'が勝ちやがりました。'=>Data::TM_VILLAGER
    ,'ようだ。おめでとう。'=>Data::TM_WOLF
    ,'けている（らしい）。'=>Data::TM_FAIRY
    ,'んだよ！（意味不明）'=>Data::TM_FAIRY
  ];
  protected $WTM_JUNA = [
     '人狼に勝利したのだ！'=>Data::TM_VILLAGER
    ,'めて去って行った……'=>Data::TM_WOLF
    ,'くことはなかった……'=>Data::TM_FAIRY
    ,'すすべがなかった……'=>Data::TM_FAIRY
  ];
  protected $WTM_WBBS = [
     'る日々は去ったのだ！'=>Data::TM_VILLAGER
    ,'の村を去っていった。'=>Data::TM_WOLF
    ,'生き残っていた……。'=>Data::TM_FAIRY
  ];
  protected $SKILL = [
     "村人"=>[Data::SKL_VILLAGER,Data::TM_VILLAGER]
    ,"人狼"=>[Data::SKL_WOLF,Data::TM_WOLF]
    ,"占い師"=>[Data::SKL_SEER,Data::TM_VILLAGER]
    ,"霊能者"=>[Data::SKL_MEDIUM,Data::TM_VILLAGER]
    ,"狂人"=>[Data::SKL_LUNATIC,Data::TM_WOLF]
    ,"狩人"=>[Data::SKL_GUARD,Data::TM_VILLAGER]
    ,"守護者"=>[Data::SKL_GUARD,Data::TM_VILLAGER]
    ,"共有者"=>[Data::SKL_FM,Data::TM_VILLAGER]
    ,"結社員"=>[Data::SKL_FM,Data::TM_VILLAGER]
    ,"妖魔"=>[Data::SKL_FAIRY,Data::TM_FAIRY]
    ,"ハムスター人間"=>[Data::SKL_FAIRY,Data::TM_FAIRY]
    ,"囁き狂人"=>[Data::SKL_WHISPER,Data::TM_WOLF]
    ,"Ｃ国狂人"=>[Data::SKL_WHISPER,Data::TM_WOLF]
    ,"聖痕者"=>[Data::SKL_STIGMA,Data::TM_VILLAGER]
    ,"狂信者"=>[Data::SKL_FANATIC,Data::TM_WOLF]
    ,"共鳴者"=>[Data::SKL_FM_WIS,Data::TM_VILLAGER]
    ,"天魔"=>[Data::SKL_FRY_WIS,Data::TM_FAIRY]
    ,"コウモリ人間"=>[Data::SKL_FRY_WIS,Data::TM_FAIRY]
    ,"呪狼"=>[Data::SKL_WOLF_CURSED,Data::TM_WOLF]
    ,"智狼"=>[Data::SKL_WISEWOLF,Data::TM_WOLF]
    ,"悪戯妖精"=>[Data::SKL_PIXY,Data::TM_FAIRY]
    ,"ピクシー"=>[Data::SKL_PIXY,Data::TM_FAIRY]
    ,"キューピッド"=>[Data::SKL_QP,Data::TM_LOVERS]
    ,"今週のトイレ当番"=>[Data::SKL_FM,Data::TM_VILLAGER]//静寂
    ];
  protected $SKL_FOOL = [
     "ただの人"=>[Data::SKL_VILLAGER,Data::TM_VILLAGER]
    ,"おおかみ"=>[Data::SKL_WOLF,Data::TM_WOLF]
    ,"エスパー"=>[Data::SKL_SEER,Data::TM_VILLAGER]
    ,"イタコ"=>[Data::SKL_MEDIUM,Data::TM_VILLAGER]
    ,"人狼スキー"=>[Data::SKL_LUNATIC,Data::TM_WOLF]
    ,"ストーカー"=>[Data::SKL_GUARD,Data::TM_VILLAGER]
    ,"夫婦"=>[Data::SKL_FM,Data::TM_VILLAGER]
    ,"ハム"=>[Data::SKL_FAIRY,Data::TM_FAIRY]
    ,"人狼教神官"=>[Data::SKL_WHISPER,Data::TM_WOLF]
    ,"痣もち"=>[Data::SKL_STIGMA,Data::TM_VILLAGER]
    ,"人狼教信者"=>[Data::SKL_FANATIC,Data::TM_WOLF]
    ,"おしどり夫婦"=>[Data::SKL_FM_WIS,Data::TM_VILLAGER]
    ,"コウモリ"=>[Data::SKL_FRY_WIS,Data::TM_FAIRY]
    ,"逆恨み狼"=>[Data::SKL_WOLF_CURSED,Data::TM_WOLF]
    ,"グルメ"=>[Data::SKL_WISEWOLF,Data::TM_WOLF]
    ,"イタズラっ子"=>[Data::SKL_PIXY,Data::TM_FAIRY]
    ];
  protected $DT_NORMAL = [
     '処刑された。'=>['.+(\(ランダム投票\)|投票した。)(.+) は村人達の手により処刑された。',Data::DES_HANGED]
    ,'刑された……'=>['.+(\(ランダム投票\)|投票した) ?(.+) は村人の手により処刑された……',Data::DES_HANGED]
    ,'突然死した。'=>['^( ?)(.+) は、突然死した。',Data::DES_RETIRED]
    ,'発見された。'=>['(.+)朝、 ?(.+) が無残.+',Data::DES_EATEN]
    ,'後を追った。'=>['^( ?)(.+) は(絆に引きずられるように|哀しみに暮れて) .+ の後を追った。',Data::DES_SUICIDE]
  ];
  protected $DT_FOOL = [
     'ち殺された。'=>['.+投票(した（らしい）。|してみた。) ?(.+) は村人達によってたかってぶち殺された。',Data::DES_HANGED]
    ,'ぶっ倒れた。'=>['^( ?)(.+) は、ぶっ倒れた。',Data::DES_RETIRED]
    ,'ったみたい。'=>['',Data::DES_EATEN]
    ,'えを食った。'=>['^( ?)(.+) は .+ の巻き添えを食った。',Data::DES_SUICIDE]
  ];
}
