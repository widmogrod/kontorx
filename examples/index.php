<?php
//inicjowanie konfiguracji
require_once 'bootstrap.php';

function getBaseUrl() 
{
    if (isset($_SERVER['SERVER_NAME'])) {
    	$host = $_SERVER['SERVER_NAME'];
    } elseif (isset($_SERVER['HTTP_HOST'])) {
    	$host = $_SERVER['HTTP_HOST'];
    } else {
    	// no host no play .. ??
    	return '';
    }

	$protocol = isset($_SERVER['SERVER_PROTOCOL']);
	if (false !== ($strpos = strpos($protocol,'/'))) {
		$protocol = substr($protocol, 0, strpos($protocol,'/'));
	} else {
		$protocol = 'http';
	}
	
	$scriptName = ltrim(dirname($_SERVER['SCRIPT_NAME']),'/');

	$baseUrl = $protocol . '://' . $host . '/' . $scriptName;
    return $baseUrl;
}

class PhpRecursiveFilterIterator extends RecursiveFilterIterator
{
	public function accept() 
	{
		$filename = $this->current()->getFilename();
		if ($this->current()->isDir()) 
		{
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
		if (!in_array($fileExtension, array('php','sql')))
			return false;
		
		if (strstr($filename, '_s.php'))
			return false;
		
		return true;
	}
}

// Budowanie drzewa przykładów
$path = dirname(__FILE__);
$rdi = new RecursiveDirectoryIterator($path);
$rdi = new PhpRecursiveFilterIterator($rdi);

require_once 'KontorX/Iterator/Reiterate/Container/DirectoryToNavigation.php';
$container = new KontorX_Iterator_Reiterate_Container_DirectoryToNavigation();
$container->setBasePath($path);
$container->setBaseUrl(getBaseUrl());

require_once 'KontorX/Iterator/Reiterate/IteratorIterator.php';
$rii = new KontorX_Iterator_Reiterate_IteratorIterator($rdi, RecursiveIteratorIterator::SELF_FIRST);
$rii->iterate($container);

require_once 'Zend/View.php';
$view = new Zend_View();
$navigation = $view->getHelper('Navigation');
$navigation->setContainer($container);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="pl">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>KontorX - extensions library for Zend Framework -  examples</title>

<link rel="stylesheet" href="resources/css/reset.css" type="text/css" />
<link rel="stylesheet" href="resources/css/960.css" type="text/css" />
<link rel="stylesheet" href="resources/css/text.css" type="text/css" />
<link rel="stylesheet" href="resources/css/datagrid.css" type="text/css" />
<link rel="stylesheet" href="resources/css/form.css" type="text/css" />


<script type="text/javascript" src="resources/js/jquery-1.4.2.min.js"></script>
<script type="text/javascript">
<!--
$(document).ready(function(){
	$('#wrapper').find('a').live('click',function(e){
		e.preventDefault();

		document.location.hash = this.href;

		$.ajax({
			url: this.href,
			type:'get',
			dataType:'script',
			success: function(data)
			{
				$('#content').html(data);
			}
		});

		$.get(this.href.replace('.php','_s.php'), function(data){
			$('#source').html(data);
		});
	});

	if (document.location.hash) {
		var hash = document.location.hash.replace('#','');
		$('#wrapper').find('a[href='+hash+']').click();
	}	
});
//-->
</script>

<!-- ** CSS ** -->
<!-- base library -->
<link rel="stylesheet" type="text/css" href="resources/js/ext/resources/css/ext-all.css" />

<!-- ** Javascript ** -->
<!-- ExtJS library: base/adapter -->
<script type="text/javascript" src="resources/js/ext/adapter/ext/ext-base.js"></script>
<!-- ExtJS library: all widgets -->
<script type="text/javascript" src="resources/js/ext/ext-all.js"></script>

<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-17227366-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

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
