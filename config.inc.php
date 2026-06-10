<?php

namespace ImmanentCodeChecker;

const STAGE_COMPLETE_PROJECT = 'stage_complete';
const STAGE_PROJECT          = 'stage_project';
const STAGE_DIRECTORY        = 'stage_directory';
const STAGE_FILE             = 'stage_file';

const EXPLORE_COMPLETE_PROJECT = 'explore_complete';
const EXPLORE_PROJECT          = 'explore_project';
const EXPLORE_DIRECTORY        = 'explore_directory';
const EXPLORE_FILE             = 'explore_file';

const ERROR_COMPLETE_PROJECT = 'error_complete_project';
const ERROR_DIRECTORY        = 'error_directory';
const ERROR_FILE             = 'error_file';
const ERROR_PROJECT          = 'error_project';

require_once __DIR__ . '/inc/DataObject.class.php';
require_once __DIR__ . '/inc/DataObjectPool.class.php';

require_once __DIR__ . '/inc/cli/parseArguments.func.php';

require_once __DIR__ . '/inc/check/initPools.func.php';
require_once __DIR__ . '/inc/check/register.func.php';
require_once __DIR__ . '/inc/check/all.func.php';
require_once __DIR__ . '/inc/check/completeProject.func.php';
require_once __DIR__ . '/inc/check/project.func.php';
require_once __DIR__ . '/inc/check/directory.func.php';
require_once __DIR__ . '/inc/check/file.func.php';

require_once __DIR__ . '/inc/error/initDirectoryPool.func.php';
require_once __DIR__ . '/inc/error/initFilePool.func.php';
require_once __DIR__ . '/inc/error/getProjectErrorValidator.func.php';
require_once __DIR__ . '/inc/error/initProjectPool.func.php';
require_once __DIR__ . '/inc/error/completeProject.func.php';
require_once __DIR__ . '/inc/error/directory.func.php';
require_once __DIR__ . '/inc/error/file.func.php';
require_once __DIR__ . '/inc/error/project.func.php';

require_once __DIR__ . '/inc/explore/project.func.php';

require_once __DIR__ . '/inc/run/project.func.php';

Check\initPools();
Error\initDirectoryPool();
Error\initFilePool();
Error\initProjectPool();
