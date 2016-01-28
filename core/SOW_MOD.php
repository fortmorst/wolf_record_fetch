<?php

class SOW_MOD extends SOW
{
  protected function make_sysword_sql($rp)
  {
    return "select name,mes_sklid,mes_dtid,mes_wtmid from sysword where name='$rp'";
  }
  protected function make_sysword_set($values,$table,$name)
  {
    $list = [];

    if($table === 'mes_sklid')
    {
      $sql = "SELECT m.name,orgid,tmid from mes_sklid m join skill s on orgid = s.id where m.id in ($values)";
      $stmt = $this->db->query($sql);
      foreach($stmt as $item)
      {
        $list[$item['name']] = ['sklid'=>(int)$item['orgid'],'tmid'=>(int)$item['tmid']];
      }
    }
    else
    {
      $sql = "SELECT * from $table where id in ($values)";
      $stmt = $this->db->query($sql);
      foreach($stmt as $item)
      {
        $list[$item['name']] = (int)$item['orgid'];
      }
    }
    $GLOBALS['syswords'][$name]->{$table} = $list;
  }
  //エピに陣営や結末表記があるSOW用

  //protected function make_sysword_sql($rp)
  //{
    //return "select name,mes_sklid,mes_tmid,mes_dtid,mes_wtmid from sysword where name='$rp'";
  //}
  //protected function make_sysword_set($values,$table,$name)
  //{
    //$sql = "SELECT * from $table where id in ($values)";
    //$stmt = $this->db->query($sql);
    //$list = [];

    //foreach($stmt as $item)
    //{
      //$list[$item['name']] = (int)$item['orgid'];
    //}
    //$GLOBALS['syswords'][$name]->{$table} = $list;
  //}
}
