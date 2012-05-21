<?php

class Settings
{
  private $settings;

  public function __construct($arguments)
  {
    $this->settings = parse_ini_file(__DIR__ . '/../settings.ini');
    $this->convertArgumentsToSettings($arguments);
  }

  private function convertArgumentsToSettings($arguments)
  {
    foreach ($arguments as $argument) {
      $this->settings[$this->getMachineName($argument->getName())] = $argument->value;
    }
  }

  private function getMachineName($argumentName)
  {
    return str_replace(' ', '_', strtolower(trim($argumentName)));
  }

  public function getValueFor($name)
  {
    if(isset($this->settings[$name])) {
      return $this->settings[$name];
    }

    if(isset($this->settings[$this->getMachineName($name)])) {
      return $this->settings[$this->getMachineName($name)];
    }
  }

}
