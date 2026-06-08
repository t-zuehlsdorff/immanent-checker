<?php

namespace ImmanentCodeChecker\Check;

/**
  * Execute all checks registered for project analysis.
  *
  * The explored project after filtering is passed as
  * \ImmanentCodeChecker\DataObjectPool to every check registered for
  * STAGE_PROJECT.
  **/
function project() : void {

  $objProject = new \ImmanentCodeChecker\DataObjectPool(\ImmanentCodeChecker\EXPLORE_PROJECT);
  $objChecks  = new \ImmanentCodeChecker\DataObjectPool(\ImmanentCodeChecker\STAGE_PROJECT);

  foreach($objChecks->getAll() AS $objCheck) {

    $cloCallback = $objCheck->get('callback');
    $cloCallback($objProject);

  }

}
