<?php

$autoload_location = '/vendor/autoload.php';
$tries=0;
while (!is_file(__DIR__.$autoload_location)) 
{ 
	$autoload_location='/..'.$autoload_location;
	$tries++;
	if ($tries>10) die("Error trying to find autoload file try to make a composer update first\n");
}
require_once __DIR__.$autoload_location;
//require_once __DIR__.'/conf/config.php';

use \Doctrine\DBAL\Configuration;
use Omatech\Editora\Generator\ReverseEngineerator;
use Omatech\Editora\Utils\Strings;

ini_set("memory_limit", "5000M");
set_time_limit(0);

$options_array = getopt(null, ['from::', 'to::'
	, 'dbhost:', 'dbuser:', 'dbpass:', 'dbname:'
	, 'outputformat:', 'outputfile:'
	, 'help']);
//print_r($options_array);
if (isset($options_array['help'])) {
	echo 'Takes out the editora structure and generates a compatible generator file

From parameters:
--from= db4 | db5 (only db4 supported by now)
--dbhost= database host
--dbuser= database user
--dbpass= database password 
--dbname= database name 

To parameters:
--to= file 
--outputformat= (excel, json, array) (only array and json supported by now)
--outputfile= name of the file to export

Others:
--help this help!

example: 
	
1) Take info from an existing editora and dump array to file
php reverse-engineer-editora.php --from=db4 --dbhost=localhost --dbuser=root --dbpass=xxx --dbname=intranetmutua --outputformat=array --outputfile=../sql/reverse_engineer_editora_array.php
';
die;
}

if (!isset($options_array['from']) || !isset($options_array['to'])) {
	echo "Missing from or to parameters, use --help for help!\n";
	die;
}

$from_version = 4;
if ($options_array['from'] == 'db5') {
	$from_version = 5;
}

if ($from_version==5) die ("DB5 not supported yet!\n");

$dbal_config = new \Doctrine\DBAL\Configuration();

$conn_from = null;
if ($options_array['from'] == 'db4' || $options_array['from'] == 'db5') {
	$connection_params = array(
		'dbname' => $options_array['dbname'],
		'user' => $options_array['dbuser'],
		'password' => $options_array['dbpass'],
		'host' => $options_array['dbhost'],
		'driver' => 'pdo_mysql',
		'charset' => 'utf8'
	);

	$conn_from = \Doctrine\DBAL\DriverManager::getConnection($connection_params, $dbal_config);
}

if ($conn_from)
{
	$reverseengineerator=new \Omatech\Editora\Generator\ReverseEngineerator($conn_from, array());
	$data=$reverseengineerator->reverseEngineerEditora();
	//echo \Omatech\Editora\Utils\Strings::array2string($data);
	print_r($data);
	echo $reverseengineerator->arrayToCode($data);
	die;
}
else
{
	die("DB from connection not set, see help for more info\n");
}

if ($options_array['outputformat']=='array')
{
	if (isset($options_array['outputfile']))
	{

	}
	else
	{
		die("Missing outputfile see help for more info\n");
	}
}
else
{
	die("Only array outputformat supported see help for more info\n");
}


