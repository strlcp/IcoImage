<?php 

/* class ico php */

require_once('phpthumb.ico.php');
require_once('phpthumb.functions.php'); 
Class Ico {
  private $_resource;
  private $_meta;
  private $_image;
  static $_result;

  const DEFAULT_QUALITY = 80;
  const DEFAULT_INTERLACE = true;
  

  private function __construct($resource, stdClass $meta){
    $this->_resource = $resource;
    $this->_meta = $meta;
  }

  public function __destruct(){
    if(is_resource($this->_resource)) {
      imagedestroy($this->_resource);
    }
  }

  public function Resource(){
    return $this->_resource;
  }
  
  public function Meta(){
    return $this->_meta;
  }


/**
* Given a path to an image, `$image`, this function will verify it's
* existence, and generate a resource for use with PHP's image functions
* based off the file's type (.gif, .jpg, .png).
* Images must be RGB, CMYK jpg's are not supported due to GD limitations.
*
* @param string $image
* The path to the file
* @return Image
*/
  public static function load($image){
    if(!is_file($image) || !is_readable($image)){
      throw new Exception(sprintf('Error loading image <code>%s</code>. Check it exists and is readable.', str_replace(DOCROOT, '', $image)));
    }

    $meta = self::getMetaInformation($image);

    switch($meta->type) {
// GIF
    case IMAGETYPE_GIF:
      $resource = imagecreatefromgif($image);
      break;

// JPEG
    case IMAGETYPE_JPEG:
      if($meta->channels <= 3){
	$resource = imagecreatefromjpeg($image);
      }
// Can't handle CMYK JPEG files
      else{
	throw new Exception('Cannot load CMYK JPG images');	
      }
      break;

// PNG
    case IMAGETYPE_PNG:
      $resource = imagecreatefrompng($image);
      break;

    default:
      throw new Exception('Unsupported image type. Supported types: GIF, JPEG and PNG');
      break;
    }

    if(!is_resource($resource)){
      throw new Exception(sprintf('Error loading image <code>%s</code>. Check it exists and is readable.', str_replace(DOCROOT, '', $image)));
    }

    $obj = new self($resource, $meta);

    return $obj;
  }

/**
* Given a path to a file, this function will attempt to find out the
* dimensions, type and channel information using `getimagesize`.
*
* @link http://www.php.net/manual/en/function.getimagesize.php
* @param string $file
* The path to the image.
*/
  public static function getMetaInformation($file){
    if(!$array = @getimagesize($file)) return false;
      $meta = array();

      $meta['width'] = $array[0];
      $meta['height'] = $array[1];
      $meta['type'] = $array[2];
      $meta['channels'] = isset($array['channels']) ? $array['channels'] : false;
      
      return (object)$meta;
  }

 /**	
 * Given an GD-Image resource
 * building white planes and fitting the recource to the planes  	
 * putting in an array sideLength -> recource.  
 *
 * @param  resource GdImge	
 * @return array int->resource
 */
  
  public function run($res) {

	$gd_image_array = array();  
	//$resolution_array = array ("16", "32", "64");
	$resolution_array = array ("32", "64");
/* adding 96, 128 and 256 favicon dont work at my place */
// making white plane 

      $relation = ( $this->Meta()->height / $this->Meta()->width);
   // making planes 
      foreach ($resolution_array as $no) {
	      $ict= ImageCreateTrueColor($no, $no);
	      $new = ImageCreateTrueColor($no, $no);
	      $back = ImageColorAllocate ($ict, 255, 255, 255); // making bg white
	      imagefill($ict ,0,0,$back);
	      imagecopyresampled($new, $ict, 0, 0, 0, 0, $no, $no, $no, $no);
              $gd_image_array[$no] = $ict;		 
      }
    // fitting the image to each plane, placing middle  
      foreach ($gd_image_array as $key => $gd_img){
         if ($relation >= 1 ) { 
	    $ylenght = intval($key);
	    $xlenght = intval($key / $relation); 
	  }
	  else {
	    $xlenght = intval( $key) ;
	    $ylenght = intval ($key * $relation);
	  }
      	imagecopyresampled($gd_img, $res, (($key - $xlenght )/2) ,(($key - $ylenght) / 2) ,0,0, $xlenght, $ylenght, $this->Meta()->width, $this->Meta()->height);
      }	
      return $gd_image_array;
}

/**
* Given the imageresouce array, 
* returns an ico as string
*
*@param  array GDImageResource  (build by this->run) 	
*@return string icon
*/

  public  function render($img_array){

	    $ico = new phpthumb_ico();
	    $img = $ico->GD2ICOstring($img_array);
	    return $img; 

}
/**
*	Given a cachefile with icon
*	printing header and ico
*	
*	@param string CacheFile
*/
  public static function display ($cache_file) {

	    header('Content-Type: image/vnd.microsoft.icon');
	    header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($cache_file));
            ob_clean();
            flush();
            readfile($cache_file);
            exit;
	    return(true );
  }
 /**
 *	Given Cachefile and ico as string
 *	saving the ico in cachefile
 *	
 *	@param string CacheFile
 *	@param string Ico
 */
  
   public static function save($cache_file, $img) {
	file_put_contents($cache_file, $img); 
	return $img;
   }
  
  
}

?>