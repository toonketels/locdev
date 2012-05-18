#! /Applications/MAMP/bin/php/php5.3.6/bin/php
<?php
require(__DIR__ . '/Argument.php' );

$arg = new Argument('test', 'This is a test', function($value = FALSE) {
  return ("test" == $value);
});


function validated($valid) {
  print $valid ? "This is valid\n" : "This is NOT valid\n";
}

validated($arg->validate("test"));
validated($arg->validate("Hello world"));

echo $arg->getName();

exit(0);






?>
