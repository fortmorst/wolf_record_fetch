<?php

class Connect_DB
{
  private  $pdo;

  function __construct()
  {
  }

  function connect()
  {
    try{
      $this->pdo = new DBAdapter();
      return true;
    } catch (pdoexception $e){
      var_dump($e->getMessage());
      return false;
    }
  }
  function disconnect()
  {
    $this->pdo = null;
  }

  function prepare($sql)
  {
    return $this->pdo->prepare($sql);
  }
  function query($sql)
  {
    return $this->pdo->query($sql);
  }

  function insert_db($cid,$village,$cast)
  {
    $vid = $this->insert_village($village->get_vars(),$cid);
    
    if(isset($vid['id']))
    {
      if(!$this->insert_user((int)$vid['id'],$cast))
      {
        echo 'ERROR: Cannot insert users.'.PHP_EOL;
        return false;
      }
    }
    else
    {
      echo 'ERROR: Cannot fetch vid.'.PHP_EOL;
      return false;
    }
    return true;
  }
  private function check_not_duplicate($vno,$cid)
  {
    $sql = "SELECT vno FROM village where cid=".$cid." AND vno=:vno";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':vno',$vno,PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch();
  }

  private function insert_village($village,$cid)
  {
    $is_duplicate = $this->check_not_duplicate($village['vno'],$cid);
    if(!$is_duplicate['vno'])
    {
      $sql = "INSERT INTO village(cid,vno,name,date,nop,rglid,days,wtmid,rgl_detail) VALUES (".$cid.",:vno,:name,:date,:nop,:rglid,:days,:wtmid,:rgl_detail)";
      $stmt = $this->pdo->prepare($sql);
      foreach($village as $key=>$value)
      {
        switch($key)
        {
          case 'vno':
          case 'nop':
          case 'rglid':
          case 'days':
          case 'wtmid':
            $stmt->bindValue(':'.$key,$value,PDO::PARAM_INT);
            break;
          case 'name':
          case 'date':
          case 'rgl_detail':
            $stmt->bindValue(':'.$key,$value,PDO::PARAM_STR);
            break;
          default:
            echo 'ERROR: '.$village['vno'].' has unknown key in array.'.PHP_EOL;
            return;
        }
      }
      if($stmt->execute())
      {
        return $this->check_vid($village['vno'],$cid);
      }
      else
      {
        echo 'ERROR: No. '.$village['vno'].' not inserted.'.PHP_EOL;
        return false;
      }
    }
    else
    {
      echo 'NOTICE: vno.'.$village['vno'].' is already inserted.'.PHP_EOL;
      return false;
    }
  }

  private function check_vid($vno,$cid)
  {
    $sql = "SELECT id FROM village WHERE cid=".$cid." AND vno=:vno";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':vno',$vno,PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch();
  }

  private function insert_user($vid,$cast)
  {
    foreach($cast as $object)
    {
      $user = $object->get_vars();
      $sql = "INSERT INTO users (vid,persona,player,role,dtid,end,sklid,tmid,life,rltid) values (:vid,:persona,:player,:role,:dtid,:end,:sklid,:tmid,:life,:rltid)";
      $stmt = $this->pdo->prepare($sql);

      $stmt->bindValue(':vid',$vid,PDO::PARAM_INT);
      foreach($user as $key=>$value)
      {
        switch($key)
        {
          case 'dtid':
          case 'end':
          case 'sklid':
          case 'tmid':
          case 'rltid':
            $stmt->bindValue(':'.$key,$value,PDO::PARAM_INT);
            break;
          case 'persona':
          case 'player':
          case 'role':
          case 'life':
            $stmt->bindValue(':'.$key,$value,PDO::PARAM_STR);
            break;
          default:
            echo 'ERROR: '.$user['persona'].' has unknown key in array.'.PHP_EOL;
            return;
        }
      }
      if(!$stmt->execute())
      {
        echo 'ERROR:'.$user['persona'].' in vid:'.$vid.' could not inserted'.PHP_EOL;
        return false;
      }
    }
    return true;
  }

}
