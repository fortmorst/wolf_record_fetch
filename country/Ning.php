<?php

class Ning extends Country
{
  private $url_epi;
  private $skill =
    [
        "村人"  =>Data::SKL_VILLAGER
       ,"占い師"=>Data::SKL_SEER
       ,"霊能者"=>Data::SKL_MEDIUM
       ,"狩人"  =>Data::SKL_GUARD
       ,"人狼"  =>Data::SKL_WOLF
       ,"狂人"  =>Data::SKL_LUNATIC
    ]; 

  protected function fetch_village()
  {
    $this->fetch_from_pro();
    $this->fetch_from_epi();
  }

  protected function fetch_from_pro()
  {
    $this->fetch->load_file($this->url."&meslog=000_ready");
    sleep(1);

    $this->fetch_name();
    $this->fetch_date();
    $this->fetch_days();

    $this->fetch->clear();
  }
  protected function fetch_name()
  {
    $name = $this->fetch->find('title',0)->plaintext;
    $this->village->name = preg_replace('/人狼.+\d+ (.+)/','$1',$name);
  }
  protected function fetch_date()
  {
    $date = $this->fetch->find('div.ch1',0)->find('a',1)->name;
    $this->village->date = date("y-m-d",preg_replace('/mes(.+)/','$1',$date));
  }
  protected function fetch_days()
  {
    $url = preg_replace("/index\.rb\?vid=/","",$this->url_org);
    $this->url_epi = $url.$this->fetch->find('p a',-2)->href;
    $this->village->days = preg_replace("/.+=0(\d{2})_party/", "$1", $this->url_epi) + 1;
  }

  protected function fetch_from_epi()
  {
    $filesize = $this->remote_filesize($this->url_epi);
    if(!$filesize || $filesize > 1000000)
    {
      throw new Exception($this->village->vno.'ERROR: **エピローグが壊れています** 手動で取得後キューを削除して下さい。');
    }
    $this->fetch->load_file($this->url_epi);
    sleep(1);
    $this->make_cast();
    $this->fetch_wtmid();

    $this->fetch->clear();
  }
  protected function remote_filesize($url) {
      static $regex = '/^Content-Length: *+\K\d++$/im';
      if (!$fp = @fopen($url, 'rb')) {
          return false;
      }
      if (
          isset($http_response_header) &&
          preg_match($regex, implode("\n", $http_response_header), $matches)
      ) {
          return (int)$matches[0];
      }
      return strlen(stream_get_contents($fp));
  }

  protected function make_cast()
  {
    $cast = preg_replace("/\r\n/","",$this->fetch->find('div.announce',-1)->plaintext);
    //simple_html_domを抜けてきたタグを削除(IDに{}があるとbrやaが残る)
    $cast = preg_replace([ '/<br \/>/','/<a href=[^>]+>/','/<\/a>/' ],['','',''],$cast);
    $cast = explode('だった。',$cast);
    //最後のスペース削除
    array_pop($cast);
    $this->cast = $cast;
  }
  protected function fetch_wtmid()
  {
    $wtmid = mb_substr($this->fetch->find('div.announce',-2)->plaintext,0,3);
    switch($wtmid)
    {
      case '全ての': //村勝利
        $this->village->wtmid = Data::TM_VILLAGER;
        break;
      case 'もう人': //狼勝利
        $this->village->wtmid = Data::TM_WOLF;
        break;
      default:
        $this->output_comment('undefined',__function__,$wtmid);
        break;
    }
  }

  protected function insert_users()
  {
    $list = [];
    $this->users = [];
    foreach($this->cast as $key=>$person)
    {
      $this->user = new User();
      $this->fetch_users($person);
      $this->users[] = $this->user;
      //生存者を除く名前リストを作る
      $list[] = $this->user->persona;
      if($this->user->dtid === Data::DES_ALIVE)
      {
        unset($list[$key]);
      }
    }
    $this->fetch_from_daily($list);
    $this->fetch_life();

    foreach($this->users as $user)
    {
      if(!$user->is_valid())
      {
        $this->output_comment('n_user',__function__);
      }
    }
  }
  protected function fetch_users($person)
  {
    $person = preg_replace("/ ?(.+) （(.+)）、(生存|死亡)。(.+)$/", "$1#SP#$2#SP#$3#SP#$4", $person);
    $person = explode('#SP#',$person);
    $person[1] = $this->modify_player($person[1]);

    $this->user->persona = $person[0];
    $this->user->player  = $person[1];
    $this->user->role    = $person[3]; 

    $this->fetch_sklid();
    $this->fetch_tmid();
    $this->fetch_rltid();

    $this->is_alive($person[2]);
  }
  protected function is_alive($status)
  {
    if($status === '生存')
    {
      return true;
    }
    else
    {
      return false;
    }
  }

  protected function fetch_from_daily($list)
  {
    $days = $this->village->days -1; //初日=0
    for($i=1; $i<=$days; $i++)
    {
      $this->fetch->load_file($this->make_daily_url($i));
      sleep(1);
      $announce = $this->fetch->find('div.announce');
      foreach($announce as $item)
      {
        $destiny = mb_substr(trim($item->plaintext),-6,6);
        $destiny = preg_replace("/\r\n/","",$destiny);

        switch($destiny)
        {
          case "突然死した。":
            $persona = preg_replace("/^ ?(.+) は、突然死した。 ?/", "$1", $item->plaintext);
            $key = array_search($persona,$list);
            $this->users[$key]->dtid = Data::DES_RETIRED;
            break;
          case "処刑された。":
            $persona = preg_replace("/(.+\r\n){1,}\r\n(.+) は村人達の手により処刑された。 ?/", "$2", $item->plaintext);
            $key = array_search($persona,$list);
            $this->users[$key]->dtid = Data::DES_HANGED;
            break;
          case "発見された。":
            $persona = preg_replace("/.+朝、(.+) が無残.+\r\n ?/", "$1", $item->plaintext);
            $key = array_search($persona,$list);
            $this->users[$key]->dtid = Data::DES_EATEN;
            break;
          default:
            continue;
        }   
        $this->users[$key]->end = $i+1;
      }
      $this->fetch->clear();
    }
  }
  protected function make_daily_url($day)
  {
    if($day === $this->village->days-1)
    {
      $suffix = '_party';
    }
    else
    {
      $suffix = '_progress';
    }
    $day = str_pad($day,3,"0",STR_PAD_LEFT);

    return $this->url.'&meslog='.$day.$suffix;
  }
  protected function fetch_sklid()
  {
    $this->user->sklid = $this->skill[$this->user->role];
  }
  protected function fetch_tmid()
  {
    if($this->user->role === "人狼" || $this->user->role === "狂人")
    {
      $this->user->tmid = Data::TM_WOLF;
    }
    else
    {
      $this->user->tmid = Data::TM_VILLAGER;
    }
  }
  protected function fetch_life()
  {
    foreach($this->users as $key=>$user)
    {
      if(!$this->users[$key]->life)
      {
        $this->users[$key]->life = round(($this->users[$key]->end-1) / $this->village->days,3);
      }
    }
  }
  protected function fetch_rltid()
  {
    if($this->user->tmid === $this->village->wtmid)
    {
      $this->user->rltid = Data::RSL_WIN;
    }
    else
    {
      $this->user->rltid = Data::RSL_LOSE;
    }
  }
}
