<?php

namespace ImmanentCodeChecker\Parser;

/**
  * @param $strFilePath - path to the PHP file to parse
  *
  * @returns array - normalized token_get_all() output
  *
  * Parse a PHP file with token_get_all() and normalize the result.
  *
  * token_get_all() returns PHP tokens as arrays and single-character tokens
  * such as brackets as strings. This parser normalizes both forms into one
  * structure:
  *
  * array('type'  => int,
  *       'name'  => string,
  *       'value' => string,
  *       'line'  => int)
  *
  * PHP tokens use their token id as type and token_name() as name.
  * Single-character tokens use -1 as type and the character itself as name.
  **/
function phpTokenGetAll(string $strFilePath): array {

  $arrParsedTokens     = token_get_all(file_get_contents($strFilePath));
  $arrNormalizedTokens = array();
  $intCurrentLine      = 1;

  foreach($arrParsedTokens AS $mixToken) {

    if(is_array($mixToken)) {

      $arrNormalizedTokens[] = array('type'  => $mixToken[0],
                                     'name'  => token_name($mixToken[0]),
                                     'value' => $mixToken[1],
                                     'line'  => $mixToken[2]);

      $intCurrentLine = $mixToken[2] + substr_count($mixToken[1], "\n");
      
      continue;

    }

    $arrNormalizedTokens[] = array('type'  => -1,
                                   'name'  => $mixToken,
                                   'value' => $mixToken,
                                   'line'  => $intCurrentLine);

    $intCurrentLine += substr_count($mixToken, "\n");

  }

  return $arrNormalizedTokens;

}
