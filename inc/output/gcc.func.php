<?php

namespace ImmanentChecker\Output;

/**
  * Collect all errors from every error pool and return them in GCC format.
  *
  * Each error is one line:
  *
  *   relative_path:line: [check] message
  *
  * For errors without a file (project, complete_project), the format is:
  *
  *   [check] message
  *
  * For directory errors without a line number:
  *
  *   relative_path: [check] message
  *
  * @return string - GCC-formatted output, or empty string when no errors
  **/
function gcc() : string {

  $arrLines = array();

  foreach(collectProjectErrors(\ImmanentChecker\ERROR_COMPLETE_PROJECT) AS $arrError)
    $arrLines[] = "[{$arrError['check']}] {$arrError['message']}";

  foreach(collectProjectErrors(\ImmanentChecker\ERROR_PROJECT) AS $arrError)
    $arrLines[] = "[{$arrError['check']}] {$arrError['message']}";

  foreach(collectDirectoryErrors() AS $arrError)
    $arrLines[] = "{$arrError['relative_path']}: [{$arrError['check']}] {$arrError['message']}";

  foreach(collectFileErrors() AS $arrError) {

    $strLine = '';

    if(!is_null($arrError['line']))
      $strLine = ":{$arrError['line']}";

    $arrLines[] = "{$arrError['relative_path']}{$strLine}: [{$arrError['check']}] {$arrError['message']}";

  }

  if(0 === count($arrLines))
    return '';

  return implode(PHP_EOL, $arrLines);

}
