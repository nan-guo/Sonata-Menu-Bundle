'use strict'

jQuery(document).ready(function($) {
	var menuDepth = jQuery('#menu-depth').val();
	var list = null;
	var results = [];
	var items = jQuery('#items');

	jQuery('#nestable').nestable({
        maxDepth: menuDepth,
    }).on('change', function(e){
    	list   = e.length ? e : jQuery(e.target);
    	results = JSON.stringify(list.nestable('serialize'));
    	items.val(results);
    });
});