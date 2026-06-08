<?php

namespace ImmanentCodeChecker\Check;

/**
  * Execute all checks registered for complete project analysis.
  *
  * The complete explored project is passed as
  * \ImmanentCodeChecker\DataObjectPool to every check registered for
  * STAGE_COMPLETE_PROJECT.
  **/
function completeProject() : void {

  $objProject = new \ImmanentCodeChecker\DataObjectPool(\ImmanentCodeChecker\EXPLORE_COMPLETE_PROJECT);
  $objChecks  = new \ImmanentCodeChecker\DataObjectPool(\ImmanentCodeChecker\STAGE_COMPLETE_PROJECT);

  foreach($objChecks->getAll() AS $objCheck) {

    $cloCallback = $objCheck->get('callback');
    $cloCallback($objProject);

  }

}
