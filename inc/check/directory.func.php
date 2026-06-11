<?php

namespace ImmanentCodeChecker\Check;

/**
  * Execute all checks registered for directory analysis.
  *
  * Every explored directory is passed as \ImmanentCodeChecker\DataObject to every
  * check registered for STAGE_DIRECTORY.
  **/
function directory() : void {

  $objDirectories = new \ImmanentCodeChecker\DataObjectPool(\ImmanentCodeChecker\EXPLORE_DIRECTORY);
  $objChecks      = new \ImmanentCodeChecker\DataObjectPool(\ImmanentCodeChecker\STAGE_DIRECTORY);

  foreach($objDirectories->getAll() AS $objDirectory) {

    foreach($objChecks->getAll() AS $objCheck) {

      if(!fnmatch($objCheck->get('pattern'), $objDirectory->get('relative_path')))
        continue;

      $cloCallback = $objCheck->get('callback');
      $cloCallback($objDirectory);

    }

  }

}
