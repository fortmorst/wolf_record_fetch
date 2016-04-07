<?php

class Data_Test
{
  private  $vid
          ,$db
          ;

  function __construct()
  {
    $this->db = new Connect_DB();
  }
  function check_from_DB($cid,$village,$users)
  {
    $this->db->connect();

    $village = $this->modify_village($village);
    $users = $this->modify_users($users);

    $village_db = $this->fetch_village_from_DB($cid,$village["\0Village\0vno"]);
    $users_db   = $this->fetch_users_from_DB();

    $this->compare_village_data($village,$village_db);
    $this->compare_users_data($users,$users_db);

    $this->db = null;
  }
  private function modify_village($village)
  {
    $village = (array)$village;
    unset($village["\0Village\0evil_rgl"],$village["\0Village\0rp"],$village["\0Village\0policy"],$village["\0Village\0add_winner"],$village["\0Village\0is_card"]);
    return $village;
  }
  private function modify_users($users)
  {
    $users = (array)$users;
    foreach($users as &$user)
    {
      $user = (array)$user;
    }
    return $users;
  }
  private function fetch_village_from_DB($cid,$vno)
  {
    $sql = "select * from village where cid=$cid and vno=$vno";
    $stmt = $this->db->query($sql);
    $stmt = $stmt->fetch();

    $this->vid = $stmt['id'];
    unset($stmt['cid'],$stmt['vid']);

    return $stmt;
  }
  private function fetch_users_from_DB()
  {
    $sql = "select * from users where vid=$this->vid";
    $stmt = $this->db->query($sql);
    $stmt = $stmt->fetchAll();

    //double->stringに変換した際桁が合わなくなる問題の対策
    foreach($stmt as &$item)
    {
      if($item['life'] === "0.000")
      {
        $item['life'] = "0";
      }
      else
      {
        do {
          if(mb_substr($item['life'],-1) === "0")
          {
            $item['life'] = mb_substr($item['life'],0,-1);
          }
        } while (mb_substr($item['life'],-1) === "0");
      }
    }

    return $stmt;
  }
  private function compare_village_data($original,$db)
  {
    $cp= array_diff($original,$db);
    if(!empty($cp))
    {
      echo '★★DBのデータと異なります->'.$original["\0Village\0vno"].".".$original["\0Village\0name"].PHP_EOL;
      var_dump($cp);
    }
  }
  private function compare_users_data($original,$db)
  {
    $count = count($original) -1;
    for($i = 0; $i<=$count; $i++)
    {
      $cp= array_diff($original[$i],$db[$i]);
      if(!empty($cp))
      {
        echo '★★DBのデータと異なります->'.$original[$i]["\0User\0persona"].PHP_EOL;
        var_dump($cp);
      }
    }
  }
}
