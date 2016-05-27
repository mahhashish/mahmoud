<?php

/*******************************************************************************
 * Aliro - the modern, accessible content management system
 *
 * This code is copyright (c) Aliro Software Ltd - please see the notice in the 
 * index.php file for full details or visit http://aliro.org/copyright
 *
 * Some parts of Aliro are developed from other open source code, and for more 
 * information on this, please see the index.php file or visit 
 * http://aliro.org/credits
 *
 * Author: Martin Brampton
 * counterpoint@aliro.org
 *
 * aliroImage is used to create image objects that can be manipulated using the GD library
 */

final class aliroImage {
	private $path = '';
	private $image = null;
	private $type = '';
	private $x_size = 0;
	private $y_size = 0;

	public function __construct ($path) {
		$this->path = $path;
		$info = pathinfo($path);
		$this->type = isset($info['extension']) ? $info['extension'] : '';
		if ('jpg' == $this->type) $this->type = 'jpeg';
		if ('jpeg' == $this->type) $this->image = imagecreatefromjpeg($path);
		elseif ('png' == $this->type) $this->image = imagecreatefrompng($path);
        elseif ('gif' == $this->type) $this->image = imagecreatefromgif($path);
		else aliroRequest::getInstance()->setErrorMessage (T_('Class aliroImage create - given path has invalid extension - not jpg, jpeg, gif, png'), _ALIRO_ERROR_SEVERE);
		if ($this->image) {
			$this->x_size = imagesx($this->image);
			$this->y_size = imagesy($this->image);
		}
	}
	
	public function getType () {
		return $this->type;
	}
	
	public function getWidth () {
		return $this->x_size;
	}
	
	public function getHeight () {
		return $this->y_size;
	}
	
	//This function will resize any PNG or JPG or PNG image to whatever size you specify
	//It will keep aspect ratios.
	public function imgresize ($new_width, $new_height, $noEnlarge=true) {
		if (!$this->image) {
			aliroRequest::getInstance()->setErrorMessage(T_('Class aliroImage resize failed - no valid image created'), E_USER_ERROR);
			return;
		}
		//grab original sizes
		$old_x = $this->x_size;
		$old_y = $this->y_size;

		// new math to figure aspect ratio
		$ratio = min($new_width/$old_x, $new_height/$old_y);
		if (1.0 < $ratio AND $noEnlarge) return;
		$new_height = $old_y * $ratio;
		$new_width = $old_x * $ratio;

		//generate a blank final image
		$dst_img=ImageCreateTrueColor($new_width, $new_height);

		//this resamples the original image and I think uses bicub to create a new image
		if (imagecopyresampled($dst_img, $this->image, 0, 0, 0, 0, $new_width, $new_height, $old_x, $old_y)) {
			$this->image = $dst_img;
			$this->x_size = $new_width;
			$this->y_size = $new_height;
		}
		else aliroRequest::getInstance()->setErrorMessage(T_('Class aliroImage imagecopyresampled failed - image not resized'), E_USER_ERROR);
	}
	
	public function saveAs ($path, $highQuality=false) {
		if (!$this->image) {
			aliroRequest::getInstance()->setErrorMessage(T_('Class aliroImage save image failed - no valid image created'), _ALIRO_ERROR_SEVERE);
			return false;
		}
		//unlink it if it already exists in destination
		$info = pathinfo($path);
		$exts = array('jpg', 'jpeg', 'png', 'gif');
		if (in_array($info['extension'], $exts)) {
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
			return true;
		}		
		aliroRequest::getInstance()->setErrorMessage(T_('Class aliroImage save method - path has wrong extension'), _ALIRO_ERROR_SEVERE);
		return false;
	}

}
	