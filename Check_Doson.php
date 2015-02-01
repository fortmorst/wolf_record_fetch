<?php
ini_set('display_errors','on');
mb_internal_encoding("UTF-8");
mb_regex_encoding('UTF-8');

require __DIR__.'/../lib/ClassLoader.php';
$class_loader = new ClassLoader([__DIR__.'/core',__DIR__.'/country',__DIR__.'/rs',__DIR__.'/../lib']);
//connect
try{
  $pdo = new DBAdapter();
} catch (pdoexception $e){
  var_dump($e->getMessage());
  return;
}
//検索するID
$pl_you = 'fortmorst';
$pl_who = 'luxx';

//計測ここから
$time_start = microtime();

//こっちの方が早い
//vidのリストだけ取得した後、もう一度SQLを発行する
$sql = 'select vid from users where player=:pl_you OR player=:pl_who group by vid having count(*)>=2';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':pl_you',$pl_you,PDO::PARAM_STR);
$stmt->bindValue(':pl_who',$pl_who,PDO::PARAM_STR);
$stmt->execute();
$vid_table = [];
$holder_q = [];
$count = 0;
//プレースホルダに「?」と名前両方は使えないので、名前に合わせる
foreach($stmt as $item)
{
  $vid_table[] = (int)$item['vid'];
  $holder_q[] = ':vid'.$count;
  $count++;
}
$sql = 'select * from users where vid in ('.implode(",",$holder_q).') and (player=:pl_you OR player=:pl_who) ORDER BY vid';
$stmt = $pdo->prepare($sql);
foreach($vid_table as $key=>$value)
{
  $stmt->bindValue(':vid'.$key,$value,PDO::PARAM_INT);
}


//inで一括取得する場合
//$sql = 'select * from users where vid in(select vid from users where player=:pl_you OR player=:pl_who group by vid having count(*)>=2) and (player=:pl_you OR player=:pl_who) ORDER BY vid';
//$stmt = $pdo->prepare($sql);


$stmt->bindValue(':pl_you',$pl_you,PDO::PARAM_STR);
$stmt->bindValue(':pl_who',$pl_who,PDO::PARAM_STR);
$stmt->execute();

$table = $stmt->fetchAll();
var_dump($table);

//計測ここまで
$time_end = microtime();
var_dump($time_end-$time_start);
exit;
