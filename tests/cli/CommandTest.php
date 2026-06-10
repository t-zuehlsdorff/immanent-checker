<?php

namespace APHPUnit\Testcases;

const CLI_COMMAND = __DIR__ . '/../../bin/immanent-code-checker';

/**
  * Expect the CLI command to accept valid suite and project directories.
  **/
function testCliCommandAcceptsValidArguments() {

  $strCommand = PHP_BINARY    . ' ' . escapeshellarg(CLI_COMMAND) .
                ' --suite '   . escapeshellarg(__DIR__ . '/../run/test-data/suite') .
                ' --exclude ' . escapeshellarg('vendor/*') .
                ' --project ' . escapeshellarg(__DIR__ . '/../explore/test-data/project');

  exec($strCommand, $arrOutput, $intExitCode);

  assertEquals($intExitCode, 0);

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
  * Expect the CLI command to run every given project with the selected suites.
  **/
function testCliCommandAcceptsMultipleProjects() {

  $strCommand = PHP_BINARY    . ' ' . escapeshellarg(CLI_COMMAND) .
                ' --suite '   . escapeshellarg(__DIR__ . '/../run/test-data/suite') .
                ' --project ' . escapeshellarg(__DIR__ . '/../explore/test-data/project') .
                ' --project ' . escapeshellarg(__DIR__ . '/../explore/test-data/project');

  exec($strCommand, $arrOutput, $intExitCode);

  assertEquals($intExitCode, 0);

}
