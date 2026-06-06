<?php

namespace ImmanentCodeChecker\Check;

/**
  * @param $strStage       - one of the 4 Stages to register the check for
  * @param $strName        - the unique name of the check
  * @param $cloCallback    - the callback to execute the check
  * @param $strDescription - optional description of the check
  *
  * Register the provided Check to the defined Stage.
  *
  * Requires a unique name of the check to allow easier management and debugging
  * in case of failures.
  *
  * Provide an optional $strDescription to manage the expectations about the
  * check.
  **/
function register(string $strStage, string $strName, callable $cloCallback, string $strDescription = '') : void {

  $arrStages = array(\ImmanentCodeChecker\STAGE_COMPLETE_PROJECT,
                     \ImmanentCodeChecker\STAGE_PROJECT,
                     \ImmanentCodeChecker\STAGE_DIRECTORY,
                     \ImmanentCodeChecker\STAGE_FILE);

  if(!in_array($strStage, $arrStages))
    throw new \Exception ("Given analysis-stage is invalid: '$strStage'");

  $objRegistry = new \ImmanentCodeChecker\DataObjectPool($strStage);

  if($objRegistry->exists($strName))
    throw new \Exception ("Check-Name already exists, use a unique one. Given: '$strName'");

  $objRegistry->add($strName, array('name'        => $strName,
                                    'description' => $strDescription,
                                    'callback'    => $cloCallback));
  
}
