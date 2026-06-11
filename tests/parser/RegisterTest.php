<?php

namespace APHPUnit\Testcases;

require_once __DIR__ . '/../../config.inc.php';

/**
  * Expect a registered parser to be stored with name, type, and callback.
  **/
function testParserRegisterStoresParserInRegistry() {

  $strName     = 'ParserRegisterStoresParserInRegistry' . uniqid();
  $cloCallback = function (string $strFilePath) { return $strFilePath; };

  \ImmanentCodeChecker\Parser\register($strName,
                                       \ImmanentCodeChecker\PARSER_TYPE_FILE,
                                       $cloCallback);

  $objRegistry = new \ImmanentCodeChecker\DataObjectPool(\ImmanentCodeChecker\PARSER_REGISTRY);
  $arrParser   = $objRegistry->get($strName)->getAll();

  assertEquals($arrParser['name'],     $strName);
  assertEquals($arrParser['type'],     \ImmanentCodeChecker\PARSER_TYPE_FILE);
  assertEquals($arrParser['callback'], $cloCallback);

}

/**
  * Expect parser registration to reject duplicate parser names.
  **/
function testParserRegisterRejectsDuplicateName() {

  $strName     = 'ParserRegisterRejectsDuplicateName' . uniqid();
  $cloCallback = function (string $strFilePath) { return $strFilePath; };

  \ImmanentCodeChecker\Parser\register($strName,
                                       \ImmanentCodeChecker\PARSER_TYPE_FILE,
                                       $cloCallback);

  $cloTest = function () use ($strName, $cloCallback) {

    \ImmanentCodeChecker\Parser\register($strName,
                                         \ImmanentCodeChecker\PARSER_TYPE_FILE,
                                         $cloCallback);

  };

  expectException($cloTest, "\Exception");

}

/**
  * Expect parser registration to reject empty parser names.
  **/
function testParserRegisterRejectsEmptyName() {

  $cloTest = function () {

    \ImmanentCodeChecker\Parser\register('',
                                         \ImmanentCodeChecker\PARSER_TYPE_FILE,
                                         function (string $strFilePath) { return $strFilePath; });

  };

  expectException($cloTest, "\Exception");

}

/**
  * Expect parser registration to reject parser names with surrounding
  * whitespace.
  **/
function testParserRegisterRejectsNameWithSurroundingWhitespace() {

  $cloTest = function () {

    \ImmanentCodeChecker\Parser\register(' InvalidParserName ',
                                         \ImmanentCodeChecker\PARSER_TYPE_FILE,
                                         function (string $strFilePath) { return $strFilePath; });

  };

  expectException($cloTest, "\Exception");

}

/**
  * Expect parser registration to reject unknown parser types.
  **/
function testParserRegisterRejectsUnknownType() {

  $cloTest = function () {

    \ImmanentCodeChecker\Parser\register('ParserRegisterRejectsUnknownType' . uniqid(),
                                         'unknown-parser-type',
                                         function (string $strFilePath) { return $strFilePath; });

  };

  expectException($cloTest, "\Exception");

}
