<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * RSS widget
 * 
 *
 * @author	Partikule Studio
 *
 * @usage	To be called from a view : 
 * 			<\ion:widget name="rss" url="..." display="5" />
 *
 *			url : 	URL
 *			nb :	Number of items to display
 *
 */
class Rss extends Widget
{
	function run($url, $nb) 
	{
		$data = array();
	
		$xml = @simplexml_load_file($url);
		
		if ($xml)
		{
			// Title
			$title= $xml->xpath('//channel/title');
			$data['title'] = lang('widget_rss_title') . (String) $title[0];
			
			// Items
			$items= $xml->xpath('//channel/item');
			
			
			$posts = array();
			$i = 0;
			while ($i < $nb)
			{
				$posts[] = (Array) $items[$i];
				$i++;
			}
			$data['posts'] = $posts;
			
	// trace($posts);

			return $this->render('rss', $data);
		}
		else
		{
			return $this->show_error(lang('widget_weather_url_not_reachable') . '<br/>' . $url);
		}
	}
} 