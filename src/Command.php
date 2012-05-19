<?php

abstract class Command
{
  abstract public function execute();
  abstract protected function validateArguments($arguments);

  protected $errors = '';
  protected $arguments = array();

  protected function getAllowedArguments()
  {
    return array();
  }

  /**
   * Ask a question to user and return response.
   */
  protected function ask($question)
  {
    $fh = fopen("php://stdin", 'r');
    print($question . "\n");
    return fgets($fh);
  }

  /**
   * Will set and validate the arguments.
   */
  public function setArguments($argumentsPassed)
  {
    $allowedArguments = $this->getAllowedArguments();

    // Check each individual argument that...
    // it is in the allowed arguments list,
    // and that the allwed argument validate.
    // Non allowed arguments just get ignored.
    foreach ($argumentsPassed as $argKey => $argValue) {
      if(isset($allowedArguments[$argKey])) {
        $argObject = $allowedArguments[$argKey];
        // Validate argument
        $this->errors .= $argObject->validate($argValue);
        // Even arguments wich don't validate get added to $arguments
        // list because we want to report all errors in one time.
        $this->arguments[$argKey] = $allowedArguments[$argKey];
      }
    }

    // Perform additional custom validation.
    $this->errors .= $this->validateArguments($this->arguments);

    return $this->errors;
  }

}


