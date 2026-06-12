<?php

namespace ImmanentChecker\Error;

/**
  * Initialize the project-error and complete-project-error pools with their
  * validators.
  **/
function initProjectPool(): void {

  $cloProjectErrorValidator = getProjectErrorValidator();

  $objRegistry = new \ImmanentChecker\DataObjectPool(\ImmanentChecker\ERROR_PROJECT);
  $objRegistry->setValidator($cloProjectErrorValidator);

  $objRegistry = new \ImmanentChecker\DataObjectPool(\ImmanentChecker\ERROR_COMPLETE_PROJECT);
  $objRegistry->setValidator($cloProjectErrorValidator);

}
