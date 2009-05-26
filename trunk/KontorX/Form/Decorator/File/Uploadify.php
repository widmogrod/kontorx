<?php
require_once 'Zend/Form/Decorator/Abstract.php';
require_once 'Zend/Form/Decorator/Marker/File/Interface.php';
class KontorX_Form_Decorator_File_Uploadify extends Zend_Form_Decorator_Abstract implements Zend_Form_Decorator_Marker_File_Interface {

	/**
	 * @var string
	 */
	private $_basePath = '/js/jquery/uploadify/';

	/**
	 * @param string $path
	 * @return KontorX_Form_Decorator_File_Uploadify
	 */
	public function setBasePath($path) {
		$this->_basePath = (string) $path;
		return $this;
	}
	
	/**
	 * @var string
	 */
	private $_partialScript;
	
	/**
	 * @param $partial
	 * @return KontorX_Form_Decorator_File_Uploadify
	 */
	public function setViewPartialScript($partial) {
		$this->_partialScript = (string) $partial;
		return $this;
	}
	
	/**
	 * @param string $id
	 * @return string
	 */
	protected function _getDefaultPartial($id) {
		$partial = '<div class="uploadify-nav"><a class="uploadify-upload" href="%s">Wyślij pliki</a> <a class="uploadify-clear-queue" href="%s">Wyczyść kolejkę</a></div>';
  		$partial = sprintf($partial,
  				 	   sprintf('javascript:$(\'#%s\').fileUploadStart();', $id),
  				 	   sprintf('javascript:$(\'#%s\').fileUploadClearQueue();', $id));
		return $partial;
	}

	/**
	 * @var array
	 */
	protected $_uploadifyOptions = array(
		'uploader',
		'script',
		'scriptData',
		'scriptAccess',
		'folder',
		'multi',
		'auto',
		'fileDesc',
		'fileExt',
		'fileDataName',
		'sizeLimit',
		'buttonText',
		'buttonImg',
		'rollover',
		'width',
		'height',
		'wmode',
		'cancelImg',
		'displayData',
		'onInit',
		'onSelect',
		'onSelectOnce',
		'onCancel',
		'onClearQueue',
		'onError',
		'onProgress',
		'onComplete',
		'onAllComplete'
	);
	
	
	/**
	 * @var array
	 */
	protected $_uploadifyDefaultOptions = array(
		'uploader' 	=> '[basePath]/uploader.swf',
		'folder'	=> '[basePath]/uploads-folder',
		'cancelImg'	=> '[basePath]/cancel.png'
	);
	
	/**
	 * @return string as JSON
	 */
	protected function _getOptions() {
		$options = $this->getOptions();
		
		if (!isset($options['fileDataName'])) {
			$options['fileDataName'] = $this->getElement()->getName();
		}
		
		// only setings keys
		$options = array_intersect_key($options, array_flip($this->_uploadifyOptions));
		// set default
		$options += $this->_uploadifyDefaultOptions;
		
		$result = array();
		foreach ($options as $key => $val) {
			if (is_bool($val)) {
				$val = $val ? 'true' : 'false';
			} else
			if (is_string($val)) {
				if (substr($val,0,strlen('function')) != 'function') {
					$val = "'$val'";
				}
			} else
			if (is_array($val)) {
				$val = Zend_Json::encode($val);
			}

			$result[] = $key . ':' . $val;
		}
		$options = implode(',',$result);

		$replace = array(
			'[basePath]' => trim($this->_basePath,'/'),
		);

		return str_replace(array_keys($replace),
						   array_values($replace),
						   $options);
	}
	
	public function render($content) {
		$element = $this->getElement();
		if (!$element instanceof KontorX_Form_Element_File) {
			return $content;
		}
		
		$id = $element->getId();
		$options = $this->_getOptions();
		$script = sprintf('jQuery("#%s").fileUpload({%s})', $id, $options);

		$view = $element->getView();
		$view->inlineScript()->appendScript($script);

		$partial = (null === $this->_partialScript)
			? $this->_getDefaultPartial($id)
			: $view->partial($this->_partialScript, array('id' => $id));

		$placement = $this->getPlacement();
		$separator = $this->getSeparator();
		
		switch ($placement) {
            case self::APPEND:
                return $content . $separator . $partial;
            case self::PREPEND:
                return $partial . $separator . $content;
        }
	}	
}