<?php
class KontorX_Template_Controller_Plugin_Template extends Zend_Controller_Plugin_Abstract {

	/**
	 * @param KontorX_Template $template
	 * @return void
	 */
	public function __construct(KontorX_Template $template) {
		$this->setTemplate($template);
	}
	
	/**
	 * @var KontorX_Template
	 */
	protected $_template;

	/**
	 * @param KontorX_Template $template
	 * @return void
	 */
	public function setTemplate(KontorX_Template $template) {
		$this->_template = $template;
	}

	/**
	 * @return KontorX_Template
	 */
	public function getTemplate() {
		return $this->_template;
	}

	public function postDispatch(Zend_Controller_Request_Abstract $request) {
		if (!$request->isDispatched()) {
			return;
		}

		$template = $this->getTemplate();
		if (false === $template->isStartedLayout()) {
			return;
		}

		$layout = $template->getLayout();

        /**
         * @todo nie zawsze musi to oznaczać że nie nalerzy
         * inicjować ścieżek skoro juz jakieś są..
         */
        if (null === $layout->getLayoutPath()) {
        	$this->_initLayoutPath($layout);
        }
        
		// ustawienie nazwy szablonu
        if (null !== ($layoutName = $template->getLayoutName())) {
        	$layout->setLayout($layoutName);
        }

        if (!$template->isAllowedStyleConfig()) {
        	return;
        }

        $templateConfig = $template->getStyleConfig();
        if (isset($templateConfig->$layoutName)) {
        	$templateConfig = $templateConfig->$layoutName;
        }

        // konfiguracja też może determinowac nazwę szablonu
		if (isset($templateConfig->layout)) {
        	$layout->setLayout($templateConfig->layout);
        }

        $this->_initViewHelpers($templateConfig);
	}
	
	/**
	 * @param Zend_Layout $layout
	 * @return KontorX_Template_Controller_Plugin_Template;
	 */
	protected function _initLayoutPath(Zend_Layout $layout) {
		$template = $this->getTemplate();
		/* @var Zend_View_Interface */
		$view = $template->getView();
		$paths = $template->getTemplatePaths(true);
		$paths = array_diff($paths, $view->getScriptPaths());
		foreach ($paths as $path) {
			if (method_exists($view, 'addScriptPath')) {
                $view->addScriptPath($path);
            } else {
                $view->setScriptPath($path);
            }
		}
		return $this;
	}
	
	/**
	 * @param Zend_Config $options
	 * @return void
	 */
	protected function _initViewHelpers(Zend_Config $options) {
		$template = $this->getTemplate();
		$view = $template->getView();

		// doctype
		if (isset($options->doctype)) {
			$view->doctype($options->doctype);
		}

        // title
        if (isset($options->title)) {
            $headTitle = $view->getHelper('HeadTitle');
            $title = $options->title;
            $separator = ' ';
            if (isset($title->title)) {
            	$title = $title->title;
            	$separator = isset($title->separator)
            		? $title->separator : $separator;
            } 

            $headTitle->append($title);
            $headTitle->setSeparator($separator);
        }

		// meta
        if (isset($options->meta)
        		&& isset($options->meta->name)) {
        			
            /* @var $headMeta Zend_View_Helper_HeadMeta */
            $headMeta = $view->getHelper('HeadMeta');

            $meta = array();
            foreach ($headMeta->getContainer() as $key) {
            	if (isset($key->name)) {
            		if ($key->name == 'keywords') {
						$meta['keywords'] = true;
					} else if ($key->name == 'description') {
						$meta['description'] = true;
					}
            	}
            }

            if (!isset($meta['keywords'])
            		&& isset($options->meta->name->keywords)) {
                $headMeta->setName('keywords', $options->meta->name->keywords);
            }
            if (!isset($meta['description']) 
            		&& isset($options->meta->name->description)) {
                $headMeta->setName('description', $options->meta->name->description);
            }

            if (isset($options->meta->httpEquiv)
            		&& $options->meta->httpEquiv instanceof Zend_Config) {
	            foreach ($options->meta->httpEquiv as $obj) {
	            	$headMeta->setHttpEquiv($obj->key,
	            							$obj->content,
	            							isset($obj->modifiers) ? $obj->modifiers->toArray() : array());
	            }
            }
        }

        // script
        if (isset($options->script)) {
        	/* @var $headScript Zend_View_Helper_HeadScript */
            $headScript = $view->getHelper('HeadScript');
            $i = 0;
            foreach ($options->script->js as $file) {
                $headScript->offsetSetFile(++$i,
            								$file->src,
            								isset($file->type) ? $file->type : null,
            								isset($file->attribs) ? $file->attribs->toArray() : array());
            }
        }
        
		// inlineScript
        if (isset($options->inlineScript)) {
        	/* @var $inlineScript Zend_View_Helper_InlineScript */
            $inlineScript = $view->getHelper('InlineScript');
            $i = 0;
            foreach ($options->inlineScript->js as $file) {
            	$inlineScript->offsetSetFile(++$i,
            								$file->src,
            								isset($file->type) ? $file->type : null,
            								isset($file->attribs) ? $file->attribs->toArray() : array());
            }
        }

        // link
        if (isset($options->links)) {
        	/* @var $headLink Zend_View_Helper_HeadLink */
        	$headLink = $view->getHelper('HeadLink');
            foreach ($options->links->css->toArray() as $file) {
                if (!isset($file['rel'])) {
                	$file['rel'] = 'stylesheet';
                }
            	if (!isset($file['media'])) {
					$file['media'] = 'screen';
				}
                $headLink->headLink($file);
            }
        }
        
        
	}
}