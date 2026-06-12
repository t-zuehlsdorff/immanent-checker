[[_TOC_]]

# Immanent Checker

The Immanent Checker:

* reduces the need of writing Unit-Tests while improving source code quality
* allows linting the whole project by being language agnostic
* enables complex tests through the whole project
* can be easily extended to cover your needs in these topics

# Background Theory

Modern linters focus on source code structure, while static analysis tools focus
on issues that arise from the structure of the code. Both need the source code,
but they address very different needs and have typical limitations at the same
time.

This separation is arbitrary and obstructive. It created a gap that the
**Immanent Checker** closes: checking expected code and expected code behavior.

To illustrate this, consider a simple example: reading a file at a given path.
Every good function will check:

1. whether the path exists
1. whether the file is readable
1. whether opening the file succeeded
1. whether each read access succeeded

Unit tests are commonly used to check whether the code *behaves* accordingly.
This is unnecessary, because each of these points can be checked through source
code analysis. The source code shows whether the corresponding code exists or
not. A general check of the codebase for this requirement therefore reduces the
effort needed for unit tests. At the same time, it improves quality because the
rule is applied coherently across the project and does not need to be tested for
each individual function.

There are many such semantic dependencies between functionality and
expectation. They are usually specific, but cross-project. Projects designed for
high performance or low development effort may intentionally choose not to
follow such a rule. Other projects may always value it, except for a specific
hotspot that receives special handling.

The **Immanent Checker** makes it possible to check exactly these semantic
relationships. Linting, style guide enforcement, and static code analysis are
only a subset of its work.

# Use Cases

The tool is not limited to application source code. Because checks operate on
project structures and parser output, the same model can be used to solve very
different validation problems. Typical use cases include:

* checking a PHP backend together with an HTML frontend
* validating the structure of Makefiles in the FreeBSD ports tree
* detecting deprecated code sections
* enforcing conventions in configuration files
* verifying that generated files follow the expected layout
* checking whether every CLI command class is registered in a central registry
* checking whether every migration has a matching rollback definition
* checking whether every public API route is documented
* checking whether configuration files only use allowed keys
* checking whether SQL files use forbidden or deprecated constructs
* checking whether plugin directories always contain a manifest, entry point,
  and tests
* checking whether translation keys used in templates exist in language files
* checking whether Dockerfiles or Makefiles follow project-wide structure rules

# How It Works

To use the **Immanent Checker** effectively, it helps to understand how it
works. A project run consists of 3 phases, which are described below:

## 1. Exploration

In the first step, the *complete* project is explored. The checker creates an
overview of the entire project at file-system level. This includes:

* all files
* all directories
* file permissions

At this point, there is *no* content-level validation yet. Exploration is used
to take inventory of the project. Only what is known can be checked.

## 2. Analysis

Analysis happens in 4 sections. It is especially important to understand that
checks are registered for a specific analysis section. When that section is
processed, all checks registered for it are called one after another. They
receive the context of the current section as an argument and are responsible
for the rest of their analysis themselves.

One might intuitively expect the system to iterate over all files and call the
different checks for each file. That iteration is not the central control flow
of the system. The central control flow is the analysis section for which a
check registered itself. The sections are:

1. The complete project

Some checks analyze the complete project, for example to verify whether naming
conventions or directory structures are followed. This section can also be used
to check for the existence of directories and files that should not be analyzed
further.

2. The complete project after exclusion filters

Exclusion filters can be defined to prevent files or directories from being
checked. A typical example is excluding third-party code in directories such as
`vendor` or `node_modules`.

The separation between the complete project and the project after exclusion
filters is intentional. Some checks need to know that certain files or
directories exist without analyzing their contents. For example, a check can
verify that third-party code exists and is excluded correctly, while other
checks only operate on the project's own code.

If a check needs to inspect the entire *own* project, it is executed here. For
example, naming conventions for files and directories are checked in this
section.

This section is also useful for checks that need relationships across the
project's own code. For example, a check can collect all functions found in the
source code and verify that each of them is referenced by at least one test
case. Third-party code remains excluded, so vendor functions do not affect the
result.

3. An entire directory

Checks that need to inspect a directory recursively are executed here. Examples
include checking whether all relevant files exist in a plugin directory or
whether a directory is coherent in itself.

4. A file

The most common type of check inspects a file. Everything for which the context
of a single file is sufficient is checked here.

## 3. Result

All findings from the analysis are collected in the result. It is important to
understand that the **Immanent Checker** has an absolute quality standard.
When a check is defined, every violation is considered an **error**.

Severity levels are intentionally not part of this model. An enabled check
describes a hard project requirement. If that requirement is violated, the
result is erroneous.

Accordingly, the result consists of a list of documented violations with the
relevant information needed to fix them.

Furthermore, a single error is enough to return an exit code != 0, so CI systems
fail accordingly.

# Checks

A check is registered for an analysis section. When it is called, it receives
the active context of that section as an argument. Depending on the section,
this is the complete project structure, the project structure after exclusion
rules, the path to a directory, or the path to a file.

Checks for directories and files can also register with an optional pattern. If
such a pattern is set, the check is only called when the path that would be
passed to it matches the pattern. Pattern matching uses PHP's `fnmatch`
semantics, which are similar to common shell-style glob patterns. Patterns are
matched against project-relative paths, without the project root prefix. For
example:

* `*.php` matches every project-relative file path ending in `.php`
* `src/*.php` matches PHP files directly inside `src`
* `src/*/Controller.php` matches `Controller.php` one directory below `src`

The behavior of a check is fully under its own control. Some checks only read a
file, for example to verify that it always ends with an empty line. Other checks
call a parser and operate on its result. A check can call one or more parsers to
achieve its goal. This is intentional: the checker framework does not prescribe
whether a check works on raw files, parser output, or any other derived
representation.

A check reports violations through the checker API. It can report any number of
violations during a single run.

# Check Suites

Checks are loaded through check suites. A check suite is a directory that
contains the checks for a run. The suite may be small and only contain one
registration file, but it may also be a complete project with its own source
code, dependencies, tests, and documentation.

The checker only needs one defined entry point from the suite. That entry point
loads everything the suite needs and registers its checks through the checker
API.

For example, a suite can be structured like this:

```text
my-check-suite/
  register.php
  composer.json
  vendor/
  src/
  tests/
  Readme.md
```

The `register.php` file is responsible for preparing the suite and registering
the checks. It is executed by the checker when the suite is loaded.

The file should contain only suite bootstrap and registration code. A typical
`register.php` loads the suite's own dependencies first and then registers
checks and, later, parsers:

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

\ImmanentChecker\Check\register(...);
```

The file must not start a run, explore a project, execute checks, or print
output. Those actions are controlled by the checker. The suite only declares
what it provides.

The registration should stay direct and easy to inspect. Even complex suites
should make it clear from `register.php` which checks are registered.

This keeps the checker itself small. Complex checks and check collections can
live in their own projects, with their own internal structure and their own
tests. The checker only defines the run, the analysis stages, and the APIs used
to register checks and report errors.

# CLI

The command line interface defines which suites and projects are part of a run.
At least one suite and at least one project must be given. Both can be provided
multiple times, which allows composing a run from several check suites and
running them for several project directories.

Exclude patterns can be provided multiple times. They are passed to project
exploration and are matched against project-relative paths.

For every given project, the selected suites are loaded and all check stages are
executed.

Example:

```bash
bin/immanent-checker --suite /path/to/base-suite \
  --suite /path/to/project-suite \
  --exclude 'vendor/*' \
  --exclude 'node_modules/*' \
  --project /path/to/project
```

# Project Runs

A project run loads one or more check suites, explores one project directory
with the given exclude patterns, and executes all check stages.

The run API is intentionally small:

```php
\ImmanentChecker\Run\project($strProjectPath,
                                 $arrSuitePaths,
                                 array('vendor/*', 'node_modules/*'));
```

Every suite directory must contain a `register.php` file. This file is loaded
before the project is explored and is responsible for registering the checks
that should run.

# Parser

The **Immanent Checker** is language-agnostic. It does not assume any
specific language and is explicitly designed to check multiple languages within
a project by using parsers.

A parser reads a file and creates a defined form from it. This can be a syntax
tree, a token stream, a list of metadata, or any other format that is useful for
checks.

Parsers are independent components. A check does not bring its own parser. If a
check needs parser output, it declares that parser as a requirement and works
with its output format.

Accordingly, parsers make file contents understandable. Checks verify whether
these contents meet the expectations of the project.

Parsers can be provided by the checker itself or by a check suite. Built-in
parsers are useful for common formats. Suite parsers are useful for specialised
formats, project-specific languages, framework conventions, or complex
domain-specific analysis.

Custom parsers can be registered by a suite. This makes it possible to check
additional languages, configuration formats, or completely custom syntaxes
without adding them to the checker core.

Parser registration requires a unique parser name, a parser type, a callback,
and optionally a pattern. For now, only file parsers are supported. A file
parser callback receives the path to the file it should parse.

```php
\ImmanentChecker\Parser\register('my.parser',
                                     \ImmanentChecker\PARSER_TYPE_FILE,
                                     function (string $strFilePath) {
                                       return file_get_contents($strFilePath);
                                     },
                                     '*');
```

The pattern is matched against project-relative paths using PHP's `fnmatch()`
semantics, which are similar to common shell-style glob patterns. If no pattern
is given, `*` is used and the parser may apply to every file.

It is possible to register multiple parsers for the same language. This is
useful because different parsers produce different output formats. Depending on
the goal, one format or another may be better suited for implementing a check.

For file checks, parser usage is prepared before the checks are executed. The
file stage collects all parser requirements declared by the registered file
checks. For each file, every required parser is executed once and stores its
result. The file checks are executed afterwards and read the prepared parser
results.

This keeps parsing separate from checking. A check does not parse the file
itself. It reads parser results that were prepared for the file through
`Parser\get()`:

```php
$arrTokens = \ImmanentChecker\Parser\get(\ImmanentChecker\PARSER_PHP_TOKEN_GET_ALL);
```

The function takes the name of the registered parser and returns its result for
the file currently being checked. A single file can be processed by multiple
parsers. Each result is accessible independently by its parser name:

```php
$arrTokens = \ImmanentChecker\Parser\get(\ImmanentChecker\PARSER_PHP_TOKEN_GET_ALL);
$arrCustom = \ImmanentChecker\Parser\get('MY_CUSTOM_PARSER');
```

If no result exists for the given parser name, the parser either did not match
the current file or the name is incorrect. Both cases cause an exception.

Within a run, files are assumed not to change. Parser results are therefore
read-only runtime data. This makes the model suitable for later optimisation:
parser results can be cached per file, stored in shared memory, or reused by
parallel check workers without changing the responsibility of checks.

In general, parsers operate at file level. Everything that concerns project-wide
relationships remains the responsibility of checks.

## Built-In Parser: PHP_TOKEN_GET_ALL

`PHP_TOKEN_GET_ALL` is a built-in file parser for PHP source files. It is based
on PHP's [`token_get_all()`](https://www.php.net/manual/en/function.token-get-all.php)
function and uses PHP's official
[`token_name()`](https://www.php.net/manual/en/function.token-name.php) function
to resolve token ids to readable token names.

The PHP manual documents the available parser tokens in the
[List of Parser Tokens](https://www.php.net/manual/en/tokens.php). Those token
names are the names used in this parser output for PHP tokens.

The parser is registered automatically:

```php
\ImmanentChecker\PARSER_PHP_TOKEN_GET_ALL
```

It is registered as a file parser with the pattern `*.php`, so it only applies
to project-relative PHP file paths.

It can also be referenced by its literal parser name:

```php
PHP_TOKEN_GET_ALL
```

The parser normalizes PHP's native token stream. Native `token_get_all()` returns
PHP tokens as arrays, but single-character tokens such as `(`, `)`, `{`, `}`,
`;`, or `=` as plain strings. `PHP_TOKEN_GET_ALL` converts both forms into one
consistent structure:

```php
array('type'  => int,
      'name'  => string,
      'value' => string,
      'line'  => int)
```

For PHP tokens, `type` is the original integer token id and `name` is the
official token name:

```php
array('type'  => T_STRING,
      'name'  => 'T_STRING',
      'value' => 'strtoupper',
      'line'  => 3)
```

For single-character tokens, `type` is `-1` and `name` is the character itself:

```php
array('type'  => -1,
      'name'  => '(',
      'value' => '(',
      'line'  => 3)
```

This allows checks to iterate over one token list without special handling for
PHP's mixed token formats.
