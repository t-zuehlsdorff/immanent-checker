<?php

namespace ImmanentCodeChecker\Output;

/**
  * @param $strPool - the error pool constant to collect from
  *
  * @return array - list of project-like errors
  *
  * Collect errors from a project-like error pool (complete_project or project).
  *
  * Decodes the details field from a JSON string into a native value so the
  * output nests correctly. If details is empty, the key is omitted.
  **/
function collectProjectErrors(string $strPool) : array {

  $objRegistry = new \ImmanentCodeChecker\DataObjectPool($strPool);
  $arrErrors   = array();

  foreach($objRegistry->getAll() AS $objError) {

    $arrError = array('check'   => $objError->get('check'),
                      'message' => $objError->get('message'),
                      'files'   => $objError->get('files'));

    $strDetails = $objError->get('details');

    if($strDetails !== '')
      $arrError['details'] = json_decode($strDetails, true);

    $arrErrors[] = $arrError;

  }

  return $arrErrors;

}
