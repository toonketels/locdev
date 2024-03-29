<?php

class Application
{
  protected $name;
  protected $arguments = array();
  protected $error = "";
  protected $commands = array();
  protected $currentCommand;
  private $settings;

  public function __construct($name)
  {
    $this->name = $name;
    $this->loadDefaultSettings();
    $this->init();
  }

  public function init()
  {
    global $argc;
    global $argv;
    // parse arguments
    $arguments = $argv;
    array_shift($arguments);
    $this->parseArguments($arguments);

    // Print errors if there at end of initialization.
    $this->printErrors();
  }


  /**
   * Loads default settings from a settings.ini file.
   */
  private function loadDefaultSettings()
  {
    $this->settings = parse_ini_file(__DIR__ . '/../settings.ini');
  }


  /**
   * Parses the arguments.
   * Checks if they are in the correct format.
   */
  private function parseArguments($arguments)
  {
    // one - are flogs
    // two --name=... are additional arguments
    // no -, --, this is an argumenta
    $args = array();
    $i = 0;
    $error = '';

    foreach ($arguments as $delta => $argument) {

     // check for argument of type --name=value
     if(substr($argument,0, 2) == '--') {

        $item = substr($argument, 2);
        $items = explode('=', $item);
        if(count($items) < 2) {

         $error .= "Arguments starting with -- should be in the for of --argname=value for $argument\n";

       } else {

         $args[$items[0]] = $items[1];
       }
      }

     // check for argument of type -a
     elseif(substr($argument, 0, 1) == '-') {

      $item = substr($argument, 1);
         if(strlen($item) != 1) {
           $error .= "Arguments starting with - should be in the form of -a (single char) for $argument\n";
         } else {
           $args[$item] = $item;
         }
      }

      // last type just command
      else {
        $args[$i] = $argument;
        $i++;
      }
    }

    if($error) {
      $this->error .= $error;
    }

    // extract current command
    if(isset($args[0])) {
      $this->currentCommand = $args[0];
      unset($args[0]);
    } else {
      $this->error = "Type \"$this->name help\" for help.\n";
      $this->terminate();
    }

    // set arguments
    $this->arguments = $args;
  }


  public function setCommand($name, $commandClass)
  {
    $this->commands[$name] = $commandClass;
  }


  /**
   * Print errors and return.
   */
  public function printErrors()
  {
    if(!empty($this->error)) {
      print $this->error;
      exit(1);
    }
  }


  public function run()
  {
    if(isset($this->commands[$this->currentCommand])) {
      // Create a command object
      $currentCommand = new $this->commands[$this->currentCommand]();
      $this->error .=  $currentCommand->setValues($this->arguments);
      $this->printErrors();
      $currentCommand->execute();
    } else {
      $this->error .= "Command $this->currentCommand does not exist. Type help for more info about commands.";
    }

    $this->terminate();
  }


  protected function terminate()
  {
    $this->printErrors();
    exit(0);
  }


}
