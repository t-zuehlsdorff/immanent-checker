<?php

namespace ImmanentChecker\Error;

/**
  * @param $strCheck   - the name of the check that reported the error
  * @param $strMessage - human-readable error message
  * @param $objFile    - file context the error belongs to
  * @param $intLine    - optional line number the error belongs to
  *
  * @throws \Exception - if the file context does not provide required fields
  * @throws \Exception - if the created error does not validate
  *
  * Register an error for a file context.
  *
  * $strCheck and $strMessage must be non-empty strings without leading or
  * trailing whitespace. $objFile must provide the exploration fields full_path
  * and relative_path. $intLine may be null. If it is provided, it must be an
  * integer greater than zero.
  *
  * The resulting error is validated by the ERROR_FILE pool before it is stored.
  * Invalid error data is rejected instead of being stored.
  **/
function file(string $strCheck,
              string $strMessage,
              \ImmanentChecker\DataObject $objFile,
              ?int   $intLine = null): void {

  $objRegistry = new \ImmanentChecker\DataObjectPool(\ImmanentChecker\ERROR_FILE);

  $arrError = array('check'         => $strCheck,
                    'message'       => $strMessage,
                    'full_path'     => $objFile->get('full_path'),
                    'relative_path' => $objFile->get('relative_path'),
                    'line'          => $intLine);

  $objRegistry->add(uniqid(), $arrError);

}
