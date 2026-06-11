<?php

namespace ImmanentCodeChecker\Parser;

/**
  * Initialize the parser registry pool with its validator.
  *
  * The parser registry stores parser definitions with the following structure:
  *
  * array('name'     => string,
  *       'type'     => string,
  *       'callback' => callable,
  *       'pattern'  => string)
  *
  * The name identifies the parser inside the registry. It must be a non-empty
  * string without leading or trailing whitespace.
  *
  * The type describes what kind of input the parser expects. At the moment only
  * PARSER_TYPE_FILE is supported. A file parser receives the path to the file it
  * should parse.
  *
  * The callback contains the parser implementation and must be callable.
  *
  * The pattern limits the parser to project-relative file paths. It must be a
  * non-empty string without leading or trailing whitespace. The default pattern
  * is '*', which means that the parser may apply to every file.
  **/
function initPool(): void {

  $objRegistry = new \ImmanentCodeChecker\DataObjectPool(\ImmanentCodeChecker\PARSER_REGISTRY);

  $objRegistry->setValidator(function(array $arrData): bool {

    if(!array_key_exists('name', $arrData))
      return false;

    if(!is_string($arrData['name']))
      return false;

    if(strlen(trim($arrData['name'])) < 1)
      return false;

    if($arrData['name'] !== trim($arrData['name']))
      return false;

    if(!array_key_exists('type', $arrData))
      return false;

    if($arrData['type'] !== \ImmanentCodeChecker\PARSER_TYPE_FILE)
      return false;

    if(!array_key_exists('callback', $arrData))
      return false;

    if(!is_callable($arrData['callback']))
      return false;

    if(!array_key_exists('pattern', $arrData))
      return false;

    if(!is_string($arrData['pattern']))
      return false;

    if(strlen(trim($arrData['pattern'])) < 1)
      return false;

    if($arrData['pattern'] !== trim($arrData['pattern']))
      return false;

    return true;

  });

}
