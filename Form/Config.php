<?php
require_once 'Zend/Form.php';

/**
 * KontorX_Form_Config
 *
 * @package 	KontorX_Form
 * @version 	0.1.0
 * @license		GNU GPL
 * @author 		Marcin `widmogror` Habryn, widmogrod@gmail.com
 */
class KontorX_Form_Config extends Zend_Form {

    /**
     * Konstruktor
     *
     * @param Zend_Config $model
     * @param Zend_Config|array|null $options
     */
    public function __construct(Zend_Config $model, $options = null) {
        $this->setConfigModel($model);
        parent::__construct($options);
    }

    public function init() {
        $model = $this->getConfigModel();
        if (null === $model) {
            require_once 'Zend/Form/Exception.php';
            throw new Zend_Form_Exception("Config model is no set");
        }

        $this->_setupFormFromConfig($model);
    }

    /**
     * Przygotowuje formularz z @Zend_Config
     * @param Zend_Config $model
     */
    protected function _setupFormFromConfig($model) {
        foreach ($model as $config) {
            
        }
    }

    /**
     * @var Zend_Config
     */
    protected $_configModel = null;

    public function setConfigModel(Zend_Config $model) {
        $this->_configModel = $model;
    }
    
    public function getConfigModel() {
        return $this->_configModel;
    }

    /**
     * Tworzy Form_Element
     *
     * @param array $options
     * @param bool $loop
     * @return array
     */
    protected function _createElement(array $options, $loop = false) {
        // TODO Dodać możliwośc pobierania nazwy z opisu pola w DB (?)
        $element = null;
        $elementName = $options['COLUMN_NAME'];
        $elementOptions = array(
            'label' => $elementName
        );

        $dataType = strtoupper($options['DATA_TYPE']);
        switch ($dataType) {
            case 'TIMESTAMP':
            case 'DATETIME':
                $element = 'text';
                // dodanie atrybutu class
                if (isset($elementOptions['class'])) {
                    $elementOptions['class'] += ' datetime';
                } else {
                    $elementOptions['class'] = 'datetime';
                }

            break;
            case 'VARCHAR':
                if ($options['LENGTH'] < 100) {
                    $element = 'text';
                } else {
                    $element = 'textarea';
                    $elementOptions += array('class' => 'medium');
                }
            break;
            case 'TINYINT':
            case 'INTEGER':
            case 'FLOAT':
            case 'BOOL':
            case 'BOOLEAN':
                switch($options['LENGTH']) {
                    case 1:
                    $element = 'checkbox';
                    break;
                    default:
                    $element = 'text';
                }
            break;
            case 'TEXT':
                $element = 'textarea';
            break;
            default:
                if ($loop) {
                    $element = 'text';
                    break;
                }

                if (!preg_match('#(?P<type>\w+)\((?P<length>[0-9]+)\)#i', $dataType, $matched)) {
                    $element = 'text';
                    break;
                }

                $options['DATA_TYPE'] = $matched['type'];
                $options['LENGTH'] 	  = $matched['length'];
                return $this->_createElement($options, true);
        }

        return array($element, $elementName, $elementOptions);
    }
}