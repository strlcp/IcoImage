

IcoImge is a simple Image Iconifer

Overview

  like the great JIT IcoIamge is called from the .htaccess with an own rule.
  you can call it by the keyword ico in your url, like:
  
  http://localhost/ICO/path_to_file  
  
  The image found at the file will be fitet in the middle of square, background is wihte.
  
  afterwards an ico is build with sizes of (64, 32, 16)px, to use it as a favicon like:
  
  
  <link rel="icon" type="image/vnd.microsoft.icon" href="http://host/ICO/path_to_file" />
  
  
  
  IMPORTANT: ico only work with caching enabled. on the fly headers dont work.

Installation

    Upload the 'ico-image' folder in this archive to your Symphony 'extensions' folder.
    Enable it by selecting the "Ico Image", choose Enable from the with-selected menu, then click Apply.

    Your .htacess is merged automatic.