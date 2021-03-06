<?php
	require_once(SITE_PATH."protected/config.php");
	require_once(SITE_PATH."protected/library/library.php");
	$sep = getenv('COMSPEC')? ';' : ':';
	//C2
	$path = CLASSES.$sep.CONROLLERS.$sep.MODULES;
	
	# ini_set()
	ini_set('include_path', $path);
	ini_set('session.use_trans_sid', false);
	header("Content-Type: text/html; charset=utf-8");
	session_start();

	$db = array('host'=> $DB_Host,
				'name'=> $DB_Name,
				'user'=> $DB_UserName,
				'password'=> $DB_Password,
				'charset'=> $DB_Charset);
	
	$tpl = array('source'=>$PathToTemplate,
				 'styles'=>$PathToCSS,
				 'images'=>$PathToImages,
				 'jscripts'=>$PathToJavascripts,
				 'flash'=>$PathToFlash,
				 'tmp'=>$PathToTMP,
				 'dump'=>$PathToDUMP);
	# Registry::set('db_settings', $db);
	$registry = new Registry;
	
	# 
	$registry->set('db_settings', $db);
	$registry->set('tpl_settings', $tpl);
	$registry->set('theme', $theme);
	$registry->set('editor', $editor);
	
	$db = new PDOchild($registry);//echo var_dump($registry['db_settings']);
	$language = $db->rows("SELECT * FROM `language`");
	$registry->set('key_lang', getUri($language));
	$registry->set('key_lang_admin', getUriAdm($language));
	//echo '<br />end '.$_SESSION['key_lang'];

	if($_SESSION['key_lang']=="ru")define('LINK', '');
	else define('LINK', '/'.$_SESSION['key_lang']);
	
	# 
	$parser = new Parser();
	if(!empty($_POST)){$parser->parse_recursive_tree($_POST);}
	if(!empty($_GET)){$parser->parse_recursive_tree($_GET);} 
	if(!empty($_COOKIE)){$parser->parse_recursive_tree($_COOKIE);}

	
	# router
	$router = new Router($registry);
	echo $router->getParams();
	//$registry->set('router', $router);
?>