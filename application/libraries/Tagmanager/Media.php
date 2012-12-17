<?php
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://ionizecms.com/doc-license
 * @link		http://ionizecms.com
 * @since		Version 0.92
 *
 */

/**
 * Ionize Tagmanager Media Class
 *
 * Manage users login Form
 *
 * @package		Ionize
 * @subpackage	Libraries
 * @category	TagManager Libraries
 *
 */
class TagManager_Media extends TagManager
{
	/**
	 * Default resize method
	 * 4 methods are allowed : 'square', 'adaptive', 'border', 'wider_side'
	 *
	 * @var string
	 *
	 */
	public static $default_resize_method = 'wider_side';


	public static $tag_definitions = array
	(
		'medias' => 			'tag_medias',

		'media' =>				'tag_expand',
		'media:src' => 			'tag_media_src',
//		'media:src' => 			'tag_simple_value',
		'media:thumb_folder' => 'tag_media_thumb_folder',
		'media:size' => 		'tag_media_size',

		'media:alt' => 			'tag_simple_value',
		'media:base_path' => 	'tag_simple_value',
		'media:path' => 		'tag_simple_value',
		'media:link' => 		'tag_simple_value',
		'media:file_name' => 	'tag_simple_value',
		'medias:description' => 'tag_simple_value',
		'medias:copyright' => 	'tag_simple_value',
		'medias:extension' => 	'tag_simple_value',
		'medias:mime' => 		'tag_simple_value',
	);


	// ------------------------------------------------------------------------


	/**
	 * Filters the medias regarding the type, extension, range.
	 *
	 */
	public static function filter_medias(FTL_Binding $tag, $medias)
	{
		// Media type
		$type = $tag->getAttribute('type');

		// Media extension
		$extension = $tag->getAttribute('extension');

		// Number of wished displayed medias
		$limit = $tag->getAttribute('limit');

		// DEPRECATED : Use limit instead. For compat. reasons
		if (is_null($limit))
			$limit = $tag->getAttribute('num');

		// Range : Start and stop index, coma separated
		$range = $tag->getAttribute('range');
		if (!is_null($range))
			$range = explode(',', $range);

		$from = $to = FALSE;
		
		if (is_array($range))
		{
			$from = $range[0];
			$to = (isset($range[1]) && $range[1] >= $range[0]) ? $range[1] : FALSE;
		}

		// Return list ?
		// If set to "list", will return the media list, coma separated.
		// Usefull for javascript
		// Not yet implemented
		$return = $tag->getAttribute('return', FALSE);

		$i = 0;

		if ( ! is_null($type))
		{
			$filtered_medias = array();

			if ( ! empty($medias))
			{
				// First get the correct media type
				// filter by type
				foreach($medias as $media)
				{
					if ($media['type'] == $type && ($i < $limit OR is_null($limit)) )
					{
						// Only filter on lang if lang_display is set for the media
						if ( ! empty($media['lang_display']))
						{
							if ($media['lang_display'] == Settings::get_lang('current'))
								$filtered_medias[] = $media;						
						}
						else
							$filtered_medias[] = $media;
					}
				}
				
				// Filter by extension if needed
				if (!is_null($extension))
				{
					$extension = explode(',', $extension);
					
					$tmp_medias = $filtered_medias;
					$filtered_medias = array();
					
					foreach($tmp_medias as $media)
					{
						if (in_array($media['extension'], $extension))
						{
							$filtered_medias[] = $media;
						}
					}
				}
				// Range / Limit ?
				if ( ! is_null($range))
				{

					$length = ($to !== FALSE) ? $to + 1 - $from  : count($filtered_medias) + 1 - $from;

					if ($limit > 0 && $limit < $length) $length = $limit;

					$from = $from -1;

					$filtered_medias = array_slice($filtered_medias, $from, $length);
				}
				else if ($limit > 0)
				{
					$filtered_medias = array_slice($filtered_medias, 0, $limit);
				}
			}
			return $filtered_medias;
		}
		return $medias;
	}


	// ------------------------------------------------------------------------


	public static function get_medias(FTL_Binding $tag)
	{
		self::load_model('media_model');
		// Pagination ?
		// $tag_pagination = $tag->getAttribute('pagination');

		// Type filter, limit, SQL filter
		$type = $tag->getAttribute('type');
		$limit = $tag->getAttribute('limit', 0);
		$filter = $tag->getAttribute('filter');

		// Order. Default order : ordering ASC
		$order_by = $tag->getAttribute('order_by', 'date DESC');
		$where = array('order_by' => $order_by);

		// Add type / limit to the where array
		if ( ! is_null($type)) $where['type'] = 'picture';
		if ( $limit ) $where['limit'] = $limit;

		// Get from DB
		$medias = self::$ci->media_model->get_lang_list(
			$where,
			$lang = Settings::get_lang(),
			$filter
		);

		return $medias;
	}


	// ------------------------------------------------------------------------
	

	public static function tag_medias(FTL_Binding $tag)
	{
		$str = '';

		// Get the parent 'medias' data array.
		$medias = $tag->getValue();

		// Get all medias id no parent data
		if (empty($medias) && $tag->getDataParent() == NULL)
			$medias = self::get_medias($tag);

		if ( ! empty($medias))
		{
			// Extend Fields tags
			self::create_extend_tags($tag, 'media');

			// Medias lib, to process the "src" value
			self::$ci->load->library('medias');

			// Filter the parent's medias
			$medias = self::filter_medias($tag, $medias);

			$count = count($medias);
			$tag->set('count', $count);

			// Make medias in random order
			if ( $tag->getAttribute('random') == TRUE)
				shuffle ($medias);

			// Process additional data : src, extension
			foreach($medias as $key => $media)
			{
				$src = base_url() . $media['path'];

				if ($media['type'] == 'picture')
				{
					$settings = self::_get_src_settings($tag);

					if ( ! empty($settings['size']))
						$src = self::$ci->medias->get_src($media, $settings, Settings::get('no_source_picture'));
				}

				$medias[$key]['src'] = $src;
			}

			$tag->set('medias', $medias);

			foreach($medias as $key => $media)
			{
				// Each media has its index and the number of displayed media
				$media['index'] = $key + 1;
				$media['count'] = $count;

				$tag->set('media', $media);
				$tag->set('count', $count);
				$tag->set('index', $key);

				$str .= $tag->expand();
			}
		}
		return self::wrap($tag, $str);
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns the media complete src URL
	 * 
	 * @usage : <ion:src [size="200" square="<true|false>" unsharp="true|false"]  />
	 *			For pictures, if size is set, returns the path to one thumb with this size
	 *
	 */
	public static function tag_media_src(FTL_Binding $tag)
	{
		$media = $tag->get('media');

		if ( ! empty($media))
		{
			if ($media['type'] == 'picture')
			{
				$settings = self::_get_src_settings($tag);

				if (empty($settings['size']))
					return base_url() . $media['path'];

				self::$ci->load->library('medias');
				
				return self::$ci->medias->get_src($media, $settings, Settings::get('no_source_picture'));
			}

			return base_url() . $media['path'];
		}

		return '';
	}
	
	
	// ------------------------------------------------------------------------

	public static function tag_media_thumb_folder(FTL_Binding $tag)
	{
		return self::_get_thumb_folder($tag);
	}


	/**
	 * Returns the media size
	 *
	 * @usage : <ion:size dim="width|height" />
	 *
	 */
	public static function tag_media_size(FTL_Binding $tag)
	{
		// thumb folder name (without the 'thumb_' prefix)
		$folder = (isset($tag->attr['folder']) ) ? 'thumb_' . $tag->attr['folder'] : FALSE;

		$dim = (isset($tag->attr['dim']) ) ? $tag->attr['dim'] : FALSE;

		$media = $tag->locals->media;

		if (isset($media['size']))
		{
			return $media['size'][$dim];
		}
		else
		{
			if ( ! empty($media))
			{
				// Media source complete URL
				if ($folder !== FALSE) 
					$folder = base_url() . $media['base_path'] . $folder . '/' . $media['file_name'];
				else
					$folder = base_url() . $media['path'];
	
				// Get media size
				if ($d = @getimagesize($folder))
				{
					return ($dim == 'width') ? $d['0'] : $d['1'];
				}
			}
		}
		return '';
	}
	
	
	// ------------------------------------------------------------------------


	/**
	 * Get the dimensions of a picture
	 *
	 * @param	string	Complete path to the image file
	 * @return	array	Array of dimension.
	 *					'width' : contains the width
	 *					'height' : contains the height
	 *
	 */
	private static function get_image_dimensions($path)
	{
		$dim = array();
		
		if (function_exists('getimagesize'))
		{
			$d = @getimagesize($path);
			
			if ($d !== FALSE)
			{
				$dim['width']	= $d['0'];
				$dim['height']	= $d['1'];
				return $dim;
			}
		}
		return FALSE;
	}
	

	// ------------------------------------------------------------------------


	protected static function _get_src_settings(FTL_Binding $tag)
	{
		$setting_keys = array
		(
			'method',		// 'square', 'adaptive', 'border', 'width', 'height'
			'size',
			'watermark',
			'unsharp',
			'start',		// attribute for 'square' method
			'color',		// attribute for 'border' method
			'refresh',
		);

		$settings = array_fill_keys($setting_keys, '');

		// <ion:medias /> parent
		$parent = $tag->getParent('medias');
		if ( !is_null($parent))
			$settings = array_merge($settings, $parent->getAttributes());

		// <ion:media /> parent
		$parent = $tag->getParent('media');
		if ( !is_null($parent))
			$settings = array_merge($settings, $parent->getAttributes());

		$settings = array_merge($settings, $tag->getAttributes());

		if (empty($settings['method']))
			$settings['method'] = self::$default_resize_method;

		return $settings;
	}


	// ------------------------------------------------------------------------


	private static function _get_thumb_folder(FTL_Binding $tag)
	{
		self::$ci->load->library('medias');

		$settings = self::_get_src_settings($tag);

		return self::$ci->medias->get_thumb_folder($settings);
	}
}

/* End of file Media.php */
/* Location: /application/libraries/Tagmanager/Media.php */