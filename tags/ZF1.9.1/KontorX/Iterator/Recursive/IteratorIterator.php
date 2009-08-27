<?php
class KontorX_Iterator_Recursive_IteratorIterator extends RecursiveIteratorIterator {
	public function iterate() {
		$this->a = $this->p = new ArrayObject();

		while ($this->valid()) {
			
			$c = $this->current()->toArray();
			$cc = array(
				'name' => $c['name']
			);
			$this->c = new ArrayObject($cc);
			$this->p->append($this->c);
			$this->next();
		}
		
		Zend_Debug::dump($this->a);
	}

	public function beginChildren() {
		$this->p = $this->c;
	}

	public function endChildren() {
		$this->p = $this->a;
	}
}