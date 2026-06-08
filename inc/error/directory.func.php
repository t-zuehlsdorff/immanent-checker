<?php

namespace ImmanentCodeChecker\Error;

/**
  * @param $strCheck     - the name of the check that reported the error
  * @param $strMessage   - human-readable error message
  * @param $objDirectory - directory context the error belongs to
  *
  * @throws \Exception - if the directory context does not provide required fields
  * @throws \Exception - if the created error does not validate
  *
  * Register an error for a directory context.
  *
  * $strCheck and $strMessage must be non-empty strings without leading or
  * trailing whitespace. $objDirectory must provide the exploration fields
  * full_path and relative_path.
  *
  * The resulting error is validated by the ERROR_DIRECTORY pool before it is
  * stored. Invalid error data is rejected instead of being stored.
  **/
function directory(string $strCheck,
                   string $strMessage,
                   \ImmanentCodeChecker\DataObject $objDirectory): void {

  $objRegistry = new \ImmanentCodeChecker\DataObjectPool(\ImmanentCodeChecker\ERROR_DIRECTORY);

  $arrError = array('check'         => $strCheck,
                    'message'       => $strMessage,
                    'full_path'     => $objDirectory->get('full_path'),
                    'relative_path' => $objDirectory->get('relative_path'));

  $objRegistry->add(uniqid(), $arrError);

}
