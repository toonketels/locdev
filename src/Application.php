<?php

class Application
{
  protected $name;
  protected $arguments = array();
  protected $error = "";
  protected $commands = array();
  protected $currentCommand;

  public function __construct($name)
  {
    $this->name = $name;
    $this->init();
    $this->printErrors();
  }

  public function init()
  {
    global $argc;
    global $argv;

    if($argc == 1) {
      $this->error = "Type \"$this->name help\" for help.\n";
      $this->terminate();
    }

    // parse arguments
    $arguments = $argv;
    array_shift($arguments);
    $this->parseArguments($arguments);
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
    $this->currentCommand = $args[0];
    unset($args[0]);
    // set arguments
    $this->arguments = $args;
  }


  public function setCommand($name, Command $command)
  {
    $this->commands[$name] = $command;
  }


  public function map() {

    $error = '';
    $arguments;

    // Check all required arguments are given
    $missing_required_arguments = $this->mapper->get_missing_arguments(array_keys($this->arguments));
    foreach ($missing_required_arguments as $arg_key => $arg_object) {
      $error .= 'Argument $arg_key is missing';
    }

    foreach ($this->arguments as $arg_key => $arg_value) {

      // check whether argument is valid
      if(!$this->mapper->is_valid_argument($arg_key)) {

        $error .= "$arg_key $arg_value is not a valid argument";
      } else {

        // Instanciate object
        $arg_class = $this->mapper->get_argument_class($arg_key);
        $argument = new $arg_class($arg_value);
        // Validate argument
        $pos_error = $argument->validate();
        if($pos_error) {
          $error .= $pos_error;
        } else {
          $arguments[] = $argument;
        }

      }
    }
    $this->error = $error;
    return $arguments;
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


  public function shutDown()
  {
    if(isset($this->commands[$this->currentCommand])) {
      $currentCommand = $this->commands[$this->currentCommand];
      $this->error .=  $currentCommand->setArguments($this->arguments);
      $this->printErrors();
      $currentCommand->execute();
    } else {
      $this->error .= "Command $this->currentCommand does not exist. Type help for more info about commands.";
      $this->printErrors();
    }
  }


  protected function terminate()
  {
    $this->printErrors();
    exit(0);
  }

}