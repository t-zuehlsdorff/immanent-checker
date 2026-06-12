<?php

namespace ImmanentChecker\Error;

/**
  * Initialize the file-error pool with its validator.
  *
  * A file error has the following structure:
  *
  * array('check'         => string,   // name of the reporting check
  *       'message'       => string,   // human-readable error description
  *       'full_path'     => string,   // absolute path to the affected file
  *       'relative_path' => string,   // project-relative path to the file
  *       'line'          => ?int)     // affected line or null
  *
  * The path fields must be named exactly like the fields created during project
  * exploration. This keeps file contexts and file errors compatible without
  * translation. The line field may be null because not every file-level error
  * belongs to a specific line.
  *
  * The check field identifies the rule that reported the error. This makes the
  * result traceable even if many checks report errors for the same file. The
  * message field describes the violated expectation in a form that can be shown
  * to the user. The full_path field points to the affected file on the local
  * filesystem, while relative_path keeps the same file addressable relative to
  * the explored project. The optional line field narrows the error down to a
  * concrete source line when the check can provide one.
  *
  * All string fields are required and must contain at least one non-whitespace
  * character. Leading and trailing whitespace is rejected, so the stored result
  * is already normalized for output and comparison. The line field must either
  * be null or an integer greater than zero, because source lines are counted
  * starting at 1.
  *
  * The validator protects the public error API from storing malformed file
  * errors, which is important because checks can be implemented externally.
  **/
function initFilePool(): void {

  $objRegistry = new \ImmanentChecker\DataObjectPool(\ImmanentChecker\ERROR_FILE);
  $objRegistry->setValidator('\ImmanentChecker\Error\validateFileError');

}
