<?php

namespace ImmanentCodeChecker\Check;

/**
  * Execute all registered checks for all supported analysis stages.
  **/
function all() : void {

  completeProject();
  project();
  directory();
  file();
  
}
