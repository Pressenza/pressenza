jQuery(document).ready(function($){

	// Make Youtube and Vimeo responsive
	// http://css-tricks.com/NetMag/FluidWidthVideo/Article-FluidWidthVideo.php
	var $allVideos = $("iframe[src^='//player.vimeo.com'], iframe[src^='http://www.youtube.com']"),
	$fluidEl = $(".article-view-content");
	$allVideos.each(function() {
		$(this).data('aspectRatio', this.height / this.width)
	    .removeAttr('height')
	    .removeAttr('width');
	});
	$(window).resize(function() {
	  var newWidth = $fluidEl.width();
	  $allVideos.each(function() {
	    var $el = $(this);
	    $el
	      .width(newWidth)
	      .height(newWidth * $el.data('aspectRatio'));
	  });
	}).resize();

    $(".wp-caption").removeAttr('style');

});