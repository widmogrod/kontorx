<?php
/**
 * @author gabriel
 */
interface KontorX_Search_Semantic_Interpreter_Interface {

    /**
     * @param KontorX_Search_Semantic_Context_Interface $context
     * @return bool
     */
    public function interpret(KontorX_Search_Semantic_Context_Interface $context);
}