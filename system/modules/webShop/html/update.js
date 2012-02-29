var wsUpdate = new Class({
	updateBar: false,
	
	initialize: function() {
		this.updateBar = new Element('div', {
			'id': 'ctrlWSUpdate'
		});
		this.updateBar.set('html', 'Es ist eine neue Version des <a href="http://www.typolight-webshop.de/" onclick="window.open(this.href); return false;">TYPOlight webShop</a> verfügbar!');
	
		$('top').appendChild(this.updateBar);
		
	}
});

window.addEvent('domready', function() {
	var update = new wsUpdate();
});
