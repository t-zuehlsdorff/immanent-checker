<?php

namespace APHPUnit\Testcases;

require_once __DIR__ . '/../../config.inc.php';

/**
  * Expect Parser\get() to return the result stored by a parser.
  **/
function testParserGetReturnsStoredResult() {

  $strParserName = 'ParserGetReturnsStoredResult' . uniqid();
  $arrExpected   = array('token' => 'test');

  $objResults = new \ImmanentCodeChecker\DataObjectPool(\ImmanentCodeChecker\PARSER_RESULT);
  $objResults->add($strParserName,
                   array('name'      => $strParserName,
                         'full_path' => '/tmp/test.php',
                         'result'    => $arrExpected));

  $arrResult = \ImmanentCodeChecker\Parser\get($strParserName);

  assertEquals($arrResult, $arrExpected);

  $objResults->delete($strParserName);

}

/**
  * Expect Parser\get() to throw an exception when the parser name does not
  * exist in the result pool.
  **/
function testParserGetThrowsForUnknownParser() {

  $cloTest = function () {

    \ImmanentCodeChecker\Parser\get('ParserGetThrowsForUnknownParser' . uniqid());

  };

  expectException($cloTest, "\Exception");

}

/**
  * Expect Parser\get() to return the result during a file check run and to
  * fail after the run when results have been discarded.
  **/
function testParserGetWorksInsideFileCheckAndFailsAfter() {

  $strCheckName   = 'ParserGetWorksInsideFileCheckAndFailsAfter';
  $strParserName  = 'ParserGetWorksInsideFileCheckAndFailsAfterParser' . uniqid();
  $strProjectPath = realpath(__DIR__ . '/../explore/test-data/project');
  $arrCaptured    = array();

  \ImmanentCodeChecker\Parser\register($strParserName,
                                       \ImmanentCodeChecker\PARSER_TYPE_FILE,
                                       function (string $strFilePath) {

                                         return 'parsed:' . basename($strFilePath);

                                       },
                                       'src/*.php');

  \ImmanentCodeChecker\Check\register(\ImmanentCodeChecker\STAGE_FILE,
                                      $strCheckName,
                                      function (\ImmanentCodeChecker\DataObject $objFile) use ($strCheckName, $strParserName, &$arrCaptured) {

                                        if($objFile->get('relative_path') !== 'src/Test.php')
                                          return;

                                        $arrCaptured[] = \ImmanentCodeChecker\Parser\get($strParserName);

                                      });

  \ImmanentCodeChecker\Explore\project(__DIR__ . '/../explore/test-data/project');
  \ImmanentCodeChecker\Check\file();

  assertEquals($arrCaptured, array('parsed:Test.php'));

  $cloTest = function () use ($strParserName) {

    \ImmanentCodeChecker\Parser\get($strParserName);

  };

  expectException($cloTest, "\Exception");

}
