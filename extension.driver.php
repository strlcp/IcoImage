<?php

    class extension_Ico_Image extends Extension{

        private $_trigger;
        private static $_name = 'IcoImage';

 
        public function install() {
	  try {
	      $htaccess = file_get_contents(DOCROOT . '/.htaccess');
	    // Cannot use $1 in a preg_replace replacement string, so using a token instead -- copied form jit 
	      $token = md5(time());

	    $rule = "
	### ICO RULE
	RewriteRule ^ico\/(.+\.(jpg|gif|jpeg|png|bmp))\$ extensions/ico_image/lib/image.php?param={$token} [B,L,NC]" . PHP_EOL . PHP_EOL;

      // Remove existing the rules
	$htaccess = self::__removeImageRules($htaccess);

      if(preg_match('/### ICO RULE/', $htaccess)){
	  $htaccess = preg_replace('/### ICO RULE/', $rule, $htaccess);
	}
      else{
	  $htaccess = preg_replace('/RewriteRule .\* - \[S=14\]\s*/i', "RewriteRule .* - [S=14]" . PHP_EOL ."{$rule}\t", $htaccess);
    }

// Replace the token with the real value
    $htaccess = str_replace($token, '$1', $htaccess);

if(file_put_contents(DOCROOT . '/.htaccess', $htaccess)) {
// Now add Configuration values
// Symphony::Configuration()->set('cache', '1', 'image');


// Create workspace directory
//      General::realiseDirectory(WORKSPACE . '/icoimage', Symphony::Configuration()->get('write_mode', 'directory'));

      return Symphony::Configuration()->write();
    }
    else return false;
    }
    catch (Exception $ex) {
	Administration::instance()->Page->pageAlert(__('An error occurred while installing %s. %s', array(__('IcoImage'), $ex->getMessage())), Alert::ERROR);
	return false;
      }
    }

private static function __removeImageRules($string){
	return preg_replace('/RewriteRule \^ico[^\r\n]+[\r\n\t]?/i', NULL, $string);
}


  public function enable(){
    return $this->install();
  }

   public function uninstall(){
  //  General::deleteDirectory(WORKSPACE . '/icoimage');
    return $this->disable();
  }

  public function disable() {
    try {
	$htaccess = file_get_contents(DOCROOT . '/.htaccess');
	$htaccess = self::__removeImageRules($htaccess);
	$htaccess = preg_replace('/### ICO RULE/', NULL, $htaccess);

	return file_put_contents(DOCROOT . '/.htaccess', $htaccess);
      }
      catch (Exception $ex) {
	Administration::instance()->Page->pageAlert(__('An error occurred while installing %s. %s', array(__('IcoImage'), $ex->getMessage())), Alert::ERROR);
	return false;
      }
  }

}