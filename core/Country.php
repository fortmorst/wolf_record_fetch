<?php

abstract class Country
{
  protected  $cid
            ,$url_org
            ,$url
            ,$queue
            ,$db
            ,$policy
            ,$is_evil
            ,$village
            ,$fetch
            ,$cast
            ,$user
            ,$users = []
            ,$doppel = []
            ,$syswords =[]
            ,$cid_parent
            ;

  function __construct($id,$url,$policy,$is_evil,$cid_parent,$queue)
  {
    $this->cid = $id;
    $this->url_org = str_replace("%n","",$url);
    $this->queue = $queue;
    if($policy !== null)
    {
      $this->policy = (int)$policy;
    }
    $this->is_evil = $is_evil;
    if($cid_parent !== null)
    {
      $this->cid_parent = (int)$cid_parent;
    }
    $this->db = new Connect_DB();
  }

  function insert()
  {
    $this->db->connect();
    $this->make_doppel_array();
    $this->fetch = new simple_html_dom();
    //テストの場合
    //$Data_Test = new Data_Test();
    //村番号順に挿入
    foreach($this->queue as $vno)
    {
      $this->url = $this->url_org.$vno;
      if(!$this->insert_village($vno))
      {
        $this->output_comment("fetch_error",__function__);
        $this->fetch->clear();
        continue;
      }
      $this->fetch->clear();
      //テストの場合
      //$Data_Test->check_from_DB($this->cid,$this->village,$this->users);
      continue;
      //村を挿入する
      $vid = $this->db->insert_db($this->cid,$this->village,$this->users);
      if($vid !== false)
      {
        if($this->village->wtmid !== 0)
        {
          $str = "☕️";
        }
        else
        {
          $str = "☕️💃";
        }
        echo "{$str}{$vid} / {$this->village->vno} {$this->village->name} を取得しました。".PHP_EOL;
        //キューに村番号がある場合は削除する
        if($this->db->check_vno_in_queue($this->cid,$vno))
        {
          $sql = "DELETE FROM `village_queue` where `cid`={$this->cid} AND vno={$vno}";
          $this->db->query($sql);
        }
      }
    }
    $this->db->disconnect();
  }

  protected function insert_village($vno)
  {
    $this->village = new Village($vno);

    if($this->fetch_village() === false)
    {
      //廃村村のユーザ初期化
      $this->users = [];
    }
    else
    {
      $this->insert_users();
      $this->check_role();
    }
    var_dump($this->village->get_vars());

    return($this->village->is_valid())? true : false;
  }
  protected function insert_as_ruin()
  {
    $this->village->days = 0;
    $this->village->rglid = Data::RGL_RUIN;
    $this->village->nop = 1;
    $this->village->wtmid = Data::TM_RUIN;
    $this->village->rgl_detail = '1,';

    $this->output_comment("ruin_prologue",__function__);
  }
  protected function check_ruin()
  {
    $info = 'p.info';
    $infosp = 'p.infosp';

    if(count($this->fetch->find($info)) <= 1 && count($this->fetch->find($infosp)) === 0)
    {
      return false;
    }
    else
    {
      return true;
    }
  }
  protected function is_sysword_existed($rp)
  {
    //親国idが指定されている場合はそちらで引く
    if($this->cid_parent !== null)
    {
      $cid = $this->cid_parent;
    }
    else
    {
      $cid = $this->cid;
    }

    $sql = "SELECT `sn`.`id` FROM `sysword` `sn` JOIN `country_sysword` ON `sysid` = `sn`.`id` WHERE `cid` = {$cid} AND `sn`.`name` = '{$rp}'";
    $stmt = $this->db->query($sql);
    $stmt = $stmt->fetch();

    return($stmt !== false)? $stmt['id'] : false;
  }
  protected function fetch_sysword($rp)
  {
    $sysid = $this->is_sysword_existed($rp);
    //該当言い換えがなければデフォルト言い換えを使用する
    if($sysid === false)
    {
      $this->output_comment("undefined",__FUNCTION__,$rp);
      $rp = Data::RP_DEFAULT;
      if(isset($this->syswords[$rp]))
      {
        return;
      }
      $sysid = Data::RP_DEFAULT_ID;
    }
    $this->syswords[$rp] = [];
    $this->make_sysword_set($rp,$sysid);
  }
  abstract protected function make_sysword_set($rp,$sysid);
  protected function make_sysword_name_orgid_set($table,$sysid)
  {
    $list = [];
    $sql = "SELECT `m`.`name`,`m`.`orgid` FROM `mes_{$table}` `m` JOIN `mes_{$table}_sysword` `ms` ON `ms`.`msid` = `m`.`id` WHERE `ms`.`sysid` = {$sysid}";
    $stmt = $this->db->query($sql);
    foreach($stmt as $item)
    {
      $list[$item['name']] = (int)$item['orgid'];
    }
    return $list;
  }
  protected function make_sysword_dtsys_set($sysid)
  {
    $list = [];
    $sql = "SELECT `m`.`name`,`regex`,`orgid` FROM `mes_dt_sys` `m` JOIN `destiny` `d` ON `orgid` = `d`.`id` JOIN `mes_dt_sys_sysword` `ms` ON `ms`.`msid` = `m`.`id` WHERE `ms`.`sysid`={$sysid}";
    $stmt = $this->db->query($sql);
    foreach($stmt as $item)
    {
      $list[$item['name']] = ['regex'=>$item['regex'],'dtid'=>(int)$item['orgid']];
    }
    return $list;
  }
  protected function insert_users()
  {
    $this->users = [];
    foreach($this->cast as $person)
    {
      $this->user = new User();
      $this->fetch_users($person);
      if(!$this->user->is_valid())
      {
        $this->output_comment('n_user',__function__,$this->user->persona);
      }
      //エラーでも歯抜けが起きないように入れる
      $this->users[] = $this->user;
    }
  }
  protected function check_role()
  {
    $roles = [];
    foreach($this->users as $user)
    {
      if($user->tmid !== Data::TM_ONLOOKER)
      {
        $this->village->nop++;
        $roles[] = $user->sklid;
      }
    }
    sort($roles);
    $this->make_rgl_detail(implode(',',$roles));
  }
  protected function make_rgl_detail($rgl)
  {
    $this->village->rgl_detail = $rgl.',';

    //既に特殊ルールが挿入されている村はスキップ
    if($this->village->rglid === null)
    {
      //一番大きいsklidを取得
      $max = (int)mb_substr($rgl,mb_strrpos($rgl,',')+1);
      $this->make_rglid($max,$rgl.',');
    }
  }
  protected function make_rglid_over_16($max,$rgl)
  {
    switch($max)
    {
      case Data::SKL_WHISPER: //C,聖痕入りC
        if(mb_strpos($rgl,Data::SKL_STIGMA) !== false)
        {
          $pattern = '(1,){8,}2,3,4,(7,){3,}9,12,';
          $rglid = Data::RGL_C_ST;
        }
        else
        {
          $pattern = '(1,){7,}2,3,4,((5,){2}(7,){3,}|(7,){3,}(8,){2})12,';
          $rglid = Data::RGL_C;
        }
        break;
      case Data::SKL_STIGMA: //試験壱、聖痕入りG
        if(mb_strpos($rgl,Data::SKL_STIGMA.','.Data::SKL_STIGMA) !== false || mb_strpos($rgl,Data::SKL_LUNATIC.','.Data::SKL_LUNATIC) !== false)
        {
          $pattern = '(1,){7,}2,3,4,((5,){2}(6,){2}(7,){3,}9,|6,(7,){3,}(9,){2})';
          $rglid = Data::RGL_TES1;
        }
        else
        {
          $pattern = '(1,){8,}2,3,4,6,(7,){3}9,';
          $rglid = Data::RGL_G_ST;
        }
        break;
      case Data::SKL_FM_WIS: //F
        $pattern = '(1,){7,}2,3,4,6,(7,){3,}(8,){2}';
        $rglid = Data::RGL_F;
        break;
      case Data::SKL_WOLF: //F,G
        if(mb_strpos($rgl,','.Data::SKL_FM) !== false)
        {
          $pattern = '(1,){7,}2,3,4,(5,){2}6,(7,){3,}';
          $rglid = Data::RGL_F;
        }
        else
        {
          $pattern = '(1,){9,}2,3,4,6,(7,){3,}';
          $rglid = Data::RGL_G;
        }
        break;
      case Data::SKL_LUNA_WIS: //叫迷が一人の時はF狂扱い
        //聖痕二人なら試験壱
        if(mb_strpos($rgl,Data::SKL_STIGMA.','.Data::SKL_STIGMA) !== false)
        {
          $pattern = '(1,){7,}2,3,4,(7,){3,}(9,){2}13,';
          $rglid = Data::RGL_TES1;
        }
        else if(mb_strpos($rgl,Data::SKL_STIGMA) !== false)
        {
          $pattern = '(1,){8,}2,3,4,(7,){3,}9,13,';
          $rglid = Data::RGL_G_ST;
        }
        else if(mb_strpos($rgl,','.Data::SKL_FM) !== false || mb_strpos($rgl,','.Data::SKL_FM_WIS) !== false)
        {
          $pattern = '(1,){7,}2,3,4,((5,){2}(7,){3,}|(7,){3,}(8,){2})13,';
          $rglid = Data::RGL_F;
        }
        else
        {
          $pattern = '(1,){9,}2,3,4,(7,){3,}13,';
          $rglid = Data::RGL_G;
        }
        break;
    }
    return [$pattern,$rglid];
  }
  protected function make_rglid_under_16($max,$rgl)
  {
    switch($max)
    {
      case Data::SKL_WHISPER: //少人数C狼2/3
        if(mb_strpos($rgl,Data::SKL_WOLF.','.Data::SKL_WOLF.','.Data::SKL_WOLF) !== false)
        {
          $pattern = '(1,){4,}2,(3,)?(4,)?((5,){2}(7,){3}|(7,){3}(8,){2}|(7,){3})12,';
          $rglid = Data::RGL_S_C3;
        }
        else
        {
          $pattern = '(1,){3,}2,(3,)?(4,)?((5,){2}(7,){2}|(7,){2}(8,){2}|(7,){2})12,';
          $rglid = Data::RGL_S_C2;
        }
        break;
      case Data::SKL_STIGMA: //試験壱
        $pattern = '((1,){7,}2,3,4,6,(7,){3}9|(1,){5,}2,3,4,(6,){2}(7,){2}9,)';
        $rglid = Data::RGL_TES1;
        break;
      case Data::SKL_FM_WIS: //少人数狼2/3
        if(mb_strpos($rgl,Data::SKL_WOLF.','.Data::SKL_WOLF.','.Data::SKL_WOLF) !== false)
        {
          $pattern = '(1,){2,}2,(3,)?(4,)?6,(7,){3}(8,){2}';
          $rglid = Data::RGL_S_3;
        }
        else
        {
          $pattern = '(1,){2,}2,(3,)?(4,)?6,(7,){2}(8,){2}';
          $rglid = Data::RGL_S_2;
        }
        break;
      case Data::SKL_WOLF: //少人数狼1/2/3
        if(mb_strpos($rgl,Data::SKL_WOLF.','.Data::SKL_WOLF.','.Data::SKL_WOLF) !== false)
        {
          $pattern = '(1,){2,}2,(3,)?(4,)?((5,){2}(6,)?(7,){3}|(6,)?(7,){3}(8,){2}|(6,)?(7,){3})';
          $rglid = Data::RGL_S_3;
        }
        else if(mb_strpos($rgl,Data::SKL_WOLF.','.Data::SKL_WOLF) !== false)
        {
          $pattern = '(1,){2,}2,(3,)?(4,)?((5,){2}(6,)?(7,){2}|(6,)?(7,){2}(8,){2}|(6,)?(7,){2})';
          $rglid = Data::RGL_S_2;
        }
        else
        {
          $pattern = '(1,){2,}2,(3,)?(4,)?(6,)?7,';
          $rglid = Data::RGL_S_1;
        }
        break;
      case Data::SKL_LUNA_WIS: //叫迷が一人の時はF狂扱い
        if(mb_strpos($rgl,Data::SKL_WOLF.','.Data::SKL_WOLF.','.Data::SKL_WOLF) !== false)
        {
          $pattern = '(1,){2,}2,(3,)?(4,)?((5,){2}(7,){3}|(7,){3}(8,){2}|(7,){3})13,';
          $rglid = Data::RGL_S_3;
        }
        else if(mb_strpos($rgl,Data::SKL_WOLF.','.Data::SKL_WOLF) !== false)
        {
          $pattern = '(1,){2,}2,(3,)?(4,)?((5,){2}(7,){2}|(7,){2}(8,){2}|(7,){2})13,';
          $rglid = Data::RGL_S_2;
        }
        else
        {
          $pattern = '(1,){2,}2,(3,)?(4,)?7,13,';
          $rglid = Data::RGL_S_1;
        }
        break;
    }
    return [$pattern,$rglid];
  }
  protected function make_rglid($max,$rgl)
  {
    switch(true)
    {
      case ($max > Data::SKL_QP_SELF): //求愛よりも大きいsklidはすべて特殊役職
      case ($max >= Data::SKL_QP && mb_strpos($rgl,Data::SKL_FAIRY)): //恋公両方いる
      case ($max < Data::SKL_WOLF): //恩恵人狼
        $this->village->rglid = Data::RGL_ETC;
        return;
      case ($max === Data::SKL_FAIRY): //妖魔入り
        $this->village->rglid = Data::RGL_E;
        return;
      case ($max >= Data::SKL_QP): //恋入り
        $this->village->rglid = Data::RGL_LOVE;
        return;
      case (mb_strpos($rgl,','.Data::SKL_SEER) === false): //占い師なし
        $this->village->rglid = Data::RGL_HERO;
        return;
      case (mb_strpos($rgl,Data::SKL_FANATIC) !== false): //試験弐
        $pattern = '(1,){5,}2,3,(4,)?((5,){2}(7,){2,}|(7,){2,}(8,){2}|(7,){2,})(9,)?11,';
        $rglid = Data::RGL_TES2;
        break;
      case (mb_strpos($rgl,Data::SKL_LUNA_WIS.','.Data::SKL_LUNA_WIS) !== false): //試験参
        $pattern = '(1,){4,}2,3,(4,)?((5,){2}(7,){2,}|(7,){2,}(8,){2}|(7,){2,})(9,)?(13,){2}';
        $rglid = Data::RGL_TES3;
        break;
      default:
        if($this->village->nop >= 16)
        {
          $rgl_ary = $this->make_rglid_over_16($max,$rgl);
        }
        else
        {
          $rgl_ary = $this->make_rglid_under_16($max,$rgl);
        }
        $pattern = $rgl_ary[0];
        $rglid = $rgl_ary[1];
        break;
    }
    //パターン照合
    if(mb_ereg_match($pattern,$rgl))
    {
      $this->village->rglid = $rglid;
    }
    else
    {
      $this->village->rglid = Data::RGL_ETC;
    }
  }
  protected function fetch_from_sysword($value,$column)
  {
    if(array_key_exists($value,$this->syswords[$this->village->rp][$column]))
    {
      $this->user->{$column} = $this->syswords[$this->village->rp][$column][$value];
    }
    else
    {
      $this->user->{$column} = null;
      $this->output_comment('undefined',__FUNCTION__,$value);
    }
  }
  protected function fetch_policy()
  {
    $rp = '/[^A-Z+]RP|[^Ａ-Ｚ+]ＲＰ|[^ァ-ヾ+]ネタ村|[^ァ-ヾ+]ランダ村|[^ァ-ヾ+]ラ神|[^ァ-ヾ+]ランダム|[^ァ-ヾ+]テスト村|薔薇村|百合村|[^ァ-ヾ+]グリード村|[^A-Z+]GR村|[^Ａ-Ｚ+]ＧＲ村|スゴロク/u';
    if(preg_match($rp,$this->village->name))
    {
      $this->village->policy = false;
    }
    else
    {
      $this->village->policy = true;
    }
  }
  protected function make_doppel_array()
  {
    //country_doppelからその国のdoppelリストを持ってくる
    $sql = "select d.player,d.country from doppel d join country_doppel cd on d.id=cd.dpid where cd.cid=".$this->cid;
    $stmt = $this->db->query($sql);

    //配列化
    foreach($stmt as $item)
    {
      $this->doppel[$item['player']] = $item['country'];
    }
  }
  protected function modify_player($player)
  {
    $player = $this->check_endspace($player);
    $player = $this->check_doppel($player);

    return $player;
  }
  protected function check_endspace($player)
  {
    //末尾に半角スペースがある場合は、読み込めるように変換する
    return mb_ereg_replace(' \z','&amp;nbsp;',$player);
  }
  protected function check_doppel($player)
  {
    if(array_key_exists($player,$this->doppel))
    {
      return $player.'&lt;'.$this->doppel[$player].'&gt;';
    }
    else
    {
      return $player;
    }
  }
  protected function check_syswords($value,$table)
  {
    return array_key_exists($value,$this->syswords[$this->village->rp][$table]);
  }
  protected function insert_onlooker()
  {
    $this->user->sklid = Data::SKL_ONLOOKER;
    $this->user->tmid = Data::TM_ONLOOKER;
    $this->user->dtid  = Data::DES_ONLOOKER;
    $this->user->end   = 1;
    $this->user->life  = 0.000;
    $this->user->rltid = Data::RSL_ONLOOKER;
  }
  protected function insert_alive()
  {
    $this->user->dtid = Data::DES_ALIVE;
    $this->user->end = $this->village->days;
    $this->user->life = 1.000;
  }
  protected function fetch_life()
  {
    if($this->user->dtid === Data::DES_ALIVE)
    {
      $this->user->life = 1.000;
    }
    else if($this->user->tmid === Data::TM_ONLOOKER)
    {
      $this->user->life = 0.000;
    }
    else
    {
      $this->user->life = round(($this->user->end-1) / $this->village->days,3);
    }
  }
  protected function output_comment($type,$function,$detail='')
  {
    switch($type)
    {
      case 'undefined':
        $str = '⚠️NOTICE-> '.$detail.' は未定義の値です。';
        break;
      case 'n_user':
        $str = '⚠️NOTICE->' .$detail.'は正常に取得できませんでした。';
        break;
      case 'fool':
        $str = "⚠️NOTICE-> 適当系の被襲撃者です。手動で入力して下さい。";
        break;
      case 'ruin_prologue':
        $str = 'note-> 開始前に廃村しています。';
        break;
      case 'ruin_midway':
        $str = 'note-> 進行中に廃村しています。非勝負扱いで取得します。';
        break;
      case 'fetch_error':
        $str = '❌ERROR-> 村を取得できませんでした。';
        break;
    }
    echo ">$function vno. ".$this->village->vno." / $str".PHP_EOL;
  }

  abstract protected function fetch_village();
  abstract protected function fetch_name();
  abstract protected function fetch_date();
  abstract protected function fetch_days();
  abstract protected function fetch_wtmid();

  abstract protected function make_cast();
  abstract protected function fetch_users($person);
}
