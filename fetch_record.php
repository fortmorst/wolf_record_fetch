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

$sql = "select id,class,url,url_log,ruin from country where class";

//引数から国リスト取得orDBから国リスト取得
if(isset($argv[1]))
{
  //引数に渡した国だけ取得
  $countries = '='.'"'.$argv[1].'"';
}
else
{
  //定数から国名リスト取得
  $countries = ' is not NULL';
}

//国詳細取得
$sql = $sql.$countries;
$stmt = $db->prepare_sql($sql);
$stmt->execute();


//更新チェック
echo '---'.$ctry.'-------'.PHP_EOL;
try
{
  $check_village = new CheckVillage($stmt);
  $stmt = $check_village->check($stmt);
  foreach($stmt as $item)
  {
    //村取得
    $country = $item['class'];
    ${$country} = new $country;
    ${$country}->insert();
    unset(${$country});
  }
}
catch(Exception $e)
{

  echo '>ERROR '.$e->getMessage().PHP_EOL.'Caught Error->Skip'.PHP_EOL;
  if(isset(${$country}))
  {
    unset(${$country});
  }
  continue;
}

echo '----------------------'.PHP_EOL.'>>>END<<<'.PHP_EOL;


//
//更新のある国だけ読み込む
//DB切断
$db->disconnect();
