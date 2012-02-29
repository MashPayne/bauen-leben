var ws_tabs = new Class({
	
	activeTab: 0,
	tabContents: [],
	activeTabBtn: false,
	
	initialize: function(tabBox) {
	
	    $(tabBox).getElements('.ws_tabContent').each(function(item, index) {
	    	this.tabContents.push(item);
	    	item.setStyle('display', 'none');
	    }.bind(this));
	    var isFirst = true;
		$(tabBox).getElements('.ws_tabBtn').each(function(item, index) {
			if(isFirst) {
				item.addClass('active');
				this.activeTabBtn = item;
				isFirst = false;
			}
			item.addEvent('click', function() {
				this.setButtonActive(item);
				this.showTabContent(index);
			}.bind(this));
		}.bind(this));
		
		this.tabContents[this.activeTab].setStyle('display', 'block');
		
		
	},
	
	showTabContent: function(index) {
		$(this.tabContents[this.activeTab]).setStyle('display', 'none');
		$(this.tabContents[index]).setStyle('display', 'block');
		this.activeTab = index;
	},
	
	setButtonActive: function(item) {
		item.addClass('active');
		if(this.activeTabBtn)
			this.activeTabBtn.removeClass('active');
		this.activeTabBtn = item;
		
	}
	
});
