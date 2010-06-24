<?php
require_once 'KontorX/Iterator/Reiterate/Container.php';

/**
 * Create recursive ArrayObject
 * 
 * @author Gabriel
 */
class KontorX_Iterator_Reiterate_Container_DirectoryToArray
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

	/**
	 * Przygotowanie struktury plików dla
	 * @param $data DirectoryIterator
	 * @return KontorX_Iterator_Reiterate_Container_DirectoryToArray
	 */
	public function getInstance($data = null) {
		if (null !== $data) {
			$input = array(
				'label' => $data->getFilename(),
				'title' => $data->getFilename(),
				'href'  => $data->getPathname()
			);
			return new self($input);
		}
		return new self;
	}
}