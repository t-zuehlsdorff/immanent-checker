<?php

namespace ImmanentChecker\Cli;

/**
  * @param $arrArguments - command line arguments including the command name
  *
  * @throws \Exception - if required arguments are missing or unknown arguments are given
  *
  * Parse the CLI arguments for a project run.
  **/
function parseArguments(array $arrArguments): array {

  array_shift($arrArguments);

  $arrResult  = array('exclude' => array(),
                      'suite'   => array(),
                      'project' => array());
  
  $arrAllowed = array('--exclude', '--suite', '--project');

  $intArgNum = count($arrArguments);

  for($intI = 0; $intI < $intArgNum; $intI = $intI + 2) {

    if(!in_array($arrArguments[$intI], $arrAllowed))
      throw new \Exception ("Unknown parameter given: '{$arrArguments[$intI]}'");

    if(empty($arrArguments[$intI + 1]))
      throw new \Exception ("Missing value for parameter - check everything please!");

    $arrResult[substr($arrArguments[$intI], 2)][] = $arrArguments[$intI +1];
    
  }

  foreach(array('suite', 'project') AS $strArg)
    foreach($arrResult[$strArg] AS $strPath)
      if(!is_dir($strPath))
        throw new \Exception ("Given path for $strArg is not a directory: '$strPath'");


  foreach(array('suite', 'project') AS $strArg)
    if(empty($arrResult[$strArg]))
      throw new \Exception ("At least one value must be set for parameter '--$strArg'");
  
  return $arrResult;
  
}
