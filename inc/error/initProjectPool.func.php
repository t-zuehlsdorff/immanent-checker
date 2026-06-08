<?php

namespace ImmanentCodeChecker\Error;

/**
  * Initialize the project-error and complete-project-error pools with their
  * validators.
  **/
function initProjectPool(): void {

  $cloProjectErrorValidator = getProjectErrorValidator();

  $objRegistry = new \ImmanentCodeChecker\DataObjectPool(\ImmanentCodeChecker\ERROR_PROJECT);
  $objRegistry->setValidator($cloProjectErrorValidator);

  $objRegistry = new \ImmanentCodeChecker\DataObjectPool(\ImmanentCodeChecker\ERROR_COMPLETE_PROJECT);
  $objRegistry->setValidator($cloProjectErrorValidator);

}
