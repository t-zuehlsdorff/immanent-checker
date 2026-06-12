<?php

namespace APHPUnit\Testcases;

require_once __DIR__ . '/../../config.inc.php';

/**
  * Expect Parser\get() to return the result stored by a parser.
  **/
function testParserGetReturnsStoredResult() {

  $strParserName = 'ParserGetReturnsStoredResult' . uniqid();
  $arrExpected   = array('token' => 'test');

  $objResults = new \ImmanentChecker\DataObjectPool(\ImmanentChecker\PARSER_RESULT);
  $objResults->add($strParserName,
                   array('name'      => $strParserName,
                         'full_path' => '/tmp/test.php',
                         'result'    => $arrExpected));

  $arrResult = \ImmanentChecker\Parser\get($strParserName);

  assertEquals($arrResult, $arrExpected);

  $objResults->delete($strParserName);

}

/**
  * Expect Parser\get() to throw an exception when the parser name does not
  * exist in the result pool.
  **/
function testParserGetThrowsForUnknownParser() {

  $cloTest = function () {

    \ImmanentChecker\Parser\get('ParserGetThrowsForUnknownParser' . uniqid());

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

  \ImmanentChecker\Parser\register($strParserName,
                                       \ImmanentChecker\PARSER_TYPE_FILE,
                                       function (string $strFilePath) {

                                         return 'parsed:' . basename($strFilePath);

                                       },
                                       'src/*.php');

  \ImmanentChecker\Check\register(\ImmanentChecker\STAGE_FILE,
                                      $strCheckName,
                                      function (\ImmanentChecker\DataObject $objFile) use ($strCheckName, $strParserName, &$arrCaptured) {

                                        if($objFile->get('relative_path') !== 'src/Test.php')
                                          return;

                                        $arrCaptured[] = \ImmanentChecker\Parser\get($strParserName);

                                      });

  \ImmanentChecker\Explore\project(__DIR__ . '/../explore/test-data/project');
  \ImmanentChecker\Check\file();

  assertEquals($arrCaptured, array('parsed:Test.php'));

  $cloTest = function () use ($strParserName) {

    \ImmanentChecker\Parser\get($strParserName);

  };

  expectException($cloTest, "\Exception");

}
