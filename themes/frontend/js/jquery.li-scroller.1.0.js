/*!
 * liScroll 1.0
 * Examples and documentation at:
 * http://www.gcmingati.net/wordpress/wp-content/lab/jquery/newsticker/jq-liscroll/scrollanimate.html
 * 2007-2010 Gian Carlo Mingati
 * Version: 1.0.2.1 (22-APRIL-2011)
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 * Requires:
 * jQuery v1.2.x or later
 *
 */
;jQuery.fn.liScroll=function(a){a=jQuery.extend({travelocity:0.07},a);return this.each(function(){var c=jQuery(this);c.addClass("newsticker");var f=1;c.find("li").each(function(j){f+=jQuery(this,j).outerWidth(true)});var i=c.wrap("<div class='mask'></div>");var e=c.parent().wrap("<div class='tickercontainer'></div>");var g=c.parent().parent().width();c.width(f);var d=f+g;var b=d/a.travelocity;function h(k,j){c.animate({left:"-="+k},j,"linear",function(){c.css("left",g);h(d,b)})}h(d,b);c.hover(function(){jQuery(this).stop()},function(){var l=jQuery(this).offset();var j=l.left+f;var k=j/a.travelocity;h(j,k)})})};