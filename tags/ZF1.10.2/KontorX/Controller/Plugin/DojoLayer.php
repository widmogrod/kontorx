<?php
require_once 'Zend/Controller/Plugin/Abstract.php';
class KontorX_Controller_Plugin_DojoLayer extends Zend_Controller_Plugin_Abstract {

    public $layerScript;

    public $buildProfile;

    public function dispatchLoopShutdown() {
    	$this->layerScript = PUBLIC_PATHNAME . '/js/promotor/dojo/admin.js';
    	$this->buildProfile = PUBLIC_PATHNAME . '/js/promotor/dojo/admin.profile.js';

    	if (!file_exists($this->layerScript)) {
            $this->generateDojoLayer();
        }
//        if (!file_exists($this->buildProfile)) {
//            $this->generateBuildProfile();
//        }
    }

    /**
     * @var Zend_View_Interface
     */
    protected $_view;

    /**
     * @param Zend_View_Interface $view
     * @return void
     */
    public function setView(Zend_View_Interface $view) {
    	$this->_view = $view;
    }
    
    /**
     * @return Zend_View_Interface
     */
    public function getView() {
    	if (null === $this->_view) {
    		require_once 'Zend/Registry.php';
    		if (Zend_Registry::isRegistered('Zend_View')) {
    			$this->_view = Zend_Registry::get('Zend_View');
    		} else {
    			require_once 'Zend/View.php';
    			$this->_view = new Zend_View(); 
    		}
    	}
    	return $this->_view;
    }

    /**
     * @var Zend_Dojo_BuildLayer
     */
    protected $_build;
    
    /**
     * @return Zend_Dojo_BuildLayer
     */
    public function getBuild(){
        if (null === $this->_build) {
            $this->_build = new Zend_Dojo_BuildLayer(array(
                'view'      => $this->getView(),
                'layerName' => 'promotor.admin',
            	'consumeJavascript' => false
            ));
        }
        return $this->_build;
    }

    public function generateDojoLayer() {
        $build = $this->getBuild();
        $layerContents = $build->generateLayerScript();
        if (!is_dir(dirname($this->layerScript))) {
            mkdir(dirname($this->layerScript));
        }
        file_put_contents($this->layerScript, $layerContents, 0666);
    }
    
	public function generateBuildProfile() {
        $profile = $this->getBuild()->generateBuildProfile();
        file_put_contents($this->buildProfile, $profile, 0666);
    }
}