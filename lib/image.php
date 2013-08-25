<?php
/* icomage lib */
@ini_set('display_errors', '-1');
@ini_set("gd.jpeg_ignore_warning", 1);

define('DOCROOT', rtrim(realpath(dirname(__FILE__) . '/../../../'), '/'));
define('DOMAIN', rtrim(rtrim($_SERVER['HTTP_HOST'], '/') . str_replace('/extensions/ico_image/lib', NULL, dirname($_SERVER['PHP_SELF'])), '/'));

// Include some parts of the engine
require_once(DOCROOT . '/symphony/lib/boot/bundle.php');
require_once(CORE . '/class.errorhandler.php');
require_once(CORE . '/class.log.php');
require_once('class.ico.php');
require_once(TOOLKIT . '/class.page.php');
require_once(CONFIG);
define_safe('CACHING', ($settings['image']['cache'] == 1 ? true : false));
// Setup the environment
if(method_exists('DateTimeObj', 'setSettings')) {
  DateTimeObj::setSettings($settings['region']);
}
else {
  DateTimeObj::setDefaultTimezone($settings['region']['timezone']);
}

/*
set_error_handler('__errorHandler');

function __errorHandler($errno=NULL, $errstr, $errfile=NULL, $errline=NULL, $errcontext=NULL){
  global $param;

  if(error_reporting() != 0 && in_array($errno, array(E_WARNING, E_USER_WARNING, E_ERROR, E_USER_ERROR))){
  $Log = new Log(ACTIVITY_LOG);
  $Log->pushToLog("{$errno} - ".strip_tags((is_object($errstr) ? $errstr->generate() : $errstr)).($errfile ? " in file {$errfile}" : '') . ($errline ? " on line {$errline}" : ''), $errno, true);
  $Log->pushToLog(
  sprintf(
  'Ico class param dump - mode: %d, width: %d, height: %d, position: %d, background: %d, file: %s, external: %d, raw input: %s',
  $_GET['param']
    ), E_NOTICE, true
  );
  }
}



*/


$imgpath = $_GET[param];

$imgpath = DOCROOT . "/workspace/" . $imgpath;

	// If CACHING is enabled, check to see that the cached file is still valid.
	if(CACHING === true){
	$cache_file = sprintf('%s/%s_%s', CACHE, md5($_REQUEST['param']), basename($img_path));
		// Cache has expired or doesn't exist
		if(is_file($cache_file) && (filemtime($cache_file) < $last_modified)){
			unlink($cache_file);
		}
		else if(is_file($cache_file)) {
			$image_path = $cache_file;
			 touch($cache_file);
			 Ico::display($cache_file); 
		}
	}
try{
  $image = Ico::load($imgpath);
  if(!$image instanceof Ico) {
    throw new Exception('Error generating image');
  }
}
catch(Exception $e){
  Page::renderStatusCode(Page::HTTP_STATUS_BAD_REQUEST);
  trigger_error($e->getMessage(), E_USER_ERROR);
  echo $e->getMessage();
  exit;
}
// building the array and fitting the images 	
    $img_array =  $image->run($image->resource());
// building the ico
    $img = $image->render($img_array);

	// If CACHING is enabled, and a cache file doesn't already exist,
	// save the JIT image to CACHE using the Quality setting from Symphony's
	// Configuration.
	if(CACHING && !is_file($cache_file)){
		if(!$image::save($cache_file, $img)) {
			Page::renderStatusCode(Page::HTTP_STATUS_NOT_FOUND);
			trigger_error('Error generating image', E_USER_ERROR);
			echo 'Error generating image, failed to create cache file.';
			exit;
		}
	}

	// Display the image in the browser using the Quality setting from Symphony's
	// Configuration. If this fails, trigger an error.
	
	if(!$image::display($cache_file, $img)) {
		Page::renderStatusCode(Page::HTTP_STATUS_NOT_FOUND);
		trigger_error('Error generating image', E_USER_ERROR);
		echo 'Error generating image';
		exit;
	}

	exit;


?>