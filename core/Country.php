<?php

abstract class Country
{
  protected  $check
            ,$cid
            ,$url
            ,$policy
            ,$village
            ,$fetch
            ,$cast
            ,$user
            ,$users = []
            ,$doppel = []
            ;
            
  protected function __construct($cid,$url_vil,$url_log)
  {
    $this->check = new Check_Village($cid,$url_vil,$url_log); 
    $this->cid = $cid;
    $this->url = $url_vil;
  }

  function insert()
  {
    $list = $this->check->get_village();
    $list = [1049,1039,1037,1036,1034,1032,1020,1018,1010,1008,990,989,986,984,981,980,964,955,942,881,876,865,850,832,828,817,816,804,801,796,787,786,753,750,747,746,743,718,714,705,693,655,636,616,614,609,608,590,588,587,584,582,581,572,568,567,561,546,542,541,538,531,526,517,511,509,501,485,480,479,472,462,461,458,450,447,444,421,414,411,400,383,376,375,374,373,368,358,357,355,350,337,318,312,305,291,279,259,254,245,240,237,233,225,220,217,215,206,204,203,200,195,193,191,189,172,167,163,162,155,153,152,151,150,149,148,144,142,137,135,133,129,126,121,119,117,116,114,110,109,107,104,101,99,98,94,89,83,82,78,77,75,68,63,61,57,56,55,54,53,51,45,43,41,40,36,34,30,27,16,11];
    //$list = [472];
    if(!$list)
    {
      $this->check->remove_queue();
      return;
    }
    $this->make_doppel_array();
    $this->fetch = new simple_html_dom();
    //$kick = [15,16,26,35,40,43];
    foreach($list as $vno)
    {
      //if(array_search($vno,$kick)  !== false)
      //{
        //echo '※: '.$vno.' is kicked by $kick list.'.PHP_EOL;
        //continue;
      //}
      if(!$this->insert_village($vno))
      {
        echo 'ERROR: '.$vno.'could not fetched.'.PHP_EOL;
        $this->fetch->clear();
        //continue;
      }
      $this->fetch->clear();
      //continue;
      $db = new Insert_DB($this->cid);
      if(!$db->connect())
      {
        return;
      }
      if($db->insert_db($this->village,$this->users))
      {
        echo '★'.$this->village->vno.'. '.$this->village->name.' is all inserted.'.PHP_EOL;
      }
      $db->disconnect();
    }
    $this->check->remove_queue();
  }

  protected function insert_village($vno)
  {
    $this->village = new Village($this->cid,$vno);

    $this->fetch_village();
    $this->insert_users();
    $this->check_role();

    //var_dump($this->village->get_vars());
    if($this->village->is_valid())
    {
      return true;
    }
    else
    {
      return false;
    }
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
        $this->output_comment('n_user');
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
    //echo ' nop=>'.$this->village->nop.' rglid=>'.$this->village->rglid.PHP_EOL;
  }
  protected function make_rgl_detail($rgl)
  {
    $this->village->rgl_detail = $rgl.',';
    //echo '>'.$this->village->vno.': rgl=>'.$rgl;

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
  protected function fetch_policy()
  {
    $rp = '/[^A-Z+]RP|[^Ａ-Ｚ+]ＲＰ|[^ァ-ヾ+]ネタ村|[^ァ-ヾ+]ランダ村|[^ァ-ヾ+]ラ神|[^ァ-ヾ+]ランダム|[^ァ-ヾ+]テスト村|薔薇村|百合村|[^ァ-ヾ+]グリード村|[^A-Z+]GR村|[^Ａ-Ｚ+]ＧＲ村|スゴロク/u';
    if(preg_match($rp,$this->village->name))
    {
      $this->village->policy = false;
      $this->output_comment('rp');
    }
    else
    {
      $this->village->policy = true;
    }
  }
  protected function make_doppel_array()
  {
    try{
      $pdo = new DBAdapter();
    } catch (pdoexception $e){
      var_dump($e->getMessage());
      exit;
    }
    //country_doppelからその国のdoppelリストを持ってくる
    $sql = "select d.player,d.country from doppel d join country_doppel cd on d.id=cd.dpid where cd.cid=".$this->cid;
    $stmt = $pdo->query($sql);

    //配列化
    foreach($stmt as $item)
    {
      $this->doppel[$item['player']] = $item['country'];
    }
    $pdo = null;
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
  protected function output_comment($type,$detail='')
  {
    switch($type)
    {
      case 'rp':
        $str =  'is guessed RP.';
        break;
      case 'undefined':
        $str = 'has undefined ->'.$detail;
        break;
      case 'n_user':
        $str = 'NOTICE:'.$this->user->persona.' could not fetched.';
        break;
    }
    echo '>'.$this->village->vno.' '.$str.PHP_EOL;
  }

  abstract protected function fetch_village();
  abstract protected function fetch_name();
  abstract protected function fetch_date();
  abstract protected function fetch_days();
  abstract protected function fetch_wtmid();

  abstract protected function make_cast();
  abstract protected function fetch_users($person);
}
