<?php

namespace ImmanentCodeChecker\Check;

/**
  * Execute all checks registered for file analysis.
  *
  * Every explored file is passed as \ImmanentCodeChecker\DataObject to every
  * check registered for STAGE_FILE.
  **/
function file() : void {

  $objFiles  = new \ImmanentCodeChecker\DataObjectPool(\ImmanentCodeChecker\EXPLORE_FILE);
  $objChecks = new \ImmanentCodeChecker\DataObjectPool(\ImmanentCodeChecker\STAGE_FILE);

  foreach($objFiles->getAll() AS $objFile) {

    foreach($objChecks->getAll() AS $objCheck) {

      $cloCallback = $objCheck->get('callback');
      $cloCallback($objFile);
      
    }
    
  }
  
}
