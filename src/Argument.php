<?php

class Argument
{
  protected $name;
  protected $description;
  protected $validationFunc;

  public function __construct($name, $description, $validation)
  {
    $this->name = $name;
    $this->description = $description;
    $this->validationFunc = $validation;
  }

  public function validate($value)
  {
    return call_user_func($this->validationFunc, $value);
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
