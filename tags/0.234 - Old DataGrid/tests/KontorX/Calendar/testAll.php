<?php
if (!defined('SETUP_TEST')) {
	require_once '../../setupTest.php';
}

$testAll = new TestSuite();

// Context
$testAll->addFile('./MonthTest.php');
$testAll->addFile('./WeekTest.php');
$testAll->addFile('./WeeksTest.php');


$testAll->run(new TextReporter());