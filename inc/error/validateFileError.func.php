<?php

namespace ImmanentChecker\Error;

/**
  * @param $arrData - error data array to validate
  *
  * Validates a file error entry. Returns true when the entry has
  * the correct structure: check, message, full_path and relative_path
  * must be non-empty trimmed strings, line must be null or int > 0.
  **/
function validateFileError(array $arrData): bool {

  $arrStringFields = array('check', 'message', 'full_path', 'relative_path');

  foreach($arrStringFields AS $strField) {

    if(!array_key_exists($strField, $arrData))
      return false;

    if(!is_string($arrData[$strField]))
      return false;

    if(strlen(trim($arrData[$strField])) < 1)
      return false;

    if($arrData[$strField] !== trim($arrData[$strField]))
      return false;

  }

  if(!array_key_exists('line', $arrData))
    return false;

  if(!is_null($arrData['line']) && !is_int($arrData['line']))
    return false;

  if(is_int($arrData['line']) && $arrData['line'] < 1)
    return false;

  return true;

}
