<?php

namespace ImmanentChecker\Parser;

/**
  * @param $strName - the name of the registered parser (e.g. PARSER_PHP_TOKEN_GET_ALL)
  *
  * @throws \Exception - if no result exists for the given parser name
  *
  * @returns mixed - the parser result for the file currently being checked
  *
  * Return the result of a named parser for the file currently being checked.
  *
  * This function must be called from within a file check callback. Before the
  * file checks run, every registered parser whose pattern matches the current
  * file is executed and its result is stored under the parser name. This
  * function looks up that stored result by parser name.
  *
  * A single file can be processed by multiple parsers. Each parser result is
  * accessible independently by its name:
  *
  *   $arrTokens = \ImmanentChecker\Parser\get(\ImmanentChecker\PARSER_PHP_TOKEN_GET_ALL);
  *   $arrOther  = \ImmanentChecker\Parser\get('MY_CUSTOM_PARSER');
  *
  * If no result exists for the given parser name, the parser either did not
  * match the current file or the name is incorrect. Both cases are errors.
  **/
function get(string $strName) : mixed {

  $objResults = new \ImmanentChecker\DataObjectPool(\ImmanentChecker\PARSER_RESULT);

  if(!$objResults->exists($strName))
    throw new \Exception("No parser result available for: '$strName'");

  return $objResults->get($strName)->get('result');

}
