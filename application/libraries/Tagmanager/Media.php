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
 * Ionize Tagmanager Login Class
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
	public static $tag_definitions = array
	(
		'medias' => 			'tag_medias',

		'medias:id_media' => 	'tag_media_id',
		'medias:alt' => 		'tag_media_alt',
		'medias:base_path' => 	'tag_media_base_path',
		'medias:path' => 		'tag_media_path',				// Can do nesting, no change if not nested ('path' => 'tag_src')
		'medias:src' => 		'tag_media_src',
		'medias:size' => 		'tag_media_size',
		'medias:title' => 		'tag_media_title',
		'medias:link' => 		'tag_media_link',
		'medias:file_name' => 	'tag_media_file_name',
		'medias:description' => 'tag_media_description',
		'medias:copyright' => 	'tag_media_copyright',
		'medias:index' => 		'tag_media_index',
		'medias:count' => 		'tag_media_count',
		'medias:date' => 		'tag_media_date',
		'medias:extension' =>	'tag_media_extension',
		
		// One media
		'media' =>				'tag_media'
	);
	

	/**
	 * Get the medias regarding the type
	 *
	 */
	public static function get_medias($tag, $medias)
	{
		// Media type
		$type = (isset($tag->attr['type']) ) ? $tag->attr['type'] : FALSE;

		// Attribute. Used by tag_media
//		$attr = (isset($tag->attr['attr']) ) ? $tag->attr['attr'] : FALSE;

		// Media extension
		$extension = (isset($tag->attr['extension']) ) ? $tag->attr['extension'] : FALSE;
		
		// Number of wished displayed medias
		$limit = (isset($tag->attr['limit'] )) ? $tag->attr['limit'] : FALSE;
		
		// num. DEPRECATED : Use limit instead
		if ($limit === FALSE)
		{
			$limit = (isset($tag->attr['num'])) ? $tag->attr['num'] : FALSE;
		}

		// Range : Start and stop index, coma separated
		$range = (isset($tag->attr['range'] )) ? explode(',', $tag->attr['range']) : FALSE;
		$from = $to = FALSE;
		
		if ($range !== FALSE)
		{
			$from = $range[0];
			$to = (isset($range[1]) && $range[1] >= $range[0]) ? $range[1] : FALSE;
		}
		
		// Return list ?
		// If set to "list", will return the media list, coma separated.
		// Usefull for javascript
		// Not yet implemented
		$return = ( ! empty($tag->attr['return'])) ? $tag->attr['return'] : FALSE;

		$i = 0;
		
		$tag->locals->count = 0;
		
		if ($type !== FALSE)
		{
			$str = '';
			$filtered_medias = array();

			if ( ! empty($medias))
			{
				// First get the correct media type
				// filter by type
				foreach($medias as $media)
				{
					if ($media['type'] == $type && ($i < $limit OR $limit === FALSE) )
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
				if ($extension !== FALSE)
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

				// 

				// Range / Limit ?
				if ($range !== FALSE)
				{
					$length = ($to !== FALSE) ? $to - $from + 1 : count($filtered_medias) - $from;
					
					if ($limit > 0 && $limit < $length) $length = $limit;
					
					$filtered_medias = array_slice($filtered_medias, $from, $length);
				}
				else if ($limit > 0)
				{
					$filtered_medias = array_slice($filtered_medias, 0, $limit);
				}
				
				// Stores the final number of medias
				$count = count($filtered_medias);
				 
				foreach($filtered_medias as $index => $media)
				{
					$i++;
					$tag->locals->media = $media;
					$tag->locals->index = $i;
					$tag->locals->count = $count;
					$tag->locals->media['index'] = $i;
					$str .= $tag->expand();
				}
			}
			return $str;
		}
		else
		{
			return;
		}
	}


	// ------------------------------------------------------------------------
	

	public static function tag_medias($tag)
	{
		$from = (isset($tag->attr['from']) ) ? $tag->attr['from'] : self::get_parent_tag($tag);;

		$obj = isset($tag->locals->{$from}) ? $tag->locals->{$from} : NULL;

		if ( is_null($obj) )
		{
			$obj = $tag->locals->page;
		}
		
		if ( isset($obj['medias']))
		{
			$medias = $obj['medias'];

			return self::wrap($tag, self::get_medias($tag, $medias));
		}
	}

	// ------------------------------------------------------------------------
	
	
	/**
	 * Returns one media
	 *
	 */
	public static function tag_media($tag)
	{
		// thumb folder name (without the 'thumb_' prefix)
		$type = (isset($tag->attr['type']) ) ? $tag->attr['type'] : FALSE;
		$attr = (isset($tag->attr['attr']) ) ? $tag->attr['attr'] : FALSE;
		$index = (isset($tag->attr['index']) && intval($tag->attr['index']) > 0 ) ? $tag->attr['index'] : '1';
		$random = (isset($tag->attr['random']) && $tag->attr['random'] == 'TRUE' ) ? TRUE : FALSE;
		$extension = (isset($tag->attr['extension']) ) ? $tag->attr['extension'] : FALSE;
		
		$medias = array();
		
		if ($type !== FALSE && $attr != FALSE)
		{
			$parent = self::get_parent_tag($tag);
			
			if (isset($tag->locals->{$parent}))
			{
				$medias = $tag->locals->{$parent}['medias'];
			}
			
			$filtered_medias = array();

			if ( ! empty($medias))
			{
				// First get the correct media type
				// filter by type
				foreach($medias as $media)
				{
					if ($media['type'] == $type)
					{
						$filtered_medias[] = $media;
					}
				}
				
				// Filter by extension if needed
				if ($extension !== FALSE)
				{
					$extension = explode(',', $extension);
					
					$tmp_medias = $filtered_medias;
					$filtered_medias = array();
					
					foreach($tmp_medias as $media)
					{
						$ext = substr($media['file_name'], strrpos($media['file_name'], '.') +1 );
						
						if (in_array($ext, $extension))
						{
							$filtered_medias[] = $media;
						}
					}
				}
				
				// Now, return the asked field
				if ( ! empty($filtered_medias))
				{
//					if ($random == TRUE)
//					{
//						$index = rand(0, count($filtered_medias - 1));
//					}
					
					if ( ! empty($filtered_medias[$index - 1 ]))
					{
						$media = $filtered_medias[$index - 1 ];

						// SRC attribute
						if ($attr == 'src')
						{
							$folder = (isset($tag->attr['folder']) ) ? 'thumb_' . $tag->attr['folder'] : FALSE;
							
							// Media source complete URL
							if ($folder !== FALSE) 
								return base_url() . $media['base_path'] . $folder . '/' . $media['file_name'];
							else
								return base_url() . $media['path'];
						}

						return $media[$attr];
					}
				}
			}
		}
	}
	
	
	// ------------------------------------------------------------------------


	/**
	 * Media Title
	 * @return 		Media Title or title of the asked parent tag.
	 * @usage 		<ion:title [or="<subtitle|alt|description|...>" from="<article|page>"] />
	 *
	 */
	public static function tag_media_title($tag)
	{
		if ( ! empty($tag->attr['from']))
		{
			$tag->attr['name'] = 'title';
			return self::tag_field($tag);
		}
		$title = self::get_value('media', 'title', $tag);

		if ( is_null($title) || $title == '')
		{
			$title = self::get_value('media', 'file_name', $tag);
		}
		
		return self::wrap($tag, $title);
	}
	
	
	// ------------------------------------------------------------------------


	public static function tag_media_alt($tag)
	{
		if ( ! empty($tag->attr['from']))
		{
			$tag->attr['name'] = 'alt';
			return self::tag_field($tag);
		}
		return self::wrap($tag, self::get_value('media', 'alt', $tag));
	}	
	
	
	// ------------------------------------------------------------------------


	public static function tag_media_description($tag)
	{
		if ( ! empty($tag->attr['from']))
		{
			$tag->attr['name'] = 'description';
			return self::tag_field($tag);
		}
		return self::wrap($tag, self::get_value('media', 'description', $tag));
	}

	
	// ------------------------------------------------------------------------


	public static function tag_media_date($tag)
	{
		if ( ! empty($tag->attr['from']))
		{
			$tag->attr['name'] = 'date';
			return self::tag_field($tag);
		}
		return self::wrap($tag, self::get_value('media', 'date', $tag));
	}

	
	// ------------------------------------------------------------------------


	/**
	 * Simple Media tags
	 *
	 */
	public static function tag_media_link($tag) { return self::wrap($tag, $tag->locals->media['link']); }
	public static function tag_media_file_name($tag) { return self::wrap($tag, $tag->locals->media['file_name']); }
	public static function tag_media_base_path($tag) { return $tag->locals->media['base_path']; }
	public static function tag_media_id($tag) { return $tag->locals->media['id_media']; }
	public static function tag_media_path($tag) { return $tag->locals->media['path']; }
	public static function tag_media_copyright($tag) { return self::wrap($tag, $tag->locals->media['copyright']); }
	public static function tag_media_index($tag) { return $tag->locals->index; }
	public static function tag_media_count($tag) { return $tag->locals->count; }

	
	// ------------------------------------------------------------------------


	/**
	 * Returns the media complete URL
	 * 
	 * @usage : <ion:src [size="200" square="<true|false>" unsharp="true|false"]  />
	 *			For pictures, if size is set, returns the path to one thumb with this size
	 *
	 */
	public static function tag_media_src($tag)
	{
		$media = $tag->get('media');

		if ( ! empty($media))
		{
			// Compatibility with older version of Ionize
			if ( ! is_null($tag->getAttribute('folder')))
			{
				$folder = 'thumb_' . $tag->getAttribute('folder');
				
				return base_url() . $media['base_path'] . $folder . '/' . $media['file_name'];
			}
			else if ($tag->getAttribute('size') !== NULL && $media['type'] == 'picture')
			{
				$ci =& get_instance();
				$ci->load->library('medias');

				$settings['size'] = $tag->getAttribute('size');
				$settings['master'] = $tag->getAttribute('master');
				$settings['square'] = $tag->getAttribute('square');
				$settings['adaptive'] = $tag->getAttribute('adaptive');
				$settings['watermark'] = $tag->getAttribute('watermark');
				$settings['background'] = $tag->getAttribute('background');
				$settings['unsharp'] = $tag->getAttribute('unsharp');
				$settings['start'] = $tag->getAttribute('start');				// Used by Square crop

				return $ci->medias->get_src($media, $settings, Settings::get('no_source_picture'));
			}

			return base_url() . $media['path'];
		}

		return '';
	}
	
	
	// ------------------------------------------------------------------------


	/**
	 * Returns the media size
	 *
	 * @usage : <ion:size folder="medium" dim="width|height" />
	 *
	 */
	public static function tag_media_size($tag)
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
	 * Returns the media info
	 *
	 * @note 	Not yet written.
	 *
	 * @todo 	To write.
	 *			Use of Filemanager->getFileInfo()
	 *			Pro : 	Easy to implement
	 *			Cons : 	Strong dependency on the .thimbs folder and the .nfo file created by FileManager.
	 *			
	 * @usage : <ion:info folder="medium" attribute="width|height" />
	 *
	 */
	public static function tag_media_info($tag)
	{
		// thumb folder name (without the 'thumb_' prefix)
		$folder = (isset($tag->attr['folder']) ) ? 'thumb_' . $tag->attr['folder'] : FALSE;

		$attribute = (isset($tag->attr['attribute']) ) ? $tag->attr['attribute'] : FALSE;

		$media = $tag->locals->media;

		if (isset($media['info']))
		{
			return $media['info'][$attribute];
		}
		else
		{
			/* TODO */

		}
		return '';
	}
	
	
	// ------------------------------------------------------------------------

	
	/**
	 * Returns the media extension
	 *
	 * @usage : <ion:extension />
	 *
	 */
	public static function tag_media_extension($tag)
	{
		$extension = substr(strrchr($tag->locals->media['file_name'], '.'), 1);
		return self::wrap($tag, $extension);
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
	
	/*
	private static function _get_thumb_file_path($tag, $media)
	{
		$thumb_folder = (Settings::get('thumb_folder')) ? Settings::get('thumb_folder') : '.thumbs';

		$size = $tag->getAttribute('size');
		//$file_prefix = $tag->getAttribute('square') ? 'square_' : '';
		
		$size_folder = self::_get_thumb_folder($tag);

		$thumb_path_segment = str_replace(Settings::get('files_path') . '/', '', $media['base_path'] );
		$thumb_base_path = DOCPATH . Settings::get('files_path') . '/' . $thumb_folder . '/';
		$thumb_path = $thumb_base_path . $thumb_path_segment;
		$thumb_file_path = $thumb_path . $size_folder . '/' . $media['file_name'];
		
		return $thumb_file_path;
	}
	*/
	
	
/*

	private static function _get_thumb_url($tag, $media)
	{
		$thumb_folder = (Settings::get('thumb_folder')) ? Settings::get('thumb_folder') : '.thumbs';
		
		$size = $tag->getAttribute('size');
		$size_folder = self::_get_thumb_folder($tag);

		$thumb_path_segment = str_replace(Settings::get('files_path') . '/', '', $media['base_path'] );
		
		return base_url() . Settings::get('files_path') . '/' . $thumb_folder . '/' . $thumb_path_segment . $size_folder . '/' . $media['file_name'];
	}
*/
/*
	private static function _get_picture_url($tag, $media)
	{
		return base_url() . $media['path'];
	}
*/

	private static function _get_thumb_folder($tag)
	{
		if($tag->getAttribute('square'))
		{
			return $tag->getAttribute('size') . 'x' . $tag->getAttribute('size');
		}
		
		// width is fixed
		if($tag->getAttribute('master') == 'width')
		{
			return $tag->getAttribute('size') . 'x';
		}
		
		// height is fixed
		if($tag->getAttribute('master') == 'height')
		{
			return 'x' . $tag->getAttribute('size');
		}
		
		if($tag->getAttribute('adaptive'))
		{
			$size = $tag->getAttribute('size');
			$folder_parts = explode(',', $size);
			
			if(isset($folder_parts[1]))
				return trim($folder_parts[0]) . 'x' . trim($folder_parts[1]) . 'a';
			
			return '';
		}
		
		if( ! is_null($tag->getAttribute('background')))
		{
			$size = $tag->getAttribute('size');
			$folder_parts = explode(',', $size);
			
			if(isset($folder_parts[1]))
				return trim($folder_parts[0]) . 'x' . trim($folder_parts[1]) . 'e';
			
			return '';

		}
		
		// The wider side of image
		return $tag->getAttribute('size');
	}
	
}


/* End of file Media.php */
/* Location: /application/libraries/Tagmanager/Media.php */