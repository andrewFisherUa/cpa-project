/**
 *  Project: Simple Tabs
 *  Description: Simple Tabs
 *  @author YouTech Company http://www.smartaddons.com
 */

!function ($) {
	"use strict";
	var SimpleTabs = function(element,options){
		this.element = $(element); 
		this.options = options;
	}
	
	SimpleTabs.prototype = {
		_animate:function(){
			$("ul.spt-tabs li.spt-tab:first-child span.spt-tab-inner, .spt-tab-content:first", this.element).addClass("selected");
			$(".spt-tabs-content .selected .spt-col",this.element).css("top", 0);
			this._tabclick();
			return this;
		},
		_tabclick:function(){
			var that = this;
			$(that.options.allTabs,that.element).click(function() {
				 that.options.el = $(this);
				if ( (!$(that.options.el).hasClass("selected")) && ($(":animated",that.element).length == 0 ) ) {
					$(that.options.allTabs,that.element).removeClass("selected");
					that.options.el.addClass("selected");
					that.options.speedOne = Math.floor(Math.random()*1000) + 500;
					that.options.speedTwo = Math.floor(Math.random()*1000) + 500;
					that.options.speedThree = Math.floor(Math.random()*1000) + 500;
					var box_wrapper = $(".spt-tabs-content",that.element);
					that.options.colOne = $(".spt-col-one").filter('.selected');
					console.log(that.options.colOne);
					that.options.colOne.animate({
						"top": -that.options.colOne.height()
					}, that.options.speedOne);
				
					that.options.colTwo = $(".spt-col-two",box_wrapper).filter('.selected');
					that.options.colTwo.animate({
						"top": -that.options.colTwo.height()
					}, that.options.speedTwo);
				
					that.options.colThree = $(".spt-col-three",box_wrapper).filter('.selected');
					that.options.colThree.animate({
						"top": -that.options.colThree.height()
					}, that.options.speedThree);
					
					$(that.options.allContentBoxes, that.element).removeClass("selected");
					var category_id = $(that.options.el, that.element).attr("class");
							category_id = category_id.replace('selected', '');
							category_id = category_id.replace('spt-tab-inner', '');
							category_id = category_id.replace('category-id-', '');
							category_id = $.trim(category_id);
					
					$(that.options.allContentBoxes, that.element).filter('.category-id-'+category_id).addClass("selected");
					$('.selected' ,box_wrapper).find('.spt-col-one').animate({
						"top": 0
					}, that.options.speedOne, function() {
						that._reset();
					});
					$('.selected', box_wrapper).find('.spt-col-two').animate({
						"top": 0
					}, that.options.speedTwo, function() {
						that._reset();
					});
					$('.selected', box_wrapper).find('.spt-col-three').animate({
						"top": 0
					}, that.options.speedThree, function() {
						that._reset();
					});
				
				};
				return false;
			})
		},
		_reset:function() {
			var that = this;
			that.options.columnReadyCounter++;
			if (that.options.columnReadyCounter == 3) {
				$(".spt-col",that.element).not(".selected .spt-col").css("top", 350);
				that.options.columnReadyCounter = 0;
			}
		}
	}
	
	$.fn.simpletabs = function (option) {
		return this.each(function () {
			var $this = $(this),
				data = $this.data('simpletabs'),
				options = $.extend({}, $.fn.simpletabs.defaults, typeof option == 'object' && option)
			if (!data) $this.data('simpletabs', (data = new SimpleTabs(this, options)))
			data._animate();
			
		})
	}
	
	$.fn.simpletabs.defaults = {
			allContentBoxes:".spt-tab-content",
			allTabs:"ul.spt-tabs li.spt-tab span.spt-tab-inner",
			el:"", colOne:"", colTwo:"", colThree:"",
			speedOne:1000,
			speedTwo:1500,
			speedThree:2000,
			columnReadyCounter:0
	}
	
	$.fn.simpletabs.Constructor = SimpleTabs
	
}(window.jQuery);	