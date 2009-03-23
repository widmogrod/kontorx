<?php
/**
 * @author gabriel
 *
 */
interface KontorX_Search_Semantic_Context_Interface extends  Iterator, Countable {
	/**
	 * @return string
	 */
	public function getInput();
	
	/**
	 * @param string $input
	 * @return void
	 */
	public function setInput($input);
	
	/**
	 * @param mixed $data
	 * @return void
	 */
	public function setOutput($data);
	
	/**
	 * @param string $name
	 * @param mixed $data
	 * @return void
	 */
	public function addOutput($name, $data);
	
	/**
	 * @param string $name
	 * @return array|null
	 */
	public function getOutput($name = null);
	
	/**
	 * @return void
	 */
	public function clearOutput();
}