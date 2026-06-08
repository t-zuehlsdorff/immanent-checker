<?php

namespace ImmanentCodeChecker\Error;

/**
  * @param $strCheck   - the name of the check that reported the error
  * @param $strMessage - human-readable error message
  * @param $objFile    - file context the error belongs to
  * @param $intLine    - optional line number the error belongs to
  *
  * Register an error for a file context.
  **/
function file(string $strCheck,
              string $strMessage,
              \ImmanentCodeChecker\DataObject $objFile,
              ?int   $intLine = null): void {

  $objRegistry = new \ImmanentCodeChecker\DataObjectPool(\ImmanentCodeChecker\ERROR_FILE);

  $arrError = array('check'         => $strCheck,
                    'message'       => $strMessage,
                    'full_path'     => $objFile->get('full_path'),
                    'relative_path' => $objFile->get('relative_path'),
                    'line'          => $intLine);

  $objRegistry->add(uniqid(), $arrError);

}
