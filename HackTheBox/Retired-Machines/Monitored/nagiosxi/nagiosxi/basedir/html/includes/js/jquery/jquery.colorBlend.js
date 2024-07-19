/**
 * jQuery Plugin colorBlend v1.6.0
 * Requires jQuery 1.2.6+ (Not tested with earlier versions).
 * Based on the Fade plugin http://plugins.jquery.com/project/fade
 * Code losely based on the Cycle plugin http://plugins.jquery.com/project/cycle It was a great resource in creating this one) 
 * Copyright (c) 2007-2008 Aaron E. [jquery at happinessinmycheeks dot com] 
 * 
 *	@param: Object Array. Arguments need to be in object notation.
 *	Returns: jQuery.
 *	Options:	
 *		param:		What css color option you wish to fade. 
 *					Such as "background-color", "color", "boarder-color", "scrollbar-face-color" etc.
 *					(default: "background-color).
 *		fps:		Frames per second (default: 30).
 *		cycles:		How many times you want the object to fade. 0 = Infinite. (default: 0).
 *		random:		Will transition from a random color to a random color. (default: false).
 *					Note: Will change isFade to false.
 *		isFade:		Will fade from the original color and back to the original color. (default: true).
 *					Note: Cannont set to true if random is set to true.
 *		fromColor:	*DEPRECIATED* Starting color. accepts RGB, Hex, Name values.
 *					Will be overwritten if random is set to true. Also accepts "random" as an option.
 *		toColor:	*DEPRECIATED* Ending color. Same as above.
 *		colorList:	Now accepts an array of color strings! colorList can accept 3 or 6 digit hex colors (#000000, #000) it can also accept rgb and color names. 
 *		alpha:		Opacity of element! accepts numerical array and old comma seperated string. (Default: [100, 100]). 
 *		isQueue:	Will queue up color aniimations for a paramater. 
 *	Examples: 
 *		$("body").colorBlend([{fromColor:"black", toColor:"white", param:"color"}]);
 *		var myColors = [
 *			{param:'color', colorList["white", "black"]},
 *			{param:'background-color', random: true, alpha:[20,75]},
 *			{param:'border-left-color', colorList: ["random", "black"]},
 *			{param:'border-right-color', fromColor:"white", "black"]},
 *			{param:'border-top-color', colorList: ["white", "black", "pink"]},
 *			{param:'border-bottom-color', colorList: ["white", "tomato", "lime"]}
 *		];
 *		$("tr").colorBlend(myColors);
 *
 *	Known issues: 
 *			* If used on a lot of objects it can cause major browser slowdown and it will eat a lot of cpu. 
 *			* Still one flickering bug when it comes to opacity. Trying to track it down. 
 *
 *	Additions:
 *		1.0.2
 *			* Added "parent" as a valid color value. Will check parents until valid color is found. 
 *				defaults to white if there are no parents with color.
 *		1.0.3
 *			* Added Alpha/Opacity blending! Add alpha:"0,100" to list of parameters. 
 *				Note: Will change the opacity of element only, not the property!
 *				If you only want text to appear and dissapear, you'll have to put it in it's own element, otherise the whole
 *				element will fade, not just your text.
 *		1.0.4
 *			* Alpha will now take just one argument alpha:"30" if you want to just change the alpha and not have it animate. 
 *			* Current is now the default fromColor value. The current value will get the current color of the element. If current is transparent, it will get the parent color.
 *			* Opposite is now the default toColor value.
 *
 *		1.3.0
 *			* Added Queueing ability, so an animation will take arguments and process them once they are available. 
 *			* Added Action parameter available arguments are stop, pause, and resume. Resume continues a paused animation. Where stop lets you assign a whole new animation to the element.
 *			* Added isQueue as an option allows you to decide if you want an option to be queued or not
 *
 *		1.4.0
 *			* Added pause all, stop all, resume all.
 *			* Have objects stored in an non-named array for traversing.
 *		1.6.0
 *			* Changed some internals for smaller quicker code. 
 *			* Added colorList. fromColor and toColor still work, but they are just converted into a colorList.
 *			* Changed alpha to an array as well, can use more than 2 params. Still works with older string based param.
 *	Bugs fixed:
 *		1.0.1 
 *			* Undesired flickering effect if colorBlend was called multiple times on the same css parameter.
 *		1.0.2 
 *			* Noticed element would keep color attributes in certain circumstances.
 *		1.0.4
 *			* Fixed bug where under certain conditions the color would flicker. 
 *		1.0.5
 *			* Great find by cratchit and he supplied the fix. Can now call colorBlend without any options. 
 *		1.2.0
 *			* Flicker fix in 1.0.4 caused other issues. Fixed for good. 
 *			* Found that if you try to get current color from scroll bar, it blows up. Added check for undefined as a color. Defaults to white.
 *		1.3.0
 *			* Found MORE flickering issues, and fixed them. I guess it's not over until the fat lady sings. Didn't see any more flicking, but I don't hear a fat lady.
 *		1.5.0
 *			* In my ignorance I noticed that alpha is taken care of quite nicely by jquery itself. No need to fix what isn't broke. Removed the custom stuff I had placed in.
 *			* Found an issue where if pausing and resuming something repeatitivly it might not sync up and cause weird flashing effects. Added isPOrS variable to check if paused or stopped. Seems to work. 
 *		1.6.1
 *			* Found that in my last release I had accidentally hosed the "current", "random", "parent" and "opposite" options for the color list.
 */
  
(function($) {
	var ver = '1.6.1';
	var gObj = [];
	var q = 0;
	var tfps = [];
	var cnt = 0;
	var cid = 0;
	
	$.fn.colorBlend = function(opts) {
		if(!opts) { opts = [{}]; }
		
		var arrySelected = [];
		this.each(function() {
			arrySelected[arrySelected.length] = $.data($(this).get(0));
		});

		return this.each(function() {
			var $cont = $(this);
			var uId = $.data($cont.get(0));
			var isFlagAll = false;
			
			if(udf(gObj[uId])) {
				gObj[uId] = [];
			}

			$.each(opts, function(i, v){
				var isFound = false;
				opts[i] = $.extend({}, $.fn.colorBlend.defaults, opts[i]);
				opts[i].queue = [];
				opts[i].internals = $.extend({}, $.fn.colorBlend.internals);
				opts[i].parent = $cont;

				if(opts[i].param == "all") {
					isFlagAll = FlagAll(opts[i].action);
				}
								
				$.each(gObj[uId], function(j, w) {
					if(gObj[uId][j].param.toLowerCase() == opts[i].param.toLowerCase() 
					|| opts[i].param.toLowerCase() == 'all') {
						if(!gObj[uId][j].internals.animating) {
							gObj[uId].splice(j, 1, setOptions(opts[i]));
						}
						isFound = true;
						return false;
					}
				});
				
				if(!isFound) {
					gObj[uId].push(setOptions(opts[i]));
				}
			});

			if(!isFlagAll) {
				$.each(gObj[uId], function(i, v){
					var ani = gObj[uId][i].internals.animating;
					var pausedOrStopped = gObj[uId][i].internals.isPOrS;
					
					$.each(opts, function(j, w) {
						if(gObj[uId][i].param.toLowerCase() != opts[j].param.toLowerCase()) {
							return true;
						}

						switch(opts[j].action) {
							case "stop":
							case "pause":
								clearTimeout(gObj[uId][i].internals.tId);
								gObj[uId][i].internals.isPOrS = true;
								pausedOrStopped = true;
								if(opts[j].action == "stop") {
									gObj[uId][i].internals.animating = false;
								}
							break;
							case "resume":
								ani = true;
								pausedOrStopped = false;
								gObj[uId][i].internals.isPOrS = false;
								go(gObj[uId][i]);
							break;
							default:
								if(ani) {
									if(gObj[uId][i].isQueue && gObj[uId][i].cycles > 0) {
										gObj[uId][i].queue.push(setOptions(opts[j]));
									}
								}
							break;
						}
					});
					
					if(!ani && !pausedOrStopped) {
						go(gObj[uId][i]);
					} 
				});
			}
		});
		
		function FlagAll(action) {
			var res = false;
			$.each(arrySelected, function(i, v) {
				var curObj = gObj[v];
				$.each(curObj, function(j, w) {
					switch(action) {
						case "stop":
						case "pause": 
							res = true;
							clearTimeout(curObj[j].internals.tId);
							curObj[j].internals.isPOrS = true;
							if(action == "stop") {
								curObj[j].internals.animating = false;
							}
						break;
						case "resume": 
							res = true;
							curObj[j].internals.isPOrS = false;
							go(curObj[j]);
						break;
					}
				});
			});

			return res;
		};
	};

	$.fn.colorBlend.defaults = {
		fps:30,
		duration:1000,
		param:"background-color",
		cycles:0,
		random:false,
		isFade:true,
		fromColor:"",
		toColor:"",
		colorList: ["current", "opposite"],
		alpha:["100", "100"],
		action:"",
		isQueue:true
	};
	
	$.fn.colorBlend.internals = {
		aniArray:  [],
		alphaArry: [],
		pos: 0,
		currentCycle: 0,
		direction: 1,
		frames: 0,
		delay: 0,
		fromRand: false,
		toRand: false,
		animating: false,
		tId: 0,
		isPOrS: false
	};

	function setOptions(Opts) {
		if(!Opts.internals.animating) {
			var alphaParam = typeof(Opts.alpha) == "string" ? Opts.alpha.split(",") : Opts.alpha;

			if(Opts.fromColor != "" && Opts.toColor != "") {
				switch(Opts.fromColor.toLowerCase()) {
					case "current":
						Opts.fromColor = Opts.parent.css(Opts.param);
						break;
					case "parent":
					case "transparent":
						Opts.fromColor = checkParentColor(Opts.parent, Opts.param);
						break;
					case "opposite":
						Opts.fromColor = OppositeColor(Opts.toColor);
						break;
					case "random":
						Opts.fromColor = rndColor();
						Opts.internals.fromRand = true;
						break;
				}
					
				switch(Opts.toColor.toLowerCase()) {
					case "current":
						Opts.toColor = Opts.parent.css(Opts.param);
						break;
					case "parent":
					case "transparent":
						Opts.toColor = checkParentColor(Opts.parent, Opts.param);
						break;
					case "opposite":
						Opts.toColor = OppositeColor(Opts.fromColor);
						break;
					case "random":
						Opts.toColor = rndColor();
						Opts.internals.toRand = true;
						break;
				}
				
				Opts.colorList = [Opts.fromColor, Opts.toColor];
			}
			
			if(Opts.colorList.length == 1) {
				if(Opts.colorList[0].toLowerCase() == "random") {
					Opts.internals.toRand = true;
					Opts.colorList[0] = rndColor();
				}
			}
			
			$.each(Opts.colorList, function(i, v) {
				switch(v.toLowerCase()) {
					case "current": 
						Opts.colorList[i] = Opts.parent.css(Opts.param) == "transparent" ? checkParentColor(Opts.parent, Opts.param) : Opts.parent.css(Opts.param);
						break;
					case "parent":
					case "transparent":
						Opts.colorList[i] = checkParentColor(Opts.parent, Opts.param);
						break;
					case "opposite":
						Opts.colorList[i] = OppositeColor(toHexColor(checkParentColor(Opts.parent, Opts.param)));
						break;
					case "random":
						Opts.colorList[i] = rndColor();
						break;
				}
			});
			
			Opts.internals.currentCycle = Opts.cycles > 0 ? Opts.cycles : 0;
			Opts.internals.frames = Math.floor(Opts.fps * (Opts.duration / 1000));
			Opts.internals.delay = Math.floor(Opts.duration / ((Opts.internals.frames+1)*Opts.colorList.length));

			if(Opts.random) {
				Opts.isFade = false;
				Opts.colorList = [rndColor(), rndColor()];
			}
				
			if(Opts.isFade) {
				Opts.internals.currentCycle = Opts.internals.currentCycle * 2;
				Opts.internals.delay = Math.floor(Opts.internals.delay / 2);
				Opts.internals.frames = Math.floor(Opts.internals.frames / 2);
			}					
				
			Opts.internals.alphaArry = buildAlphaAni(alphaParam, Opts.internals.frames);
			Opts.internals.aniArray = buildAnimation(Opts.colorList, Opts.internals.frames);
			return Opts;
		}
	}

	function go(Opts) {
		if(!Opts.internals.isPOrS) {
			var sendStop = false;

			Opts.internals.animating = true;

			Opts.parent.css(Opts.param, Opts.internals.aniArray[Opts.internals.pos]);
			setAlpha(Opts.parent, Opts.internals.alphaArry[Opts.internals.pos]);

			Opts.internals.pos += Opts.internals.direction; 

			if(Opts.internals.pos < 0 || Opts.internals.pos >= Opts.internals.aniArray.length) {
				Opts.internals.currentCycle -= Opts.internals.currentCycle != 0 ? 1 : 0;
				Opts.internals.direction = Opts.internals.direction * -1;
				Opts.internals.pos += Opts.internals.direction;

				if(Opts.random) {
					Opts.colorList = [Opts.colorList[Opts.colorList.length-1], rndColor()];
					Opts.internals.aniArray = buildAnimation(Opts.colorList, Opts.internals.frames);
				}

				if(!Opts.isFade) {
					Opts.internals.direction = 1;
					Opts.internals.pos = 0;
				}

				if(Opts.internals.currentCycle == 0 && Opts.cycles > 0) {
					sendStop = true;
				}
			}

			if(!sendStop) {
				Opts.internals.tId = setTimeout(function(){go(Opts);}, Opts.internals.delay);
			} else {
				clearTimeout(Opts.internals.tId);
				Opts.internals.tId = 0;
				if(Opts.isQueue && Opts.queue.length > 0) {
					var tmp = Opts.queue.concat();
					tmp.splice(0,1);
					Opts = $.extend(Opts, Opts.queue.shift());
					Opts.queue = tmp.concat();
					Opts.internals.tId = setTimeout(function(){go(Opts);}, Opts.internals.delay);
				} else {
					Opts.internals.animating = false;
					Opts.internals.isPOrS = true;
				}
			}
		}
	}

	function setAlpha(elm, opacity) {
		elm.css("opacity", parseFloat(opacity / 100));
	}

	function buildAlphaAni(alphaList, frames) {
		var frame = 0;
		var res = [];
		var h = 0;
		
		for(var i = 0;i < alphaList.length-1;i++) {
			var startOpacity = alphaList[i];
			var endOpacity = alphaList[i+1];
			for(frame = 0;frame<=frames;frame++) {
				h = Math.floor(startOpacity * ((frames-frame)/frames) + endOpacity * (frame/frames));
				res[res.length] = h
			}
		}
		
		if(h != alphaList[alphaList.length-1]) {
			res[res.length] = parseInt(alphaList[alphaList.length-1]);
		}
		
		return res;
	}

	function buildAnimation(colorList, frames) {
		var frame = 0;
		var r,g,b,h;
		var res = [];
		for(var i = 0;i < colorList.length-1;i++) {
			var fc = getRGB(colorList[i]); 
			var tc = getRGB(colorList[i+1]); 

			for(frame = 0;frame<=frames;frame++) {
				r = Math.floor(fc[0] * ((frames-frame)/frames) + tc[0] * (frame/frames));
				g = Math.floor(fc[1] * ((frames-frame)/frames) + tc[1] * (frame/frames));
				b = Math.floor(fc[2] * ((frames-frame)/frames) + tc[2] * (frame/frames));
				h = ColorDecToHex(r, g, b);
				res[res.length] = h;
			}
		}


		if(h.toLowerCase() != toHexColor(colorList[colorList.length-1])) {
			res[res.length] = toHexColor(colorList[colorList.length-1]);
		}
		
		return res;
	}

	var colors = {
		aliceblue:"F0F8FF", antiquewhite:"FAEBD7", aqua:"00FFFF", aquamarine:"7FFFD4",
		azure:"F0FFFF", beige:"F5F5DC", bisque:"FFE4C4", black:"000000",
		blanchedalmond:"FFEBCD", blue:"0000FF", blueviolet:"8A2BE2", brown:"A52A2A",
		burlywood:"DEB887", cadetblue:"5F9EA0", chartreuse:"7FFF00", chocolate:"D2691E",
		coral:"FF7F50", cornflowerblue:"6495ED", cornsilk:"FFF8DC", crimson:"DC143C",
		cyan:"00FFFF", darkblue:"00008B", darkcyan:"008B8B", darkgoldenrod:"B8860B",
		darkgray:"A9A9A9", darkgreen:"006400", darkkhaki:"BDB76B", darkmagenta:"8B008B",
		darkolivegreen:"556B2F", darkorange:"FF8C00", darkorchid:"9932CC", darkred:"8B0000",
		darksalmon:"E9967A", darkseagreen:"8FBC8F", darkslateblue:"483D8B", darkslategray:"2F4F4F",
		darkturquoise:"00CED1", darkviolet:"9400D3", deeppink:"FF1493", deepskyblue:"00BFFF",
		dimgray:"696969", dodgerblue:"1E90FF", firebrick:"B22222", floralwhite:"FFFAF0",
		forestgreen:"228B22", fuchsia:"FF00FF", gainsboro:"DCDCDC", ghostwhite:"F8F8FF",
		gold:"FFD700", goldenrod:"DAA520", gray:"808080", grey:"808080", green:"008000",
		greenyellow:"ADFF2F", honeydew:"F0FFF0", hotpink:"FF69B4", indianred:"CD5C5C",
		indigo:"4B0082", ivory:"FFFFF0", khaki:"F0E68C", lavender:"E6E6FA",
		lavenderblush:"FFF0F5", lawngreen:"7CFC00", lemonchiffon:"FFFACD", lightblue:"ADD8E6",
		lightcoral:"F08080", lightcyan:"E0FFFF", lightgoldenrodyellow:"FAFAD2", lightgreen:"90EE90",
		lightgrey:"D3D3D3", lightpink:"FFB6C1", lightsalmon:"FFA07A", lightseagreen:"20B2AA",
		lightskyblue:"87CEFA", lightslategray:"778899", lightsteelblue:"B0C4DE", lightyellow:"FFFFE0",
		lime:"00FF00", limegreen:"32CD32", linen:"FAF0E6", magenta:"FF00FF",
		maroon:"800000", mediumaquamarine:"66CDAA", mediumblue:"0000CD", mediumorchid:"BA55D3",
		mediumpurple:"9370DB", mediumseagreen:"3CB371", mediumslateblue:"7B68EE", mediumspringgreen:"00FA9A",
		mediumturquoise:"48D1CC", mediumvioletred:"C71585", midnightblue:"191970", mintcream:"F5FFFA",
		mistyrose:"FFE4E1", moccasin:"FFE4B5", navajowhite:"FFDEAD", navy:"000080",
		oldlace:"FDF5E6", olive:"808000", olivedrab:"6B8E23", orange:"FFA500",
		orangered:"FF4500", orchid:"DA70D6", palegoldenrod:"EEE8AA", palegreen:"98FB98",
		paleturquoise:"AFEEEE", palevioletred:"DB7093", papayawhip:"FFEFD5", peachpuff:"FFDAB9",
		peru:"CD853F", pink:"FFC0CB", plum:"DDA0DD", powderblue:"B0E0E6",
		purple:"800080", red:"FF0000", rosybrown:"BC8F8F", royalblue:"4169E1",
		saddlebrown:"8B4513", salmon:"FA8072", sandybrown:"F4A460", seagreen:"2E8B57",
		seashell:"FFF5EE", sienna:"A0522D", silver:"C0C0C0", skyblue:"87CEEB",
		slateblue:"6A5ACD", slategray:"708090", snow:"FFFAFA", springgreen:"00FF7F",
		steelblue:"4682B4", tan:"D2B48C", teal:"008080", thistle:"D8BFD8",
		tomato:"FF6347", turquoise:"40E0D0", violet:"EE82EE", wheat:"F5DEB3",
		white:"FFFFFF", whitesmoke:"F5F5F5", yellow:"FFFF00", yellowgreen:"9ACD32"
	};

	function OppositeColor(value) {
		value = toHexColor(value).split("#").join('').split('');
		var hexVals = "0123456789abcdef";
		var revHexs = hexVals.split('').reverse().join('');
		var currentPos;
		for(var i = 0;i < value.length;i++) {
			currentPos = hexVals.indexOf(value[i]);
			value[i] = revHexs.substring(currentPos,currentPos+1);
		}
		
		return "#" + value.join('');
	}

	function ColorDecToHex(r,g,b) {
		r = r.toString(16); if (r.length == 1) r = '0' + r;
		g = g.toString(16); if (g.length == 1) g = '0' + g; 
		b = b.toString(16); if (b.length == 1) b = '0' + b;
		return "#" + r + g + b;
	}

	function ColorHexToDec(value) {
		var res = [];
		value = value.replace("#", "");
		for(var i = 0;i < 3;i++) {
			res[res.length] = parseInt(value.substr(i * 2, 2), 16);
		}
		return res.join(',');
	}

	// Color Conversion functions from highlightFade
	// By Blair Mitchelmore
	// http://jquery.offput.ca/highlightFade/
	// Parse strings looking for color tuples [255,255,255]
	function getRGB(color) {
		var result;

		// Check if we're already dealing with an array of colors
		if ( color && color.constructor == Array && color.length == 3 )
			return color;

		// Look for rgb(num,num,num)
		if (result = /rgb\(\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*\)/.exec(color))
			return [parseInt(result[1]), parseInt(result[2]), parseInt(result[3])];

		// Look for rgb(num%,num%,num%)
		if (result = /rgb\(\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*\)/.exec(color))
			return [parseFloat(result[1])*2.55, parseFloat(result[2])*2.55, parseFloat(result[3])*2.55];

		// Look for #a0b1c2
		if (result = /#([a-fA-F0-9]{2})([a-fA-F0-9]{2})([a-fA-F0-9]{2})/.exec(color))
			return [parseInt(result[1],16), parseInt(result[2],16), parseInt(result[3],16)];

		// Look for #fff
		if (result = /#([a-fA-F0-9])([a-fA-F0-9])([a-fA-F0-9])/.exec(color))
			return [parseInt(result[1]+result[1],16), parseInt(result[2]+result[2],16), parseInt(result[3]+result[3],16)];

		// Otherwise, we're most likely dealing with a named color
		return ColorHexToDec(colors[jQuery.trim(color).toLowerCase()]).split(',');
	}

	function toHexColor(value) {
		var rgb = getRGB(value);
		return ColorDecToHex(parseInt(rgb[0]), parseInt(rgb[1]), parseInt(rgb[2]));
	}

	function checkParentColor(elm, param) {
		/*White is chosen as default to eliminate issues between IE and FF*/
		var pColr = "#ffffff";
		
		$(elm).parents().each(function(){
			var result = $(this).css(param);
			if(result != 'transparent' && result != '') {
				pColr = result;
				return false;
			}
		});
		
		return pColr;
	}

	function rndColor() {
		var res = [];
		var cm;
		for(var i = 0;i < 3;i++) {
			cm = randRange(0, 255).toString(16); 
			if (cm.length == 1) cm = '0' + cm;
			res[res.length] = cm;
		}
		return "#" + res.join('');
	}

	function randRange(lowVal, highVal) {
		 return Math.floor(Math.random()*(highVal-lowVal+1))+lowVal;
	}

	function udf(val) {
		return typeof(val) == 'undefined' ? true : false;
	}
})(jQuery);
