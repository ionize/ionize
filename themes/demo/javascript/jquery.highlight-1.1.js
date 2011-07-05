(function($){ 
	$.fn.extend({
		highlight: function(strings) {
 			
 			function findText(node, string) {
			  	if (node.nodeType == 3)
			   		 return searchText(node, string);		
			  	else if (node.nodeType == 1 && node.childNodes && !(/(script|style)/i.test(node.tagName))) {
			   		for (var i = 0; i < node.childNodes.length; ++i) {
			    		i += findText(node.childNodes[i], string);
			   		}
			  	}
			  	return 0;
  	
 			}
 
		   	function searchText(node, string){
		  		var position = node.data.toUpperCase().indexOf(string);
		   		if (position >= 0)
		    		return highlight(node, position, string);
		    	else
		    		return 0;
		  	}
  	
		  	 function highlight(node, position, string){
		 		var spannode = document.createElement('span');
		    	spannode.className = 'highlight';
		    	var middlebit = node.splitText(position);
		    	var endbit = middlebit.splitText(string.length);
		    	var middleclone = middlebit.cloneNode(true);
		    	spannode.appendChild(middleclone);
		    	middlebit.parentNode.replaceChild(spannode, middlebit);
		 		return 1;
		 	}
 
			 return this.each(function() {
			 	if(typeof strings == 'string')
			 		findText(this, strings.toUpperCase());	
			 	else
			 		for (var i = 0; i < strings.length; ++i) findText(this, strings[i].toUpperCase());	
			 });
        }
    }); 
})(jQuery);