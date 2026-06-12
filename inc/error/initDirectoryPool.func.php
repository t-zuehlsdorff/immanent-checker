<?php

namespace ImmanentChecker\Error;

/**
  * Initialize the directory-error pool with its validator.
  *
  * A directory error has the following structure:
  *
  * array('check'         => string,   // name of the reporting check
  *       'message'       => string,   // human-readable error description
  *       'full_path'     => string,   // absolute path to the affected directory
  *       'relative_path' => string)   // project-relative path to the directory
  *
  * The path fields must be named exactly like the fields created during project
  * exploration. This keeps directory contexts and directory errors compatible
  * without translation.
  *
  * The check field identifies the rule that reported the error. This makes the
  * result traceable even if many checks report errors for the same directory.
  * The message field describes the violated expectation in a form that can be
  * shown to the user. The full_path field points to the affected directory on
  * the local filesystem, while relative_path keeps the same directory
  * addressable relative to the explored project.
  *
  * All string fields are required and must contain at least one non-whitespace
  * character. Leading and trailing whitespace is rejected, so the stored result
  * is already normalized for output and comparison.
  *
  * The validator protects the public error API from storing malformed directory
  * errors, which is important because checks can be implemented externally.
  **/
function initDirectoryPool(): void {

  $cloDirectoryErrorValidator = function(array $arrData): bool {

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

    return true;

  };

  $objRegistry = new \ImmanentChecker\DataObjectPool(\ImmanentChecker\ERROR_DIRECTORY);
  $objRegistry->setValidator($cloDirectoryErrorValidator);

}
