<?php
interface KontorX_Import_Interface
{
    public function __construct($filename, $options = null);
    
    public function toArray();
}