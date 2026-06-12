<?php

namespace ImmanentChecker\Output;

/**
  * Collect all errors from every error pool and return them as a JSON string.
  *
  * The output structure groups errors by stage:
  *
  *   { "errors": {
  *       "complete_project": [ ... ],
  *       "project":          [ ... ],
  *       "directory":        [ ... ],
  *       "file":             [ ... ]
  *     }
  *   }
  *
  * Project and complete-project errors carry a details field that is stored as
  * a JSON string inside the error pool. This function decodes it into a native
  * value so it becomes a proper part of the JSON output instead of a quoted
  * string inside a string.
  *
  * @returns string - encoded JSON, or an empty string when no errors exist
  **/
function json() : string {

  $arrOutput = array('errors' => array('complete_project' => collectProjectErrors(\ImmanentChecker\ERROR_COMPLETE_PROJECT),
                                       'project'          => collectProjectErrors(\ImmanentChecker\ERROR_PROJECT),
                                       'directory'        => collectDirectoryErrors(),
                                       'file'             => collectFileErrors()));

  $intTotal = count($arrOutput['errors']['complete_project'])
            + count($arrOutput['errors']['project'])
            + count($arrOutput['errors']['directory'])
            + count($arrOutput['errors']['file']);

  if(0 === $intTotal)
    return '';

  return json_encode($arrOutput, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

}
