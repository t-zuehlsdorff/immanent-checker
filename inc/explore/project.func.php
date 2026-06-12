<?php

namespace ImmanentChecker\Explore;

/**
  * @param $strProjectPath      - the project path to explore
  * @param $arrExcludePatterns  - list of fnmatch patterns to exclude from the filtered project
  *
  * Explore the complete project structure.
  **/
function project(string $strProjectPath, array $arrExcludePatterns = array()): void {

  $strProjectPath = realpath($strProjectPath);

  if(false === $strProjectPath)
    throw new \Exception ("Project path does not exist: '$strProjectPath'");

  if(!is_dir($strProjectPath))
    throw new \Exception ("Project path is not a directory: '$strProjectPath'");

  $objCompleteProject = new \ImmanentChecker\DataObjectPool(\ImmanentChecker\EXPLORE_COMPLETE_PROJECT);
  $objProject         = new \ImmanentChecker\DataObjectPool(\ImmanentChecker\EXPLORE_PROJECT);
  $objDirectory       = new \ImmanentChecker\DataObjectPool(\ImmanentChecker\EXPLORE_DIRECTORY);
  $objFile            = new \ImmanentChecker\DataObjectPool(\ImmanentChecker\EXPLORE_FILE);

  $objProjectDir = new \RecursiveDirectoryIterator($strProjectPath,
                                                  \FilesystemIterator::SKIP_DOTS);
  
  $objIterator   = new \RecursiveIteratorIterator($objProjectDir,
                                                 \RecursiveIteratorIterator::SELF_FIRST);

  foreach($objIterator AS $objEntry) {

    $arrEntry = array('full_path'     => $objEntry->getPathname(),
                      'relative_path' => substr($objEntry->getPathname(), strlen($strProjectPath) + 1),
                      'permissions'   => $objEntry->getPerms());

    if(!$objCompleteProject->exists($arrEntry['full_path']))
      $objCompleteProject->add($arrEntry['full_path'], $arrEntry);

    $boolExcluded = false;

    foreach($arrExcludePatterns AS $strExcludePattern)
      if(fnmatch($strExcludePattern, $arrEntry['relative_path']))
        $boolExcluded = true;

    // if path matches ANY exclude pattern, do not store it for project,
    // directory or file stage
    if($boolExcluded)
      continue;

    if(!$objProject->exists($arrEntry['full_path']))
      $objProject->add($arrEntry['full_path'], $arrEntry);

    if($objEntry->isDir())
      if(!$objDirectory->exists($arrEntry['full_path']))
        $objDirectory->add($arrEntry['full_path'], $arrEntry);

    if($objEntry->isFile())
      if(!$objFile->exists($arrEntry['full_path']))
        $objFile->add($arrEntry['full_path'], $arrEntry);

  }

}
