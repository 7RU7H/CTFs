// make sure jQuery doesn't conflict with prototype, etc.
jQuery.noConflict();

jQuery(document).ready(function () {
// The document is loaded and ready.  Code goes here...

    // replace CGI links with PHP links
    jQuery("a").click(function () {
        var url = this.href.replace('.cgi', '.php');
        this.href = url;
    });
});