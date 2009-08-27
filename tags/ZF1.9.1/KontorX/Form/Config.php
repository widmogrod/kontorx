<?php
require_once 'Zend/Form.php';

/**
 * KontorX_Form_Config
 */
class KontorX_Form_Config extends Zend_Form {

	/**
	 * @var array
	 */
	protected $_instanceOptions;
	
	/**
	 * @param mixed $instanceOptions
	 */
	public function setInstanceOptions($instanceOptions) {
		$this->_instanceOptions = $instanceOptions;
	}
	
	/**
	 * @return mixed
	 */
	public function getInstanceOptions() {
		return $this->_instanceOptions;
	}
	
    /**
     * Konstruktor
     *
     * @param Zend_Config $model
     * @param Zend_Config|array|null $options
     */
    public function __construct(Zend_Config $model, $options = null) {
        $this->setConfigModel($model);
        $this->setInstanceOptions($options);
        $this->setIsArray(true);
        
        parent::__construct($options);

        // go! ..
        $this->setupFormFromConfigModel();
    }
    
    /**
     * Inicjujemy generacje formularza KontorX_Form_Config
     *
     */
    public function setupFormFromConfigModel() {
    	$model = $this->getConfigModel();
    	$this->_setupFormFromConfigModel($model);
    }
    
    /**
     * Przygotowuje formularz z @Zend_Config
     * @param Zend_Config $model
     */
    protected function _setupFormFromConfigModel($model) {
        foreach ($model as $elementName => $config) {
        	$elementName = (string) $elementName;
        	if ($config instanceof Zend_Config) {
        		$form = $this->_initSubFormContainer($config);
        		$form->setLegend($elementName);
        		$this->addSubForm($form, $elementName);
        	} else {
        		list($element, $elementOptions) = $this->_createElement($config, $elementName);
        		$this->addElement($element, $elementName, $elementOptions);
        	}
        }
    }

    /**
     * Inicjuje nowy KontorX_Form_Config instance
     *
     * @param Zend_Config $config
     * @return KontorX_Form_Config
     */
    protected function _initSubFormContainer(Zend_Config $config) {
    	$form = new self($config, $this->getInstanceOptions());
        $form->addDecorator('DtDdWrapper');
        $form->addDecorator('Fieldset');
        $form->removeDecorator('Form');
        return $form;
    }
    
    /**
     * @var Zend_Config
     */
    protected $_configModel = null;

    /**
     * Ustawia modelu danych
     *
     * @param Zend_Config $model
     */
    public function setConfigModel(Zend_Config $model) {
        $this->_configModel = $model;
    }
    
    /**
     * Zwraca model danych
     *
     * @return Zend_Config
     */
    public function getConfigModel() {
        return $this->_configModel;
    }

    /**
     * Tworzy Form_Element
     *
     * @param mixed $config
     * @param string $elementName
     * @return array
     */
    protected function _createElement($config, $elementName) {
        // TODO Dodać możliwośc pobierania nazwy z opisu pola w DB (?)
        $element = null;
        $elementOptions = array(
            'label' => $elementName,
        	'value' => $config
        );

        // TODO Narazie nie działa!
        if (is_bool($config)) {
        	$element = 'checkbox';
        	if (true === $config) {
        		$elementOptions['checked'] = 'checked';
        	}
        } else {
        	 $element = 'text';
        }

        return array($element, $elementOptions);
    }
}