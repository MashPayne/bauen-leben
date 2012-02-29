var webShopAjax = {
  
  /** Based on contao_src.js (c) Leo Feyer * */
 
  toggleCategoryTree: function (el, id, field, name, level)
	{
		el.blur();
		var item = $(id);
		var image = $(el).getFirst();

		
		if (item)
		{
			if (item.getStyle('display') == 'none')
			{
				item.setStyle('display', 'inline');
				image.src = image.src.replace('folPlus.gif', 'folMinus.gif');
				$(el).title = CONTAO_COLLAPSE;
				new Request.Contao().post({'REQUEST_TOKEN': REQUEST_TOKEN, 'isAjax': 1, 'action': 'togglePageTree', 'id': id, 'state': 1});
			}
			else
			{
				item.setStyle('display', 'none');
				image.src = image.src.replace('folMinus.gif', 'folPlus.gif');
				$(el).title = CONTAO_EXPAND;
				new Request.Contao().post({'REQUEST_TOKEN': REQUEST_TOKEN, 'isAjax': 1, 'action': 'togglePagetree', 'id': id, 'state': 0});
			}

			return false;
		}
		
		
		new Request.Contao(
				{
					onRequest: AjaxRequest.displayBox('Loading data …'),
					onSuccess: function(txt, json)
					{
						item = new Element('li');

						item.addClass('tl_parent');
						item.setProperty('id', id);
						item.set('html', txt);
						item.setStyle('display', 'inline');
						item.injectAfter($(el).getParent('li'));

						$(el).title = CONTAO_COLLAPSE;
						image.src = image.src.replace('folPlus.gif', 'folMinus.gif');
						AjaxRequest.hideBox();

						// HOOK
						window.fireEvent('ajax_change');
		   			}
				}).post({'REQUEST_TOKEN': REQUEST_TOKEN, 'isAjax': 1, 'action': 'loadCategoryTree', 'id': id, 'level': level, 'field': field, 'name': name, 'state': 1});
		
// new Request.Contao(
// {
// url: window.location.href,
// onStateChange: AjaxRequest.displayBox('Loading data …'),
//
// onComplete: function(txt, xml)
// {
// var ul = new Element('ul');
//
// ul.addClass('level_' + level);
// ul.set('html', txt);
//
// item = new Element('li');
//
// item.addClass('parent');
// item.setProperty('id', id);
// item.setStyle('display', 'inline');
//
// ul.injectInside(item);
// item.injectAfter($(el).getParent().getParent());
//
// image.src = image.src.replace('folPlus.gif', 'folMinus.gif');
// AjaxRequest.hideBox();
// }
// }).post({'REQUEST_TOKEN': REQUEST_TOKEN, 'isAjax': 1, 'action':
// 'loadCategoryTree', 'id': id, 'level': level, 'field': field, 'name': name,
// 'state': 1});

		return false;
	}
  
}
