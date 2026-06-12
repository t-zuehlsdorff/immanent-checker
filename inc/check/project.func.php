<?php

namespace ImmanentChecker\Check;

/**
  * Execute all checks registered for project analysis.
  *
  * The explored project after filtering is passed as
  * \ImmanentChecker\DataObjectPool to every check registered for
  * STAGE_PROJECT.
  **/
function project() : void {

  $objProject = new \ImmanentChecker\DataObjectPool(\ImmanentChecker\EXPLORE_PROJECT);
  $objChecks  = new \ImmanentChecker\DataObjectPool(\ImmanentChecker\STAGE_PROJECT);

  foreach($objChecks->getAll() AS $objCheck) {

    $cloCallback = $objCheck->get('callback');
    $cloCallback($objProject);

  }

}
