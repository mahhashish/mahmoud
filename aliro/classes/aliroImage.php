<?php

class aliroImage {
	private $path = '';
	private $image = null;

	public function __construct ($path) {
		$this->path = $path;
		$info = pathinfo($path);
		$type = $info['extension'];
		if ('jpg' == $type OR 'jpeg' == $type) $this->image = imagecreatefromjpeg($path);
		elseif ('png' == $type) $this->image = imagecreatefrompng($path);
        elseif ('gif' == $type) $this->image = imagecreatefromgif($path);
		else trigger_error (T_('Class aliroImage create - path has invalid extension - not jpg, jpeg, gif, png'), E_USER_ERROR);
	}
	
	//This function will resize any PNG or JPG or PNG image to whatever size you specify
	//It will keep aspect ratios.
	public function imgresize ($new_width, $new_height) {
		if (!$this->image) trigger_error (T_('Class aliroImage resize - no valid image created'), E_USER_ERROR);
		//grab original sizes
		$old_x=imagesx($this->image);
		$old_y=imagesy($this->image);

		// new math to figure aspect ratio
		$ratio = min($new_width/$old_x, $new_height/$old_y);
		$new_height = $old_y * $ratio;
		$new_width = $old_x * $ratio;

		//generate a blank final image
		$dst_img=ImageCreateTrueColor($new_width, $new_height);

		//this resamples the original image and I think uses bicub to create a new image
		imagecopyresampled($dst_img, $this->image, 0, 0, 0, 0, $new_width, $new_height, $old_x, $old_y);
	}
	
	public function saveAs ($path, $highQuality=false) {
		if (!$this->image) trigger_error (T_('Class aliroImage save - no valid image created'), E_USER_ERROR);
		//unlink it if it already exists in destination
		$info = pathinfo($path);
		$exts = array('jpg', 'jpeg', 'png', 'gif');
		if (in_array($info['extension'], $exts) {
			aliroFileManager::getInstance()->deleteFile($path);
			switch ($info['extension']) {
				case 'jpg':
				case 'jpeg':
					if ($highQuality) imagejpeg($this->image, $path, 100);
					else imagejpeg($this->image, $path);
					break;
				case 'png':
					if ($highQuality) imagepng($this->image, $path, 0);
					else imagepng($this->image, $path);
					break;
				case 'gif':
					imagegif($this->image, $path);
					break;
			}
		}		
		else trigger_error(T_('Class aliroImage save method - path has wrong extension'), E_USER_ERROR);
	}

}
	