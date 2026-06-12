<?php

namespace APHPUnit\Testcases;

require_once __DIR__ . '/../../config.inc.php';

/**
  * Expect json() to return an empty string when no errors exist.
  **/
function testJsonReturnsEmptyStringWithoutErrors() {

  $strResult = \ImmanentChecker\Output\json();

  assertEquals($strResult, '');

}

/**
  * Expect json() to return valid JSON containing a file error.
  **/
function testJsonOutputContainsFileError() {

  $strCheckName = 'JsonOutputContainsFileError' . uniqid();

  $objFilePool = new \ImmanentChecker\DataObjectPool(\ImmanentChecker\EXPLORE_FILE);
  $objFilePool->add('json-test-file-' . uniqid(),
                    array('full_path'     => '/tmp/test.php',
                          'relative_path' => 'test.php',
                          'permissions'   => '0644'));

  $objFile = new \ImmanentChecker\DataObject(null,
                                                array('full_path'     => '/tmp/test.php',
                                                      'relative_path' => 'test.php'));

  \ImmanentChecker\Error\file($strCheckName, 'test error message', $objFile, 42);

  $strResult   = \ImmanentChecker\Output\json();
  $arrDecoded  = json_decode($strResult, true);

  assertEquals(json_last_error(), JSON_ERROR_NONE);
  assertTrue(count($arrDecoded['errors']['file']) > 0);

  $arrFound = null;

  foreach($arrDecoded['errors']['file'] AS $arrError)
    if($arrError['check'] === $strCheckName)
      $arrFound = $arrError;

  assertEquals($arrFound['check'],         $strCheckName);
  assertEquals($arrFound['message'],       'test error message');
  assertEquals($arrFound['relative_path'], 'test.php');
  assertEquals($arrFound['line'],          42);

}

/**
  * Expect json() to return valid JSON containing a directory error.
  **/
function testJsonOutputContainsDirectoryError() {

  $strCheckName = 'JsonOutputContainsDirectoryError' . uniqid();

  $objDirectory = new \ImmanentChecker\DataObject(null,
                                                     array('full_path'     => '/tmp/src',
                                                           'relative_path' => 'src'));

  \ImmanentChecker\Error\directory($strCheckName, 'test directory error', $objDirectory);

  $strResult  = \ImmanentChecker\Output\json();
  $arrDecoded = json_decode($strResult, true);

  assertEquals(json_last_error(), JSON_ERROR_NONE);

  $arrFound = null;

  foreach($arrDecoded['errors']['directory'] AS $arrError)
    if($arrError['check'] === $strCheckName)
      $arrFound = $arrError;

  assertEquals($arrFound['check'],         $strCheckName);
  assertEquals($arrFound['message'],       'test directory error');
  assertEquals($arrFound['relative_path'], 'src');

}

/**
  * Expect json() to return valid JSON containing a project error with decoded
  * details.
  **/
function testJsonOutputDecodesProjectErrorDetails() {

  $strCheckName = 'JsonOutputDecodesProjectErrorDetails' . uniqid();
  $strDetails   = json_encode(array('expected' => 'Readme.md', 'found' => 3));

  \ImmanentChecker\Error\project($strCheckName,
                                     'test project error',
                                     array('src/File.php'),
                                     $strDetails);

  $strResult  = \ImmanentChecker\Output\json();
  $arrDecoded = json_decode($strResult, true);

  assertEquals(json_last_error(), JSON_ERROR_NONE);

  $arrFound = null;

  foreach($arrDecoded['errors']['project'] AS $arrError)
    if($arrError['check'] === $strCheckName)
      $arrFound = $arrError;

  assertEquals($arrFound['check'],              $strCheckName);
  assertEquals($arrFound['message'],            'test project error');
  assertEquals($arrFound['files'],              array('src/File.php'));
  assertEquals($arrFound['details']['expected'], 'Readme.md');
  assertEquals($arrFound['details']['found'],    3);

}

/**
  * Expect json() to omit the details field when it is empty.
  **/
function testJsonOutputOmitsEmptyDetails() {

  $strCheckName = 'JsonOutputOmitsEmptyDetails' . uniqid();

  \ImmanentChecker\Error\project($strCheckName, 'no details error');

  $strResult  = \ImmanentChecker\Output\json();
  $arrDecoded = json_decode($strResult, true);

  $arrFound = null;

  foreach($arrDecoded['errors']['project'] AS $arrError)
    if($arrError['check'] === $strCheckName)
      $arrFound = $arrError;

  assertFalse(array_key_exists('details', $arrFound));

}

/**
  * Expect json() to decode a complete-project error with details correctly.
  **/
function testJsonOutputDecodesCompleteProjectErrorDetails() {

  $strCheckName = 'JsonOutputDecodesCompleteProjectErrorDetails' . uniqid();
  $strDetails   = json_encode(array('reason' => 'missing vendor'));

  \ImmanentChecker\Error\completeProject($strCheckName,
                                             'complete project error',
                                             array(),
                                             $strDetails);

  $strResult  = \ImmanentChecker\Output\json();
  $arrDecoded = json_decode($strResult, true);

  $arrFound = null;

  foreach($arrDecoded['errors']['complete_project'] AS $arrError)
    if($arrError['check'] === $strCheckName)
      $arrFound = $arrError;

  assertEquals($arrFound['details']['reason'], 'missing vendor');

}
