<?php

namespace ImmanentChecker\Error;

/**
  * Return the validator used for project-like errors.
  *
  * A project-like error has the following structure:
  *
  * array('check'   => string,   // name of the reporting check
  *       'message' => string,   // human-readable error description
  *       'files'   => array,    // project-relative affected files
  *       'details' => string)   // optional JSON-encoded additional information
  *
  * Project-like errors describe violations that belong to the filtered project
  * or the complete project as a whole. They are not tied to one specific
  * directory or file. If a violation belongs to a concrete directory or file,
  * the corresponding directory or file error API should be used instead.
  *
  * The check field identifies the rule that reported the error. The message
  * field describes the violated expectation in a form that can be shown to the
  * user. The files field can list project-relative files affected by the
  * project-level violation. The details field stores additional check-specific
  * information as JSON string, so it stays flexible while remaining easy to
  * output and serialize.
  *
  * All string fields are required and must contain at least one non-whitespace
  * character. Leading and trailing whitespace is rejected, so the stored result
  * is already normalized for output and comparison. Every file listed in files
  * must follow the same string rules. The files field is required and may be
  * empty. The details field is required and may be an empty string. If details
  * is not empty, it must contain valid JSON.
  *
  * The validator protects the public error API from storing malformed
  * project-like errors, which is important because checks can be implemented
  * externally.
  **/
function getProjectErrorValidator(): callable {

  return '\ImmanentChecker\Error\validateProjectError';

}
