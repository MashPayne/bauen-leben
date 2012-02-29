/**
 * @author Stefan Gandlau <stefan@gandlau.net>
 */
var strRequestToken = '';

var cls_webShopAjax = new Class({
	formId: '',
	variantCache: [],
	objAjax: null,
	useAjax: true,
	hasOptions: false,
	
	loadVariant: function(frmId, attrId, attrVal) {
		htmlPost = new Array();
		this.formId = frmId;
		
		$('ctrl_changedBy').value = attrId;
		$('frmFORM_ACTION').value = 'loadVariant';
		if(!this.objAjax) {
			this.objAjax = new Form.Request($('frmArticle'), false, {
				resetForm: false,
				onSuccess: function(target, resp, xml, text, js) {
					webShopAjax.parseJSON(JSON.decode(text));
				}
			});
		}
		this.objAjax.send();
		$('frmFORM_ACTION').value = 'webShopAddCartItem';
		$('ctrl_changedBy').value = '';
	},
	
	setAttributeSelector: function(c, v, isSelectBox) {
		if(isSelectBox) {
		var box = $('attr_' + c);
		box.getElements('option').each(function(o, i) {
			if(o.value == v) {
				box.selectedIndex = i;
				$(box).onchange();
	    }
		});
		} else {
			this.loadVariant(false, c, v);
		}
	},
	
	updateVariant: function() {

		$('frmFORM_ACTION').value = 'loadVariant';
		if(!this.objAjax) {
			this.objAjax = new Form.Request($('frmArticle'), false, {
				resetForm: false,
				onSuccess: function(target, resp, xml, text, js) {
					webShopAjax.parseJSON(JSON.decode(text));
				}
			});
		}
		this.objAjax.send();
		$('frmFORM_ACTION').value = 'webShopAddCartItem';	
	},
	
	parseJSON: function(resp) {
		for(var x = 0; x < resp.length; x++) {
			act = resp[x].method;
			target = resp[x].target;
			value = resp[x].data;
			switch(act) {
				case 'set': {
					if(target) {
						$(target).set('html', value);
					}
				} break;
				case 'alert': { alert(value); } break;
				case 'updateSelectOptions': {
					var options = value;
					webShopAjax.clearBox(target);
					for (var i = 0; i < options.length; i++) {
						var selOpt = new Element('option').set('html', options[i].title);
							selOpt.value = options[i].id;
							if (options[i].selected) {
								selOpt.selected = 'selected';
			            }
						$(target).appendChild(selOpt);
					}	
					
				} break;
				case 'updateSelectOptions2': {
					var options = value;
					webShopAjax.clearBox('list_'+target);
					currentActive = 0;
					for (var i = 0; i < options.length; i++) {
						if(options[i].selected) {
							selected = ' active';
							currentActive = options[i].id;
						} else
							selected = '';
						var elem = new Element('li', {
							'class': 'attribute_elem' + selected
						});
						
						elem.set('html', options[i].title);
						$('list_' + target).appendChild(elem);
						elem.onclick = new Function('webShopAjax.updateVariantSelection(' + target.substr(5) +', ' + options[i].id + ');');
					}
					var fldCat = new Element('input');
					fldCat.name="attr[" + target.substr(5) +"]";
					fldCat.value = currentActive;
					fldCat.type = 'hidden';
					fldCat.id = 'ctrl_' + target;
					$('list_' + target).appendChild(fldCat);
				} break;
				
				case 'image': {
					$(target).src = value;
				} break;
				case 'setFormValue': {
					$(target).value = value;
				} break;
				case 'link': { $(target).href = value; } break;
				case 'toggleVisibility': {$(target).setStyle('display', value); } break;
				case 'mojoimage': {
					changeZoomImage($('articleImage'), value)
				} break;
				case 'token': {
					$('ajax_token').value = value
				} break;
				case 'setByClass': {
					$$(target).each(function(item, index) {
						item.set('html', value);
					});
				} break;
			}
		}
	},
	
	updateVariantSelection: function(cat, newval) {
		$('ctrl_attr_' + cat).value = newval;
		this.loadVariant(false, cat);
	},
	
	parseXML: function(xmlDoc) {
		this.variantCache = [];
		
		actions = xmlDoc.getElementsByTagName('action');
		if(actions.length > 0) {
			for(var x = 0; x < actions.length; x++) {
				switch(actions[x].getAttribute('method')) {
					case 'set': {
						if($(actions[x].getAttribute('target')))
							if(actions[x].hasChildNodes())
			  		  	    	$(actions[x].getAttribute('target')).set('html', actions[x].childNodes[0].nodeValue);
				    } break;
					case 'alert': alert(actions[x].childNodes[0].nodeValue); break;
					case 'updateSelectOptions': {
						var options = actions[x].getElementsByTagName('option');
						webShopAjax.clearBox(actions[x].getAttribute('target'));
						for (var i = 0; i < options.length; i++) {
							var selOpt = new Element('option').set('html', options[i].childNodes[0].nodeValue);
								selOpt.value = options[i].getAttribute('id');
								if (options[i].getAttribute('selected') == 'selected') {
									selOpt.selected = 'selected';
				            }
							$(actions[x].getAttribute('target')).appendChild(selOpt);
						}	
					} break;
					case 'updateSelectOptions2': {
						var options = actions[x].getElementsByTagName('option');
						webShopAjax.clearBox($('list_' + actions[x].getAttribute('target')));

						for (var i = 0; i < options.length; i++) {
							if(options[i].getAttribute('selected') == 'selected')
								selected = ' active';
							else
								selected = '';
							var elem = new Element('li', {
								'class': 'attribute_elem' + selected
							});
							
							elem.set('html', options[i].childNodes[0].nodeValue);
							$('list_' + actions[x].getAttribute('target')).appendChild(elem);
							elem.onclick = new Function('webShopAjax.loadVariant(false, ' + actions[x].getAttribute('target').substr(5) + ', ' + options[i].getAttribute('id') +');');
						}	
					} break;
					case 'image': {
						$(actions[x].getAttribute('target')).src = actions[x].childNodes[0].nodeValue;

					} break;
					case 'setFormValue': {
						$(actions[x].getAttribute('target')).value = actions[x].childNodes[0].nodeValue;
					} break;
					case 'link': {
						$(actions[x].getAttribute('target')).href = actions[x].childNodes[0].nodeValue;
					} break;
					case 'toggleVisibility': {
						$(actions[x].getAttribute('target')).setStyle('display', actions[x].childNodes[0].nodeValue);
					} break;
					case 'mojoimage': {
						changeZoomImage($('articleImage'), actions[x].childNodes[0].nodeValue)
					} break;
					case 'token': {
						$('ajax_token').value = actions[x].childNodes[0].nodeValue;
					} break;
				}
			}
		}
	},
	
	clearBox: function(id) {
		if($(id)) {
			el = $(id);
			while(el.childNodes.length > 0)
			  el.removeChild(el.childNodes[0]);
		}
	},
	
	updateSelection: function(box, newId) {
		
	},
	
	updateArticleByOption: function() {
		$('frmFORM_ACTION').value = 'loadVariant';
		
		if(!this.objAjax) {
			this.objAjax = new Form.Request($('frmArticle'), false, {
				resetForm: false,
				onSuccess: function(target, resp, xml, text, js) {
					webShopAjax.parseJSON(JSON.decode(text));
				}
			});
		}
		this.objAjax.send();
		$('frmFORM_ACTION').value = 'webShopAddCartItem';	
	},
	
	prepareOptionEvents: function() {
		if($$('.webShop_options').length) {
			this.hasOptions = true;
			$$('.webShop_options').each(function(item, index) {
				if(item.hasClass('options_radio')) {
					item.getElements('input:[type="radio"]').each(function(i, id) {
						i.addEvent('click', function() {webShopAjax.updateArticleByOption(); });
					});
				} else if(item.hasClass('options_checkbox')) {
					item.getElements('input:[type="checkbox"]').each(function(i, id) {
						i.addEvent('click', function() {webShopAjax.updateArticleByOption(); });
					});
				} else if(item.hasClass('options_select')) {
					item.getElements('select').each(function(i, id) {
						i.addEvent('change', function() {webShopAjax.updateArticleByOption(); });
					});
				}
			});
		}
		
	},
	
	submitForm: function(asAjax) {
		$('frmFORM_ACTION').value = 'webShopAddCartItem';
		if(!this.objAjax) {
			this.objAjax = new Form.Request($('frmArticle'), false, {
				resetForm: false,
				onSuccess: function(target, resp, xml, text, js) {
					webShopAjax.parseJSON(JSON.decode(text));
				}
			});
		}
		if(asAjax) {
			$('ctrl_isAjax').value = '1';
			this.objAjax.enable();
			this.objAjax.send();
		} else {
			this.objAjax.disable();
			$('frmFORM_ACTION').value = 'webShopAddCartItem';
			$('ctrl_isAjax').value = '';
			$('frmArticle').submit();
		}
	}
	
});

window.addEvent('domready', function() {
	webShopAjax = new cls_webShopAjax();
	webShopAjax.prepareOptionEvents();
});