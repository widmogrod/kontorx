<?php
require_once 'KontorX/Sisi/Response/Abstract.php';

/**
 * @author $Author$
 * @version $Id$
 */
class KontorX_Sisi_Response_Html extends KontorX_Sisi_Response_Abstract
{
    /**
     * @var string
     */
    protected $_scriptName = 'index';

    /**
     * @param string $scriptName
     */
    public function setScriptName($scriptName) {
        $this->_scriptName = (string) basename($scriptName);
    }

    /**
     * @return string
     */
    public function getScriptName() {
        return $this->_scriptName;
    }

    /**
     * @param string $scriptName
     */
    protected function _scriptPath($scriptName = null) {
        if (is_string($scriptName))
            $this->setScriptName($scriptName);

        $scriptName = $this->getScriptName();
        $scriptPath = SKIN_PATHNAME . '/' . SKIN_NAME . '/' . $scriptName. '.php';

        if (is_file($scriptPath))
            return $scriptPath;
    }

    /**
     * @param string $scriptName
     * @return string
     */
    public function render($scriptName = null) {
        $scriptPath = $this->_scriptPath($scriptName);
        if (!$scriptPath)
            return;

        ob_start();
		include $scriptPath;

		return ob_get_clean();
    }

    /**
     * @return string
     */
    public function send() {
        return $this->render();
    }
}
