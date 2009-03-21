<?php
/**
 * @author gabriel
 */
interface KontorX_Search_Semantic_Query_Interface {
    /**
     * @return array|null
     */
    public function query($content);
    
    /**
     * @return string|null
     */
    public function getContent();
    
    /**
     * @return string|null
     */
    public function getContentLeft();
    
    /**
     * @return string|null
     */
    public function getContentRight();
}