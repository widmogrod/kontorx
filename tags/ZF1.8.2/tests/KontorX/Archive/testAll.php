<?php
if (!defined('SETUP_TEST')) {
	require_once '../../setupTest.php';
}

$testAll = new TestSuite();

// Context
$testAll->addFile('./ZipTest.php');


$testAll->run(new TextReporter());