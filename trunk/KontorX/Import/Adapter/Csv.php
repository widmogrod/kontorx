<?php
require_once 'KontorX/Import/Interface.php';

/**
 * @author gabriel
 * @package KontorX
 * @version $Id$
 */
class KontorX_Import_Adapter_Csv implements KontorX_Import_Interface, Iterator
{
    protected $_handler;
    
    protected $_filename;
    
    protected $_length = 2048;
    
    protected $_delimiter = ';';
    
    protected $_enclosure = '"';
    
    protected $_escape = '\\';

    public function __construct($filename, $options = null)
    {
       $this->setFilename($filename);

       if ($options instanceof Zend_Config) {
           $this->setOptions($options->toArray());
       } elseif(is_array($options)) {
           $this->setOptions($options);
       }
    }
    
    public function __destruct()
    {
        $this->close();
    }
    
    public function setFilename($filename)
    {
        if (!is_file($filename)) 
        {
            require_once 'KontorX/Import/Exception.php';
            throw new KontorX_Import_Exception(sprintf('file "%s" do not exists', $filename));
        }
        
        $this->_filename = $filename;
    }
    
    public function getFilename()
    {
        return $this->_filename;
    }
    
    public function setDelimiter($delimiter)
    {
        $this->_delimiter = $delimiter;
    }
    
    public function getDelimiter()
    {
        return $this->_delimiter;
    }
    
    public function setEnclosure($enclosure)
    {
        $this->_enclosure = $enclosure;
    }
    
    public function getEnclosure()
    {
        return $this->_enclosure;
    }
    
    public function setEscape($escape)
    {
        $this->_escape = $escape;
    }
    
    public function getEscape()
    {
        return $this->_escape;
    }

    public function setOptions(array $options)
    {
        foreach ($options as $name => $value) 
        {
            $method = 'set' . ucfirst($name);
            if (method_exists($this, $method)) {
                $this->$method($value);
            } 
        }
    }

    public function open()
    {
        if (!$this->isOpen()) {
            $this->_handler = fopen($this->getFilename(), 'r');
        }
    }
    
    public function isOpen()
    {
        return is_resource($this->_handler);
    }
    
    public function close()
    {
        if ($this->isOpen()) {
            return fclose($this->_handler);
        }
    }

    public function key()
    {
        $this->open();
        return ftell($this->_handler); // ?
    }
    
    public function current()
    {
        $this->open();
        return fgetcsv($this->_handler, $this->_length, $this->_delimiter, $this->_enclosure, $this->_escape);
    }
    
    public function next()
    {
        $this->open();
    }
    
    public function valid()
    {
        $this->open();
        return !feof($this->_handler);
    } 

    public function rewind()
    {
        $this->open();
        rewind($this->_handler);
        //fseek($this->_handler, 0, SEEK_SET);
    }

    protected $_result;

    public function toArray()
    {
        if (null === $this->_result)
        {
            $this->_result = array();
            $this->rewind();
            while ($this->valid()) 
            {
                $this->_result[] = $this->current();
                $this->next();
            }
        }
        
        return $this->_result;
    }
}