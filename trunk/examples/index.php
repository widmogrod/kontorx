<?php
//inicjowanie konfiguracji
require_once 'bootstrap.php';

class PhpecursiveFilterIterator extends RecursiveFilterIterator
{
	public function accept() {
		$filename = $this->current()->getFilename();
		if ($this->current()->isDir()) {
			switch($filename) 
			{
				case '.svn':
				case 'resources':
				case '.': 
					return false;

				default:
					return true;
			}
		} else {
			switch ($filename)
			{
				case 'index.php';
				case 'bootstrap.php';
					return false;
			}
		}

		
		
		$fileExtension = strtolower(pathinfo($filename,PATHINFO_EXTENSION));
		return 'php' === $fileExtension;
	}
}

// Budowanie drzewa przykładów
$path = dirname(__FILE__);
$rdi = new RecursiveDirectoryIterator($path);
$rdi = new PhpecursiveFilterIterator($rdi);

require_once 'KontorX/Iterator/Reiterate/Container/DirectoryToNavigation.php';
$container = new KontorX_Iterator_Reiterate_Container_DirectoryToNavigation();
$container->setBasePath($path);
$container->setBaseUrl(dirname($_SERVER['SCRIPT_NAME']));

require_once 'KontorX/Iterator/Reiterate/IteratorIterator.php';
$rii = new KontorX_Iterator_Reiterate_IteratorIterator($rdi, RecursiveIteratorIterator::SELF_FIRST);
$rii->iterate($container);

require_once 'Zend/View.php';
$view = new Zend_View();
$navigation = $view->getHelper('Navigation');
$navigation->setContainer($container);
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>KontorX - extensions library for Zend Framework -  examples</title>

<link rel="stylesheet" href="resources/css/reset.css" type="text/css" />
<link rel="stylesheet" href="resources/css/960.css" type="text/css" />
<link rel="stylesheet" href="resources/css/text.css" type="text/css" />
<link rel="stylesheet" href="resources/css/datagrid.css" type="text/css" />

<script type="text/javascript" src="resources/js/jquery-1.4.2.min.js"></script>
<script type="text/javascript">
<!--
$(document).ready(function(){
	$('a').live('click',function(e){
		e.preventDefault();

		$.get(this.href, function(data){
			$('#content').html(data);
		});
		$.get(this.href + 's', function(data){
			$('#source').html(data);
		});
	});
});
//-->
</script>
</head>
<body>
<div class="container_16">
	<div class="clearfix">
		<h1>KontorX</h1>
		<h2>extensions library for Zend Framework</h2>
	</div>
	<div class="grid_4" id="sitebar">
		<?php print $navigation->menu();?>
	</div>

	<div class="grid_12">
		<div id="content" class="clearfix"></div>
		<div id="source" class="clearfix"></div>
	</div>
</div>
</body>
</html>