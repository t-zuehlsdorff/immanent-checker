<?php

namespace ImmanentChecker\Check;

/**
  * Execute all checks registered for file analysis.
  *
  * For every explored file, all registered file parsers whose pattern matches
  * the file's project-relative path are executed first. Their results are stored
  * in the temporary PARSER_RESULT pool for the duration of this file.
  *
  * After parser preparation, every explored file is passed as
  * \ImmanentChecker\DataObject to every check registered for STAGE_FILE.
  *
  * After all file checks for the current file ran, the temporary parser results
  * are discarded before the next file is processed.
  **/
function file() : void {

  $objFiles         = new \ImmanentChecker\DataObjectPool(\ImmanentChecker\EXPLORE_FILE);
  $objChecks        = new \ImmanentChecker\DataObjectPool(\ImmanentChecker\STAGE_FILE);
  $objParsers       = new \ImmanentChecker\DataObjectPool(\ImmanentChecker\PARSER_REGISTRY);
  $objParserResults = new \ImmanentChecker\DataObjectPool(\ImmanentChecker\PARSER_RESULT);

  foreach($objFiles->getAll() AS $objFile) {

    // if this is a file, parse them using all Parsers
    foreach($objParsers->getAll() AS $objParser) {

      if($objParser->get('type') !== \ImmanentChecker\PARSER_TYPE_FILE)
        continue;

      if(!fnmatch($objParser->get('pattern'), $objFile->get('relative_path')))
        continue;

      $cloParser = $objParser->get('callback');

      $objParserResults->add($objParser->get('name'),
                             array('name'      => $objParser->get('name'),
                                   'full_path' => $objFile->get('full_path'),
                                   'result'    => $cloParser($objFile->get('full_path'))));

    }

    // Excecute all checks
    foreach($objChecks->getAll() AS $objCheck) {

      if(!fnmatch($objCheck->get('pattern'), $objFile->get('relative_path')))
        continue;

      $cloCallback = $objCheck->get('callback');
      $cloCallback($objFile);

    }

    // remove parsings from pool - we do not need them anymore :)
    foreach(array_keys($objParserResults->getAll()) AS $strParserName)
      $objParserResults->delete($strParserName);
    
  }
  
}
