/**
 * @author Stefan Gandlau
 */

var webShopCheckout = {
	
	ajaxLoader: false,
	
	updateValue: function(objElem) {
		switch(objElem.type) {
			case 'checkbox':
			case 'text':
			case 'radio': {
				elemName = objElem.name;
				elemValue = objElem.value;
			} break;
			case 'select-one': {
				elemName = objElem.name;
				elemValue = objElem[objElem.selectedIndex].value;
			} break;
		}
		webShopCheckout.showLoader();
		new Request.Contao({
			url: document.location.href,
			'method': 'get',
			'data': 'FORM_ACTION=ajaxUpdate&ajaxAction=updateElem&elemName=' + elemName + '&newValue=' + elemValue,
			onSuccess: function(txt, xml) {
				webShopCheckout.hideLoader();
				webShopCheckout.parseXML(xml);
			},
			onError: function(err) {
				webShopCheckout.hideLoader();
			}
		}).get();
	},
	
	parseXML: function(xmlDoc) {
    actions = xmlDoc.getElementsByTagName('action');
    if(actions.length > 0) {
      for(var x = 0; x < actions.length; x++) {
        switch(actions[x].getAttribute('method')) {
          case 'set': {
            if($(actions[x].getAttribute('target')))
              $(actions[x].getAttribute('target')).setHTML(actions[x].childNodes[0].nodeValue);
          } break;
          case 'alert': alert(actions[x].childNodes[0].nodeValue); break;
          case 'updateSelectOptions': {
            var options = actions[x].getElementsByTagName('option');
            webShopAjax.clearBox(actions[x].getAttribute('target'));
            for (var i = 0; i < options.length; i++) {
              var selOpt = new Element('option').setHTML(options[i].childNodes[0].nodeValue);
              selOpt.value = options[i].getAttribute('id');
              if (options[i].getAttribute('selected') == 'selected') {
                selOpt.selected = 'selected';
              }
              $(actions[x].getAttribute('target')).appendChild(selOpt);
            } 
          } break;
          case 'image': {
            webShopAjax.clearBox($(actions[x].getAttribute('target')));
            var objImg = new Element('img', { 'alt': '', 'src': actions[x].childNodes[0].nodeValue });
            $(actions[x].getAttribute('target')).appendChild(objImg);
          } break;
          case 'setFormValue': {
            $(actions[x].getAttribute('target')).value = actions[x].childNodes[0].nodeValue;
          } break;
        }
      }
    }
  },
	
	showLoader: function() {
		if(!this.ajaxLoader) {
			this.ajaxLoader = new Element('div', {
				className: 'ajaxLoader',
				styles: {
					'background-image': 'url(system/modules/webShop/html/ajaxLoader.gif)',
					'background-position': 'center center',
					'background-repeat': 'no-repeat',
					'background-color': '#FFF',
					'border': '1px solid #000',
					'position': 'absolute',
					'left': $(document.body).getStyle('width').toInt() / 2 - (54 / 2) + 'px',
					'top': '250px',
					'width': '54px',
					'height': '55px',
					'padding': '20px'
				}
			});
			this.ajaxLoader.injectInside($(document.body));
		}
		this.ajaxLoader.setStyles({
			'display': 'block'
		});
	},
	
	hideLoader: function() {
		this.ajaxLoader.setStyle('display', 'none');
	},
	
	clearBox: function(id) {
    if($(id)) {
      el = $(id);
      while(el.childNodes.length > 0)
        el.removeChild(el.childNodes[0]);
    }
  },
	
	toggleAddress: function() {
		if ($('newAddress').getStyle('display') == 'none') {
		  $('newAddress').setStyle('display', 'block');
		  $('btnContinue').setStyle('display', 'none');
	  }
	}
	
}

