<?php

namespace APHPUnit\Testcases;

const CLI_COMMAND = __DIR__ . '/../../bin/immanent-checker';

/**
  * Expect the CLI command to return exit code 1 and valid JSON when a suite
  * reports errors.
  **/
function testCliCommandReturnsJsonAndExitCode1WhenErrorsExist() {

  $strCommand = PHP_BINARY    . ' ' . escapeshellarg(CLI_COMMAND) .
                ' --suite '   . escapeshellarg(__DIR__ . '/../run/test-data/suite') .
                ' --exclude ' . escapeshellarg('vendor/*') .
                ' --project ' . escapeshellarg(__DIR__ . '/../explore/test-data/project');

  exec($strCommand, $arrOutput, $intExitCode);

  $strJson    = implode("\n", $arrOutput);
  $arrDecoded = json_decode($strJson, true);

  assertEquals($intExitCode, 1);
  assertEquals(json_last_error(), JSON_ERROR_NONE);
  assertTrue(array_key_exists('errors', $arrDecoded));

}

/**
  * Expect the CLI command to return a non-zero exit code for invalid arguments.
  **/
function testCliCommandRejectsInvalidArguments() {

  $strCommand = PHP_BINARY  . ' ' . escapeshellarg(CLI_COMMAND) .
                ' --suite ' . escapeshellarg(__DIR__) .
                ' 2>&1';

  exec($strCommand, $arrOutput, $intExitCode);

  assertEquals($intExitCode, 1);
  assertTrue(count($arrOutput) > 0);

}

/**
  * Expect the CLI command to collect errors from multiple projects into one
  * JSON output.
  **/
function testCliCommandCollectsErrorsFromMultipleProjects() {

  $strCommand = PHP_BINARY    . ' ' . escapeshellarg(CLI_COMMAND) .
                ' --suite '   . escapeshellarg(__DIR__ . '/../run/test-data/suite') .
                ' --project ' . escapeshellarg(__DIR__ . '/../explore/test-data/project') .
                ' --project ' . escapeshellarg(__DIR__ . '/../explore/test-data/project');

  exec($strCommand, $arrOutput, $intExitCode);

  $strJson    = implode("\n", $arrOutput);
  $arrDecoded = json_decode($strJson, true);

  assertEquals($intExitCode, 1);
  assertEquals(json_last_error(), JSON_ERROR_NONE);
  assertTrue(count($arrDecoded['errors']['project']) >= 2);

}
