<?php defined('BASEPATH') OR exit('No direct script access allowed');


class Media extends API_Controller
{
	/**
	 * Get Method
	 * Returns one media source URL
	 * http://domain.tld/files/.thumbs/350x200/picture.jpg
	 *
	 * @url : 		/api/media/id/<media_id>
	 * @vars :		media_id	Id of the media
	 * @return		URL to the full sized media
	 *
	 * @url : 		/api/media/id/<media_id>/size/<350x200>/method/<adaptive,square, etc.>
	 * @vars :		media_id	Id of the media
	 * 				size		Asked size
	 * 				method		Wished resize method
	 * @return		URL to the thumb. Only works for pictures
	 *
	 *
	 *
	 *
	 */
	public function index_get()
	{
		// First segment of the URL is considered as the command
		$command = $this->get_segment(1);

		switch($command)
		{
			// Get one media by its ID
			case 'id':
				break;

			default:
				break;
		}

		$this->send_response();
	}


}
