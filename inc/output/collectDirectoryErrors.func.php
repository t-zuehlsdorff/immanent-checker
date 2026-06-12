<?php

namespace ImmanentChecker\Output;

/**
  * @return array - list of directory errors
  *
  * Collect errors from the directory error pool.
  **/
function collectDirectoryErrors() : array {

  $objRegistry = new \ImmanentChecker\DataObjectPool(\ImmanentChecker\ERROR_DIRECTORY);
  $arrErrors   = array();

  foreach($objRegistry->getAll() AS $objError) {

    $arrErrors[] = array('check'         => $objError->get('check'),
                         'message'       => $objError->get('message'),
                         'full_path'     => $objError->get('full_path'),
                         'relative_path' => $objError->get('relative_path'));

  }

  return $arrErrors;

}
