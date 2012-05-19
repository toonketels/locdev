<?php

class HelpCommand extends Command
{
  public function execute()
  {
    print "HELP: coming soon. \n";
  }

  protected function validateArguments($arguments)
  {
    return '';
  }
}
