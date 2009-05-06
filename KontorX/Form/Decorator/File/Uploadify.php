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
		// only setings keys
		$options = array_intersect_key($options, array_flip($this->_uploadifyOptions));
		// set default
		$options += $this->_uploadifyDefaultOptions;

		require_once 'Zend/Json/Encoder.php';
		$options = Zend_Json_Encoder::encode($options);

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
		$script = sprintf('jQuery("#%s").fileUpload(%s)', $id, $options);

		$element->getView()->inlineScript()->appendScript($script);
		
		$nav = '<div class="uploadify-nav"><a class="uploadify-upload" href="%s">Wyślij pliki</a> <a class="uploadify-clear-queue" href="%s">Wyczyść kolejkę</a></div>';
  		$nav = sprintf($nav,
  				 	   sprintf('javascript:$(\'#%s\').fileUploadStart();', $id),
  				 	   sprintf('javascript:$(\'#%s\').fileUploadClearQueue();', $id));
		
		$placement = $this->getPlacement();
		$separator = $this->getSeparator();
		
		switch ($placement) {
            case self::APPEND:
                return $content . $separator . $nav;
            case self::PREPEND:
                return $nav . $separator . $content;
        }
	}	
}