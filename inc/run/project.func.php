<?php

namespace ImmanentChecker\Run;

/**
  * @param $strProjectPath     - the project directory to check
  * @param $arrSuitePaths      - list of suite directories to load
  * @param $arrExcludePatterns - list of fnmatch patterns to exclude from analysis
  *
  * @throws \Exception - if a suite entry point is missing
  *
  * Run all registered analysis stages for one project.
  **/
function project(string $strProjectPath,
                 array  $arrSuitePaths,
                 array  $arrExcludePatterns = array()): void {

  foreach($arrSuitePaths AS $strSuitePath) {

    $strRegisterFile = $strSuitePath . '/register.php';

    if(!is_file($strRegisterFile))
      throw new \Exception ("Suite register file not found: '$strRegisterFile'");

    require_once $strRegisterFile;

  }

  \ImmanentChecker\Explore\project($strProjectPath, $arrExcludePatterns);
  \ImmanentChecker\Check\all();

}
