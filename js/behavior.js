$(function(){

	// load below
	var loadBelowInterval;
	var loadBelow = function() {
		if($(window).scrollTop() > $(".image:last").offset().top - ($(window).height() * 4)) {
			var from = $("#images .image:last");
			var images = $.get("?get=below&from="+from.attr("data-filename"))
			.success(function(data) {
				if($(data).find(".image").length > 0) {
					$("#images").append($(data).find(".image"));
					loadBelowInterval = setInterval(loadBelow, 500);
				}
			});
			clearInterval(loadBelowInterval);
		}
	}
	loadBelowInterval = setInterval(loadBelow, 500);
	
	// load above
	var loadAboveInterval;
	var loadAbove = function() {
		if($(window).scrollTop() < ($(window).height() * 4)) {
			var from = $("#images .image:first");
			var images = $.get("?get=above&from="+from.attr("data-filename"))
			.success(function(data) {
				if($(data).find(".image").length > 0) {
					var scrolltop = $(window).scrollTop();
					$("#images").prepend('<div id="hidden-images" style="display:none"></div>');
					$("#hidden-images").html($(data).find(".image"));
					var height = $("#hidden-images").height();
					var last_image_height = $("#hidden-images .image:last img").attr("height");
					$("#hidden-images").remove();
					$("#images").prepend('<div style="width:100%" class="load-below-fixup" data-height="' + last_image_height + '"/>');
					$("#images").prepend($(data).find(".image"));
					$(window).scrollTop(scrolltop + height);
					loadAboveInterval = setInterval(loadAbove, 500);
				}
			});
			clearInterval(loadAboveInterval);
		}
	}
	loadAboveInterval = setInterval(loadAbove, 500);
	
	// layout fixup for load-above
	setInterval(function() {
		var window_bottom = $(window).scrollTop() + $(window).height();
		$(".load-below-fixup").each(function() {
			if(($(this).offset().top - $(this).attr("data-height")) > window_bottom) {
				$(this).remove();
			}
		});
	}, 1000);
	
	// exif on click
	$(".image").livequery(function() {
		$(this).click(function() {
			$(this).find(".exif:hidden").show().removeClass("slide-down").addClass("slide-up").each(function() {
				if(renderHistogram) {
					$(this).find(".histogram").remove();
					$(this).append('<canvas class="histogram" width="256" height="50"></canvas>');
					var canvas = $(this).find(".histogram").get(0);
					renderHistogram($(this).parents(".image").find("img").get(0), canvas);
				}
			});
		});
		$(this).mouseleave(function() {
			$(this).find(".exif:visible").removeClass("slide-up").addClass("slide-down").fadeOut(400);
		});
	});
	
	// histogram
	var scratch_canvas = document.createElement("canvas");
	if(scratch_canvas.getContext) {
		Array.prototype.init = function(x, n) {
			if(typeof(n)=='undefined') { n = this.length; }
			while (n--) { this[n] = x; }
			return this;
		};
		Array.prototype.max = function(){
			return Math.max.apply( Math, this );
		};
		Array.prototype.min = function(){
			return Math.min.apply( Math, this );
		};
		
		renderHistogram = function(img, dest) {
			// img to temporary canvas
			scratch_canvas.width = img.width;
			scratch_canvas.height = img.height;
			var ctx = scratch_canvas.getContext('2d');
			ctx.drawImage(img, 0, 0);
			
			// data
			var data = ctx.getImageData(0, 0, img.width, img.height);
			var len = data.width * data.height * 4; // aka data.data.length * 4 ?
			var reds = [].init(0, 256);
			var greens = [].init(0, 256);
			var blues = [].init(0, 256);
			for(var i = 0; i < len; i += 4) {
				++reds[data.data[i]];
				++greens[data.data[i + 1]];
				++blues[data.data[i + 2]];
			}
			
			// scale and flip
			var max = reds.max();
			var n = greens.max(); if(n > max) max = n;
			n = blues.max(); if(n > max) max = n;
			var den = max / dest.height;
			for(var i = 0; i < 256; i++) {
				reds[i] = dest.height - (reds[i] / den);
				greens[i] = dest.height - (greens[i] / den);
				blues[i] = dest.height - (blues[i] / den);
			}
			
			// dest canvas context
			var ctx = dest.getContext("2d");
			ctx.globalCompositeOperation = "lighter";
			ctx.lineWidth = 2;
			ctx.lineCap = "round";
			ctx.lineJoin = "round";
			
			// curves
			var drawCurve = function(values, stroke, fill) {
				ctx.strokeStyle = stroke;
				ctx.fillStyle = fill;
				ctx.beginPath();
				ctx.moveTo(0.5, values[0] + 0.5);
				for(var i = 1; i < 256; i++) {
					ctx.lineTo(i + 0.5, values[i] + 0.5);
				}
				ctx.stroke();
				ctx.lineTo(dest.width - 0.5, dest.height - 0.5);
				ctx.lineTo(0.5, dest.height - 0.5);
				ctx.closePath();
				ctx.fill();
			}
			drawCurve(reds, "#800", "#f00");
			drawCurve(greens, "#080", "#0f0");
			drawCurve(blues, "#008", "#00f");
		}
	}
	
	// history
	if(history && history.replaceState) {
		var first_filename = $(".image:first").attr("data-filename");
		var history_filename = first_filename;
		$(window).bind('scrollstop', function() {
			first_filename = $(".image:first").attr("data-filename");
			var scrolltop = $(window).scrollTop();
			var nearest_filename;
			var nearest_top;
			var flag = true;
			$(".image").each(function() {
				var top = $(this).offset().top - scrolltop - 100;
				if(top > 0) {
					if(flag || top < nearest_top) {
						flag = false;
						nearest_filename = $(this).attr("data-filename");
						nearest_top = top;
					}
				}
			});
			if(nearest_filename !== history_filename) {
				history_filename = nearest_filename;
				var url = "?";
				if(history_filename !== first_filename) {
					url = "?from=" + history_filename;
				}
				history.replaceState(null, null, url);
			}
		});
	}
});