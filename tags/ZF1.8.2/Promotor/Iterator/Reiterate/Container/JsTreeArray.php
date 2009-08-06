<?php
/**
 * Create recursive ArrayObject for @see http://www.jstree.com
 * implements data structure @see http://www.jstree.com/reference/_documentation/4_data.html
 * 
 * @author gabriel
 */
class Promotor_Iterator_Reiterate_Container_JsTreeArray
	extends ArrayObject implements KontorX_Iterator_Reiterate_Container {

	public function addChildren(KontorX_Iterator_Reiterate_Container $children, $depth) {
		if ($depth < 1) {
			// dodaj rekord główny
			$this->append($children);
		} else {
			// dodaj rodzica
			if (!$this->offsetExists('children')) {
				$this->offsetSet('children', array($children));
			} else {
				$offsetChildren = $this->offsetGet('children');
				array_push($offsetChildren, $children);
				$this->offsetSet('children', $offsetChildren);
			}
		}
	}

	public function getInstance($data = null) {
		if (null !== $data) {
			$data = array(
				'attributes' => array(
					'id' => $data->id
				),
				'data' => $data->name		
			);
			return new self((array) $data);
		}
		return new self;
	}
}