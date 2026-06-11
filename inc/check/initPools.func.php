<?php

namespace ImmanentCodeChecker\Check;

/**
  * Initialize all check registry pools with their validator.
  *
  * Each registered check must provide: name (non-empty trimmed string),
  * description (trimmed string), callback (callable), and pattern (non-empty
  * trimmed string, typically an fnmatch pattern like '*.php' or the default
  * '*').
  **/
function initPools(): void {

  $cloCheckValidator = function(array $arrData): bool {

    if(!array_key_exists('name', $arrData))
      return false;

    if(!is_string($arrData['name']))
      return false;

    if(strlen(trim($arrData['name'])) < 1)
      return false;

    if($arrData['name'] !== trim($arrData['name']))
      return false;

    if(!array_key_exists('description', $arrData))
      return false;

    if(!is_string($arrData['description']))
      return false;

    if($arrData['description'] !== trim($arrData['description']))
      return false;

    if(!array_key_exists('callback', $arrData))
      return false;

    if(!is_callable($arrData['callback']))
      return false;

    if(!array_key_exists('pattern', $arrData))
      return false;

    if(!is_string($arrData['pattern']))
      return false;

    if(strlen(trim($arrData['pattern'])) < 1)
      return false;

    if($arrData['pattern'] !== trim($arrData['pattern']))
      return false;

    return true;

  };

  foreach(array(\ImmanentCodeChecker\STAGE_COMPLETE_PROJECT,
                \ImmanentCodeChecker\STAGE_PROJECT,
                \ImmanentCodeChecker\STAGE_DIRECTORY,
                \ImmanentCodeChecker\STAGE_FILE) AS $strStage) {

    $objRegistry = new \ImmanentCodeChecker\DataObjectPool($strStage);
    $objRegistry->setValidator($cloCheckValidator);

  }

}
