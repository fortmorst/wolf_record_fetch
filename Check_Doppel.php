<?php

require __DIR__.'/../lib/ClassLoader.php';
$class_loader = new ClassLoader([__DIR__.'/core',__DIR__.'/country',__DIR__.'/../lib']);

if(!isset($argv[1]))
{
  echo 'Nothing to check country.';
  exit(1);
}

$cid = $argv[1];
$ids = [];

    try{
      $pdo = new DBAdapter();
    } catch (pdoexception $e){
      var_dump($e->getMessage());
    }
$sql = "select player from doppel";
$stmt = $pdo->prepare($sql);
$stmt->execute();
foreach($stmt as $item)
{
  if(!in_array($item['player'],$ids))
  {
    $ids[] = $item['player'];
  }
}

foreach($ids as $player)
{
  $sql = "select cid,vno,v.name,player from users u JOIN village v on v.id=u.vid
     where cid=:cid and player=:player and player != 'master'";

  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(':cid',$cid,PDO::PARAM_INT);
  $stmt->bindValue(':player',$player,PDO::PARAM_STR);
  $stmt->execute();
  if(!empty($stmt))
  {
    foreach($stmt as $item)
    {
var_dump($item);
    }
  }
}

$pdo = null;
