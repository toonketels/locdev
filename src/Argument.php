<?php

class Argument
{
  protected $name;
  protected $description;
  protected $validationFunc;
  public $value;

  public function __construct($name, $description, $validation)
  {
    $this->name = $name;
    $this->description = $description;
    $this->validationFunc = $validation;
  }

  public function validate($value)
  {
    $error = call_user_func($this->validationFunc, $value);
    if(empty($error)) $this->value = $value;
    return $error;
  }

  public function getName()
  {
    return $this->name;
  }

  public function getDescription()
  {
    return $this->description;
  }

}
