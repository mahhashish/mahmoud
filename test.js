$(document).ready(function(){function doAnimations(elems){var animEndEv = 'webkitAnimationEnd animationend'; elems.each(function(){var $this = $(this), $animationType = $this.data('animation'); $this.addClass($animationType).one(animEndEv, function(){$this.removeClass($animationType); }); }); }
var $animation_elements = $('.animation-element'); var $window = $(window); function check_if_in_view(){var $animation_elements = $('.animation-element'); var window_height = $window.height(); var window_top_position = $window.scrollTop(); var window_bottom_position = (window_top_position + window_height); $.each($animation_elements, function(){var $element = $(this); if (!$element.hasClass('done')){var element_height = $element.outerHeight(); var element_top_position = $element.offset().top; var element_bottom_position = (element_top_position + element_height); if ((element_bottom_position >= window_top_position) && (element_top_position <= window_bottom_position)){$element.addClass('in-view'); $element.addClass('done'); } else{$element.removeClass('in-view'); }}}); }
$window.on('scroll resize', check_if_in_view); $window.trigger('scroll'); });