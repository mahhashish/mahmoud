<?

	function croppedimage($source,$new_placement = 0) {

		// $new_placement = (-1,0,1)

		if (!in_array($new_placement,array(-1,0,1))) $new_placement = 0;

		$source_width = imagesx($source);
		$source_height = imagesy($source);
		$source_ratio = $source_width / $source_height;

		if ($source_ratio > (4/3)) {

			// horizontal cropping
			// $new_placement:	-1 = align to left
			//			 0 = align to center
			//			 1 = align to right

			$target_width = round( ( 4/3 )*$source_height );
			$target_height = $source_height;

			$difference = $source_width - $target_width;

			switch($new_placement) {

				case -1:
					$source_copy_x = 0;
					$source_copy_y = 0;
					$source_copy_width = $source_width - $difference;
					$source_copy_height = $source_height;
					break;

				case  0:
					if (($difference % 2) == 0) {
					 	$source_copy_x = ($difference / 2);
						$source_copy_width = $source_width - ($difference / 2);
					} else {
						$source_copy_x = (($difference / 2) - 0.5);
						$source_copy_width = $source_width - round($difference / 2) + 0.5;
					}
					$source_copy_y = 0;
					$source_copy_height = $source_height;
					break;

				case  1:
					$source_copy_x = $difference;
					$source_copy_y = 0;
					$source_copy_width = $source_width-$difference;
					$source_copy_height = $source_height;
					break;
			}

		} else if ($source_ratio == (4/3)) {

			// no cropping / image have corrent ratio
			// $new_placement:	ignore this parameter

			$target_width = $source_width;
			$target_height = $source_height;

			$source_copy_x = 0;
			$source_copy_y = 0;
			$source_copy_width = $source_width;
			$source_copy_height = $source_height;

		} else {

			// vertical cropping
			// $new_placement:	-1 = align to top
			//			 0 = align to middle
			//			 1 = align to bottom

			$target_width = $source_width;
			$target_height = round(( 4/3 )*$source_width);

			$difference = $source_height - $target_height;

			switch($new_placement) {

				case -1:
					$source_copy_x = 0;
					$source_copy_y = 0;
					$source_copy_width = $source_width;
					$source_copy_height = $source_height - $difference;
					break;

				case  0:
					if (($difference % 2) == 0) {
					 	$source_copy_y = ($difference / 2);
						$source_copy_height = $source_height - ($difference / 2);
					} else {
						$source_copy_y = (($difference / 2) - 0.5);
						$source_copy_height = $source_height - ($difference / 2)+0.5;
					}
					$source_copy_x = 0;
					$source_copy_width = $source_width;
					break;

				case  1:
					$source_copy_x = 0;
					$source_copy_y = $difference;
					$source_copy_width = $target_width;
					$source_copy_height = $source_height - $difference;
					break;
			}
		}

		// ok, now we have all informations needed about source image

		$target = imagecreatetruecolor($target_width,$target_height);

		imagecopy($target,$source,0,0,$source_copy_x,$source_copy_y,$source_copy_width,$source_copy_height);

		return $target;
	}

?>