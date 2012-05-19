<?php

/**
 * Create command:
 *
 * Does the following things:
 *   - creates an empty website directory
 *     with www directory inside
 *   - creates an empty database
 *   - creates a drush alias
 *   - creates a profile alias to cd
 *     to the directory
 */
class CreateCommand extends Command
{
  protected $projectMachineName;
  private $websiteDirectory;
  private $name;
  private $domain;
  private $websiteRoot;

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
      'project' => new Argument(
        "Project directory",
        "The absolute path to the directory where the new project should be created.",
        function($argValue) {
          if(!is_dir($argValue)) {  
            return "Project directory should be an existable writable directory\n.";
          }
          return FALSE;
        }
      ),
      'root' => new Argument(
        "Website root",
        "The website root relative to the Parent directory, which defaults to $this->settings['webiste_root_path']",
        function($argValue) {
          return '';
        }
      ),
    );
  }

  /**
   * Actual execution of the command.
   */
  public function execute()
  {
    $this->createWebsiteDirectory();
    $this->createWebsiteRoot();
    $this->createDatabase();
    $this->createDrushAlias();
  }

  /**
   * Creates a new directory for the project.
   */
  protected function createWebSiteDirectory()
  {
    $project = $this->getProjectMachineName();
    $dir = isset($this->arguments['project']->value) ? $this->arguments['project']->value : $this->settings['project_path'];
    $this->websiteDirectory = $dir . '/' . $project;
    mkdir($this->websiteDirectory);
    // @todo: add exeption handling/error reporting for when goes wrong.
  }


  private function createDrushAlias()
  {
    $alias = $this->getName();
    $domain = $this->getDomain();
    $websiteRoot = $this->websiteRoot;

    $fh = fopen($this->settings['drush_alias_path'], 'a');

    $lines = array(
      '$aliases["'.$alias.'"]["uri"] = "'.$domain.'"'."\n",
      '$aliases["'.$alias.'"]["root"] = "'.$websiteRoot.'"'."\n",
      "\n",
    );
    foreach($lines as $line) {
      fputs($fh, $line);
    }
    fclose($fh);
    // @todo: errors and stuff...
  }


  private function getName()
  {
    if(isset($this->name)) {
      return $this->name;
    }

    $machine_name = $this->getProjectMachineName();
    $parts = explode('_', $machine_name);
    array_pop($parts);
    return $this->name = implode($parts, '_');
  }


  private function createDatabase()
  {
    $conn = mysql_connect("localhost", $this->settings['mysql_user'], $this->settings['mysql_pssw']);
    mysql_query("CREATE DATABASE " . $this->getProjectMachineName(), $conn);
    mysql_close($conn);
    // @todo: error handling => mysql server might not run...
  }


  /**
   * Creates a root directory within websiteDirectory.
   */
  protected function createWebsiteRoot()
  {
    $root = isset($this->arguments['root']->value) ? $this->arguments['root']->value : $this->settings['website_root_path'];
    $directories = explode('/', $root);
    $currentDir = $this->websiteDirectory;
    foreach($directories as $dirName) {
      $dir = $currentDir . '/' . $dirName;
      mkdir($dir);
      $currentDir = $dir;
    }
    $this->websiteRoot = $currentDir;
    // @todo: add error handling/reporting
  }


  /**
   * Creates a project machine based on domain name:
   * 
   * Subdomein.Domain.Dev => domain_subdomain_dev
   */
  protected function getProjectMachineName()
  {
    if($this->projectMachineName) {
      return $this->projectMachineName;
    }

    $domain = $this->getDomain();;
    $domain = strtolower($domain);
    $domain = str_replace('-', '_', $domain);
    $parts = explode('.', $domain);
    $topDomain = array_pop($parts);
    $parts = array_reverse($parts);
    array_push($parts, $topDomain);
    return implode($parts, '_');
  }


  private function getDomain()
  {
    if(isset($this->domain)) {
      return $this->domain;
    }

    return $this->domain =  $this->arguments[1]->value;
  }






}
