<?php

namespace ImmanentCodeChecker\Error;

/**
  * Initialize the file-error pool with its validator.
  *
  * A file error has the following structure:
  *
  * array('check'         => string,   // name of the reporting check
  *       'message'       => string,   // human-readable error description
  *       'full_path'     => string,   // absolute path to the affected file
  *       'relative_path' => string,   // project-relative path to the file
  *       'line'          => ?int)     // affected line or null
  *
  * The path fields must be named exactly like the fields created during project
  * exploration. This keeps file contexts and file errors compatible without
  * translation. The line field may be null because not every file-level error
  * belongs to a specific line.
  *
  * The check field identifies the rule that reported the error. This makes the
  * result traceable even if many checks report errors for the same file. The
  * message field describes the violated expectation in a form that can be shown
  * to the user. The full_path field points to the affected file on the local
  * filesystem, while relative_path keeps the same file addressable relative to
  * the explored project. The optional line field narrows the error down to a
  * concrete source line when the check can provide one.
  *
  * All string fields are required and must contain at least one non-whitespace
  * character. Leading and trailing whitespace is rejected, so the stored result
  * is already normalized for output and comparison. The line field must either
  * be null or an integer greater than zero, because source lines are counted
  * starting at 1.
  *
  * The validator protects the public error API from storing malformed file
  * errors, which is important because checks can be implemented externally.
  **/
function initFilePool(): void {

  $cloFileErrorValidator = function(array $arrData): bool {

    if(!array_key_exists('check', $arrData))
      return false;

    if(!is_string($arrData['check']))
      return false;

    if(strlen(trim($arrData['check'])) < 1)
      return false;

    if($arrData['check'] !== trim($arrData['check']))
      return false;

    if(!array_key_exists('message', $arrData))
      return false;

    if(!is_string($arrData['message']))
      return false;

    if(strlen(trim($arrData['message'])) < 1)
      return false;

    if($arrData['message'] !== trim($arrData['message']))
      return false;

    if(!array_key_exists('full_path', $arrData))
      return false;

    if(!is_string($arrData['full_path']))
      return false;

    if(strlen(trim($arrData['full_path'])) < 1)
      return false;

    if($arrData['full_path'] !== trim($arrData['full_path']))
      return false;

    if(!array_key_exists('relative_path', $arrData))
      return false;

    if(!is_string($arrData['relative_path']))
      return false;

    if(strlen(trim($arrData['relative_path'])) < 1)
      return false;

    if($arrData['relative_path'] !== trim($arrData['relative_path']))
      return false;

    if(!array_key_exists('line', $arrData))
      return false;

    if(!is_null($arrData['line']) && !is_int($arrData['line']))
      return false;

    if(is_int($arrData['line']) && $arrData['line'] < 1)
      return false;

    return true;

  };

  $objRegistry = new \ImmanentCodeChecker\DataObjectPool(\ImmanentCodeChecker\ERROR_FILE);
  $objRegistry->setValidator($cloFileErrorValidator);

}
