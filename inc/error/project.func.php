<?php

namespace ImmanentChecker\Error;

/**
  * @param $strCheck   - the name of the check that reported the error
  * @param $strMessage - human-readable error message
  * @param $arrFiles   - optional list of project-relative affected files
  * @param $strDetails - optional JSON-encoded additional check-specific information
  *
  * @throws \Exception - if the created error does not validate
  *
  * Register an error for the filtered project context.
  *
  * $strCheck and $strMessage must be non-empty strings without leading or
  * trailing whitespace. $arrFiles must contain only non-empty strings without
  * leading or trailing whitespace. $strDetails may be empty. If it is not empty,
  * it must be valid JSON without leading or trailing whitespace.
  *
  * The resulting error is validated by the ERROR_PROJECT pool before it is
  * stored. Invalid error data is rejected instead of being stored.
  **/
function project(string $strCheck,
                 string $strMessage,
                 array  $arrFiles = array(),
                 string $strDetails = ''): void {

  $objRegistry = new \ImmanentChecker\DataObjectPool(\ImmanentChecker\ERROR_PROJECT);

  $arrError = array('check'   => $strCheck,
                    'message' => $strMessage,
                    'files'   => $arrFiles,
                    'details' => $strDetails);

  $objRegistry->add(uniqid(), $arrError);

}
