<?php

namespace ImmanentCodeChecker;

const STAGE_COMPLETE_PROJECT = 'stage_complete';
const STAGE_PROJECT          = 'stage_project';
const STAGE_DIRECTORY        = 'stage_directory';
const STAGE_FILE             = 'stage_file';

require_once __DIR__ . '/inc/DataObject.class.php';
require_once __DIR__ . '/inc/DataObjectPool.class.php';

require_once __DIR__ . '/inc/check/register.func.php';
