var imageSlide = new Class({
	Implements: Options,
	options: {
		ctrl_data: false,
		ctrl_viewer: false,
		cls_single: false,
		imagesPerSlide: 6,
		transition: Fx.Transitions.Sine.easeOut,
		currentImage: 0,
		duration: 1200,
		enableZoom: false
	},
	activeimage: -1,
	loadCounter: 0,
	activated: true,
	thumbs: [],
	big: [],
	full: [],
	ctrls: [],
	titles: [],
	
	initialize: function(options) {
		this.setOptions(options);
		
		/* hide data box */
		$(this.options.ctrl_data).setStyle('display', 'none');
		
		/* build the gallery */
		this.container = $('is_container');
		this.slider = $('is_slider');
		this.sliderInner = $('is_sliderInner');
		this.viewer = $(this.options.ctrl_viewer);
		this.sliderContainer = $('is_sliderContainer');
		
		this.btnPrev = $('is_btnPrev');
		this.btnNext = $('is_btnNext');
		
		
		/* add to dom */
		this.container.inject($(this.options.ctrl_data), 'before');
		/* load images */
		$(this.options.ctrl_data).getElements(this.options.cls_single).each(function(item, index) {
			var t = item.getElement('a.is_thumb');
			var b = item.getElement('a.is_big');
			var f = item.getElement('a.is_full');
			this.thumbs.push(t);
			this.big.push(b);
			this.full.push(item.getElement('a.is_full'));
			
			/* add thumb to slider */
			var i = new Element('img', {
				src: t
			});
			i.addEvent('load', function() {
				this.loadCounter++;
				if(this.loadCounter == $(this.options.ctrl_data).getElements(this.options.cls_single).length) {
					this.prepareSlider();
				}
			}.bind(this));
			
			this.ctrls.push(i);
			
			i.addEvent('click', function() {
				this.showImage(index);
			}.bind(this));
			
			i.injectInside(this.sliderInner);
		}.bind(this));
		
	},
	
	prepareSlider: function() {
		this.sliderInner.setStyle('width', ((1 + this.thumbs.length) * this.getThumbWidth(this.thumbs[0])) + 'px');
		this.fieldOfView = this.sliderContainer.getStyle('width').toInt();
		this.pageCount = Math.ceil(this.sliderInner.getScrollSize().x / this.fieldOfView);
		this.currentPage = 0;
		this.scroller = new Fx.Scroll(this.sliderContainer, { transition: this.options.transition, duration: this.options.duration, onComplete: function() {this.activated = true;}.bind(this) });
		/* add events */
		this.btnNext.addEvent('click', function() {
			if(this.activated)
				this.showPage(1);
		}.bind(this));
		this.btnPrev.addEvent('click', function() {
			if(this.activated)
				this.showPage(-1);
		}.bind(this));
		this.showImage(0);
		
	},
	
	showPage: function(p) {
		if(p < 0) {
			if(this.currentPage > 0)
				this.doShowPage(this.currentPage - 1);
		} else if(p > 0) {
			if(this.currentPage < this.pageCount) {
				this.doShowPage(this.currentPage + 1);
			}
		}
	},
	
	doShowPage: function(index) {
		if(this.ctrls[index * this.options.imagesPerSlide]) {
			this.activated = false;
			this.scroller.toElement(this.ctrls[index * this.options.imagesPerSlide]);
			this.currentPage = index;
		}
	},
	
getThumbWidth: function(img) {
		var i = new Image();
		i.src = img;
		return(i.width + 6);
	},
		
	showImage: function(index) {
		this.viewer.src = this.big[index];
		if(this.options.enableZoom) {
			MojoZoom.makeZoomable(
			          $('articleImage'),
			          this.full[index],
			          $('zoomArea'),
			          398, 260,
			          false
			);
		} else {
			this.viewer.getParent().href = this.full[index];
		}
	}
	  
	  
});

imageSlide.implement(new Events, new Options);