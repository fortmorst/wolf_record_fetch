<?php
//コンソールのphp対策
ini_set('display_errors','on');
error_reporting(E_ALL);
mb_internal_encoding("UTF-8");
mb_regex_encoding('UTF-8');

//autoloader読込
require __DIR__.'/../lib/ClassLoader.php';
$class_loader = new ClassLoader([__DIR__.'/core',__DIR__.'/country',__DIR__.'/rs',__DIR__.'/../lib']);


//DB接続
$db = new Connect_DB();
$db->connect();
$sql = "select `id`,`name`,`class`,`check_type`,`url`,`url_log`,`policy`,`is_evil`,`talk_title` from `country` where `class`";

//引数から国リスト取得orDBから国リスト取得
if(isset($argv[1]))
{
  //引数に渡した国だけ取得
  $countries = "='$argv[1]'";
}
else
{
  //定数から国名リスト取得
  $countries = " is not NULL";
}

//国詳細取得
$sql = $sql.$countries;
$stmt = $db->query($sql);
$stmt = $stmt->fetchAll();

//DB切断
$db->disconnect();

//村番号が指定されていればそれだけ取得する
if(isset($argv[2]))
{
  foreach($argv as $key=>$item)
  {
    if($key < 2) continue;
    $stmt[0]['queue'][] = (int)$item;
  }
}
else
{
  //更新チェック
  $check_village = new Check_Village($stmt);
  $stmt = $check_village->check($stmt);
}

//国ごとに取得開始
if(!empty($stmt))
{
  foreach($stmt as $item)
  {
    try
    {
      //村取得
      $country = $item['class'];
      echo "---{$item['name']}-------".PHP_EOL;
      ${$country} = new $country((int)$item['id'],$item['url'],$item['policy'],(bool)$item['is_evil'],$item['queue']);
      ${$country}->insert();
      unset(${$country});
    }
    catch(Exception $e)
    {
      echo ">🚫  {$e->getMessage()} ->この国をスキップします。".PHP_EOL;
      if(isset(${$country}))
      {
        unset(${$country});
      }
      continue;
    }
  }
}

echo "----------------------".PHP_EOL.">>>END<<<".PHP_EOL;
