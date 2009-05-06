<?php
/**
 * @author gabriel
 *
 */
interface KontorX_Search_Semantic_Context_Interface extends  Iterator, Countable {
	/**
	 * @return void
	 */
	public function remove();
	
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
	public function addOutput($name, $data = null);

	/**
	 * @return array|null
	 */
	public function getOutput();
	
	/**
	 * @return void
	 */
	public function clearOutput();
}