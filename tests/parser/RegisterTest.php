<?php

namespace APHPUnit\Testcases;

require_once __DIR__ . '/../../config.inc.php';

/**
  * Expect a registered parser to be stored with name, type, callback, and the
  * default pattern.
  **/
function testParserRegisterStoresParserInRegistry() {

  $strName     = 'ParserRegisterStoresParserInRegistry' . uniqid();
  $cloCallback = function (string $strFilePath) { return $strFilePath; };

  \ImmanentChecker\Parser\register($strName,
                                       \ImmanentChecker\PARSER_TYPE_FILE,
                                       $cloCallback);

  $objRegistry = new \ImmanentChecker\DataObjectPool(\ImmanentChecker\PARSER_REGISTRY);
  $arrParser   = $objRegistry->get($strName)->getAll();

  assertEquals($arrParser['name'],     $strName);
  assertEquals($arrParser['type'],     \ImmanentChecker\PARSER_TYPE_FILE);
  assertEquals($arrParser['callback'], $cloCallback);
  assertEquals($arrParser['pattern'],  '*');

}

/**
  * Expect parser registration to store an explicit file pattern.
  **/
function testParserRegisterStoresExplicitPattern() {

  $strName     = 'ParserRegisterStoresExplicitPattern' . uniqid();
  $cloCallback = function (string $strFilePath) { return $strFilePath; };

  \ImmanentChecker\Parser\register($strName,
                                       \ImmanentChecker\PARSER_TYPE_FILE,
                                       $cloCallback,
                                       '*.php');

  $objRegistry = new \ImmanentChecker\DataObjectPool(\ImmanentChecker\PARSER_REGISTRY);
  $arrParser   = $objRegistry->get($strName)->getAll();

  assertEquals($arrParser['pattern'], '*.php');

}

/**
  * Expect parser registration to reject duplicate parser names.
  **/
function testParserRegisterRejectsDuplicateName() {

  $strName     = 'ParserRegisterRejectsDuplicateName' . uniqid();
  $cloCallback = function (string $strFilePath) { return $strFilePath; };

  \ImmanentChecker\Parser\register($strName,
                                       \ImmanentChecker\PARSER_TYPE_FILE,
                                       $cloCallback);

  $cloTest = function () use ($strName, $cloCallback) {

    \ImmanentChecker\Parser\register($strName,
                                         \ImmanentChecker\PARSER_TYPE_FILE,
                                         $cloCallback);

  };

  expectException($cloTest, "\Exception");

}

/**
  * Expect parser registration to reject empty parser names.
  **/
function testParserRegisterRejectsEmptyName() {

  $cloTest = function () {

    \ImmanentChecker\Parser\register('',
                                         \ImmanentChecker\PARSER_TYPE_FILE,
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

    \ImmanentChecker\Parser\register(' InvalidParserName ',
                                         \ImmanentChecker\PARSER_TYPE_FILE,
                                         function (string $strFilePath) { return $strFilePath; });

  };

  expectException($cloTest, "\Exception");

}

/**
  * Expect parser registration to reject unknown parser types.
  **/
function testParserRegisterRejectsUnknownType() {

  $cloTest = function () {

    \ImmanentChecker\Parser\register('ParserRegisterRejectsUnknownType' . uniqid(),
                                         'unknown-parser-type',
                                         function (string $strFilePath) { return $strFilePath; });

  };

  expectException($cloTest, "\Exception");

}

/**
  * Expect parser registration to reject empty patterns.
  **/
function testParserRegisterRejectsEmptyPattern() {

  $cloTest = function () {

    \ImmanentChecker\Parser\register('ParserRegisterRejectsEmptyPattern' . uniqid(),
                                         \ImmanentChecker\PARSER_TYPE_FILE,
                                         function (string $strFilePath) { return $strFilePath; },
                                         '');

  };

  expectException($cloTest, "\Exception");

}

/**
  * Expect parser registration to reject patterns with surrounding whitespace.
  **/
function testParserRegisterRejectsPatternWithSurroundingWhitespace() {

  $cloTest = function () {

    \ImmanentChecker\Parser\register('ParserRegisterRejectsPatternWithSurroundingWhitespace' . uniqid(),
                                         \ImmanentChecker\PARSER_TYPE_FILE,
                                         function (string $strFilePath) { return $strFilePath; },
                                         ' *.php ');

  };

  expectException($cloTest, "\Exception");

}
