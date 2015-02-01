<?php
//コンソールのphp対策
ini_set('display_errors','on');
error_reporting(E_ALL);
mb_internal_encoding("UTF-8");
mb_regex_encoding('UTF-8');

//autoloader読込
require __DIR__.'/../lib/ClassLoader.php';
$class_loader = new ClassLoader([__DIR__.'/core',__DIR__.'/country',__DIR__.'/rs',__DIR__.'/../lib']);

if(isset($argv[1]))
{
  //引数に渡した国だけ取得
  array_shift($argv);
  $countries = $argv;
}
else
{
  //定数から国名リスト取得
  $countries = new ReflectionClass('Cnt');
  $countries = array_keys($countries->getConstants());
}

foreach($countries as $ctry)
{
  echo '---'.$ctry.'-------'.PHP_EOL;
  ${$ctry} = new $ctry;
  try
  {
    ${$ctry}->insert();
  }
  catch(Exception $e)
  {

    echo '>ERROR '.$e->getMessage().PHP_EOL.'Caught Error->Skip'.PHP_EOL;
    unset(${$ctry});
    continue;
  }
  unset(${$ctry});
}
echo '----------------------'.PHP_EOL;
//queue取得
$fname = __DIR__.'/rs/queue.txt';
$fp = fopen($fname,'r');
echo 'Queue: '.fgets($fp).PHP_EOL;
fclose($fp);

echo '>>>END<<<'.PHP_EOL;
