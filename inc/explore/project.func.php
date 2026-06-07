<?php

namespace ImmanentCodeChecker\Explore;

/**
  * Explore the complete project structure.
  **/
function project(string $strProjectPath): void {

  $strProjectPath = realpath($strProjectPath);

  if(false === $strProjectPath)
    throw new \Exception ("Project path does not exist: '$strProjectPath'");

  if(!is_dir($strProjectPath))
    throw new \Exception ("Project path is not a directory: '$strProjectPath'");

  $objCompleteProject = new \ImmanentCodeChecker\DataObjectPool(\ImmanentCodeChecker\EXPLORE_COMPLETE_PROJECT);
  $objProject         = new \ImmanentCodeChecker\DataObjectPool(\ImmanentCodeChecker\EXPLORE_PROJECT);
  $objDirectory       = new \ImmanentCodeChecker\DataObjectPool(\ImmanentCodeChecker\EXPLORE_DIRECTORY);
  $objFile            = new \ImmanentCodeChecker\DataObjectPool(\ImmanentCodeChecker\EXPLORE_FILE);

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
