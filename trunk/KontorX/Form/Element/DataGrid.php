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
	
	public function setModel($model)
	{
		require_once 'KontorX/DataGrid.php';
		$this->_dataGrid = KontorX_DataGrid::factory($model);
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
			throw new Zend_Form_Element_Exception('KontorX_DataGrid is not set');
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
            	->addDecorator('HtmlTag', array('tag' => 'dd',
                                                'id'  => $this->getName() . '-element'))
                ->addDecorator('Description', array('tag' => 'p', 'class' => 'description'))
                ->addDecorator('Label', array('tag' => 'dt'));
        }
	}

}