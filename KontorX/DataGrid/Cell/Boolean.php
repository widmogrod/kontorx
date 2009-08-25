<?php
require_once 'KontorX/DataGrid/Cell/Abstract.php';
class KontorX_DataGrid_Cell_Boolean extends KontorX_DataGrid_Cell_Abstract {

    /**
     * Return a context as a html/text string
     * @return string
     */
    public function render() {
    	$name = $this->getAttrib('name', $this->getColumnName());
    	$value = $this->getData($name);

    	if (is_string($value)) {
    		switch(strtolower($value)) {
    			case 'true':
    			case 'yes': $value = true; break;
    				
    			case 'false':
    			case 'no':  $value = false; break;
    		}
    	} else {
    		$value = (bool) $value;
    	}

    	if ($value) {
    		$content = $this->getAttrib('true');
    	} else {
    		$content = $this->getAttrib('false');
    	}

    	// parsuj tresc w poszukiwaniu zmiennych {{...}}
		$content = preg_replace("/{([\wd_\-^}]+)}/ie", "\$this->getData('$1')", $content);

        return $content;
    }
}