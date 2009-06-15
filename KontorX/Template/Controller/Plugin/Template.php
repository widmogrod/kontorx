<?php
class KontorX_Template_Controller_Plugin_Template extends Zend_Controller_Plugin_Abstract {

	/**
	 * @var KontorX_Template
	 */
	protected $_template;
	
	public function setTemplate(KontorX_Template $template) {
		$this->_template = $template;
	}
	
	/**
	 * @return KontorX_Template
	 */
	public function getTemplate() {
		if (null === $this->_template) {
			$this->_template = KontorX_Template::getInstance();
		}
		return $this->_template;
	}

	public function postDispatch(Zend_Controller_Request_Abstract $request) {
		if (!$request->isDispatched()) {
			return;
		}

		$template = $this->getTemplate();
		if ($template->isStartedLayout()) {
			return;
		}

		$layout = $template->getLayout();
        if (!$layout->isEnabled()) {
            return;
        }

//        $theme = $template->getThemeName();
//        $style = $template->getStyleName();
//
//        if ($template->isEnabledLocale()) {
//        	
//        }

        $templatePath = $template->getTemplatePath(true);
        if (!$this->isAllowedThemeConfig()) {
        	return;
        }

        $themeConfig = $this->getThemeConfigPath(true);
        $view = $template->getView();

        // title
        if (isset($themeConfig->title)) {
            $headTitle = $view->headTitle();
            // @todo pozwolić konfiguracji ustawić separator
            $headTitle->setSeparator(' - ');
            $headTitle->prepend($themeConfig->title);
        }

        // meta
        if (isset($themeConfig->meta)
        		&& isset($themeConfig->meta->name)) {
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
            		&& isset($themeConfig->meta->name->keywords)) {
                $headMeta->setName('keywords', $themeConfig->meta->name->keywords);
            }
            if (!isset($meta['description']) 
            		&& isset($themeConfig->meta->name->description)) {
                $headMeta->setName('description', $themeConfig->meta->name->description);
            }
        }

        // script
        if (isset($themeConfig->script)) {
            $headScript = $view->getHelper('HeadScript');
            $i = 0;
            foreach ($themeConfig->script->js as $file) {
                $headScript->offsetSetFile(++$i, $file->src);
            }
        }

        // link
        if (isset($themeConfig->links)) {
        	$headLink = $view->getHelper('HeadLink');
            foreach ($themeConfig->links->css->toArray() as $file) {
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