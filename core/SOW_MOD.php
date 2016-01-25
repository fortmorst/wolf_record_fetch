<?php

class SOW_MOD extends SOW
{
  //エピに陣営や結末表記があるSOW用

  protected function make_sysword_sql($rp)
  {
    return "select name,mes_sklid,mes_tmid,mes_dtid,mes_wtmid from sysword where name='$rp'";
  }
  protected function make_sysword_set($values,$table,$name)
  {
    $sql = "SELECT * from $table where id in ($values)";
    $stmt = $this->db->query($sql);
    $list = [];

    foreach($stmt as $item)
    {
      $list[$item['name']] = (int)$item['orgid'];
    }
    $GLOBALS['syswords'][$name]->{$table} = $list;
  }
}
