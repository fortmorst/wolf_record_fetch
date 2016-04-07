<?php

class Ning extends Country
{
  private $url_epi;

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
    $this->village->rp = 'G国';
    $this->fetch_sysword($this->village->rp);

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
    $this->village->date = date("Y-m-d",preg_replace('/mes(.+)/','$1',$date));
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
  protected function remote_filesize($url)
  {
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
    $wtmid = $this->fetch->find('div.announce',-2)->plaintext;
    $wtmid = preg_replace("/\A([^\r\n]+)\r\n(.+)?\z/ms", "$1", $wtmid);
    if($this->check_syswords($wtmid,'wtmid'))
    {
      $this->village->wtmid = $GLOBALS['syswords'][$this->village->rp]->mes_wtmid[$wtmid];
    }
    else
    {
      $this->village->wtmid = Data::TM_RP;
      $this->output_comment('undefined',__FUNCTION__,$wtmid);
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
      $list[] = $this->user->persona;
      if($this->user->dtid === Data::DES_ALIVE)
      {
        unset($list[$key]);
      }
    }
    $this->fetch_from_daily($list);

    foreach($this->users as $user)
    {
      var_dump($user->get_vars());
      if(!$user->is_valid())
      {
        $this->output_comment('n_user',__function__,$user->persona);
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
    $this->fetch_rltid();

    if($person[2] === '生存')
    {
      $this->insert_alive();
    }
  }
  protected function fetch_sklid()
  {
    if($this->check_syswords($this->user->role,"sklid"))
    {
      $this->user->sklid = $GLOBALS['syswords'][$this->village->rp]->mes_sklid[$this->user->role]['sklid'];
      if($this->user->sklid === Data::SKL_LUNATIC)
      {
        $this->user->tmid = Data::TM_WOLF;
      }
      else
      {
        $this->user->tmid = $GLOBALS['syswords'][$this->village->rp]->mes_sklid[$this->user->role]['tmid'];
      }
    }
    else
    {
      $this->user->sklid= null;
      $this->user->tmid= null;
      $this->output_comment('undefined',__FUNCTION__,$this->user->role);
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
        $key_u = $this->fetch_key_u($list,$item);
        if($key_u === false)
        {
          continue;
        }
        $this->users[$key_u]->end = $i + 1;
        $this->users[$key_u]->life = round(($i) / $this->village->days,3);
      }
      $this->fetch->clear();
    }
  }
  protected function fetch_key_u($list,$item)
  {
    $destiny = trim(preg_replace("/\r\n/",'',$item->plaintext));
    $key = mb_substr(trim($item->plaintext),-8,8);

    if($this->check_syswords($key,'dt_sys'))
    {
      $regex = $GLOBALS['syswords'][$this->village->rp]->mes_dt_sys[$key]['regex'];
      $dtid  = $GLOBALS['syswords'][$this->village->rp]->mes_dt_sys[$key]['dtid']; 
    }
    else
    {
      return false;
    }

    $persona = trim(mb_ereg_replace($regex,'\2',$destiny,'m'));

    $key_u = array_search($persona,$list);
    if($key_u === false)
    {
      return false;
    }
    $this->users[$key_u]->dtid = $dtid;
    return $key_u;
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
}
