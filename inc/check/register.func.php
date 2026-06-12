<?php

namespace ImmanentCodeChecker\Check;

/**
  * @param $strStage       - one of the 4 Stages to register the check for
  * @param $strName        - the unique name of the check
  * @param $cloCallback    - the callback to execute the check
  * @param $strDescription - optional description of the check
  * @param $strPattern     - optional fnmatch pattern for file and directory checks
  *
  * Register the provided Check to the defined Stage.
  *
  * Requires a unique name of the check to allow easier management and debugging
  * in case of failures.
  *
  * Provide an optional $strDescription to manage the expectations about the
  * check.
  *
  * For file and directory checks, an optional $strPattern can be provided. If
  * set, the check is only called when the project-relative path matches the
  * pattern. Pattern matching uses PHP's fnmatch() semantics. If no pattern is
  * given, the default '*' matches every path.
  *
  * Patterns are only supported for STAGE_FILE and STAGE_DIRECTORY. Providing a
  * pattern other than '*' for STAGE_PROJECT or STAGE_COMPLETE_PROJECT is an
  * error.
  **/
function register(string $strStage, string $strName, callable $cloCallback, string $strDescription = '', string $strPattern = '*') : void {

  $arrStages = array(\ImmanentCodeChecker\STAGE_COMPLETE_PROJECT,
                     \ImmanentCodeChecker\STAGE_PROJECT,
                     \ImmanentCodeChecker\STAGE_DIRECTORY,
                     \ImmanentCodeChecker\STAGE_FILE);

  if(!in_array($strStage, $arrStages))
    throw new \Exception ("Given analysis-stage is invalid: '$strStage'");

  $arrProjectStages = array(\ImmanentCodeChecker\STAGE_COMPLETE_PROJECT,
                            \ImmanentCodeChecker\STAGE_PROJECT);

  if(in_array($strStage, $arrProjectStages) && '*' !== $strPattern)
    throw new \Exception ("Patterns are only supported for file and directory checks, not for: '$strStage'");

  $objRegistry = new \ImmanentCodeChecker\DataObjectPool($strStage);

  if($objRegistry->exists($strName))
    throw new \Exception ("Check-Name already exists, use a unique one. Given: '$strName'");

  $objRegistry->add($strName, array('name'        => $strName,
                                    'description' => $strDescription,
                                    'callback'    => $cloCallback,
                                    'pattern'     => $strPattern));

}
