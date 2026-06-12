<?php

namespace ImmanentChecker\Error;

/**
  * @param $arrData - error data array to validate
  *
  * Validates a project-like error entry. Returns true when the entry
  * has the correct structure: check and message must be non-empty
  * trimmed strings, files must be an array of trimmed strings,
  * details must be a trimmed string with valid JSON if non-empty.
  **/
function validateProjectError(array $arrData): bool {

  $arrStringFields = array('check', 'message');

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

  if(!array_key_exists('files', $arrData))
    return false;

  if(!is_array($arrData['files']))
    return false;

  foreach($arrData['files'] AS $strFile) {

    if(!is_string($strFile))
      return false;

    if(strlen(trim($strFile)) < 1)
      return false;

    if($strFile !== trim($strFile))
      return false;

  }

  if(!array_key_exists('details', $arrData))
    return false;

  if(!is_string($arrData['details']))
    return false;

  if($arrData['details'] !== trim($arrData['details']))
    return false;

  if('' !== $arrData['details']) {

    json_decode($arrData['details'], true);

    if(json_last_error() !== \JSON_ERROR_NONE)
      return false;

  }

  return true;

}
