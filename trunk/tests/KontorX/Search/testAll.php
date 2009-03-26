<?php
if (!defined('SETUP_TEST')) {
	require_once '../../setupTest.php';
}

$testAll = new TestSuite();

// Context
$testAll->addFile('./ContextTest.php');
$testAll->addFile('./ContextHelperInputFormatingTest.php');

// Interpreter
$testAll->addFile('./Interpreter/ArrayKeyExsistsTest.php');
$testAll->addFile('./Interpreter/ArrayKeyValueExsistsTest.php');
$testAll->addFile('./Interpreter/ContextToSeparatorTest.php');
$testAll->addFile('./Interpreter/DateTest.php');
$testAll->addFile('./Interpreter/InArrayTest.php');

// Logic
$testAll->addFile('./Logic/AndLogicTest.php');
$testAll->addFile('./Logic/OrLogicTest.php');

// Semantic
$testAll->addFile('./SemanticTest.php');


$testAll->run(new TextReporter());