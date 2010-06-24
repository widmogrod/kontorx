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
	$('#wrapper').find('a').live('click',function(e){
		e.preventDefault();

		$.get(this.href, function(data){
			$('#content').html(data);
		});
		$.get(this.href.replace('.php','.phps'), function(data){
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
		<h1><a href="index.php">KontorX <em>extensions library for Zend Framework</em></a></h1>
	</div>
	<div id="wrapper">
		<div class="grid_4" id="sitebar">
			<?php print $navigation->menu();?>
		</div>
	
		<div class="grid_12">
			<h3>Visual example:</h3>
			<div id="content" class="clearfix"></div>
			
			<h3>Source code:</h3>
			<div id="source" class="clearfix"></div>
		</div>
	</div>
	
	<div class="grid_16">
		<p class="footer">&copy; 2010 <a href="http://blog.widmogrod.info/">Gabriel Habryn</a>, Code license: GNU General Public License v3, Hosted on: Google Code <a href="http://code.google.com/p/kontorx/">http://code.google.com/p/kontorx/</a></p>
	</div>
</div>

</body>
</html>