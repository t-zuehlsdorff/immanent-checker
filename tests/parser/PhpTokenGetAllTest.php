<?php

namespace APHPUnit\Testcases;

require_once __DIR__ . '/../../config.inc.php';

const PHP_TOKEN_GET_ALL_TEST_FILE = __DIR__ . '/test-data/php-token-get-all/sample.php';

/**
  * Expect the built-in PHP token parser to be registered.
  **/
function testPhpTokenGetAllParserIsRegistered() {

  $objRegistry = new \ImmanentCodeChecker\DataObjectPool(\ImmanentCodeChecker\PARSER_REGISTRY);
  $arrParser   = $objRegistry->get(\ImmanentCodeChecker\PARSER_PHP_TOKEN_GET_ALL)->getAll();

  assertEquals($arrParser['name'], \ImmanentCodeChecker\PARSER_PHP_TOKEN_GET_ALL);
  assertEquals($arrParser['type'], \ImmanentCodeChecker\PARSER_TYPE_FILE);
  assertEquals($arrParser['pattern'], '*.php');
  assertTrue(is_callable($arrParser['callback']));

}

/**
  * Expect token_get_all() array tokens to include the token name.
  **/
function testPhpTokenGetAllParserNormalizesPhpTokens() {

  $arrTokens = \ImmanentCodeChecker\Parser\phpTokenGetAll(PHP_TOKEN_GET_ALL_TEST_FILE);
  $arrToken  = $arrTokens[0];

  assertEquals($arrToken['type'],  T_OPEN_TAG);
  assertEquals($arrToken['name'],  'T_OPEN_TAG');
  assertEquals($arrToken['value'], "<?php\n");
  assertEquals($arrToken['line'],  1);

}

/**
  * Expect single-character tokens such as brackets to use the same structure as
  * PHP tokens.
  **/
function testPhpTokenGetAllParserNormalizesSingleCharacterTokens() {

  $arrTokens = \ImmanentCodeChecker\Parser\phpTokenGetAll(PHP_TOKEN_GET_ALL_TEST_FILE);
  $arrFound  = null;

  foreach($arrTokens AS $arrToken)
    if($arrToken['value'] === '(')
      $arrFound = $arrToken;

  assertEquals($arrFound['type'],  -1);
  assertEquals($arrFound['name'],  '(');
  assertEquals($arrFound['value'], '(');
  assertEquals($arrFound['line'],  3);

}

/**
  * Expect the normalized tokens to preserve the complete token order of the
  * parsed file.
  **/
function testPhpTokenGetAllParserPreservesTokenOrder() {

  $arrTokens = \ImmanentCodeChecker\Parser\phpTokenGetAll(PHP_TOKEN_GET_ALL_TEST_FILE);
  $arrValues = array();
  $arrNames  = array();

  foreach($arrTokens AS $arrToken) {

    $arrValues[] = $arrToken['value'];
    $arrNames[]  = $arrToken['name'];

  }

  assertEquals($arrValues,
               array("<?php\n",
                     "\n",
                     '$strValue',
                     ' ',
                     '=',
                     ' ',
                     'strtoupper',
                     '(',
                     "'test'",
                     ')',
                     ';',
                     "\n"));

  assertEquals($arrNames,
               array('T_OPEN_TAG',
                     'T_WHITESPACE',
                     'T_VARIABLE',
                     'T_WHITESPACE',
                     '=',
                     'T_WHITESPACE',
                     'T_STRING',
                     '(',
                     'T_CONSTANT_ENCAPSED_STRING',
                     ')',
                     ';',
                     'T_WHITESPACE'));

}
