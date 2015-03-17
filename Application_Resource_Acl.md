# Introduction #

Zend Framework Acl application resource...

## Configuration ##

### Default schema ###

```
$acls = array(
              'guest' => array(
                      'role' => array(
                              'name' => 'guest', // string
                              'class' => null,   // default @see Zend_Acl_Role implementation of @see Zend_Acl_Role_Interface
                              'parents' => null  // string or array
                      ),
 
                      'resource' => array(
                              'name' => null,  // string
                              'class' => null, // default @see Zend_Acl_Resource or implementation of @see Zend_Acl_Resource_Interface
                              'parent' => null // string
                      ),

                      'assert' => array(
                              'class' => null // implementation of @see Zend_Acl_Assert_Interface
              ),

                      'privileges' => array(
                              'allow' => array(), // null or string or array
                              'deny' => array(),  // null or string or array
                      )
              ),
      );
```


### Configuration example ###

app/configuration/acl.php

[Example](http://code.google.com/p/kontorx-catalog/source/browse/trunk/app/configuration/acl.php)

```
<?php
return array(
        'acls' => array(
                'guest' => array(
                        'resource' => array(
                                'name' => null,
                                'class' => null,
                                'parent' => null
                        ),
                
                        'privileges' => array(
                                'allow' => 'show'
                        )
                ),
        
                'member' => array(
                        'role' => array(
                                'parents' => 'guest'
                        ),
                
                        'privileges' => array(
                                'allow' => array('create','update','delete','list'),
                                'deny' => array('admin_delete')
                        )
                ),
        
                'admin' => array()
        )
);
```

### Bootstrap init ###

Configuration 'resource' part...

[Example](http://code.google.com/p/kontorx-catalog/source/browse/trunk/app/configuration/application.php)

```
//(...)
'resources' => array(
                'acl' => include 'acl.php')
//(...)
```

## Controller ##
### Controller config example ###
[Example](http://code.google.com/p/kontorx-catalog/source/browse/trunk/app/modules/news/controllers/NewsController.php)
```
<?php
class News_NewsController extends Promotor_Controller_Action_Scaffold {

        public $acl = array(
                // actionName => privilage
                'list' => 'list',
                'add' => 'create',
                'addfromodt' => 'create',
                'edit' => 'update',
                'display' => 'show',
                'delete' => 'delete'
        );
```

### Controller action helper example ###

[Example](http://code.google.com/p/kontorx-catalog/source/browse/trunk/app/modules/news/controllers/NewsController.php)

```
        /**
         * @return void
         */
        // list action has privilage 'list'
        public function listAction() {
                $model = new News_Model_News();
                $table = $model->getDbTable();

                $rq = $this->getRequest();
                if ($rq->isPost()) {
                        switch ($rq->getPost('action_type')) {
                                case 'update':

                                        if (null !== $rq->getPost('editable')) {
                                                // additional check of privilages
                                                if ($this->_helper->acl->isAllowed('update')) {
                                                        $data = $rq->getPost('editable');
                                                        $model->editableUpdate($data);
                                                        $this->_helper->flashMessenger($model->getStatus());
                                                }
                                        }
                                        break;
                                case 'delete':

                                        if (null !== $rq->getPost('action_checked')) {
                                                // additional check of privilages
                                                if ($this->_helper->acl->isAllowed('delete')) {
                                                        $data = $rq->getPost('action_checked');
                                                        $model->editableDelete($data);
                                                        $this->_helper->flashMessenger($model->getStatus());
                                                }
                                        }
                                        break;
                        }                       
                }

```