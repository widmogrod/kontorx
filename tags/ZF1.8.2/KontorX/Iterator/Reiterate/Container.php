<?php
/**
 * @author gabriel
 *
 */
interface KontorX_Iterator_Reiterate_Container {

	/**
	 * @param KontorX_Iterator_Reiterate_Container $children
	 * @param int $depth
	 */
	public function addChildren(KontorX_Iterator_Reiterate_Container $children, $depth);
	
	/**
	 * @param mixed $data
	 * @return KontorX_Iterator_Reiterate_Container; 
	 */
	public function getInstance($data = null);
}