<?php
class KontorX_Form_Element_DataGrid extends Zend_Form_Element_Xhtml
{

	public function init()
	{
		$this->addPrefixPath(
			'KontorX_Form_Decorator',
			'KontorX/Form/Decorator',
			self::DECORATOR
		);
	}

	/**
	 * @var array
	 */
	protected $_columns;
	
	/**
	 * @param array $columns
	 */
	public function setColumns(array $columns)
	{	
		if ($this->_dataGrid)
			$this->_dataGrid->setColumns($columns);

		$this->_columns = $columns;
	}
	
	/**
	 * @param array|Zend_Db_Table|Zend_Db_Select $model
	 */
	public function setModel($model)
	{
		$options = array();
		
		if (is_array($this->_columns))
			$options['columns'] = $this->_columns;

		require_once 'KontorX/DataGrid.php';
		$this->_dataGrid = KontorX_DataGrid::factory($model, $options);
	}
	
	/**
	 * @var KontorX_DataGrid
	 */
	protected $_dataGrid;

	/**
	 * @param KontorX_DataGrid $grid
	 * @return void
	 */
	public function setDataGrid(KontorX_DataGrid $grid)
	{
		$this->_dataGrid = $grid;
	}

	/**
	 * @return KontorX_DataGrid 
	 */
	public function getDataGrid()
	{
		if (null === $this->_dataGrid)
		{
			if (!is_array($value = $this->getValue()))
			{
				require_once 'Zend/Form/Element/Exception.php';
				throw new Zend_Form_Element_Exception('KontorX_DataGrid is not set');
			} else {
				$this->setModel($value);
			}
		}

		return $this->_dataGrid;
	}

	public function loadDefaultDecorators()
	{
		if ($this->loadDefaultDecoratorsIsDisabled())
		{
            return;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators))
        {
            $this->addDecorator('DataGrid')
            	->addDecorator('Errors')
            	->addDecorator('Description', array('tag' => 'p', 'class' => 'description'))
            	->addDecorator('HtmlTag', array('tag' => 'dd',
                                                'id'  => $this->getName() . '-element'))
                
                ->addDecorator('Label', array('tag' => 'dt'));                
        }
	}

}
