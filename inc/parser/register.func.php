<?php

namespace ImmanentCodeChecker\Parser;

/**
  * @param $strName     - the unique name of the parser
  * @param $strType     - the type of input the parser expects
  * @param $cloCallback - the parser callback
  * @param $strPattern  - project-relative file pattern the parser applies to
  *
  * @throws \Exception - if the parser name already exists
  *
  * Register a parser.
  *
  * For now, only file parsers are supported. A file parser callback receives
  * the path to the file it should parse.
  *
  * The optional pattern limits which project-relative file paths the parser
  * applies to. If no pattern is given, the parser applies to all files.
  **/
function register(string $strName,
                  string $strType,
                  callable $cloCallback,
                  string $strPattern = '*'): void {

  $objRegistry = new \ImmanentCodeChecker\DataObjectPool(\ImmanentCodeChecker\PARSER_REGISTRY);

  if($objRegistry->exists($strName))
    throw new \Exception ("Parser-Name already exists, use a unique one. Given: '$strName'");

  $objRegistry->add($strName, array('name'     => $strName,
                                    'type'     => $strType,
                                    'callback' => $cloCallback,
                                    'pattern'  => $strPattern));

}
