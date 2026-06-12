<?php

namespace ImmanentChecker\Output;

/**
  * @returns array - list of file errors
  *
  * Collect errors from the file error pool.
  **/
function collectFileErrors() : array {

  $objRegistry = new \ImmanentChecker\DataObjectPool(\ImmanentChecker\ERROR_FILE);
  $arrErrors   = array();

  foreach($objRegistry->getAll() AS $objError) {

    $arrErrors[] = array('check'         => $objError->get('check'),
                         'message'       => $objError->get('message'),
                         'full_path'     => $objError->get('full_path'),
                         'relative_path' => $objError->get('relative_path'),
                         'line'          => $objError->get('line'));

  }

  return $arrErrors;

}
