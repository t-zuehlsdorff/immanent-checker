<?php

namespace ImmanentChecker\Check;

/**
  * Execute all checks registered for complete project analysis.
  *
  * The complete explored project is passed as
  * \ImmanentChecker\DataObjectPool to every check registered for
  * STAGE_COMPLETE_PROJECT.
  **/
function completeProject() : void {

  $objProject = new \ImmanentChecker\DataObjectPool(\ImmanentChecker\EXPLORE_COMPLETE_PROJECT);
  $objChecks  = new \ImmanentChecker\DataObjectPool(\ImmanentChecker\STAGE_COMPLETE_PROJECT);

  foreach($objChecks->getAll() AS $objCheck) {

    $cloCallback = $objCheck->get('callback');
    $cloCallback($objProject);

  }

}
