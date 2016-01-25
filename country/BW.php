<?php
class BW extends SOW_MOD
{
  protected function fetch_rp()
  {
    //情報欄から取得する
    $rp = trim($this->fetch->find('p.multicolumn_left',4)->plaintext);
    $this->village->rp = $rp;
    //言い換えリストに登録がなければ追加
    if(!isset($GLOBALS['syswords'][$rp]))
    {
      $this->fetch_sysword($rp);
    }
  }
  protected function fetch_policy()
  {
    parent::fetch_policy();
    if($this->village->policy === true)
    {
      $policy = $this->fetch->find('p.multicolumn_left',8)->plaintext;
      if(preg_match('/物語/',$policy))
      {
        $this->village->policy = false;
        $this->output_comment('rp',__function__);
      }
    }
  }
  protected function make_sysword_sql($rp)
  {
    return "select name,mes_sklid,mes_dtid,mes_wtmid from sysword where name='$rp'";
  }
  protected function make_sysword_set($values,$table,$name)
  {
    if($table === 'mes_sklid')
    {
      $sql = "SELECT m.name,orgid,tmid from mes_sklid m join skill s on orgid = s.id where m.id in ($values)";
      $stmt = $this->db->query($sql);
      $list = [];
      foreach($stmt as $item)
      {
        $list[$item['name']] = ['sklid'=>(int)$item['orgid'],'tmid'=>(int)$item['tmid']];
      }
    }
    else
    {
      $sql = "SELECT * from $table where id in ($values)";
      $stmt = $this->db->query($sql);
      $list = [];
      foreach($stmt as $item)
      {
        $list[$item['name']] = (int)$item['orgid'];
      }
    }
    $GLOBALS['syswords'][$name]->{$table} = $list;
  }
  protected function fetch_date()
  {
    $date = $this->fetch->find('td.time_info span',0)->plaintext;
    $date = mb_substr($date,0,10);
    $this->village->date = preg_replace('/(\d{4})\/(\d{2})\/(\d{2})/','\1-\2-\3',$date);
  }

  protected function make_cast()
  {
    $cast = $this->fetch->find('table.castlist tbody tr');
    array_shift($cast);
    $this->cast = $cast;
  }
  protected function insert_users()
  {
    $this->users = [];
    foreach($this->cast as $person)
    {
      $this->user = new User();
      $this->fetch_users($person);
      var_dump($this->user->get_vars());
      if(!$this->user->is_valid())
      {
        $this->output_comment('n_user',__function__);
      }
      $this->users[] = $this->user;
    }
  }
  protected function fetch_users($person)
  {
    $this->user->persona = trim($person->find('td',0)->plaintext);
    $this->fetch_player($person);
    $this->fetch_role($person);
    $this->fetch_end($person);
    $this->fetch_sklid();
    $this->fetch_rltid();
  }
  protected function fetch_role($person)
  {
    $role = $person->find('td',4)->plaintext;
    $this->user->role = mb_ereg_replace('\A(.+) \(.+\)(.+|)','\1',$role,'m');
  }
  protected function fetch_end($person)
  {
    $destiny = trim($person->find('td',3)->plaintext);
    if($destiny === '生存')
    {
      $this->user->dtid = Data::DES_ALIVE;
      $this->user->end = $this->village->days;
      $this->user->life = 1.000;
    }
    else
    {
      $dtid = mb_ereg_replace('\d+d(.+)','\1',$destiny);
      $this->user->dtid = $GLOBALS['syswords'][$this->village->rp]->mes_dtid[$dtid];
      $this->user->end = (int)mb_ereg_replace('(\d+)d.+','\1',$destiny);
      $this->user->life = round(($this->user->end-1) / $this->village->days,3);
    }
  }
}
