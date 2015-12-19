<?php

class Connect_DB
{
  private $pdo;

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

  function prepare_sql($sql)
  {
    return $this->pdo->prepare($sql);
  }

}
