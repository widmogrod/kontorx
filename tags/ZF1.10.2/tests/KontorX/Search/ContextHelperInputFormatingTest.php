<?php
if (!defined('SETUP_TEST')) {
	require_once '../../setupTest.php';
}

function get_quoted_text($input) {
	if (substr_count($input, '"') > 1) {
		$explod = explode('"', $input);
		foreach ($explod as $key => $value) {
			$value = trim($value);
			// Tylko parzyste części nie są w cudzysłowie "" 
			if ($key % 2 == 0) {
				// czy klucz zawiera inne podzielne znaki?
				if(substr_count($value, ' ') > 0) {
					foreach (explode(' ', $value) as $value) {
						$words[] = $value;
					}
				} else {
					$words[] = $value;
				}
			} else {
				$words[] = $value;				
			}
		}
		// Resetowanie kluczy
		$words = array_diff($words, array(''));
		$words = array_values($words);
		return $words;
	}
	return null;
}

class ContextHelperInputFormatingTest extends UnitTestCase {
	
	public function testQuotedText1() {
		$correct = array('to','jest','cytowany');
		$context = 'to jest "cytowany"';
		
		$result = get_quoted_text($context);
		$result = array_values($result);
//		$this->dump($result);
		
		$this->assertEqual($result, $correct, sprintf("Input nie jest taki sam jak oczekiwany '%s'", $correct));
    }
	
	public function testQuotedText2() {
		$correct = array('to','jest','cytowany, 12a','22 tekst','a','jak!');
		$context = 'to jest "cytowany, 12a" "22 tekst" a jak!';
		
		$result = get_quoted_text($context);
		$result = array_values($result);
//		$this->dump($result);
		
		$this->assertEqual($result, $correct, sprintf("Input nie jest taki sam jak oczekiwany '%s'", $correct));
    }
    
	public function testQuotedText3() {
		$correct = array('to','jest','cytowany, 12a','22 tekst','a','jak!,','to','jest','cytowany, 12a','33 tekst','a','jak.');
		$context = 'to jest "cytowany, 12a" "22 tekst" a jak!, to jest "cytowany, 12a" "33 tekst" a " jak.';
		
		$result = get_quoted_text($context);
		$result = array_values($result);
//		$this->dump($result);
		
		$this->assertEqual($result, $correct, sprintf("Input nie jest taki sam jak oczekiwany '%s'", $correct));
    }
}

$r = new ContextHelperInputFormatingTest();
$r->run(new TextReporter());