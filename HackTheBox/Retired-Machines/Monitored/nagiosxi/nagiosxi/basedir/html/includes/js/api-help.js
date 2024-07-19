
$(document).ready(function(){resize_nav();new Clipboard('.copy');$(window).resize(function(){resize_nav();});});function resize_nav(){var width=$('.nav-box').width();var height=$(window).height()-70;if($('.help-right-nav').outerHeight()>height){$('.help-right-nav').css('height',height);}else{$('.help-right-nav').css('height','auto');}
$('.help-right-nav').css('width',width);}