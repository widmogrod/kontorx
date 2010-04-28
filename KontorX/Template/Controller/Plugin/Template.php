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

        // inicjuje dodatkowe opcje
        $this->_init($templateConfig);
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
	 * @param Zend_Config $templateConfig
	 * @return void
	 */
	protected function _init(Zend_Config $templateConfig) {
		$this->_initViewHelpers($templateConfig);
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
                unset($options->meta->name->keywords);
            }
            if (!isset($meta['description']) 
            			&& isset($options->meta->name->description)) {
                $headMeta->setName('description', $options->meta->name->description);
                unset($options->meta->name->description);
            }

            if (isset($options->meta->httpEquiv)
            		&& $options->meta->httpEquiv instanceof Zend_Config) {
	            foreach ($options->meta->httpEquiv as $obj) {
	            	$headMeta->setHttpEquiv($obj->key,
	            							$obj->content,
	            							isset($obj->modifiers) ? $obj->modifiers->toArray() : array());
	            }
	            
	            unset($options->meta->httpEquiv);
            }
            if ($options->meta->name instanceof Zend_Config) {
	            foreach ($options->meta->name as $name => $value) {
		            if (!$value instanceof Zend_Config) {
	            		$headMeta->setName($name, $value);
	            	}
	            }
            }
        }

        // script
        if (isset($options->script)) {
        	$i = 0;
        	/* @var $headScript Zend_View_Helper_HeadScript */
            $headScript = $view->getHelper('HeadScript');

            // wsteczna kompatybilność
            if (isset($options->script->js)) {
            	$options->script->file = $options->script->js;
            }

            if (isset($options->script->file)
            		&& $options->script->file instanceof Iterator)
            {
	            foreach ($options->script->file as $key => $file) 
	            {
	            	/**
	            	 * Sztuczka, która zwalnia z pamięrania by wyczyścic cache przeglądarki
	            	 * gdy zostana wykonane zmiany w JS.
	            	 * 
	            	 * Krótki test pokazuje ze narzut na wydajność w pętli 100razy to ~ 0.0001 s
	            	 * zatem akceptowalne
	            	 * 
	            	 * Świadomie pomijam sprawdzenie czy plik istnieje by dodatkowe nie obciążąć skryptyu..
	            	 */
	            	$src = isset($file->src) ? $file->src : $file;
	            	if (false === strstr($src,'http://'))
	            	{
	            		$src .= '?s=' . filectime($src);
	            	}

	                $headScript->offsetSetFile(is_numeric($key) ? ++$i : $key,
	            							   $src,
	            							   isset($file->type) ? $file->type : null,
	            							   (isset($file->attribs) && $file->attribs instanceof Zend_Config)
												 	? $file->attribs->toArray() : array());
	            }
            }
            
        	if (isset($options->script->script)
            		&& $options->script->script instanceof Iterator)
            {
	            foreach ($options->script->script as $key => $script) 
	            {
	            	/**
	            	 * Sztuczka, która zwalnia z pamięrania by wyczyścic cache przeglądarki
	            	 * gdy zostana wykonane zmiany w JS.
	            	 * 
	            	 * Krótki test pokazuje ze narzut na wydajność w pętli 100razy to ~ 0.0001 s
	            	 * zatem akceptowalne
	            	 * 
	            	 * Świadomie pomijam sprawdzenie czy plik istnieje by dodatkowe nie obciążąć skryptyu..
	            	 */
	            	if (isset($script->src))
	            	{
	            		$src = $script->src;
	            		if (false === strstr($src,'http://'))
		            	{
		            		$src .= '?s=' . filectime($src);
		            	}
	            	} else {
	            		$src = $script;
	            	}

	                $headScript->offsetSetScript(is_numeric($key) ? ++$i : $key,
	            								 $src,
												 isset($script->type) ? $script->type : null,
												 (isset($script->attribs) && $script->attribs instanceof Zend_Config)
												 	? $script->attribs->toArray() : array());
	            }
            }
        }
        
		// inlineScript
        if (isset($options->inlineScript)) {
        	$i = 0;
        	/* @var $inlineScript Zend_View_Helper_InlineScript */
            $inlineScript = $view->getHelper('InlineScript');

        	// wsteczna kompatybilność
            if (isset($options->inlineScript->js)) {
            	$options->inlineScript->file = $options->inlineScript->js;
            }

            if (isset($options->inlineScript->file)
            		&& $options->inlineScript->file instanceof Iterator)
            {
	            foreach ($options->inlineScript->file as $key => $file) 
	            {
	            	/**
	            	 * Sztuczka, która zwalnia z pamięrania by wyczyścic cache przeglądarki
	            	 * gdy zostana wykonane zmiany w JS.
	            	 * 
	            	 * Krótki test pokazuje ze narzut na wydajność w pętli 100razy to ~ 0.0001 s
	            	 * zatem akceptowalne
	            	 * 
	            	 * Świadomie pomijam sprawdzenie czy plik istnieje by dodatkowe nie obciążąć skryptyu..
	            	 */
	            	$src = isset($file->src) ? $file->src : $file;
	            	if (false === strstr($src,'http://'))
	            	{
	            		$src .= '?s=' . filectime($src);
	            	}
	            	
	                $inlineScript->offsetSetFile(is_numeric($key) ? ++$i : $key,
	            							   $src,
	            							   isset($file->type) ? $file->type : null,
	            							   (isset($file->attribs) && $file->attribs instanceof Zend_Config)
												 	? $file->attribs->toArray() : array());
	            }
            }
            
        	if (isset($options->inlineScript->script)
            		&& $options->inlineScript->script instanceof Iterator)
            {
	            foreach ($options->inlineScript->script as $key => $script) 
	            {
	            	/**
	            	 * Sztuczka, która zwalnia z pamięrania by wyczyścic cache przeglądarki
	            	 * gdy zostana wykonane zmiany w JS.
	            	 * 
	            	 * Krótki test pokazuje ze narzut na wydajność w pętli 100razy to ~ 0.0001 s
	            	 * zatem akceptowalne
	            	 * 
	            	 * Świadomie pomijam sprawdzenie czy plik istnieje by dodatkowe nie obciążąć skryptyu..
	            	 */
	            	if (isset($script->src))
	            	{
	            		$src = $script->src;
	            		if (false === strstr($src,'http://'))
		            	{
		            		$src .= '?s=' . filectime($src);
		            	}
	            	} else {
	            		$src = $script;
	            	}
	            	
	                $inlineScript->offsetSetScript(is_numeric($key) ? ++$i : $key,
	            								 isset($script->src) ? $script->src : $script,
												 isset($script->type) ? $script->type : null,
												 (isset($script->attribs) && $script->attribs instanceof Zend_Config)
												 	? $script->attribs->toArray() : array());
	            }
            }
        }

        // link
        if (isset($options->links)) {
        	/* @var $headLink Zend_View_Helper_HeadLink */
        	$headLink = $view->getHelper('HeadLink');
            foreach ($options->links->css->toArray() as $file) 
            {
                if (!isset($file['rel'])) {
                	$file['rel'] = 'stylesheet';
                }
            	if (!isset($file['media'])) {
					$file['media'] = 'screen';
				}
				
				/**
            	 * Sztuczka, która zwalnia z pamięrania by wyczyścic cache przeglądarki
            	 * gdy zostana wykonane zmiany w CSS.
            	 * 
            	 * Krótki test pokazuje ze narzut na wydajność w pętli 100razy to ~ 0.0001 s
            	 * zatem akceptowalne
            	 * 
            	 * Świadomie pomijam sprawdzenie czy plik istnieje by dodatkowe nie obciążąć skryptyu..
            	 */
				
				if (false === strstr($file['href'],'http://'))
				{
					// ltrim jest dla kompatybilności wstecznej.. 
					// nie moga (nie powinny) być absolutne linki wewnętrzne!
					$file['href'] .= '?s=' . filectime(ltrim($file['href'], '/'));
				}
				
                $headLink->headLink($file);
            }
        }
	}
}