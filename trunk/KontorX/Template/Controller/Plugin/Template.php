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
		if (!$template->isStartedLayout()) {
			return;
		}

        /**
         * @todo nie zawsze musi to oznaczać że nie nalerzy
         * inicjować ścieżek skoro juz jakieś są..
         */
        if (null === $layout->getLayoutPath()) {
        	$this->_initLayoutPath($layout);
        }

        if (!$template->isAllowedThemeConfig()) {
        	return;
        }

        // ustawienie nazwy szablonu
        if (null === ($layoutName = $layoutName= $layout->getLayout())) {
	        if (null !== ($layoutName = $template->getLayoutName())) {
	        	$layout->setLayout($layoutName);
	        }
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
		$view = $layout->getView();

		$template = $this->getTemplate();
		foreach ($template->getTemplatePaths(true) as $path) {
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

        // title
        if (isset($templateConfig->title)) {
            $headTitle = $view->getHelper('HeadTitle');
            $headTitle->append($templateConfig->title);
        }

		// meta
        if (isset($templateConfig->meta)
        		&& isset($templateConfig->meta->name)) {
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
            		&& isset($templateConfig->meta->name->keywords)) {
                $headMeta->setName('keywords', $templateConfig->meta->name->keywords);
            }
            if (!isset($meta['description']) 
            		&& isset($templateConfig->meta->name->description)) {
                $headMeta->setName('description', $templateConfig->meta->name->description);
            }
        }

        // script
        if (isset($templateConfig->script)) {
            $headScript = $view->getHelper('HeadScript');
            $i = 0;
            foreach ($templateConfig->script->js as $file) {
                $headScript->offsetSetFile(++$i, $file->src);
            }
        }

        // link
        if (isset($templateConfig->links)) {
        	$headLink = $view->getHelper('HeadLink');
            foreach ($templateConfig->links->css->toArray() as $file) {
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