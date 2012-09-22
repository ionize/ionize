/**
 * Declare and create the DEMO_MODULE object
 *
 */
var DEMO_MODULE = (DEMO_MODULE || {});

DEMO_MODULE.append = function(hash){
	Object.append(DEMO_MODULE, hash);
}.bind(DEMO_MODULE);

DEMO_MODULE.append(
{
	baseUrl: base_url,
	adminUrl: admin_url,
	moduleUrl: admin_url + 'module/demo/',

	/**
	 * Called when one author is dropped to one droppable element
	 * This method receives :
	 *
	 * @param DOM element   Dragged clone of the element
	 * @param DOM element   DOM Element on which the element is dropped
	 * @param event         The event
	 *
	 */
	dropAuthorOnParent: function(element, droppable, event)
	{
		ION.JSON(
			this.moduleUrl + 'author/add_link',
			{
				'parent': droppable.getProperty('data-parent'),
				'id_parent': droppable.getProperty('data-parent-id'),
				'id_author': element.getProperty('data-id')
			}
		);
	}

});
