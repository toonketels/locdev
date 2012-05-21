#! /Applications/MAMP/bin/php/php5.3.6/bin/php
<?php
require(__DIR__ . '/src/Argument.php');
require(__DIR__ . '/src/Application.php');
require(__DIR__ . '/src/Settings.php');
require(__DIR__ . '/src/Command.php');
require(__DIR__ . '/cmd/CreateCommand.php');
require(__DIR__ . '/cmd/HelpCommand.php');


$app = new Application("wstart.php");

// Map commands
$app->setCommand('help', 'HelpCommand');
$app->setCommand('create', 'CreateCommand');

$app->run();

?>
