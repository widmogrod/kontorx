# Introduction #

`KontorX_DataGrid`

![http://kontorx.googlecode.com/svn/wiki/images/kontorx_data_grid.png](http://kontorx.googlecode.com/svn/wiki/images/kontorx_data_grid.png)

## Example ##

### Controller ###

controllers/IndexController.php

```

(...)
public function listAction() {
	$model = new Gallery_Model_Image();
	$table = $model->getDbTable();

	$rq = $this->getRequest();
	if ($rq->isPost()) {
		switch ($rq->getPost('action_type')) {
			case 'update':
				if (null !== $rq->getPost('editable')) {
					$data = $rq->getPost('editable');
					$model->editableUpdate($data);
					$this->_helper->flashMessenger($model->getStatus());
				}
				break;
			case 'delete':
				if (null !== $rq->getPost('action_checked')) {
					$data = $rq->getPost('action_checked');
					$model->editableDelete($data);
					$this->_helper->flashMessenger($model->getStatus());
				}
				break;
		}			
	}

	// setup data grid
	$config = $this->_helper->loader->config('image.xml');
	$grid = KontorX_DataGrid::factory($model->selectListGrid(), $config->grid);
	$grid->setValues($this->_getAllParams());

 	// $this->_setupDataGridPaginator($grid);
	
	$this->view->grid = $grid;
}

(...)
```

### View ###
views/scripts/index/list.phtml

```
<?php
print $this->grid->render($this, '_partial/dataGrid.phtml');
```

views/scripts/`_`partial/dataGrid.phtml

```
<form action="" method="post">
<?php print $this->formSelect('action_type',null,null,array(
	'Wybierz akcje','update' => 'Aktualizuj', 'delete' => 'Usuń'
))?>
<?php print $this->formSubmit(null, 'Wykonaj akcje')?>
<?php
	/* @var $placeholder Zend_View_Helper_Placeholder */
	$placeholder = $this->getHelper('Placeholder');
?>
<table class="kx_datagrid">
	<thead>
		<tr class="kx_columns">
			<?php
				$groupColumn = null;
				/* @var $column KontorX_DataGrid_Column_Interface */
				foreach ($this->columns as $column):
			?>
				<?php if (!$column->isGroup()):?>
					<td class="kx_column <?php print $column->class ?>" rowspan="<?php print $column->rowspan ?>" style="<?php print $column->style ?>"><?php print $column ?></td>
				<?php else:?>
					<?php $groupColumn = $column;?>
				<?php endif;?>
			<?php endforeach; ?>
		</tr>
		<tr class="kx_filters">
			<?php
				foreach ($this->columns as $i => $column):
					$filters = $column->getFilters();

			?>

				<?php $placeholder->placeholder("filter-$i")->captureStart();?>
				
					<?php
						/* @var $filter KontorX_DataGrid_Filter_Interface */
						foreach ($filters as $filter):
					?>
					<span class="kx_filter"><?php print $filter?></span>
					<?php endforeach; ?>
				
				<?php $placeholder->placeholder("filter-$i")->captureEnd();?>

				<?php 

					// sprawdz, czy jest ustawione filtrowanie
					if ($column->isGroup()) {
						// sprawdz, czy filter nalerzy do kolumny zgrupowanej
						// filt(er|ry) - miejsca w wierszu zgrupowania
						$placeholder->placeholder('filter-group')
							->set($placeholder->placeholder("filter-$i"));
	
					} else {
						// filt(er|ry) - tutaj jest ich miejsce :)
						print '<td class="kx_filter_set">' . $placeholder->placeholder("filter-$i") . '</td>';
					}
				?>

			<?php endforeach; ?>
		</tr>
		
		<tr class="kx_pagination">
			<td colspan="<?php print count($this->columns) ?>">
				<?php if ($this->paginator): ?>
				<?php print $this->paginationControl($this->paginator, 'Sliding','_partial/pagination.phtml', array('valuesQuery' => $this->valuesQuery)); ?>
				<?php endif;?>
			</td>
		</tr>
		
	</thead>
	<tbody class="kx_rows">
		
	
		<?php $this->rowset->rewind()?>
		<?php
			/* @var $cellset KontorX_DataGrid_Adapter_Cellset_Interface */
			foreach ($this->rowset as $cellset): ?>

		<?php if ($cellset->hasGroupedCell()): ?>
		<tr class="kx_group_cell">
			<td colspan="<?php print count($cellset)?>">
				<div class="kx_column">
					<?php print $groupColumn?>
				</div>

				<div class="kx_column_value">
				<?php
					print $cellset->getGroupCell();
				?>
				</div>
				<div class="kx_filters_set">
				<?php
					// miejsca na filt(er|ry)
					print $placeholder->placeholder('filter-group');
			?>
				</div>
			</td>
		</tr>
		<?php endif; ?>

		<tr class="<?php print $this->cycle(array('odd','even'))?>">
			<?php
				/* @var $cell KontorX_DataGrid_Cell_Interface */
				foreach ($cellset as $cell): ?>
			<td><?php print $cell;?></td>
			<?php endforeach; ?>
		</tr>
		
		<?php endforeach; ?>
	</tbody>
	<tfoot>
		<tr class="kx_pagination">
			<td colspan="<?php print count($this->columns) ?>">
				<?php if ($this->paginator): ?>
				<?php print $this->paginationControl($this->paginator, 'Sliding','_partial/pagination.phtml', array('valuesQuery' => $this->valuesQuery)); ?>
				<?php endif;?>
			</td>
		</tr>
	</tfoot>
</table>

</form>
```

### Configuration ###

DataGrid configuration file: _image.xml_

```
<?xml version="1.0" encoding="UTF-8"?>
<config>
	<grid>
		<prefixPaths>
			<prefixPath type="cell" prefix="Promotor_DataGrid_Cell_" path="Promotor/DataGrid/Cell" />
		</prefixPaths>

		<RequestValues>GET</RequestValues>
        <columns>
        	<id type="ChechboxManager" style="width:20px;" displayNone="1">
                <cell type="Editable_FormCheckbox">
                	<primaryKey>id</primaryKey>
					<prefix>action_checked</prefix>
                </cell>
            </id>
			<publicated type="Text" style="width:40px;">
				<name><![CDATA[<acronym title="Publikować">Pub</acronym>]]></name>
                <cell type="Editable_YesNo">
                	<primaryKey>id</primaryKey>
                </cell>
            </publicated>
			<name type="Order" name="Nazwa produktu">
                <filter type="Text"></filter>
                <cell type="Html">
					<content><![CDATA[
						<a class="action edit small" href="/shop/product/edit/{id}">{name}</a>
						<small class="right blur"><b class="action attach small">alias:</b> {alias}</small>
					]]></content>
                </cell>
            </name>
            <manufacturer type="Text" name="Producent" style="width:80px;">
                <filter type="Group">
                	<options key="manufacturer_id" label="manufacturer"/>                		
                </filter>

                <cell type="Text" />
            </manufacturer>
            <price type="Order" name="Cena" style="width: 80px;">
                <filter type="FromToNumeric"></filter>
                <cell type="Html">
					<content><![CDATA[
					<p style="text-align:right;">{price} zł</p>
					]]></content>
                </cell>
            </price>
            <quantity type="Order" name="Ilość" style="width: 80px;">
                <filter type="FromToNumeric"></filter>
                <cell type="Html">
					<content><![CDATA[
					<p style="text-align:right;">{quantity}</p>
					]]></content>
                </cell>
            </quantity>
			<idx type="Order" name="Kolejność" style="width: 20px;">
				<!-- domyślen sortowanie -->
				<options order="asc" />

                <cell type="Editable_FormText">
                	<primaryKey>id</primaryKey>
                </cell>
            </idx>
			<edit type="Text">
				<options style="width:60px;" displayNone="1"/>
				<filters>
					<filter1 type="Submit">
						<options label="Szukaj" class="action find"/>
					</filter1>
					<filter2 type="Reset">
						<options class="action reset"/>
					</filter2>
				</filters>
                <cell type="Url">
                	<name>Edytuj</name>
					<action>edit</action>
                	<class>action edit small</class>
                	<primaryKey>id</primaryKey>
                </cell>
            </edit>
			<delete type="Text" style="width:20px;" displayNone="1">
                <cell type="Url">
                	<name>Usuń</name>
					<action>delete</action>
                	<class>action trash ico</class>
                	<primaryKey>id</primaryKey>
                </cell>
            </delete>
        </columns>
	</grid>	
</config>
```