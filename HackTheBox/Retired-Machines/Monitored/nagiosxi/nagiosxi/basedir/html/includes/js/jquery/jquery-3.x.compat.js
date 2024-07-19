/**
* Override jquery >= 3.3.1 functions because of wkhtml2pdf 12.5 issues
* 
* load module after jquery 
*/


/**
 * .html()
 * 
 * $(target).html()
 * $(target).html(contentToWrite)
 * 
 */

(function ($) {
  var originalHtml = $.fn.html;
  $.fn.html = function(par) {   
    if (arguments.length >= 1) {  
        //write mode      
        if (typeof par == 'object') {
            //console.log('>> html with element '+par[0].id);
            $(this).empty();
            this[0].appendChild(par);
            return this;
        }
        return originalHtml.call(this,par);
    }
    return originalHtml.call(this);
  };
})(jQuery);


/**
 * .append()
 * 
 * $(target).append(contentToBeAppended)
 * 
 */
(function ($) {
  var originalAppend = $.fn.append;
  $.fn.append = function(par) {
      if (arguments.length >= 1 && typeof par == 'object') {
          //console.log('>> append '+(typeof par)+' '+par[0].id+ ' to '+ (typeof this));
          this[0].appendChild(par);
          return this;
      }
      return originalAppend.call(this, par);
  };
})(jQuery);


/**
 * .after()
 * 
 * $(target).after(contentToBeInserted)
 */
(function ($) {
    var originalAfter = $.fn.after;
    $.fn.after = function(par) {
      if (arguments.length >= 1 && typeof par == 'object') {
          //console.log('>> after with element '+par[0].id);
          this[0].parentNode.insertBefore(par[0], this[0].nextSibling);
          return this;
      }
      return originalAfter.call(this, par);
    };
  })(jQuery);


/**
 * .appendTo()
 * 
 * $(contentToBeAppended).appendTo(jqTarget)
 */
(function ($) {
    var originalAppendTo = $.fn.appendTo;
    $.fn.appendTo = function(par) {
      if (arguments.length >= 1 && typeof par == 'object') {
          //console.log('>> appendTo '+(typeof this[0])+' '+this[0].id+ ' to '+ par[0].id);
          par[0].appendChild(this[0]);
          return this;
      }
      return originalAppendTo.call(this, par);
    };
  })(jQuery);
