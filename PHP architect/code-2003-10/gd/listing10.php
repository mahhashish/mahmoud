<?

	function croppedimagepreview($source,$new_placement = 0) {

		// $new_placement = (-1,0,1)

		if (!in_array($new_placement,array(-1,0,1))) $new_placement = 0;

		$source_width = imagesx($source);
		$source_height = imagesy($source);
		$source_ratio = $source_width / $source_height;

		$target = imagecreatetruecolor($source_width,$source_height);

		imagecopy($target,$source,0,0,0,0,$source_width,$source_height);

		imagealphablending($target,TRUE);

		$overlay_color = imagecolorallocatealpha($target,255,255,255,31);

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
					imagefilledrectangle($target,0,0,$difference,$source_height,$overlay_color);
					break;
				case  0:
					if (($difference % 2) == 0) {
					 	$difference1 = ($difference / 2);
					 	$difference2 = ($difference / 2);
					} else {
						$difference1 = (($difference / 2) - 0.5);
						$difference2 = (($difference / 2) + 0.5);
					}
					imagefilledrectangle($target,0,0,$difference1,$source_height,$overlay_color);
					imagefilledrectangle($target,$source_width-$difference2,0,$source_width,$source_height,$overlay_color);
					break;
				case  1:
					imagefilledrectangle($target,$source_width-$difference,0,$source_width,$source_height,$overlay_color);
					break;
			}


		} else if ($source_ratio == (4/3)) {

			// no cropping / image have corrent ratio
			// $new_placement:	ignore this parameter

			$target_width = $source_width;
			$target_height = $source_height;

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
					imagefilledrectangle($target,0,0,$source_width,$difference,$overlay_color);
					break;

				case  0:
					if (($difference % 2) == 0) {
					 	$difference1 = ($difference / 2);
					 	$difference2 = ($difference / 2);
					} else {
						$difference1 = (($difference / 2) - 0.5);
						$difference2 = (($difference / 2) + 0.5);
					}

					imagefilledrectangle($target,0,0,$source_height,$difference1,$overlay_color);
					imagefilledrectangle($target,0,$source_height-$difference2,$source_width,$source_height,$overlay_color);


					break;

				case  1:
					imagefilledrectangle($target,0,$source_height-$difference,$source_width,$source_height,$overlay_color);
					break;
			}



		}

		return $target;

	}

?>