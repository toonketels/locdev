<?php

class CreateCommand extends Command
{
  public function execute()
  {
    print "CREATE: to do...\n";
  }

  protected function validateArguments($arguments)
  {
    // We need at least a domain name...
    // Domain name is passed as first argument.
    if(!isset($arguments[1])) {
      return "Please provide a domain name for which to set the environment up.\n";
    }

    return '';
  }


  /**
   * Returns an associative array of allowed arguments list.
   * Keys can be:
   *  - numerical
   *  - single char
   *  - string
   */
  protected function getAllowedArguments()
  {
    return array(
      1 => new Argument(
        "domain",
        "The desired domain of the new website",
        function($argValue) {
          if(!is_string($argValue) ||
             (count(explode('.', $argValue)) < 2)) {
            return "You should pass a domain name, it should have at least two parts like sitename.dev\n";
          }
          return FALSE;
        }
      ),
    );
  }

}
