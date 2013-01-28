<?php
require(strtr(dirname(__FILE__), '\\', '/') . '/Filemanager/Tooling.php');
require(strtr(dirname(__FILE__), '\\', '/') . '/Filemanager/Image.class.php');
require(strtr(dirname(__FILE__), '\\', '/') . '/getid3/getid3.php');

//-------------------------------------------------------------------------------------------------------------

if (!defined('DEVELOPMENT')) define('DEVELOPMENT', 0);   // make sure this #define is always known to us


// the jpeg quality for the largest thumbnails (smaller ones are automatically done at increasingly higher quality)
define('MTFM_THUMBNAIL_JPEG_QUALITY', 80);

// the number of directory levels in the thumbnail cache; set to 2 when you expect to handle huge image collections.
//
// Note that each directory level distributes the files evenly across 256 directories; hence, you may set this
// level count to 2 when you expect to handle more than 32K images in total -- as each image will have two thumbnails:
// a 48px small one and a 250px large one.
define('MTFM_NUMBER_OF_DIRLEVELS_FOR_CACHE', 1);

// minimum number of cached getID3 results; cache is automatically pruned
define('MTFM_MIN_GETID3_CACHESIZE', 16);

// allow MTFM to use finfo_open() to help us produce mime types for files. This is slower than the basic file extension to mimetype mapping
define('MTFM_USE_FINFO_OPEN', FALSE);

// flags for clean_ID3info_results()
define('MTFM_CLEAN_ID3_STRIP_EMBEDDED_IMAGES',      0x0001);


/**
 * Cache element class custom-tailored for the MTFM: includes the code to construct a unique
 * (thumbnail) cache filename and derive suitable cache filenames from the same template with
 * minimal effort.
 *
 * Makes sure the generated (thumbpath) template is unique for each source file ('$legal_url'). We prevent
 * reduced performance for large file sets: all thumbnails/templates derived from any files in the entire
 * FileManager-managed directory tree, rooted by options['filesDir'], can become a huge collection,
 * so we distribute them across a (thumbnail/cache) directory tree, which is created on demand.
 *
 * The thumbnails cache directory tree is determined by the MD5 of the full path to the source file ($legal_url),
 * using the first two characters of the MD5, making for a span of 256 (directories).
 *
 * Note: when you expect to manage a really HUGE file collection from FM, you may dial up the
 *       MTFM_NUMBER_OF_DIRLEVELS_FOR_CACHE define to 2 here.
 */
class MTFMCacheItem
{
	protected $store;

	protected $legal_url;
	protected $file;
	protected $dirty;
	protected $persistent_edits;
	protected $loaded;
	protected $fstat;

	protected $cache_dir;
	protected $cache_dir_mode;  // UNIX access bits: UGA:RWX
	protected $cache_dir_url;
	protected $cache_base;      // cache filename template base
	protected $cache_tnext;     // thumbnail extension

	protected $cache_file;

	public function __construct($fm_obj, $legal_url, $prefetch = FALSE, $persistent_edits = TRUE)
	{
		$this->init($fm_obj, $legal_url, $prefetch, $persistent_edits);
	}

	public function init($fm_obj, $legal_url, $prefetch = FALSE, $persistent_edits)
	{
		$this->dirty = FALSE;
		$this->persistent_edits = $persistent_edits;
		$this->loaded = FALSE;
		$this->store = array();

		$fmopts = $fm_obj->getSettings();

		$this->legal_url = $legal_url;
		$this->file = $fm_obj->get_full_path($legal_url);
		$this->fstat = NULL;

		$fi = pathinfo($legal_url);
		if (is_dir($this->file))
		{
			$filename = $fi['basename'];
			unset($fi['extension']);
			$ext = '';
		}
		else
		{
			$filename = $fi['filename'];
			$ext = strtolower((isset($fi['extension']) && strlen($fi['extension']) > 0) ? '.' . $fi['extension'] : '');
			switch ($ext)
			{
			case '.gif':
			case '.png':
			case '.jpg':
			case '.jpeg':
				break;

			case '.mp3':
				// default to JPG, as embedded images don't contain transparency info:
				$ext = '.jpg';
				break;

			default:
				//$ext = preg_replace('/[^A-Za-z0-9.]+/', '_', $ext);

				// default to PNG, as it'll handle transparancy and full color both:
				$ext = '.png';
				break;
			}
		}

		// as the cache file is generated, but NOT guaranteed from a safe filepath (FM may be visiting unsafe
		// image files when they exist in a preloaded directory tree!) we do the full safe-filename transform
		// on the name itself.
		// The MD5 is taken from the untrammeled original, though:
		$dircode = md5($legal_url);

		$dir = '';
		for ($i = 0; $i < MTFM_NUMBER_OF_DIRLEVELS_FOR_CACHE; $i++)
		{
			$dir .= substr($dircode, 0, 2) . '/';
			$dircode = substr($dircode, 2);
		}

		$fn = substr($dircode, 0, 4) . '_' . preg_replace('/[^A-Za-z0-9]+/', '_', $filename);
		$dircode = substr($dircode, 4);
		$fn = substr($fn . $dircode, 0, 38);

		$this->cache_dir_url = $fmopts['thumbsDir'] . $dir;
		$this->cache_dir = $fmopts['thumbnailCacheDir'] . $dir;
		$this->cache_dir_mode = $fmopts['chmod'];
		$this->cache_base = $fn;
		$this->cache_tnext = $ext;

		$cache_url = $fn . '-meta.nfo';
		$this->cache_file = $this->cache_dir . $cache_url;

		if ($prefetch)
		{
			$this->load();
		}
	}

	public function load()
	{
		if (!$this->loaded)
		{
			$this->loaded = TRUE; // always mark as loaded, even when the load fails

			if (!is_array($this->fstat) && file_exists($this->file))
			{
				$this->fstat = @stat($this->file);
			}
			if (file_exists($this->cache_file))
			{
				include($this->cache_file);  // unserialize();

				if (   isset($statdata) && isset($data) && is_array($data) && is_array($this->fstat) && is_Array($statdata)
					&& $statdata[10] == $this->fstat[10] // ctime
					&& $statdata[9]  == $this->fstat[9]   // mtime
					&& $statdata[7]  == $this->fstat[7]   // size
				   )
				{
					if (!DEVELOPMENT)
					{
						// mix disk cache data with items already existing in RAM cache: we use a delayed-load scheme which necessitates this.
						$this->store = array_merge($data, $this->store);
					}
				}
				else
				{
					// nuke disk cache!
					@unlink($this->cache_file);
				}
			}
		}
	}

	public function delete($every_ting_baby = FALSE)
	{
		$rv = TRUE;
		$dir = $this->cache_dir;
		$dir_exists = file_exists($dir);

		// What do I get for ten dollars?
		if ($every_ting_baby)
		{
			if ($dir_exists)
			{
				$dir_and_mask = $dir . $this->cache_base . '*';
				$coll = safe_glob($dir_and_mask, GLOB_NODOTS | GLOB_NOSORT);

				if ($coll !== FALSE)
				{
					foreach($coll['files'] as $filename)
					{
						$file = $dir . $filename;
						$rv &= @unlink($file);
					}
				}
			}
		}
		else if (file_exists($this->cache_file))
		{
			// nuke cache!
			$rv &= @unlink($this->cache_file);
		}

		// as the thumbnail subdirectory may now be entirely empty, try to remove it as well,
		// but do NOT yack when we don't succeed: there may be other thumbnails, etc. in there still!
		if ($dir_exists)
		{
			for ($i = 0; $i < MTFM_NUMBER_OF_DIRLEVELS_FOR_CACHE; $i++)
			{
				@rmdir($dir);
				$dir = dirname($dir);
			}
		}

		// also clear the data cached in RAM:
		$this->dirty = FALSE;
		$this->loaded = TRUE;  // we know the cache file doesn't exist any longer, so don't bother trying to load it again later on!
		$this->store = array();

		return $rv;
	}

	public function __destruct()
	{
		if ($this->dirty && $this->persistent_edits)
		{
			// store data to persistent storage:
			if (!$this->mkCacheDir() && !$this->loaded)
			{
				// fetch from disk before saving in order to ensure RAM cache is mixed with _existing_ _valid_ disk cache (RAM wins on individual items).
				$this->load();
			}

			if (!is_array($this->fstat) && file_exists($this->file))
			{
				$this->fstat = @stat($this->file);
			}

			$data = '<?php

// legal URL: ' . $this->legal_url . '

$statdata = ' . var_export($this->fstat, TRUE) . ';

$data = ' . var_export($this->store, TRUE) . ';' . PHP_EOL;

			@file_put_contents($this->cache_file, $data);
		}
	}

	/*
	 * @param boolean $persistent    (default: TRUE) TRUE when we should also check the persistent cache storage for this item/key
	 */
	public function fetch($key, $persistent = TRUE)
	{
		if (isset($this->store[$key]))
		{
			return $this->store[$key];
		}
		else if ($persistent && !$this->loaded)
		{
			// only fetch from disk when we ask for items which haven't been stored yet.
			$this->load();
			if (isset($this->store[$key]))
			{
				return $this->store[$key];
			}
		}

		return NULL;
	}

	/*
	 * @param boolean $persistent    (default: TRUE) TRUE when we should also store this item/key in the persistent cache storage
	 */
	public function store($key, $value, $persistent = TRUE)
	{
		if (isset($this->store[$key]))
		{
			$persistent &= ($this->store[$key] !== $value); // only mark cache as dirty when we actully CHANGE the value stored in here!
		}
		$this->dirty |= ($persistent && $this->persistent_edits);
		$this->store[$key] = $value;
	}


	public function getThumbPath($dimensions)
	{
		assert(!empty($dimensions));
		return $this->cache_dir . $this->cache_base . '-' . $dimensions . $this->cache_tnext;
	}

	public function getThumbURL($dimensions)
	{
		assert(!empty($dimensions));
		return $this->cache_dir_url . $this->cache_base . '-' . $dimensions . $this->cache_tnext;
	}

	public function mkCacheDir()
	{
		if (!is_dir($this->cache_dir))
		{
			@mkdir($this->cache_dir, $this->cache_dir_mode, TRUE);
			return TRUE;
		}
		return FALSE;
	}

	public function getMimeType()
	{
		if (!empty($this->store['mime_type']))
		{
			return $this->store['mime_type'];
		}
		//$mime = $fm_obj->getMimeFromExtension($file);
		return NULL;
	}
}



class MTFMCache
{
	protected $store;           // assoc. array: stores cached data
	protected $store_ts;        // assoc. array: stores corresponding 'cache timestamps' for use by the LRU algorithm
	protected $store_lru_ts;    // integer: current 'cache timestamp'
	protected $min_cache_size;  // integer: minimum cache size limit (maximum is a statistical derivate of this one, about twice as large)

	public function __construct($min_cache_size)
	{
		$this->store = array();
		$this->store_ts = array();
		// store_lru_ts stores a 'timestamp' counter to track LRU: 'timestamps' older than threshold are discarded when cache is full
		$this->store_lru_ts = 0;
		$this->min_cache_size = $min_cache_size;
	}

	/*
	 * Return a reference to the cache slot. When the cache slot did not exist before, it will be created, and
	 * the value stored in the slot will be NULL.
	 *
	 * You can store any arbitrary data in a cache slot: it doesn't have to be a MTFMCacheItem instance.
	 */
	public function &pick($key, $fm_obj = NULL, $create_if_not_exist = TRUE)
	{
		assert(!empty($key));

		$age_limit = $this->store_lru_ts - $this->min_cache_size;

		if (isset($this->store[$key]))
		{
			// mark as LRU entry; only update the timestamp when it's rather old (age/2) to prevent
			// cache flushing due to hammering of a few entries:
			if ($this->store_ts[$key] < $age_limit + $this->min_cache_size / 2)
			{
				$this->store_ts[$key] = $this->store_lru_ts++;
			}
		}
		else if ($create_if_not_exist)
		{
			// only start pruning when we run the risk of overflow. Heuristic: when we're at 50% fill rate, we can expect more requests to come in, so we start pruning already
			if (count($this->store_ts) >= $this->min_cache_size / 2)
			{
				/*
				 * Cleanup/cache size restriction algorithm:
				 *
				 * Randomly probe the cache and check whether the probe has a 'timestamp' older than the configured
				 * minimum required lifetime. When the probe is older, it is discarded from the cache.
				 *
				 * As the probe is assumed to be perfectly random, further assuming we've got a cache size of N,
				 * then the chance we pick a probe older then age A is (N - A) / N  -- picking any age X has a
				 * chance of 1/N as random implies flat distribution. Hitting any of the most recent A entries
				 * is A * 1/N, hence picking any older item is 1 - A/N == (N - A) / N
				 *
				 * This means the growth of the cache beyond the given age limit A is a logarithmic curve, but
				 * we like to have a guaranteed upper limit significantly below N = +Inf, so we probe the cache
				 * TWICE for each addition: given a cache size of 2N, one of these probes should, on average,
				 * be successful, thus removing one cache entry on average for a cache size of 2N. As we only
				 * add 1 item at the same time, the statistically expected bound of the cache will be 2N.
				 * As chances increase for both probes to be successful when cache size increases, the risk
				 * of a (very) large cache size at any point in time is dwindingly small, while cost is constant
				 * per cache transaction (insert + dual probe).
				 *
				 * This scheme is expected to be faster (thanks to log growth curve and linear insert/prune costs)
				 * than the usual where one keeps meticulous track of the entries and their age and entries are
				 * discarded in order, oldest first.
				 */
				$probe_index = array_rand($this->store_ts);
				if ($this->store_ts[$probe_index] < $age_limit)
				{
					// discard antiquated entry:
					unset($this->store_ts[$probe_index]);
					unset($this->store[$probe_index]);
				}
				$probe_index = array_rand($this->store_ts);
				if ($this->store_ts[$probe_index] < $age_limit)
				{
					// discard antiquated entry:
					unset($this->store_ts[$probe_index]);
					unset($this->store[$probe_index]);
				}
			}

			/*
			 * add this slot (empty for now) to the cache. Only do this AFTER the pruning, so it won't risk being
			 * picked by the random process in there. We _need_ this one right now. ;-)
			 */
			$this->store[$key] = (!empty($fm_obj) ? new MTFMCacheItem($fm_obj, $key) : NULL);
			$this->store_ts[$key] = $this->store_lru_ts++;
		}
		else
		{
			// do not clutter the cache; all we're probably after this time is the assistance of a MTFMCacheItem:
			// provide a dummy cache entry, nulled and all; we won't be saving the stored data, if any, anyhow.
			if (isset($this->store['!']) && !empty($fm_obj))
			{
				$this->store['!']->init($fm_obj, $key, FALSE, FALSE);
			}
			else
			{
				$this->store['!'] = (!empty($fm_obj) ? new MTFMCacheItem($fm_obj, $key, FALSE, FALSE) : NULL);
			}
			$this->store_ts['!'] = 0;
			$key = '!';
		}

		return $this->store[$key];
	}
}



class FileManager
{
	protected $options;
	
	protected $getid3;
	protected $getid3_cache;
	protected $icon_cache;              // cache the icon paths per size (large/small) and file extension

	protected $thumbnailCacheDir;
	protected $thumbnailCacheParentDir;  // assistant precalculated value for scandir/view
	protected $filesDir;           // precalculated filesystem path eqv. of options['filesDir']

	public function __construct($options)
	{
		$this->options = array_merge(array
		(
			'filesDir' => NULL,
			'assetsDir' => NULL,
			'thumbsDir' => NULL,				// with trailing slash

			'thumbSmallSize' => 48,
			'thumbBigSize' => 250,

			'FileSystemPath4mimeTypesMapFile' => strtr(dirname(__FILE__), '\\', '/') . '/Filemanager/MimeTypes.ini',  // an absolute filesystem path anywhere; when relative, it will be assumed to be against options['URIpath4RequestScript']

			// Path to the Mimes pure PHP file.
			// Mimes must be set in one $mimes array.
			'mimesPath' => strtr(dirname(__FILE__), '\\', '/') . '/Filemanager/mimes.php',												// Mimes pure PHP array.

			'documentRoot' => $this->getDocumentRoot(),
			'URIpath4RequestScript' => NULL,                                            // default is $_SERVER['SCRIPT_NAME']
			'dateFormat' => 'j M Y - H:i',
			'maxUploadSize' => 2600 * 2600 * 3,

			// Allow to specify the "Resize Large Images" tolerance level.
			'maxImageDimension' => array('width' => 1024, 'height' => 768),
			'upload' => FALSE,
			'destroy' => FALSE,
			'create' => FALSE,
			'move' => FALSE,
			'download' => FALSE,

			'dump' => FALSE,

			/* ^^^ this last one is easily circumnavigated if it's about images: when you can view 'em, you can 'download' them anyway.
			 *     However, for other mime types which are not previewable / viewable 'in their full bluntal nugity' ;-) , this will
			 *     be a strong deterent.
			 */
			'allowExtChange' => FALSE,
			'safe' => TRUE,
			'filter' => NULL,
			'allowed_extensions' => NULL,		// Array of allowed extensions. Bypass the 'safe' mode.
			'chmod' => 0777,
			'cleanFileName' => TRUE,

			'DetailIsAuthorized_cb' => NULL,
			'UploadIsAuthorized_cb' => NULL,
			'DownloadIsAuthorized_cb' => NULL,
			'CreateIsAuthorized_cb' => NULL,
			'MoveIsAuthorized_cb' => NULL,
			'showHiddenFoldersAndFiles' => FALSE      // Hide dot dirs/files ?
		), (is_array($options) ? $options : array()));


		// apply default to URIpath4RequestScript:
		if (empty($this->options['URIpath4RequestScript']))
		{
			$this->options['URIpath4RequestScript'] = $this->getURIpath4RequestScript();
		}
		// log_message('error', 'URIpath4RequestScript : ' . $this->options['URIpath4RequestScript']);

		$this->managedBaseDir = $this->url_path2file_path($this->options['filesDir']);

		// Precalculates Cache dirs
		$this->thumbnailCacheDir = $this->url_path2file_path($this->options['thumbsDir']);
		$this->thumbnailCacheParentDir = $this->url_path2file_path(self::getParentDir($this->options['thumbsDir']));

		// Mimes
		$mimes = NULL;
		if (is_file($this->options['mimesPath']))
			include($this->options['mimesPath']);
		$this->options['mimes'] = $mimes;
		unset($mimes);

		$this->options['FileSystemPath4mimeTypesMapFile'] = @realpath($this->options['FileSystemPath4mimeTypesMapFile']);
		if (empty($this->options['FileSystemPath4mimeTypesMapFile']))
		{
			throw new Exception('nofile');
		}
		$this->options['FileSystemPath4mimeTypesMapFile'] = strtr($this->options['FileSystemPath4mimeTypesMapFile'], '\\', '/');

		// getID3 is slower as it *copies* the image to the temp dir before processing: see GetDataImageSize().
		// This is done as getID3 can also analyze *embedded* images, for which this approach is required.
		$this->getid3 = new getID3();
		$this->getid3->setOption(array('encoding' => 'UTF-8'));
		//$this->getid3->encoding = 'UTF-8';

		$this->getid3_cache = new MTFMCache(MTFM_MIN_GETID3_CACHESIZE);

		$this->icon_cache = array(array(), array());
	}

	/**
	 * @return array the FileManager options and settings.
	 */
	public function getSettings()
	{
		return array_merge(array(
			'thumbnailCacheDir' => $this->thumbnailCacheDir,
			'thumbnailCacheParentDir' => $this->thumbnailCacheParentDir,
			'managedBaseDir' => $this->managedBaseDir
		), $this->options);
	}




	/**
	 * Central entry point for any client side request.
	 */
	public function fireEvent($event = NULL)
	{
		$event = (!empty($event) ? 'on' . ucfirst($event) : NULL);
		if (!$event || !method_exists($this, $event)) $event = 'onView';

		$this->{$event}();
	}


	/**
	 * Generalized 'view' handler, which produces a directory listing.
	 *
	 * Return the directory listing in a nested array, suitable for JSON encoding.
	 */
	protected function _onView($legal_url, $json, $mime_filter, $file_preselect_arg = NULL, $filemask = '*')
	{
		$v_ex_code = 'nofile';
		$dir = $this->get_full_path($legal_url);
		$dir_up = NULL;
		$coll = NULL;

		if (is_dir($dir))
		{
			$coll = $this->scandir($dir, $filemask, FALSE, 0, ($this->options['showHiddenFoldersAndFiles'] ? ~GLOB_NOHIDDEN : ~0));
			if ($coll !== FALSE)
			{
				// To ensure '..' ends up at the very top of the view
				$dir_up = array_pop($coll['dirs']);
				if ($dir_up !== NULL && $dir_up !== '..')
				{
					$coll['dirs'][] = $dir_up;
					$dir_up = NULL;
				}
				natcasesort($coll['dirs']);
				natcasesort($coll['files']);

				$v_ex_code = NULL;
			}
		}

		$fileinfo = array(
			'legal_url' => $legal_url,
			'dir' => $dir,
			'collection' => $coll,
			'file_preselect' => $file_preselect_arg,
			'preliminary_json' => $json,
			'validation_failure' => $v_ex_code
		);

		// View Authorization Callback
		if (
			! empty($this->options['ViewIsAuthorized_cb'])
			&& function_exists($this->options['ViewIsAuthorized_cb'])
			&& ! $this->options['ViewIsAuthorized_cb']($this, 'view', $fileinfo))
		{
			throw new Exception('authorized');
		}

		$file_preselect_index = -1;
		$out = array(array(), array());

		$mime = 'text/directory';

		// Process the '..' dir
		if ($dir_up !== NULL)
		{
			$filename = '..';
			$l_url = $legal_url . $filename;
			$iconspec = 'is.directory_up';

			$icon48 = 	$this->getIcon($iconspec, FALSE);
			$icon48_e = $this->rawurlencode_path($icon48);
			$icon = 	$this->getIcon($iconspec, TRUE);
			$icon_e = 	$this->rawurlencode_path($icon);

			$out[1][] = array(
				'path' => $l_url,
				'name' => $filename,
				'mime' => $mime,
				'icon48' => $icon48_e,
				'icon' => $icon_e
			);
		}

		$iconspec_d = 'is.directory';

		$icon48_d = $this->getIcon($iconspec_d, FALSE);
		$icon48_de = $this->rawurlencode_path($icon48_d);
		$icon_d = $this->getIcon($iconspec_d, TRUE);
		$icon_de = $this->rawurlencode_path($icon_d);

		foreach ($coll['dirs'] as $filename)
		{
			$l_url = $legal_url . $filename;

			$out[1][] = array(
				'path' => $l_url,
				'name' => $filename,
				'mime' => $mime,
				'icon48' => $icon48_de,
				'icon' => $icon_de
			);
		}

		// and now list the files in the directory
		$idx = 0;
		foreach ($coll['files'] as $filename)
		{
			$l_url = $legal_url . $filename;

			// Do not allow the getFileInfo()/imageinfo() overhead per file for very large directories; just guess the mimetype from the filename alone.
			// The real mimetype will show up in the 'details' view anyway as we'll have called getFileInfo() by then!
			$mime = $this->getMimeFromExtension($filename);
			$iconspec = $filename;

			if ( ! $this->isAllowedExtension($filename))
				continue;

			if ($filename === $file_preselect_arg)
				$file_preselect_index = $idx;

			/*
			 * offload the thumbnailing process to another event ('event=detail / mode=direct') to be fired by the client
			 * when it's time to render the thumbnail: the offloading helps us tremendously in coping with large
			 * directories:
			 * WE simply assume the thumbnail will be there, so we don't even need to check for its existence
			 * (which saves us one more file_exists() per item at the very least). And when it doesn't, that's
			 * for the event=thumbnail handler to worry about (creating the thumbnail on demand or serving
			 * a generic icon image instead).
			 *
			 * For now, simply assign a basic icon to any and all; the 'detail' event will replace this item in the frontend
			 * when the time has arrives when that 'detail' request has been answered.
			 */
			$icon48 = $this->getIcon($iconspec, FALSE);
			$icon48_e = $this->rawurlencode_path($icon48);

			$icon = $this->getIcon($iconspec, TRUE);
			$icon_e = $this->rawurlencode_path($icon);

			$out[0][] = array(
					'path' => $l_url,
					'name' => $filename,
					'mime' => $mime,
					// we don't know the thumbnail paths yet, this will trigger deferred requests: (event=detail, mode=direct)
					'thumbs_deferred' => TRUE,
					'icon48' => $icon48_e,
					'icon' => $icon_e
				);
			$idx++;
		}

		return array_merge((is_array($json) ? $json : array()), array(
			'root' => substr($this->options['filesDir'], 1),
			'this_dir' => array(
				'path' => $legal_url,
				'name' => basename($legal_url),
				'date' => date($this->options['dateFormat'], @filemtime($dir)),
				'mime' => 'text/directory',
				'icon48' => $icon48_de,
				'icon' => $icon_de
			),
			'preselect_index' => ($file_preselect_index >= 0 ? $file_preselect_index + count($out[1]) + 1 : 0),
			'preselect_name' => ($file_preselect_index >= 0 ? $file_preselect_arg : NULL),
			'dirs' => $out[1],
			'files' => $out[0]
		));
	}


	/**
	 * Process the 'view' event (default event fired by fireEvent() method)
	 *
	 * Returns a JSON encoded directory view list.
	 *
	 * Expected parameters:
	 *
	 * $_POST['directory']     path relative to basedir a.k.a. options['filesDir'] root
	 *
	 * $_POST['file_preselect']     optional filename or path:
	 *                         when a filename, this is the filename of a file in this directory
	 *                         which should be located and selected. When found, the backend will
	 *                         provide an index number pointing at the corresponding JSON files[]
	 *                         entry to assist the front-end in jumping to that particular item
	 *                         in the view.
	 *
	 *                         when a path, it is either an absolute or a relative path:
	 *                         either is assumed to be a URI URI path, i.e. rooted at
	 *                           DocumentRoot.
	 *                         The path will be transformed to a LEGAL URI path and
	 *                         will OVERRIDE the $_POST['directory'] path.
	 *                         Otherwise, this mode acts as when only a filename was specified here.
	 *                         This mode is useful to help a frontend to quickly jump to a file
	 *                         pointed at by a URI.
	 *
	 *                         N.B.: This also the only entry which accepts absolute URI paths and
	 *                               transforms them to LEGAL URI paths.
	 *
	 *                         When the specified path is illegal, i.e. does not reside inside the
	 *                         options['filesDir']-rooted LEGAL URI subtree, it will be discarded
	 *                         entirely (as all file paths, whether they are absolute or relative,
	 *                         must end up inside the options['filesDir']-rooted subtree to be
	 *                         considered manageable files) and the process will continue as if
	 *                         the $_POST['file_preselect'] entry had not been set.
	 *
	 * $_POST['filter']        optional mimetype filter string, amy be the part up to and
	 *                         including the slash '/' or the full mimetype. Only files
	 *                         matching this (set of) mimetypes will be listed.
	 *                         Examples: 'image/' or 'application/zip'
	 *
	 * Errors will produce a JSON encoded error report, including at least two fields:
	 *
	 * status                  0 for error; nonzero for success
	 *
	 * error                   error message
	 *
	 * Next to these, the JSON encoded output will, with high probability, include a
	 * list view of a valid parent or 'basedir' as a fast and easy fallback mechanism for client side
	 * viewing code, jumping back to a existing directory. However, severe and repetitive errors may not produce this
	 * 'fallback view list' so proper client code should check the 'status' field in the
	 * JSON output.
	 */
	protected function onView()
	{
		$emsg = NULL;
		$jserr = array(
				'status' => 1
			);

		$mime_filter = $this->getPOST('filter', $this->options['filter']);
		$legal_url = NULL;

		try
		{
			$dir_arg = $this->getPOST('directory');
			$legal_url = $this->get_legal_url($dir_arg . '/');
			$file_preselect_arg = $this->getPOST('file_preselect');

			try
			{
				if ( ! empty($file_preselect_arg))
				{
					// check if this a path instead of just a basename, then convert to legal_url and split across filename and directory.
					if (strpos($file_preselect_arg, '/') !== FALSE)
					{
						// this will also convert a relative path to an absolute path before transforming it to a LEGAL URI path:
						$legal_presel = $this->abs2legal_url_path($file_preselect_arg);

						$prseli = pathinfo($legal_presel);
						$file_preselect_arg = $prseli['basename'];

						// override the directory!
						$legal_url = $prseli['dirname'];
						$legal_url = self::enforceTrailingSlash($legal_url);
					}
					else
					{
						$file_preselect_arg = basename($file_preselect_arg);
					}
				}
			}
			catch(Exception $e)
			{
				// discard the preselect input entirely:
				$file_preselect_arg = NULL;
			}
		}
		catch(Exception $e)
		{
			$emsg = $e->getMessage();
			$legal_url = '/';
			$file_preselect_arg = NULL;
		}

		// loop until we drop below the bottomdir; meanwhile getDir() above guarantees that $dir is a subdir of bottomdir, hence dir >= bottomdir.
		$original_legal_url = $legal_url;
		do
		{
			try
			{
				$rv = $this->_onView($legal_url, $jserr, $mime_filter, $file_preselect_arg);

				$this->sendHttpHeaders('Content-Type: application/json');
				echo json_encode($rv);
				return;
			}
			catch(Exception $e)
			{
				if ($emsg === NULL)
					$emsg = $e->getMessage();
			}

			// step down to the parent dir and retry:
			$legal_url = self::getParentDir($legal_url);
			$file_preselect_arg = NULL;

			$jserr['status']++;

		} while ($legal_url !== FALSE);

		$this->modify_json4exception($jserr, $emsg, 'path = ' . $original_legal_url);
		$this->sendHttpHeaders('Content-Type: application/json');
		echo json_encode($jserr);
		die();
	}

	/**
	 * Process the 'detail' event
	 *
	 * Returns a JSON encoded HTML chunk describing the specified file (metadata such
	 * as size, format and possibly a thumbnail image as well)
	 *
	 * Expected parameters:
	 *
	 * $_POST['directory']     path relative to basedir a.k.a. options['filesDir'] root
	 *
	 * $_POST['file']          filename (including extension, of course) of the file to
	 *                         be detailed.
	 *
	 * $_POST['filter']        optional mimetype filter string, amy be the part up to and
	 *                         including the slash '/' or the full mimetype. Only files
	 *                         matching this (set of) mimetypes will be listed.
	 *                         Examples: 'image/' or 'application/zip'
	 *
	 * $_POST['mode']          'auto' or 'direct': in 'direct' mode, all thumbnails are
	 *                         forcibly generated _right_ _now_ as the client, using this
	 *                         mode, tells us delayed generating and loading of the
	 *                         thumbnail image(s) is out of the question.
	 *                         'auto' mode will simply provide direct thumbnail image
	 *                         URLs when those are available in cache, while 'auto' mode
	 *                         will neglect to provide those, expecting the frontend to
	 *                         delay-load them through another 'event=detail / mode=direct'
	 *                         request later on.
	 *                         'metaHTML': show the metadata as extra HTML content in
	 *                         the preview pane (you can also turn that off using CSS:
	 *                             div.filemanager div.filemanager-diag-dump
	 *                             {
	 *                                 display: none;
	 *                             }
	 *                         'metaJSON': deliver the extra getID3 metadata in JSON format
	 *                         in the json['metadata'] field.
	 *
	 *                         Modes can be mixed by adding a '+' between them.
	 *
	 * Errors will produce a JSON encoded error report, including at least two fields:
	 *
	 * status                  0 for error; nonzero for success
	 *
	 * error                   error message
	 */
	protected function onDetail()
	{
		$emsg = NULL;
		$legal_url = NULL;
		$file_arg = NULL;
		$jserr = array('status' => 1);

		try
		{
			$v_ex_code = 'nofile';

			$mode = $this->getPOST('mode');
			$mode = explode('+', $mode);

			if (empty($mode))
				$mode = array();

			$file_arg = $this->getPOST('file');

			$dir_arg = $this->getPOST('directory');
			$legal_url = $this->get_legal_url($dir_arg . '/');

			$filename = NULL;
			$file = NULL;
			$mime = NULL;
			$meta = NULL;

			if ( ! empty($file_arg))
			{
				$filename = basename($file_arg);
				// must normalize the combo as the user CAN legitimally request filename == '.' (directory detail view) for this event!
				$path = $this->get_legal_url($legal_url . $filename);
				//echo " path = $path, ($legal_url . $filename);\n";
				$legal_url = $path;
				// must transform here so alias/etc. expansions inside get_full_path() get a chance:
				$file = $this->get_full_path($legal_url);

				if (is_readable($file))
				{
					if (is_file($file))
					{
						if ( ! $this->isAllowedExtension($filename))
							throw new Exception('${backend.extension}');
					}
				}
			}

			$fileinfo = array(
				'legal_url' => $legal_url,
				'file' => $file,
				'mode' => $mode,
				'meta_data' => $meta,
				'preliminary_json' => $jserr,
				'validation_failure' => $v_ex_code
			);

			if (
				! empty($this->options['DetailIsAuthorized_cb'])
				&& function_exists($this->options['DetailIsAuthorized_cb'])
				&& !$this->options['DetailIsAuthorized_cb']($this, 'detail', $fileinfo)
			)
			{
				throw new Exception('authorized');
			}

			// File Details
			$jserr = $this->extractDetailInfo($jserr, $legal_url, $meta, $mode);

			$this->sendHttpHeaders('Content-Type: application/json');

			echo json_encode($jserr);
			return;
		}
		catch(Exception $e)
		{
			$emsg = $e->getMessage();
		}

		$this->modify_json4exception($jserr, $emsg, 'file = ' . $file_arg . ', path = ' . $legal_url);

		/*
		 * TODO : Check if necessary (which case ?)
		$icon48 = $this->getIconForError($emsg, 'is.default-error', FALSE);
		$icon48_e = $this->rawurlencode_path($icon48);
		$icon = $this->getIconForError($emsg, 'is.default-error', TRUE);
		$icon_e = $this->rawurlencode_path($icon);
		$jserr['thumb250'] = NULL;
		$jserr['thumb48'] = NULL;
		$jserr['icon48'] = $icon48_e;
		$jserr['icon'] = $icon_e;

		$postdiag_err_HTML = '<p class="err_info">' . $emsg . '</p>';
		$preview_HTML = '${nopreview}';
		$content = '';
		$content .= '<div class="filemanager-preview-content">' . $preview_HTML . '</div>';
		$content .= '<div class="filemanager-errors">' . $postdiag_err_HTML . '</div>';

		$json['content'] = self::compressHTML($content);

		$this->sendHttpHeaders('Content-Type: application/json');

		// when we fail here, it's pretty darn bad and nothing to it.
		// just push the error JSON and go.
		echo json_encode($jserr);
		*/
	}

	/**
	 * Process the 'destroy' event
	 *
	 * Delete the specified file or directory and return a JSON encoded status of success
	 * or failure.
	 *
	 * Note that when images are deleted, so are their thumbnails.
	 *
	 * Expected parameters:
	 *
	 * $_POST['directory']     path relative to basedir a.k.a. options['filesDir'] root
	 *
	 * $_POST['file']          filename (including extension, of course) of the file to
	 *                         be detailed.
	 *
	 * $_POST['filter']        optional mimetype filter string, amy be the part up to and
	 *                         including the slash '/' or the full mimetype. Only files
	 *                         matching this (set of) mimetypes will be listed.
	 *                         Examples: 'image/' or 'application/zip'
	 *
	 * Errors will produce a JSON encoded error report, including at least two fields:
	 *
	 * status                  0 for error; nonzero for success
	 *
	 * error                   error message
	 */
	protected function onDestroy()
	{
		$file_arg = $legal_url = NULL;
		$jserr = array('status' => 1);

		try
		{
			if (!$this->options['destroy'])
				throw new Exception('disabled:destroy');

			$file_arg = $this->getPOST('file');
			$dir_arg = $this->getPOST('directory');
			$legal_url = $this->get_legal_url($dir_arg . '/');

			if ( ! empty($file_arg))
			{
				$filename = basename($file_arg);
				$legal_url .= $filename;

				// must transform here so alias/etc. expansions inside get_full_path() get a chance:
				$file = $this->get_full_path($legal_url);

				if (file_exists($file))
				{
					if (is_file($file))
					{
						// Extension not allowed ?
						if ( ! $this->isAllowedExtension($filename))
							throw new Exception('${backend.extension}');
					}
				}
				else
				{
					throw new Exception('${backend.nofile}');
				}
			}

			if ( ! $this->unlink($legal_url))
			{
				throw new Exception('unlink_failed:' . $legal_url);
			}

			$this->sendHttpHeaders('Content-Type: application/json');

			echo json_encode(array(
				'status' => 1,
				'content' => 'destroyed'
			));
			return;
		}
		catch(Exception $e)
		{
			$emsg = $e->getMessage();
		}

		$jserr['status'] = 0;
		$this->modify_json4exception($jserr, $emsg, 'file = ' . $file_arg . ', path = ' . $legal_url);
		$this->sendHttpHeaders('Content-Type: application/json');
		echo json_encode($jserr);
	}

	/**
	 * Process the 'create' event
	 *
	 * Create the specified subdirectory and give it the configured permissions
	 * (options['chmod'], default 0777) and return a JSON encoded status of success
	 * or failure.
	 *
	 * Expected parameters:
	 *
	 * $_POST['directory']     path relative to basedir a.k.a. options['filesDir'] root
	 *
	 * $_POST['file']          name of the subdirectory to be created
	 *
	 * Extra input parameters considered while producing the JSON encoded directory view.
	 * These may not seem relevant for an empty directory, but these parameters are also
	 * considered when providing the fallback directory view in case an error occurred
	 * and then the listed directory (either the parent or the basedir itself) may very
	 * likely not be empty!
	 *
	 * $_POST['filter']        optional mimetype filter string, amy be the part up to and
	 *                         including the slash '/' or the full mimetype. Only files
	 *                         matching this (set of) mimetypes will be listed.
	 *                         Examples: 'image/' or 'application/zip'
	 *
	 * Errors will produce a JSON encoded error report, including at least two fields:
	 *
	 * status                  0 for error; nonzero for success
	 *
	 * error                   error message
	 */
	protected function onCreate()
	{
		$emsg = NULL;
		$jserr = array('status' => 1);

		$mime_filter = $this->getPOST('filter', $this->options['filter']);

		$file_arg = NULL;
		$legal_url = NULL;

		try
		{
			if (!$this->options['create'])
				throw new Exception('disabled:create');

			$v_ex_code = 'nofile';

			$file_arg = $this->getPOST('file');

			$dir_arg = $this->getPOST('directory');
			$legal_url = $this->get_legal_url($dir_arg . '/');

			// must transform here so alias/etc. expansions inside get_full_path() get a chance:
			$dir = $this->get_full_path($legal_url);

			$filename = NULL;
			$file = NULL;
			$newdir = NULL;

			if ( ! empty($file_arg))
			{
				$filename = basename($file_arg);
				$filename = $this->cleanFilename($filename, array(), '_');

				if (!$this->IsHiddenNameAllowed($file_arg))
				{
					throw new Exception('${backend.authorized}');
				}
				else
				{
					if (is_dir($dir))
					{
						// New dir name, without extension
						$file = $this->getUniqueName(array('filename' => $filename), $dir);

						if ($file !== NULL)
						{
							$newdir = $this->get_full_path($legal_url . $file);
							$v_ex_code = NULL;
						}
					}
				}
			}

			if ( ! @mkdir($newdir, $this->options['chmod'], TRUE))
				throw new Exception('mkdir_failed:' . $this->get_legal_path($legal_url) . $file);

			@chmod($newdir, $this->options['chmod']);
			$this->sendHttpHeaders('Content-Type: application/json');

			// Success, show the new directory as a list view
			$rv = $this->_onView($legal_url . $file . '/', $jserr, $mime_filter);

			echo json_encode($rv);
			return;
		}
		catch(Exception $e)
		{
			// catching other severe failures; since this can be anything and should only happen in the direst of circumstances, we don't bother translating
			$emsg = $e->getMessage();
			$jserr['status'] = 0;

			// and fall back to showing the PARENT directory
			try
			{
				$jserr = $this->_onView($legal_url, $jserr, $mime_filter);
			}
			catch (Exception $e)
			{
				// and fall back to showing the BASEDIR directory
				try
				{
					$legal_url = $this->options['filesDir'];
					$jserr = $this->_onView($legal_url, $jserr, $mime_filter);
				}
				// when we fail here, it's pretty darn bad and nothing to it.
				catch (Exception $e){}
			}
		}

		$this->modify_json4exception($jserr, $emsg, 'directory = ' . $file_arg . ', path = ' . $legal_url);
		$this->sendHttpHeaders('Content-Type: application/json');
		echo json_encode($jserr);
	}

	/**
	 * Process the 'download' event
	 *
	 * Send the file content of the specified file for download by the client.
	 * Only files residing within the directory tree rooted by the
	 * 'basedir' (options['filesDir']) will be allowed to be downloaded.
	 *
	 * Expected parameters:
	 *
	 * $_POST['file']         filepath of the file to be downloaded
	 *
	 * On errors a HTTP 403 error response will be sent instead.
	 *
	 * @throws Exception
	 */
	protected function onDownload()
	{
		$emsg = NULL;
		$file_arg = NULL;
		$file = NULL;
		$jserr = array('status' => 1);

		try
		{
			if ( ! $this->options['download'])
				throw new Exception('disabled:download');

			$v_ex_code = 'nofile';

			$file_arg = $this->getPOST('file');

			$legal_url = NULL;
			$file = NULL;
			$mime = NULL;
			$meta = NULL;
			if (!empty($file_arg))
			{
				$legal_url = $this->get_legal_url($file_arg);

				// must transform here so alias/etc. expansions inside get_full_path() get a chance:
				$file = $this->get_full_path($legal_url);

				if (is_readable($file))
				{
					if (is_file($file))
					{
						if ( ! $this->isAllowedExtension($file))
							throw new Exception('${backend.extension}');
					}
				}
			}

			$fileinfo = array(
				'legal_url' => $legal_url,
				'file' => $file,
				'meta_data' => $meta,
				'validation_failure' => $v_ex_code
			);

			// Download Authorization Callback
			if (
				! empty($this->options['DownloadIsAuthorized_cb'])
				&& function_exists($this->options['DownloadIsAuthorized_cb'])
				&& !$this->options['DownloadIsAuthorized_cb']($this, 'download', $fileinfo)
			)
			{
				throw new Exception('authorized');
			}

			if ($fd = fopen($file, 'rb'))
			{
				$fsize = filesize($file);
				$fi = pathinfo($legal_url);

				$hdrs = array();
				$hdrs[] = 'Content-Type: application/octet-stream';
				$hdrs[] = 'Content-Disposition: attachment; filename="' . $fi['basename'] . '"';
				$hdrs[] = 'Content-length: ' . $fsize;
				$hdrs[] = 'Expires: 0';
				$hdrs[] = 'Cache-Control: must-revalidate, post-check=0, pre-check=0';
				$hdrs[] = '!Cache-Control: private';

				$this->sendHttpHeaders($hdrs);

				fpassthru($fd);
				fclose($fd);
				return;
			}

			$emsg = 'read_error';
		}
		catch(Exception $e)
		{
			$emsg = $e->getMessage();
		}

		// we don't care whether it's a 404, a 403 or something else entirely: we feed 'em a 403 and that's final!
		header('x', true, 403);

		$this->modify_json4exception($jserr, $emsg, 'file = ' . $this->mkSafe4Display($file_arg . ', destination path = ' . $file));

		// Safer for iframes: the 'application/json' mime type would cause FF3.X to pop up a save/view dialog when transmitting these error reports!
		$this->sendHttpHeaders('Content-Type: text/plain');
		echo json_encode($jserr);
	}

	/**
	 * Process the 'upload' event
	 *
	 * Process and store the uploaded file in the designated location.
	 * Images will be resized when possible and applicable. A thumbnail image will also
	 * be preproduced when possible.
	 * Return a JSON encoded status of success or failure.
	 *
	 * Expected parameters:
	 *
	 * $_POST['directory']    path relative to basedir a.k.a. options['filesDir'] root
	 *
	 * $_POST['resize']       nonzero value indicates any uploaded image should be resized to the configured
	 *                        options['maxImageDimension'] width and height whenever possible
	 *
	 * $_POST['filter']       optional mimetype filter string, amy be the part up to and
	 *                        including the slash '/' or the full mimetype. Only files
	 *                        matching this (set of) mimetypes will be listed.
	 *                        Examples: 'image/' or 'application/zip'
	 *
	 * $_FILES[]              the metadata for the uploaded file
	 *
	 * $_POST['reportContentType']
	 *                        if you want a specific content type header set on our response, put it here.
	 *                        This is needed for when we are posting an upload response to a hidden iframe, the
	 *                        default application/json mimetype breaks down in that case at least for Firefox 3.X
	 *                        as the browser will pop up a save/view dialog before JS can access the transmitted data.
	 *
	 * Errors will produce a JSON encoded error report, including at least two fields:
	 *
	 * status                 0 for error; nonzero for success
	 *
	 * error                  error message
	 */
	protected function onUpload()
	{
		try
		{
			// Upload global authorization ?
			if ( ! $this->options['upload'])
				throw new Exception('disabled:upload');

			if ($this->is_HTML5_upload())
			{
				$response = $this->HTML5_upload();
				$this->sendHttpHeaders(array('Content-type: application/json'));
			}
			else
			{
				$response = $this->HTML4_upload();
			}
		}
		catch(Exception $e)
		{
			$response = array(
				'error' => $e->getMessage(),
				'finish' => TRUE
			);
		}

		echo json_encode($response);
	}



	protected function HTML5_upload()
	{
		$max_upload = self::convert_size(ini_get('upload_max_filesize'));
		$max_post = self::convert_size(ini_get('post_max_size'));
		$memory_limit = self::convert_size(ini_get('memory_limit'));

		$limit = min($max_upload, $max_post, $memory_limit);

		$headers = $this->getHttpHeaders();
		$directory = ! empty($headers['X-Directory']) ? $headers['X-Directory'] : '';

		$filter = ! empty($headers['X-Filter']) ? $headers['X-Filter'] : NULL;
		$resize = (bool) ! empty($headers['X-Resize']) ? $headers['X-Resize'] : FALSE;
		$resume_flag = ! empty($headers['X-File-Resume']) ? FILE_APPEND : 0;

		// Prepare the response
		$response = array(
			'id'    	=> $headers['X-File-Id'],
			'directory' => $directory,
			'name'  	=> basename($headers['X-File-Name']),
			'size'  	=> $headers['Content-Length'],
			'error' 	=> UPLOAD_ERR_OK,
			'finish' 	=> FALSE,
		);

		try
		{
			$filename = $response['name'];

			// Size OK ?
			if ($response['size'] > $limit)
				throw new Exception('${backend.size}');

			// Allowed extension ?
			if ( ! $this->isAllowedExtension($filename))
				throw new Exception('${backend.extension}');

			// Full dir path
			$legal_dir_url = $this->get_legal_url($directory . '/');
			$dir = $this->get_full_path($legal_dir_url);

			// No resume : Get one unique filename
			if ( ! $resume_flag)
			{
				$filename = $this->getUniqueName($response['name'], $dir);
				// TODO : Check, test
				// $response['name'] = $filename;
			}

			// Authorization callback
			$fileinfo = array(
				'legal_dir_url' => $legal_dir_url,
				'dir' => $dir,
				'filename' => $filename,
				'size' => $response['size'],
				'maxsize' => $limit,
				'overwrite' => false,
				'resize' => $resize,
				'chmod' => $this->options['chmod'] & 0666,
			);

			if (
				! empty($this->options['UploadIsAuthorized_cb'])
				&& function_exists($this->options['UploadIsAuthorized_cb'])
				&& !$this->options['UploadIsAuthorized_cb']($this, 'upload', $fileinfo)
			)
			{
				throw new Exception('authorized');
			}

			// Creates safe file names
			if ($this->options['cleanFileName'])
				$filename = $this->cleanFilename($filename, array(), '_');

			// full file path
			// $legal_url = $legal_dir_url . $filename;
			// $file_path = $this->get_full_path($legal_url);
			$file_path = $dir . $filename;

			if (file_put_contents($file_path, file_get_contents('php://input'), $resume_flag) === FALSE)
			{
				throw new Exception('${backend.path_not_writable}');
			}
			else
			{
				// Upload finished ?
				if (filesize($file_path) == $headers['X-File-Size'])
				{
					$response['finish'] = TRUE;

					// Prevent execution
					@chmod($file_path, $this->options['chmod'] & 0666);

					// Resize after upload if asked
					$mime = $this->getMimeFromExtension($filename);
					if ($this->startsWith($mime, 'image/') && $resize == TRUE)
					{
						$this->resizePicture($file_path);
					}
				}
			}
		}
		catch(Exception $e)
		{
			$response['error'] = $e->getMessage();
			$response['finish'] = TRUE;
		}

		return $response;

	}

	protected function HTML4_upload()
	{
		$file_input_prefix = $_POST['file_input_prefix'];
		$directory = ! empty($_POST['directory']) ? $_POST['directory'] : '';
		$filter = ! empty($_POST['filter']) ? $_POST['filter'] : NULL;
		$resize = (bool) ! empty($_POST['resize']) ? $_POST['resize'] : FALSE;

		// Upload file using traditional method
		$response = array();

		try
		{
			foreach ($_FILES as $k => $file)
			{
				$response = array(
					'key' => 			(int)substr($k, strpos($k, $file_input_prefix) + strlen($file_input_prefix)),
					'name' => 			basename($file['name']),
					'upload_name' => 	$file['name'],
					'size' => 			$file['size'],
					'error' => 			$file['error'],
					'finish' => 		FALSE,
				);

				if ($response['error'] == 0)
				{
					// Full dir path
					$legal_dir_url = $this->get_legal_url($directory . '/');
					$dir = $this->get_full_path($legal_dir_url);

					$filename = $this->getUniqueName($response['name'], $dir);
					$file_path = $dir . $filename;

					// Allowed extension ?
					if ( ! $this->isAllowedExtension($filename))
						throw new Exception('${backend.extension}');

					if (move_uploaded_file($file['tmp_name'], $file_path) === FALSE)
					{
						throw new Exception('${backend.path_not_writable}');
					}
					else
					{
						$response['finish'] = TRUE;

						// Prevent execution
						@chmod($file_path, $this->options['chmod'] & 0666);

						// Resize after upload if asked
						$mime = $this->getMimeFromExtension($filename);
						if ($this->startsWith($mime, 'image/') && $resize == TRUE)
						{
							$this->resizePicture($file_path);
						}
					}
				}
				else
				{
					// log_message('error', '.... File ERROR ....');
				}
			}
		}
		catch(Exception $e)
		{
			$response['error'] = $e->getMessage();
			$response['finish'] = TRUE;
		}

		return $response;
	}


	protected function resizePicture($file_path)
	{
		$img = new Image($file_path);

		$img->resize(
			$this->options['maxImageDimension']['width'],
			$this->options['maxImageDimension']['height']
		)->save();

		unset($img);
	}


	/**
	 *
	 * Detect if upload method is HTML5
	 *
	 * @return	boolean
	 *
	 */
	public function is_HTML5_upload()
	{
		return (empty($_FILES) && empty($_POST));
	}

	public function getHttpHeaders()
	{
		// GetAllHeaders doesn't work with PHP-CGI
		if (function_exists('getallheaders'))
		{
			$headers = getallheaders();
		}
		else
		{
			$headers = array(
				'Content-Length' => $_SERVER['CONTENT_LENGTH'],
				'X-File-Id' 	=> $_SERVER['HTTP_X_FILE_ID'],
				'X-File-Name' 	=> $_SERVER['HTTP_X_FILE_NAME'],
				'X-File-Resume' => $_SERVER['HTTP_X_FILE_RESUME'],
				'X-File-Size' 	=> $_SERVER['HTTP_X_FILE_SIZE'],
				'X-Directory' 	=> $_SERVER['X-Directory'],
				'X-Filter' 		=> $_SERVER['X-Filter'],
				'X-Resize' 		=> $_SERVER['X-Resize'],
			);
		}

		return $headers;
	}

	/**
	 * Process the 'move' event (with is used by both move/copy and rename client side actions)
	 *
	 * Copy or move/rename a given file or directory and return a JSON encoded status of success
	 * or failure.
	 *
	 * Expected parameters:
	 *
	 *   $_POST['copy']          nonzero value means copy, zero or nil for move/rename
	 *
	 * Source filespec:
	 *
	 *   $_POST['directory']     path relative to basedir a.k.a. options['filesDir'] root
	 *
	 *   $_POST['file']          original name of the file/subdirectory to be renamed/copied
	 *
	 * Destination filespec:
	 *
	 *   $_POST['newDirectory']  path relative to basedir a.k.a. options['filesDir'] root;
	 *                           target directory where the file must be moved / copied
	 *
	 *   $_POST['name']          target name of the file/subdirectory to be renamed
	 *
	 * Errors will produce a JSON encoded error report, including at least two fields:
	 *
	 * status                    0 for error; nonzero for success
	 *
	 * error                     error message
	 */
	protected function onMove()
	{
		$emsg = NULL;
		$file_arg = NULL;
		$legal_url = NULL;
		$newpath = NULL;
		$jserr = array('status' => 1);

		try
		{
			if (!$this->options['move'])
				throw new Exception('disabled:rn_mv_cp');

			$file_arg = $this->getPOST('file');
			$dir_arg = $this->getPOST('directory');
			$legal_url = $this->get_legal_url($dir_arg . '/');

			// must transform here so alias/etc. expansions inside get_full_path() get a chance:
			$dir = $this->get_full_path($legal_url);

			$newdir_arg = $this->getPOST('newDirectory');
			$newname_arg = $this->getPOST('name');
			$rename = (empty($newdir_arg) && !empty($newname_arg));

			$is_copy = !!$this->getPOST('copy');

			$filename = NULL;
			$path = NULL;
			$fn = NULL;
			$legal_newurl = NULL;
			$newdir = NULL;
			$newname = NULL;
			$newpath = NULL;
			$is_dir = FALSE;

			if ( ! $this->IsHiddenPathAllowed($newdir_arg) || ! $this->IsHiddenNameAllowed($newname_arg))
			{
				throw new Exception('authorized');
			}

			if ( ! empty($file_arg))
			{
				$filename = basename($file_arg);
				$path = $this->get_full_path($legal_url . $filename);

				if (file_exists($path))
				{
					$is_dir = is_dir($path);

					// note: we do not support copying entire directories, though directory rename/move is okay
					if ($is_copy && $is_dir)
					{
						throw new Exception('disabled:rn_mv_cp');
					}
					else if ($rename)
					{
						$fn = 'rename';
						$legal_newurl = $legal_url;
						$newdir = $dir;

						$newname = basename($newname_arg);
						if ($is_dir)
							$newname = $this->getUniqueName(array('filename' => $newname), $newdir);  // a directory has no 'extension'
						else
							$newname = $this->getUniqueName($newname, $newdir);

						if ($newname === NULL)
						{
							throw new Exception('nonewfile');
						}
						else
						{
							// Protection agains extension change
							$extOld = pathinfo($filename, PATHINFO_EXTENSION);
							$extNew = pathinfo($newname, PATHINFO_EXTENSION);
							if ((!$this->options['allowExtChange'] || (!$is_dir && empty($extNew))) && !empty($extOld) && strtolower($extOld) != strtolower($extNew))
							{
								$newname .= '.' . $extOld;
							}
						}
					}
					else
					{
						$fn = ($is_copy ? 'copy' : 'rename' /* 'move' */);
						$legal_newurl = $this->get_legal_url($newdir_arg . '/');
						$newdir = $this->get_full_path($legal_newurl);

						if ($is_dir)
							$newname = $this->getUniqueName(array('filename' => $filename), $newdir);
						else
							$newname = $this->getUniqueName($filename, $newdir);

						if ($newname === NULL)
							throw new Exception('nonewfile');
					}

					$newpath = $this->get_full_path($legal_newurl . $newname);
				}
				else
					throw new Exception('nofile');
			}

			$fileinfo = array(
				'legal_url' => $legal_url,
				'dir' => $dir,
				'path' => $path,
				'name' => $filename,
				'legal_newurl' => $legal_newurl,
				'newdir' => $newdir,
				'newpath' => $newpath,
				'newname' => $newname,
				'rename' => $rename,
				'is_dir' => $is_dir,
				'function' => $fn,
				'preliminary_json' => $jserr,
			);

			// Move Authorization callback
			if (
				! empty($this->options['MoveIsAuthorized_cb'])
				&& function_exists($this->options['MoveIsAuthorized_cb'])
				&& !$this->options['MoveIsAuthorized_cb']($this, 'move', $fileinfo))
			{
				throw new Exception('validation_failure');
			}

			if ($rename)
			{
				// try to remove the thumbnail & other cache entries related to the original file; don't mind if it doesn't exist
				$flurl = $legal_url . $filename;
				$meta = &$this->getid3_cache->pick($flurl, $this, FALSE);
				assert($meta != NULL);
				if (!$meta->delete(TRUE))
				{
					throw new Exception('delete_cache_entries_failed');
				}
				unset($meta);
			}

			if (!function_exists($fn))
				throw new Exception((empty($fn) ? 'rename' : $fn) . '_failed');
			if (!@$fn($path, $newpath))
				throw new Exception($fn . '_failed');

			// Json response
			$this->sendHttpHeaders('Content-Type: application/json');
			$jserr['name'] = $newname;
			echo json_encode($jserr);
			return;
		}
		catch(Exception $e)
		{
			$emsg = $e->getMessage();
		}

		$this->modify_json4exception($jserr, $emsg, 'file = ' . $file_arg . ', path = ' . $legal_url . ', destination path = ' . $newpath);
		$this->sendHttpHeaders('Content-Type: application/json');
		echo json_encode($jserr);
	}


	/**
	 * Send the listed headers when possible; the input parameter is an array of header strings or a single header string.
	 *
	 * NOTE: when a header string starts with the '!' character, it means that header is required
	 * to be appended to the header output set and not overwrite any existing equal header.
	 */
	public function sendHttpHeaders($headers)
	{
		if (!headers_sent())
		{
			$headers = array_merge(array(
				'Expires: Fri, 01 Jan 1990 00:00:00 GMT',
				'Cache-Control: no-cache, no-store, max-age=0, must-revalidate'
			), (is_array($headers) ? $headers : array($headers)));

			foreach($headers as $h)
			{
				$append_flag = ($h[0] == '!');
				$h = ltrim($h, '!');
				header($h, $append_flag);
			}
		}
	}


	/**
	 * Derived from   http://www.php.net/manual/en/function.filesize.php#100097
	 * @param $bytes
	 *
	 * @return string
	 */
	public function format_bytes($bytes)
	{
		if ($bytes < 1024)
			return $bytes . ' Bytes';
		elseif ($bytes < 1048576)
			return round($bytes / 1024, 2) . ' KB (' . $bytes . ' Bytes)';
		elseif ($bytes < 1073741824)
			return round($bytes / 1048576, 2) . ' MB (' . $bytes . ' Bytes)';
		else
			return round($bytes / 1073741824, 2) . ' GB (' . $bytes . ' Bytes)';
	}


	/**
	 * Produce a HTML snippet detailing the given file in the JSON 'content' element; place additional info
	 * in the JSON elements 'thumbnail', 'thumb48', 'thumb250', 'width', 'height', ...
	 *
	 * Return an augmented JSON array.
	 *
	 * Throw an exception on error.
	 */
	public function extractDetailInfo($json_in, $legal_url, &$meta, $mode)
	{
		$auto_thumb_gen_mode = !in_array('direct', $mode, TRUE);
		$metaHTML_mode = in_array('metaHTML', $mode, TRUE);
		$metaJSON_mode = in_array('metaJSON', $mode, TRUE);

		$url = $this->get_legal_path($legal_url);
		$filename = basename($url);

		// must transform here so alias/etc. expansions inside url_path2file_path() get a chance:
		$file = $this->url_path2file_path($url);

		$isdir = !is_file($file);
		$mime = NULL;

		// only perform the (costly) getID3 scan when it hasn't been done before,
		// i.e. can we re-use previously obtained data or not?
		if (!is_object($meta))
		{
			$meta = $this->getFileInfo($file, $legal_url);
		}
		// File
		if ( ! $isdir)
		{
			$mime = $meta->getMimeType();
			$mime2 = $this->getMimeFromExtension($file);
			$meta->store('mime_type from file extension', $mime2);
            $iconspec = $filename;
		}
		// Folder
		else if (is_dir($file))
		{
			$mime = $meta->getMimeType();
			// $mime = 'text/directory';
			$iconspec = 'is.directory';
		}
		else
		{
			throw new Exception('nofile');
		}

		// it's an internal error when this entry do not exist in the cache store by now!
		$fi = $meta->fetch('analysis');
		//assert(!empty($fi));

		$icon48 = $this->getIcon($iconspec, FALSE);
		$icon = $this->getIcon($iconspec, TRUE);

		$thumb250 = $meta->fetch('thumb250_direct');
		$thumb48 = $meta->fetch('thumb48_direct');
		$thumb250_e = FALSE;
		$thumb48_e  = FALSE;

		$tstamp_str = date($this->options['dateFormat'], @filemtime($file));
		$fsize = @filesize($file);

		$json = array_merge(array(
			'content' => self::compressHTML('<div class="margin">
				${nopreview}
			</div>')
		),
		array(
			'path' => $legal_url,
			'name' => $filename,
			'date' => $tstamp_str,
			'mime' => $mime,
			'size' => $fsize
		));

		if (empty($fsize))
		{
			$fsize_str = '-';
		}
		else
		{
			// convert to T/G/M/K-bytes:
			$fsize_str = $this->format_bytes($fsize);
		}

		$content = '<dl>
						<dt>${modified}</dt>
						<dd class="filemanager-modified">' . $tstamp_str . '</dd>
						<dt>${type}</dt>
						<dd class="filemanager-type">' . $mime . '</dd>
						<dt>${size}</dt>
						<dd class="filemanager-size">' . $fsize_str . '</dd>';
		$content_dl_term = FALSE;

		$preview_HTML = NULL;
		$postdiag_err_HTML = '';
		$postdiag_dump_HTML = '';
		$thumbnails_done_or_deferred = FALSE;   // TRUE: mark our thumbnail work as 'done'; any NULL thumbnails represent deferred generation entries!
		$check_for_embedded_img = FALSE;

		$mime_els = explode('/', $mime);
		for(;;) // bogus loop; only meant to assist the [mime remapping] state machine in here
		{
			switch ($mime_els[0])
			{
			case 'image':
				/*
				 * thumbnail_gen_mode === 'auto':
				 *
				 * offload the thumbnailing process to another event ('event=thumbnail') to be fired by the client
				 * when it's time to render the thumbnail:
				 * WE simply assume the thumbnail will be there, and when it doesn't, that's
				 * for the event=thumbnail handler to worry about (creating the thumbnail on demand or serving
				 * a generic icon image instead). Meanwhile, we are able to speed up the response process here quite
				 * a bit (rendering thumbnails from very large images can take a lot of time!)
				 *
				 * To further improve matters, we first generate the 250px thumbnail and then generate the 48px
				 * thumbnail from that one (if it doesn't already exist). That saves us one more time processing
				 * the (possibly huge) original image; downscaling the 250px file is quite fast, relatively speaking.
				 *
				 * That bit of code ASSUMES that the thumbnail will be generated from the file argument, while
				 * the url argument is used to determine the thumbnail name/path.
				 */
				$emsg = NULL;
				try
				{
					if (empty($thumb250))
					{
						$thumb250 = $this->getThumb($meta, $file, $this->options['thumbBigSize'], $this->options['thumbBigSize'], $auto_thumb_gen_mode);
					}
					if (!empty($thumb250))
					{
						$thumb250_e = $this->rawurlencode_path($thumb250);
					}
					if (empty($thumb48))
					{
						$thumb48 = $this->getThumb($meta, (!empty($thumb250) ? $this->url_path2file_path($thumb250) : $file), $this->options['thumbSmallSize'], $this->options['thumbSmallSize'], $auto_thumb_gen_mode);
					}
					if (!empty($thumb48))
					{
						$thumb48_e = $this->rawurlencode_path($thumb48);
					}

					if (empty($thumb48) || empty($thumb250))
					{
						/*
						 * do NOT generate the thumbnail itself yet (it takes too much time!) but do check whether it CAN be generated
						 * at all: THAT is a (relatively speaking) fast operation!
						 */
						$imginfo = Image::checkFileForProcessing($file);
					}
					$thumbnails_done_or_deferred = TRUE;
				}
				catch (Exception $e)
				{
					$emsg = $e->getMessage();
					$icon48 = $this->getIconForError($emsg, $legal_url, FALSE);
					$icon = $this->getIconForError($emsg, $legal_url, TRUE);
					// even cache the fail: that means next time around we don't suffer the failure but immediately serve the error icon instead.
				}

				$width = round($this->getID3infoItem($fi, 0, 'video', 'resolution_x'));
				$height = round($this->getID3infoItem($fi, 0, 'video', 'resolution_y'));
				$json['width'] = $width;
				$json['height'] = $height;

				$content .= '
						<dt>${width}</dt><dd>' . $width . 'px</dd>
						<dt>${height}</dt><dd>' . $height . 'px</dd>
					</dl>';
				$content_dl_term = TRUE;

				$sw_make = $this->mkSafeUTF8($this->getID3infoItem($fi, NULL, 'jpg', 'exif', 'IFD0', 'Software'));
				$time_make = $this->mkSafeUTF8($this->getID3infoItem($fi, NULL, 'jpg', 'exif', 'IFD0', 'DateTime'));

				if (!empty($sw_make) || !empty($time_make))
				{
					$content .= '<p>Made with ' . (empty($sw_make) ? '???' : $sw_make) . ' @ ' . (empty($time_make) ? '???' : $time_make) . '</p>';
				}

				// are we delaying the thumbnail generation? When yes, then we need to infer the thumbnail dimensions *anyway*!
				if (empty($thumb48) && $thumbnails_done_or_deferred)
				{
					$dims = $this->predictThumbDimensions($width, $height, $this->options['thumbSmallSize'], $this->options['thumbSmallSize']);

					$json['thumb48_width'] = $dims['width'];
					$json['thumb48_height'] = $dims['height'];
				}
				if (empty($thumb250))
				{
					if ($thumbnails_done_or_deferred)
					{
						// to show the loader.gif in the preview <img> tag, we MUST set a width+height there, so we guestimate the thumbnail250 size as accurately as possible
						//
						// derive size from original:
						$dims = $this->predictThumbDimensions($width, $height, $this->options['thumbBigSize'], $this->options['thumbBigSize']);

						$preview_HTML = '<a href="' . $this->rawurlencode_path($url) . '" data-milkbox="single" title="' . htmlentities($filename, ENT_QUOTES, 'UTF-8') . '">
									   <img src="' . $this->options['assetsDir'] . 'images/transparent.gif" class="preview" alt="preview" style="width: ' . $dims['width'] . 'px; height: ' . $dims['height'] . 'px;" />
									 </a>';

						$json['thumb250_width'] = $dims['width'];
						$json['thumb250_height'] = $dims['height'];
					}
					else
					{
						// when we get here, a failure occurred before, so we only will have the icons. So we use those:
						$preview_HTML = '<a href="' . $this->rawurlencode_path($url) . '" data-milkbox="single" title="' . htmlentities($filename, ENT_QUOTES, 'UTF-8') . '">
									   <img src="' . $this->rawurlencode_path($icon48) . '" class="preview" alt="preview" />
									 </a>';
					}
				}
				// else: defer the $preview_HTML production until we're at the end of this and have fetched the actual thumbnail dimensions

				if (!empty($emsg))
				{
					// use the abilities of modify_json4exception() to munge/format the exception message:
					$jsa = array('error' => '');
					$this->modify_json4exception($jsa, $emsg, 'path = ' . $url);

					// Partikule : Do not display errors concerning getID3()
					// $postdiag_err_HTML .= "\n" . '<p class="err_info">' . $jsa['error'] . '</p>';

					if (strpos($emsg, 'img_will_not_fit') !== FALSE)
					{
						$earr = explode(':', $emsg, 2);
						$postdiag_err_HTML .= "\n" . '<p class="tech_info">Estimated minimum memory requirements to create thumbnails for this image: ' . $earr[1] . '</p>';
					}
				}
				break;

			case 'text':
				switch ($mime_els[1])
				{
				case 'directory':
					$content = '<dl>';
					
					$preview_HTML = '';
					break;

				default:
					// text preview:
					$filecontent = @file_get_contents($file, FALSE, NULL, 0);
					if ($filecontent === FALSE)
						throw new Exception('nofile');

					if (!$this->isBinary($filecontent))
					{
						$preview_HTML = '<pre>' . str_replace(array('$', "\t"), array('&#36;', '&nbsp;&nbsp;'), htmlentities($filecontent, ENT_NOQUOTES, 'UTF-8')) . '</pre>';
					}
					else
					{
						// else: fall back to 'no preview available' (if getID3 didn't deliver instead...)
						$mime_els[0] = 'unknown'; // remap!
						continue 3;
					}
					break;
				}
				break;

			case 'application':
				switch ($mime_els[1])
				{
				case 'x-javascript':
					$mime_els[0] = 'text'; // remap!
					continue 3;

				case 'zip':
					$out = array(array(), array());
					$info = $this->getID3infoItem($fi, NULL, 'zip', 'files');
					if (is_array($info))
					{
						foreach ($info as $name => $size)
						{
							$name = $this->mkSafeUTF8($name);
							$isdir = is_array($size);
							$out[$isdir ? 0 : 1][$name] = '<li><a><img src="' . $this->rawurlencode_path($this->getIcon($name, TRUE)) . '" alt="" /> ' . $name . '</a></li>';
						}
						natcasesort($out[0]);
						natcasesort($out[1]);
						$preview_HTML = '<ul>' . implode(array_merge($out[0], $out[1])) . '</ul>';
					}
					break;

				case 'x-shockwave-flash':
					$check_for_embedded_img = TRUE;

					$info = $this->getID3infoItem($fi, NULL, 'swf', 'header');
					if (is_array($info))
					{
						$width = round($this->getID3infoItem($fi, 0, 'swf', 'header', 'frame_width') / 10);
						$height = round($this->getID3infoItem($fi, 0, 'swf', 'header', 'frame_height') / 10);
						$json['width'] = $width;
						$json['height'] = $height;

						$content .= '
								<dt>${width}</dt><dd>' . $width . 'px</dd>
								<dt>${height}</dt><dd>' . $height . 'px</dd>
								<dt>${length}</dt><dd>' . round($this->getID3infoItem($fi, 0, 'swf', 'header', 'length') / $this->getID3infoItem($fi, 25, 'swf', 'header', 'frame_count')) . 's</dd>
							</dl>';
						$content_dl_term = TRUE;
					}
					break;

				default:
					// else: fall back to 'no preview available' (if getID3 didn't deliver instead...)
					$mime_els[0] = 'unknown'; // remap!
					continue 3;
				}
				break;

			case 'audio':
				$check_for_embedded_img = TRUE;

				$title = $this->mkSafeUTF8($this->getID3infoItem($fi, $this->getID3infoItem($fi, '???', 'tags', 'id3v1', 'title', 0), 'tags', 'id3v2', 'title', 0));
				$artist = $this->mkSafeUTF8($this->getID3infoItem($fi, $this->getID3infoItem($fi, '???', 'tags', 'id3v1', 'artist', 0), 'tags', 'id3v2', 'artist', 0));
				$album = $this->mkSafeUTF8($this->getID3infoItem($fi, $this->getID3infoItem($fi, '???', 'tags', 'id3v1', 'album', 0), 'tags', 'id3v2', 'album', 0));

				$content .= '
						<dt>${title}</dt><dd>' . $title . '</dd>
						<dt>${artist}</dt><dd>' . $artist . '</dd>
						<dt>${album}</dt><dd>' . $album . '</dd>
						<dt>${length}</dt><dd>' . $this->mkSafeUTF8($this->getID3infoItem($fi, '???', 'playtime_string')) . '</dd>
						<dt>${bitrate}</dt><dd>' . round($this->getID3infoItem($fi, 0, 'bitrate') / 1000) . 'kbps</dd>
					</dl>';
				$content_dl_term = TRUE;
				break;

			case 'video':
				$check_for_embedded_img = TRUE;

				$a_fmt = $this->mkSafeUTF8($this->getID3infoItem($fi, '???', 'audio', 'dataformat'));
				$a_samplerate = round($this->getID3infoItem($fi, 0, 'audio', 'sample_rate') / 1000, 1);
				$a_bitrate = round($this->getID3infoItem($fi, 0, 'audio', 'bitrate') / 1000, 1);
				$a_bitrate_mode = $this->mkSafeUTF8($this->getID3infoItem($fi, '???', 'audio', 'bitrate_mode'));
				$a_channels = round($this->getID3infoItem($fi, 0, 'audio', 'channels'));
				$a_codec = $this->mkSafeUTF8($this->getID3infoItem($fi, '', 'audio', 'codec'));
				$a_streams = $this->getID3infoItem($fi, '???', 'audio', 'streams');
				$a_streamcount = (is_array($a_streams) ? count($a_streams) : 0);

				$v_fmt = $this->mkSafeUTF8($this->getID3infoItem($fi, '???', 'video', 'dataformat'));
				$v_bitrate = round($this->getID3infoItem($fi, 0, 'video', 'bitrate') / 1000, 1);
				$v_bitrate_mode = $this->mkSafeUTF8($this->getID3infoItem($fi, '???', 'video', 'bitrate_mode'));
				$v_framerate = round($this->getID3infoItem($fi, 0, 'video', 'frame_rate'), 5);
				$v_width = round($this->getID3infoItem($fi, '???', 'video', 'resolution_x'));
				$v_height = round($this->getID3infoItem($fi, '???', 'video', 'resolution_y'));
				$v_par = round($this->getID3infoItem($fi, 1.0, 'video', 'pixel_aspect_ratio'), 7);
				$v_codec = $this->mkSafeUTF8($this->getID3infoItem($fi, '', 'video', 'codec'));

				$g_bitrate = round($this->getID3infoItem($fi, 0, 'bitrate') / 1000, 1);
				$g_playtime_str = $this->mkSafeUTF8($this->getID3infoItem($fi, '???', 'playtime_string'));

				$content .= '
						<dt>Audio</dt><dd>';
				if ($a_fmt === '???' && $a_samplerate == 0 && $a_bitrate == 0 && $a_bitrate_mode === '???' && $a_channels == 0 && empty($a_codec) && $a_streams === '???' && $a_streamcount == 0)
				{
					$content .= '-';
				}
				else
				{
					$content .= $a_fmt . (!empty($a_codec) ? ' (' . $a_codec . ')' : '') .
								(!empty($a_channels) ? ($a_channels === 1 ? ' (mono)' : ($a_channels === 2 ? ' (stereo)' : ' (' . $a_channels . ' channels)')) : '') .
								': ' . $a_samplerate . ' kHz @ ' . $a_bitrate . ' kbps (' . strtoupper($a_bitrate_mode) . ')' .
								($a_streamcount > 1 ? ' (' . $a_streamcount . ' streams)' : '');
				}
				$content .= '</dd>
						<dt>Video</dt><dd>' . $v_fmt . (!empty($v_codec) ? ' (' . $v_codec . ')' : '') .  ': ' . $v_framerate . ' fps @ ' . $v_bitrate . ' kbps (' . strtoupper($v_bitrate_mode) . ')' .
											($v_par != 1.0 ? ', PAR: ' . $v_par : '') .
									'</dd>
						<dt>${width}</dt><dd>' . $v_width . 'px</dd>
						<dt>${height}</dt><dd>' . $v_height . 'px</dd>
						<dt>${length}</dt><dd>' . $g_playtime_str . '</dd>
						<dt>${bitrate}</dt><dd>' . $g_bitrate . 'kbps</dd>
					</dl>';
				$content_dl_term = TRUE;
				break;

			default:
				// fall back to 'no preview available' (if getID3 didn't deliver instead...)
				break;
			}
			break;
		}

		if (!$content_dl_term)
		{
			$content .= '</dl>';
		}

		if (!empty($fi['error']))
		{
			$postdiag_err_HTML .= '<p class="err_info">' . $this->mkSafeUTF8(implode(', ', $fi['error'])) . '</p>';
		}

		$emsgX = NULL;
		if (empty($thumb250))
		{
			if (!$thumbnails_done_or_deferred)
			{
				// check if we have stored a thumbnail for this file anyhow:
				$thumb250 = $this->getThumb($meta, $file, $this->options['thumbBigSize'], $this->options['thumbBigSize'], TRUE);
				if (empty($thumb250))
				{
					if (!empty($fi) && $check_for_embedded_img)
					{
						/*
						 * No thumbnail available yet, so find me one!
						 *
						 * When we find a thumbnail during the 'cleanup' scan, we don't know up front if it's suitable to be used directly,
						 * so we treat it as an alternative 'original' file and generate a 250px/48px thumbnail set from it.
						 *
						 * When the embedded thumbnail is small enough, the thumbnail creation process will be simply a copy action, so relatively
						 * low cost.
						 */
						$embed = $this->extract_ID3info_embedded_image($fi);
						//@file_put_contents(dirname(__FILE__) . '/extract_embedded_img.log', print_r(array('html' => $preview_HTML, 'json' => $json, 'thumb250_e' => $thumb250_e, 'thumb250' => $thumb250, 'embed' => $embed, 'fileinfo' => $fi), true));
						if (is_object($embed))
						{
							$thumbX = $meta->getThumbURL('embed');
							$tfi = pathinfo($thumbX);
							$tfi['extension'] = image_type_to_extension($embed->metadata[2]);
							$thumbX = $tfi['dirname'] . '/' . $tfi['filename'] . '.' . $tfi['extension'];
							$thumbX = $this->normalize($thumbX);
							$thumbX_f = $this->url_path2file_path($thumbX);
							// as we've spent some effort to dig out the embedded thumbnail, and 'knowing' (assuming) that generally
							// embedded thumbnails are not too large, we don't concern ourselves with delaying the thumbnail generation (the
							// source file mapping is not bidirectional, either!) and go straight ahead and produce the 250px thumbnail at least.
							$thumb250   = FALSE;
							$thumb250_e = FALSE;
							$thumb48    = FALSE;
							$thumb48_e  = FALSE;
							$meta->mkCacheDir();
							if (FALSE === file_put_contents($thumbX_f, $embed->imagedata))
							{
								@unlink($thumbX_f);
								$emsgX = 'Cannot save embedded image data to cache.';
								$icon48 = $this->getIcon('is.default-error', FALSE);
								$icon = $this->getIcon('is.default-error', TRUE);
							}
							else
							{
								try
								{
									$thumb250 = $this->getThumb($meta, $thumbX_f, $this->options['thumbBigSize'], $this->options['thumbBigSize'], FALSE);
									if (!empty($thumb250))
									{
										$thumb250_e = $this->rawurlencode_path($thumb250);
									}
									$thumb48 = $this->getThumb($meta, (!empty($thumb250) ? $this->url_path2file_path($thumb250) : $thumbX_f), $this->options['thumbSmallSize'], $this->options['thumbSmallSize'], FALSE);
									if (!empty($thumb48))
									{
										$thumb48_e = $this->rawurlencode_path($thumb48);
									}
								}
								catch (Exception $e)
								{
									$emsgX = $e->getMessage();
									$icon48 = $this->getIconForError($emsgX, $legal_url, FALSE);
									$icon = $this->getIconForError($emsgX, $legal_url, TRUE);
								}
							}
						}
					}
				}
				else
				{
					// !empty($thumb250)
					$thumb250_e = $this->rawurlencode_path($thumb250);
					try
					{
						$thumb48 = $this->getThumb($meta, $this->url_path2file_path($thumb250), $this->options['thumbSmallSize'], $this->options['thumbSmallSize'], FALSE);
						assert(!empty($thumb48));
						$thumb48_e = $this->rawurlencode_path($thumb48);
					}
					catch (Exception $e)
					{
						$emsgX = $e->getMessage();
						$icon48 = $this->getIconForError($emsgX, $legal_url, FALSE);
						$icon = $this->getIconForError($emsgX, $legal_url, TRUE);
						$thumb48 = FALSE;
						$thumb48_e = FALSE;
					}
				}
			}
		}
		else // if (!empty($thumb250))
		{
			if (empty($thumb250_e))
			{
				$thumb250_e = $this->rawurlencode_path($thumb250);
			}
			if (empty($thumb48))
			{
				try
				{
					$thumb48 = $this->getThumb($meta, $this->url_path2file_path($thumb250), $this->options['thumbSmallSize'], $this->options['thumbSmallSize'], FALSE);
					assert(!empty($thumb48));
					$thumb48_e = $this->rawurlencode_path($thumb48);
				}
				catch (Exception $e)
				{
					$emsgX = $e->getMessage();
					$icon48 = $this->getIconForError($emsgX, $legal_url, FALSE);
					$icon = $this->getIconForError($emsgX, $legal_url, TRUE);
					$thumb48 = FALSE;
					$thumb48_e = FALSE;
				}
			}
			if (empty($thumb48_e))
			{
				$thumb48_e = $this->rawurlencode_path($thumb48);
			}
		}

		// also provide X/Y size info with each direct-access thumbnail file:
		if (!empty($thumb250))
		{
			$json['thumb250'] = $thumb250_e;
			$meta->store('thumb250_direct', $thumb250);

			$tnsize = $meta->fetch('thumb250_info');
			if (empty($tnsize))
			{
				$tnsize = getimagesize($this->url_path2file_path($thumb250));
				$meta->store('thumb250_info', $tnsize);
			}
			if (is_array($tnsize))
			{
				$json['thumb250_width'] = $tnsize[0];
				$json['thumb250_height'] = $tnsize[1];

				if (empty($preview_HTML))
				{
					$preview_HTML = '<a href="' . $this->rawurlencode_path($url) . '" data-milkbox="single" title="' . htmlentities($filename, ENT_QUOTES, 'UTF-8') . '">
									   <img src="' . $thumb250_e . '" class="preview" alt="' . (!empty($emsgX) ? $this->mkSafe4HTMLattr($emsgX) : 'preview') . '"
											style="width: ' . $tnsize[0] . 'px; height: ' . $tnsize[1] . 'px;" />
									 </a>';
				}
			}
		}
		if (!empty($thumb48))
		{
			$json['thumb48'] = $thumb48_e;
			$meta->store('thumb48_direct', $thumb48);

			$tnsize = $meta->fetch('thumb48_info');
			if (empty($tnsize))
			{
				$tnsize = getimagesize($this->url_path2file_path($thumb48));
				$meta->store('thumb48_info', $tnsize);
			}
			if (is_array($tnsize))
			{
				$json['thumb48_width'] = $tnsize[0];
				$json['thumb48_height'] = $tnsize[1];
			}
		}
		if ($thumbnails_done_or_deferred && (empty($thumbs250) || empty($thumbs48)))
		{
			$json['thumbs_deferred'] = TRUE;
		}
		else
		{
			$json['thumbs_deferred'] = FALSE;
		}

		if (!empty($icon48))
		{
			$icon48_e = $this->rawurlencode_path($icon48);
			$json['icon48'] = $icon48_e;
		}
		if (!empty($icon))
		{
			$icon_e = $this->rawurlencode_path($icon);
			$json['icon'] = $icon_e;
		}

		$fi4dump = NULL;
		if ($this->options['dump'] && ! empty($fi))
		{
			try
			{
				$fi4dump = $meta->fetch('file_info_dump');
				if (empty($fi4dump))
				{
					$fi4dump = array_merge(array(), $fi); // clone $fi
					$this->clean_ID3info_results($fi4dump);
					$meta->store('file_info_dump', $fi4dump);
				}

				$dump = self::table_var_dump($fi4dump, FALSE);

				$postdiag_dump_HTML .= "\n" . $dump . "\n";
			}
			catch(Exception $e)
			{
				$postdiag_err_HTML .= '<p class="err_info">' . $e->getMessage() . '</p>';
			}
		}

		if ($preview_HTML === NULL)
			$preview_HTML = '<div class="center">${nopreview}</div>';

		if (!empty($preview_HTML))
			$content = '<div class="filemanager-preview-content">' . $preview_HTML . '</div>' . $content;

		// Dump info
		if ( $this->options['dump'] && ! empty($postdiag_dump_HTML) && $metaHTML_mode)
		{
			if (!empty($postdiag_err_HTML))
				$content .= '<div class="filemanager-errors">' . $postdiag_err_HTML . '</div>';
			$content .= '<div class="filemanager-diag-dump">' . $postdiag_dump_HTML . '</div>';
		}

		$json['content'] = self::compressHTML($content);
		$json['metadata'] = ($metaJSON_mode ? $fi4dump : NULL);

		return array_merge((is_array($json_in) ? $json_in : array()), $json);
	}

	/**
	 * Traverse the getID3 info[] array tree and fetch the item pointed at by the variable number of indices specified
	 * as additional parameters to this function.
	 *
	 * Return the default value when the indicated element does not exist in the info[] set; otherwise return the located item.
	 *
	 * The purpose of this method is to act as a safe go-in-between for the fileManager to collect arbitrary getID3 data and
	 * not get a PHP error when some item in there does not exist.
	 */
	public function getID3infoItem($getid3_info_obj, $default_value /* , ... */ )
	{
		$rv = FALSE;
		$argc = func_num_args();

		for ($i = 2; $i < $argc; $i++)
		{
			if (!is_array($getid3_info_obj))
			{
				return $default_value;
			}

			$index = func_get_arg($i);
			if (array_key_exists($index, $getid3_info_obj))
			{
				$getid3_info_obj = $getid3_info_obj[$index];
			}
			else
			{
				return $default_value;
			}
		}
		// WARNING: convert '$' to the HTML entity to prevent the JS/client side from 'seeing' the $ and start ${xyz} template variable replacement erroneously
		return str_replace('$', '&#36;', $getid3_info_obj);
	}



	/**
	 * Extract an embedded image from the getID3 info data.
	 *
	 * Return FALSE when no embedded image was found, otherwise return an array of the metadata and the binary image data itself.
	 */
	protected function extract_ID3info_embedded_image(&$arr)
	{
		if (is_array($arr))
		{
			foreach ($arr as $key => &$value)
			{
				if ($key === 'data' && isset($arr['image_mime']))
				{
					// first make sure this is a valid image
					$imageinfo = array();
					$imagechunkcheck = getid3_lib::GetDataImageSize($value, $imageinfo);
					if (is_array($imagechunkcheck) && isset($imagechunkcheck[2]) && in_array($imagechunkcheck[2], array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG)))
					{
						return new EmbeddedImageContainer($imagechunkcheck, $value);
					}
				}
				else if (is_array($value))
				{
					$rv = $this->extract_ID3info_embedded_image($value);
					if ($rv !== FALSE)
						return $rv;
				}
			}
		}
		return FALSE;
	}

	protected function fold_quicktime_subatoms(&$arr, &$inject, $key_prefix)
	{
		$satms = FALSE;
		if (!empty($arr['subatoms']) && is_array($arr['subatoms']) && !empty($arr['hierarchy']) && !empty($arr['name']) && $arr['hierarchy'] == $arr['name'])
		{
			// fold these up all the way to the root:
			$key_prefix .= '.' . $arr['hierarchy'];

			$satms = $arr['subatoms'];
			unset($arr['subatoms']);
			$inject[$key_prefix] = $satms;
		}

		foreach ($arr as $key => &$value)
		{
			if (is_array($value))
			{
				$this->fold_quicktime_subatoms($value, $inject, $key_prefix);
			}
		}

		if ($satms !== FALSE)
		{
			$this->fold_quicktime_subatoms($inject[$key_prefix], $inject, $key_prefix);
		}
	}

	// assumes an input array with the keys in CamelCase semi-Hungarian Notation. Convert those keys to something humanly grokkable after it is processed by table_var_dump().
	protected function clean_AVI_Hungarian(&$arr)
	{
		$dst = array();
		foreach($arr as $key => &$value)
		{
			$nk = explode('_', preg_replace('/([A-Z])/', '_\\1', $key));
			switch ($nk[0])
			{
			case 'dw':
			case 'n':
			case 'w':
			case 'bi':
				unset($nk[0]);
				break;
			}
			$dst[strtolower(implode('_', $nk))] = $value;
		}
		$arr = $dst;
	}

	// another round of scanning to rewrite the keys to human legibility: as this changes the keys, we'll need to rewrite all entries to keep order intact
	protected function clean_ID3info_keys(&$arr)
	{
		$dst = array();
		foreach($arr as $key => &$value)
		{
			$key = strtr($key, "_\x00", '  ');

			// custom transformations: (hopefully switch/case/case/... is faster than str_replace/strtr here)
			switch ((string)$key)
			{
			default:
				//$key = $this->mkSafeUTF8($key);
				if (preg_match('/[^ -~]/', $key))
				{
					// non-ASCII values in the key: hexdump those characters!
					$klen = strlen($key);
					$nk = '';
					for ($i = 0; $i < $klen; ++$i)
					{
						$c = ord($key[$i]);

						if ($c >= 32 && $c <= 127)
						{
							$nk .= chr($c);
						}
						else
						{
							$nk .= sprintf('$%02x', $c);
						}
					}
					$key = $nk;
				}
				break;

			case 'avdataend':
				$key = 'AV data end';
				break;

			case 'avdataoffset':
				$key = 'AV data offset';
				break;

			case 'channelmode':
				$key = 'channel mode';
				break;

			case 'dataformat':
				$key = 'data format';
				break;

			case 'fileformat':
				$key = 'file format';
				break;

			case 'modeextension':
				$key = 'mode extension';
				break;
			}

			$dst[$key] = $value;
			if (is_array($value))
			{
				$this->clean_ID3info_keys($dst[$key]);
			}
		}
		$arr = $dst;
	}

	protected function clean_ID3info_results_r(&$arr, $flags)
	{
		if (is_array($arr))
		{
			// heuristic #1: fold all the quickatoms subatoms using their hierarchy and name fields; this is a tree rewrite
			if (array_key_exists('quicktime', $arr) && is_array($arr['quicktime']))
			{
				$inject = array();
				$this->fold_quicktime_subatoms($arr['quicktime'], $inject, 'quicktime');

				// can't use array_splice on associative arrays, so we rewrite $arr now:
				$newarr = array();
				foreach ($arr as $key => &$value)
				{
					$newarr[$key] = $value;
					if ($key === 'quicktime')
					{
						foreach ($inject as $ik => &$iv)
						{
							$newarr[$ik] = $iv;
						}
					}
				}
				$arr = $newarr;
				unset($inject);
				unset($newarr);
			}

			$activity = TRUE;
			while ($activity)
			{
				$activity = FALSE; // assume there's nothing to do anymore. Prove us wrong now...

				// heuristic #2: when the number of items in the array which are themselves arrays is 80%, contract the set
				$todo = array();
				foreach ($arr as $key => &$value)
				{
					if (is_array($value))
					{
						$acnt = 0;
						foreach ($value as $sk => &$sv)
						{
							if (is_array($sv))
							{
								$acnt++;
							}
						}

						// the floor() here helps to fold single-element arrays alongside! :-)
						if (floor(0.5 * count($value)) <= $acnt)
						{
							$todo[] = $key;
						}
					}
				}
				if (count($todo) > 0)
				{
					$inject = array();
					foreach ($arr as $key => &$value)
					{
						if (is_array($value) && in_array($key, $todo))
						{
							unset($todo[$key]);

							foreach ($value as $sk => &$sv)
							{
								$nk = $key . '.' . $sk;

								// pull up single entry subsubarrays at the same time!
								if (is_array($sv) && count($sv) == 1)
								{
									foreach ($sv as $sk2 => &$sv2)
									{
										$nk .= '.' . $sk2;
										$inject[$nk] = $sv2;
									}
								}
								else
								{
									$inject[$nk] = $sv;
								}
							}
						}
						else
						{
							$inject[$key] = $value;
						}
					}
					$arr = $inject;
					$activity = TRUE;
				}
			}

			foreach ($arr as $key => &$value)
			{
				if ($key === 'data' && isset($arr['image_mime']))
				{
					// when this is a valid image, it's already available as a thumbnail, most probably
					$imageinfo = array();
					$imagechunkcheck = getid3_lib::GetDataImageSize($value, $imageinfo);
					if (is_array($imagechunkcheck) && isset($imagechunkcheck[2]) && in_array($imagechunkcheck[2], array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG)))
					{
						if ($flags & MTFM_CLEAN_ID3_STRIP_EMBEDDED_IMAGES)
						{
							$value = new BinaryDataContainer('(embedded image ' . image_type_to_extension($imagechunkcheck[2]) . ' data...)');
						}
						else
						{
							$value = new EmbeddedImageContainer($imagechunkcheck, $value);
						}
					}
					else
					{
						if ($flags & MTFM_CLEAN_ID3_STRIP_EMBEDDED_IMAGES)
						{
							$value = new BinaryDataContainer('(unidentified image data ... ' . (is_string($value) ? 'length = ' . strlen($value) : '') . ' -- ' . print_r($imagechunkcheck) . ')');
						}
						else
						{
							$this->clean_ID3info_results_r($value, $flags);
						}
					}
				}
				else if (isset($arr[$key . '_guid']))
				{
					// convert guid raw binary data to hex:
					$temp = unpack('H*', $value);
					$value = new BinaryDataContainer($temp[1]);
				}
				else if (  $key === 'non_intra_quant'                                                                       // MPEG quantization matrix
						|| $key === 'error_correct_type'
						)
				{
					// convert raw binary data to hex in 32 bit chunks:
					$temp = unpack('H*', $value);
					$temp = str_split($temp[1], 8);
					$value = new BinaryDataContainer(implode(' ', $temp));
				}
				else if ($key === 'data' && is_string($value) && isset($arr['frame_name']) && isset($arr['encoding']) && isset($arr['datalength'])) // MP3 tag chunk
				{
					$str = $this->mkSafeUTF8(trim(strtr(getid3_lib::iconv_fallback($arr['encoding'], 'UTF-8', $value), "\x00", ' ')));
					$temp = unpack('H*', $value);
					$temp = str_split($temp[1], 8);
					$value = new BinaryDataContainer(implode(' ', $temp) . (!empty($str) ? ' (' . $str . ')' : ''));
				}
				else if (  ($key === 'data' && is_string($value) && isset($arr['offset']) && isset($arr['size']))           // AVI offset/size/data items: data = binary
						|| ($key === 'type_specific_data' && is_string($value) /* && isset($arr['type_specific_len']) */ )  // munch WMV/RM 'type specific data': binary   ('type specific len' will occur alongside, but not in WMV)
						)
				{
					// a bit like UNIX strings tool: strip out anything which isn't at least possibly legible
					$str = ' ' . preg_replace('/[^ !#-~]/', ' ', strtr($value, "\x00", ' ')) . ' ';     // convert non-ASCII and double quote " to space
					do
					{
						$repl_count = 0;
						$str = preg_replace(array('/ [^ ] /',
												  '/ [^A-Za-z0-9\\(.]/',
												  '/ [A-Za-z0-9][^A-Za-z0-9\\(. ] /'
												 ), ' ', $str, -1, $repl_count);
					} while ($repl_count > 0);
					$str = trim($str);

					if (strlen($value) <= 256)
					{
						$temp = unpack('H*', $value);
						$temp = str_split($temp[1], 8);
						$temp = implode(' ', $temp);
						$value = new BinaryDataContainer($temp . (!empty($str) > 0 ? ' (' . $str . ')' : ''));
					}
					else
					{
						$value = new BinaryDataContainer('binary data... (length = ' . strlen($value) . ' bytes)');
					}
				}
				else if (is_scalar($value) && preg_match('/^(dw[A-Z]|n[A-Z]|w[A-Z]|bi[A-Z])[a-zA-Z]+$/', $key))
				{
					// AVI sections which use Hungarian notation, at least partially
					$this->clean_AVI_Hungarian($arr);
					// and rescan the transformed key set...
					$this->clean_ID3info_results($arr);
					break; // exit this loop
				}
				else if (is_array($value))
				{
					// heuristic #3: when the value is an array of integers, implode them to a comma-separated list (string) instead:
					$is_all_ints = TRUE;
					for ($sk = count($value) - 1; $sk >= 0; --$sk)
					{
						if (!array_key_exists($sk, $value) || !is_int($value[$sk]))
						{
							$is_all_ints = FALSE;
							break;
						}
					}
					if ($is_all_ints)
					{
						$s = implode(', ', $value);
						$value = $s;
					}
					else
					{
						$this->clean_ID3info_results_r($value, $flags);
					}
				}
				else
				{
					$this->clean_ID3info_results_r($value, $flags);
				}
			}
		}
		else if (is_string($arr))
		{
			// is this a cleaned up item? Yes, then there's a full-ASCII string here, sans newlines, etc.
			$len = strlen($arr);
			$value = rtrim($arr, "\x00");
			$value = strtr($value, "\x00", ' ');    // because preg_match() doesn't 'see' NUL bytes...
			if (preg_match("/[^ -~\n\r\t]/", $value))
			{
				if ($len > 0 && $len < 256)
				{
					// check if we can turn it into something UTF8-LEGAL; when not, we hexdump!
					$im = str_replace('?', '&QMaRK;', $value);
					$dst = $this->mkSafeUTF8($im);
					if (strpos($dst, '?') === FALSE)
					{
						// it's a UTF-8 legal string now!
						$arr = str_replace('&QMaRK;', '?', $dst);
					}
					else
					{
						// convert raw binary data to hex in 32 bit chunks:
						$temp = unpack('H*', $arr);
						$temp = str_split($temp[1], 8);
						$arr = new BinaryDataContainer(implode(' ', $temp));
					}
				}
				else
				{
					$arr = new BinaryDataContainer('(unidentified binary data ... length = ' . strlen($arr) . ')');
				}
			}
			else
			{
				$arr = $value;   // don't store as a 'processed' item (shortcut)
			}
		}
		else if (is_bool($arr) ||
				 is_int($arr) ||
				 is_float($arr) ||
				 is_null($arr))
		{
		}
		else if (is_object($arr) && !isset($arr->id3_procsupport_obj))
		{
			$arr = new BinaryDataContainer('(object) ' . $this->mkSafeUTF8(print_r($arr, TRUE)));
		}
		else if (is_resource($arr))
		{
			$arr = new BinaryDataContainer('(resource) ' . $this->mkSafeUTF8(print_r($arr, TRUE)));
		}
		else
		{
			$arr = new BinaryDataContainer('(unidentified type: ' . gettype($arr) . ') ' . $this->mkSafeUTF8(print_r($arr, TRUE)));
		}
	}

	protected function clean_ID3info_results(&$arr, $flags = 0)
	{
		unset($arr['GETID3_VERSION']);
		unset($arr['filepath']);
		unset($arr['filename']);
		unset($arr['filenamepath']);
		unset($arr['cache_hash']);
		unset($arr['cache_file']);

		$this->clean_ID3info_results_r($arr, $flags);

		// heuristic #4: convert keys to something legible:
		if (is_array($arr))
		{
			$this->clean_ID3info_keys($arr);
		}
	}








	/**
	 * Delete a file or directory, inclusing subdirectories and files.
	 *
	 * Return TRUE on success, FALSE when an error occurred.
	 *
	 * Note that the routine will try to persevere and keep deleting other subdirectories
	 * and files, even when an error occurred for one or more of the subitems: this is
	 * a best effort policy.
	 */
	protected function unlink($legal_url)
	{
		$rv = TRUE;

		// must transform here so alias/etc. expansions inside get_full_path() get a chance:
		$file = $this->get_full_path($legal_url);

		if (is_dir($file))
		{
			$dir = self::enforceTrailingSlash($file);
			$url = self::enforceTrailingSlash($legal_url);
			$coll = $this->scandir($dir, '*', FALSE, 0, ~GLOB_NOHIDDEN);

			if ($coll !== FALSE)
			{
				foreach ($coll['dirs'] as $f)
				{
					if ($f === '.' || $f === '..')
						continue;

					$rv &= $this->unlink($url . $f);
				}
				foreach ($coll['files'] as $f)
				{
					$rv &= $this->unlink($url . $f);
				}
			}
			else
			{
				$rv = FALSE;
			}

			$rv &= @rmdir($file);
		}
		else if (file_exists($file))
		{
			if (is_file($file))
			{
				// Extension not allowed ?
				if ( ! $this->isAllowedExtension($file))
					return FALSE;
			}

			$rv &= @unlink($file);

			unset($meta);
		}
		return $rv;
	}

	/**
	 * glob() wrapper: accepts the same options as Tooling.php::safe_glob()
	 *
	 * However, this method will also ensure the '..' directory entry is only returned,
	 * even while asked for, when the parent directory can be legally traversed by the FileManager.
	 *
	 * Return a dual array (possibly empty) of directories and files, or FALSE on error.
	 *
	 * IMPORTANT: this function GUARANTEES that, when present at all, the double-dot '..'
	 *            entry is the very last entry in the array.
	 *            This guarantee is important as onView() et al depend on it.
	 */
	public function scandir($dir, $filemask, $see_thumbnail_dir, $glob_flags_or, $glob_flags_and)
	{
		// list files, except the thumbnail folder itself or any file in it:
		$dir = self::enforceTrailingSlash($dir);

		$just_below_thumbnail_dir = FALSE;
		if ( ! $see_thumbnail_dir)
		{
			$tnpath = $this->thumbnailCacheDir;
			if ($this->startsWith($dir, $tnpath))
				return FALSE;

			$tnparent = $this->thumbnailCacheParentDir;
			$just_below_thumbnail_dir = ($dir == $tnparent);

			$tndir = basename(substr($this->options['thumbsDir'], 0, -1));
		}

		$at_basedir = ($this->managedBaseDir == $dir);

		$flags = GLOB_NODOTS | GLOB_NOHIDDEN | GLOB_NOSORT;
		$flags &= $glob_flags_and;
		$flags |= $glob_flags_or;
		$coll = safe_glob($dir . $filemask, $flags);

		if ($coll !== FALSE)
		{
			if ($just_below_thumbnail_dir)
			{
				foreach($coll['dirs'] as $k => $dir)
				{
					if ($dir === $tndir)
					{
						unset($coll['dirs'][$k]);
						break;
					}
				}
			}

			if (!$at_basedir)
			{
				$coll['dirs'][] = '..';
			}
		}

		return $coll;
	}



	/**
	 * Check the $extension argument and replace it with a suitable 'safe' extension.
	 *
	public function getSafeExtension($extension, $safe_extension = 'txt', $mandatory_extension = 'txt')
	{
		if (!is_string($extension) || $extension === '')
		{
			return (!empty($mandatory_extension) ? $mandatory_extension : (!empty($safe_extension) ? $safe_extension : $extension));
		}
		$extension = strtolower($extension);
		switch ($extension)
		{
			case 'exe':
			case 'dll':
			case 'com':
			case 'sys':
			case 'bat':
			case 'pl':
			case 'sh':
			case 'php':
			case 'php3':
			case 'php4':
			case 'php5':
			case 'phps':
				return (!empty($safe_extension) ? $safe_extension : $extension);

			default:
				return $extension;
		}
	}
	*/

	/**
	 * Only allow a 'dotted', i.e. UNIX hidden filename when options['safe'] == FALSE
	 */
	public function IsHiddenNameAllowed($file)
	{
		if ($this->options['safe'] && ! empty($file))
		{
			if ($file !== '.' && $file !== '..' && $file[0] === '.')
			{
				return FALSE;
			}
		}
		return TRUE;
	}

	public function IsHiddenPathAllowed($path)
	{
		if ($this->options['safe'] && !empty($path))
		{
			$path = strtr($path, '\\', '/');
			$segs = explode('/', $path);
			foreach($segs as $file)
			{
				if (!$this->IsHiddenNameAllowed($file))
				{
					return FALSE;
				}
			}
		}
		return TRUE;
	}


	/**
	 * Make a cleaned-up, unique filename
	 *
	 * Return the file (dir + name + ext), or a unique, yet non-existing, variant thereof, where the filename
	 * is appended with a '_' and a number, e.g. '_1', when the file itself already exists in the given
	 * directory. The directory part of the returned value equals $dir.
	 *
	 * Return NULL when $file is empty or when the specified directory does not reside within the
	 * directory tree rooted by options['filesDir']
	 *
	 * Note that the given filename will be converted to a legal filename, containing a filesystem-legal
	 * subset of ASCII characters only, before being used and returned by this function.
	 *
	 * @param mixed $fileinfo     either a string containing a filename+ext or an array as produced by pathinfo().
	 * @daram string $dir         path pointing at where the given file may exist.
	 *
	 * @return a filepath consisting of $dir and the cleaned up and possibly sequenced filename and file extension
	 *         as provided by $fileinfo, or NULL on error.
	 */
	public function getUniqueName($fileinfo, $dir)
	{
		$dir = self::enforceTrailingSlash($dir);

		if (is_string($fileinfo))
		{
			$fileinfo = pathinfo($fileinfo);
		}

		if (!is_array($fileinfo))
		{
			return NULL;
		}
		$dotfile = (strlen($fileinfo['filename']) == 0);

		/*
		 * since 'pagetitle()' is used to produce a unique, non-existing filename, we can forget the dirscan
		 * and simply check whether the constructed filename/path exists or not and bump the suffix number
		 * by 1 until it does not, thus quickly producing a unique filename.
		 *
		 * This is faster than using a dirscan to collect a set of existing filenames and feeding them as
		 * an option array to pagetitle(), particularly for large directories.
		 */
		$filename = $this->pagetitle($fileinfo['filename'], NULL, '-_., []()~!@+' /* . '#&' */, '-_,~@+#&');
		if (!$filename && !$dotfile)
			return NULL;

		// also clean up the extension: only allow alphanumerics in there!
		$ext = $this->pagetitle(isset($fileinfo['extension']) ? $fileinfo['extension'] : NULL);
		$ext = (strlen($ext) > 0 ? '.' . $ext : NULL);
		// make sure the generated filename is SAFE:
		$fname = $filename . $ext;
		$file = $dir . $fname;
		if (file_exists($file))
		{
			if ($dotfile)
			{
				$filename = $fname;
				$ext = '';
			}

			/*
			 * make a unique name. Do this by postfixing the filename with '_X' where X is a sequential number.
			 *
			 * Note that when the input name is already such a 'sequenced' name, the sequence number is
			 * extracted from it and sequencing continues from there, hence input 'file_5' would, if it already
			 * existed, thus be bumped up to become 'file_6' and so on, until a filename is found which
			 * does not yet exist in the designated directory.
			 */
			$i = 1;
			if (preg_match('/^(.*)_([1-9][0-9]*)$/', $filename, $matches))
			{
				$i = intval($matches[2]);
				if ('P'.$i !== 'P'.$matches[2] || $i > 100000)
				{
					// very large number: not a sequence number!
					$i = 1;
				}
				else
				{
					$filename = $matches[1];
				}
			}
			do
			{
				$fname = $filename . ($i ? '_' . $i : '') . $ext;
				$file = $dir . $fname;
				$i++;
			} while (file_exists($file));
		}

		// $fname is now guaranteed to NOT exist in the given directory
		return $fname;
	}



	/**
	 * Predict the actual width/height dimensions of the thumbnail, given the original image's dimensions and the given size limits.
	 *
	 * Note: exists as a method in this class, so you can override it when you override getThumb().
	 */
	public function predictThumbDimensions($orig_x, $orig_y, $max_x = NULL, $max_y = NULL, $ratio = TRUE, $resizeWhenSmaller = FALSE)
	{
		return Image::calculate_resize_dimensions($orig_x, $orig_y, $max_x, $max_y, $ratio, $resizeWhenSmaller);
	}


	/**
	 * Returns the URI path to the apropriate icon image for the given file / directory.
	 *
	 * NOTES:
	 *
	 * 1) any $path with an 'extension' of '.directory' is assumed to be a directory.
	 *
	 * 2) This method specifically does NOT check whether the given path exists or not: it just looks at
	 *    the filename extension passed to it, that's all.
	 *
	 * Note #2 is important as this enables this function to also serve as icon fetcher for ZIP content viewer, etc.:
	 * after all, those files do not exist physically on disk themselves!
	 */
	public function getIcon($file, $smallIcon)
	{
		$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

		if (array_key_exists($ext, $this->icon_cache[!$smallIcon]))
		{
			return $this->icon_cache[!$smallIcon][$ext];
		}

		$largeDir = ( ! $smallIcon ? 'large/' : '');
		$url_path = $this->options['assetsDir'] . 'images/icons/' . $largeDir . $ext . '.png';

		$path = (is_file($this->url_path2file_path($url_path)))
			? $url_path
			: $this->options['assetsDir'] . 'images/icons/' . $largeDir . 'default.png';

		$this->icon_cache[!$smallIcon][$ext] = $path;

		return $path;
	}

	/**
	 * Return the path to the thumbnail of the specified image, the thumbnail having its
	 * width and height limited to $width pixels.
	 *
	 * When the thumbnail image does not exist yet, it will created on the fly.
	 *
	 * @param object $meta
	 *                             the cache record instance related to the original image. Is used
	 *                             to access the cache and generate a suitable thumbnail filename.
	 *
	 * @param string $path         filesystem path to the original image. Is used to derive
	 *                             the thumbnail content from.
	 *
	 * @param integer $width       the maximum number of pixels for width of the
	 *                             thumbnail.
	 *
	 * @param integer $height      the maximum number of pixels for height of the
	 *                             thumbnail.
	 */
	public function getThumb($meta, $path, $width, $height, $onlyIfExistsInCache = FALSE)
	{
		$thumbPath = $meta->getThumbPath($width . 'x' . $height);
		if (!is_file($thumbPath))
		{
			if ($onlyIfExistsInCache)
				return FALSE;

			// make sure the cache subdirectory exists where we are going to store the thumbnail:
			$meta->mkCacheDir();

			$img = new Image($path);
			// generally save as lossy / lower-Q jpeg to reduce filesize, unless orig is PNG/GIF, higher quality for smaller thumbnails:
			$img->resize($width, $height)->save($thumbPath, min(98, max(MTFM_THUMBNAIL_JPEG_QUALITY, MTFM_THUMBNAIL_JPEG_QUALITY + 0.15 * (250 - min($width, $height)))), TRUE);

			if (DEVELOPMENT)
			{
				$imginfo = $img->getMetaInfo();
				$meta->store('img_info', $imginfo);

				$meta->store('memory used', number_format(memory_get_peak_usage() / 1E6, 1) . ' MB');
				$meta->store('memory estimated', number_format(@$imginfo['fileinfo']['usage_guestimate'] / 1E6, 1) . ' MB');
				$meta->store('memory suggested', number_format(@$imginfo['fileinfo']['usage_min_advised'] / 1E6, 1) . ' MB');
			}

			unset($img);
		}
		return $meta->getThumbURL($width . 'x' . $height);
	}

	/**
	 * Assistant function which produces the best possible icon image path for the given error/exception message.
	 */
	public function getIconForError($emsg, $original_filename, $small_icon)
	{
		if (empty($emsg))
		{
			// just go and pick the extension-related icon for this one; nothing is wrong today, it seems.
			$thumb_path = (!empty($original_filename) ? $original_filename : 'is.default-missing');
		}
		else
		{
			$thumb_path = 'is.default-error';

			if (strpos($emsg, 'img_will_not_fit') !== FALSE)
			{
				$thumb_path = 'is.oversized_img';
			}
			else if (strpos($emsg, 'nofile') !== FALSE)
			{
				$thumb_path = 'is.default-missing';
			}
			else if (strpos($emsg, 'unsupported_imgfmt') !== FALSE)
			{
				// just go and pick the extension-related icon for this one; nothing seriously wrong here.
				$thumb_path = (!empty($original_filename) ? $original_filename : $thumb_path);
			}
			else if (strpos($emsg, 'image') !== FALSE)
			{
				$thumb_path = 'badly.broken_img';
			}
		}

		$img_filepath = $this->getIcon($thumb_path, $small_icon);

		return $img_filepath;
	}









	/**
	 * Convert the given content to something that is safe to copy straight to HTML
	 */
	public function mkSafe4Display($str)
	{
		// only allow ASCII to pass:
		$str = preg_replace("/[^ -~\t\r\n]/", '?', $str);
		$str = str_replace('%3C', '?', $str);             // in case someone want's to get really fancy: nuke the URLencoded '<'

		// anything that's more complex than a simple <TAG> is nuked:
		$str = preg_replace('/<([\/]?script\s*[\/]?)>/', '?', $str);               // but first make sure '<script>' doesn't make it through the next regex!
		$str = preg_replace('/<([\/]?[a-zA-Z]+\s*[\/]?)>/', "\x04\\1\x05", $str);
		$str = strip_tags($str);
		$str = strtr($str, "\x04", '<');
		$str = strtr($str, "\x05", '>');
		return $str;
	}


	/**
	 * Make data suitable for inclusion in a HTML tag attribute value: strip all tags and encode quotes!
	 */
	public function mkSafe4HTMLattr($str)
	{
		$str = str_replace('%3C', '?', $str);             // in case someone want's to get really fancy: nuke the URLencoded '<'
		$str = strip_tags($str);
		return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
	}

	/**
	 * inspired by http://nl3.php.net/manual/en/function.utf8-encode.php#102382; mix & mash to make sure the result is LEGAL UTF-8
	 *
	 * Introduced after the JSON encoder kept spitting out 'null' instead of a string value for a few choice French JPEGs with very non-UTF EXIF content. :-(
	 */
	public function mkSafeUTF8($str)
	{
		// kill NUL bytes: they don't belong in here!
		$str = strtr($str, "\x00", ' ');

		if (!mb_check_encoding($str, 'UTF-8') || $str !== mb_convert_encoding(mb_convert_encoding($str, 'UTF-32', 'UTF-8'), 'UTF-8', 'UTF-32'))
		{
			$encoding = mb_detect_encoding($str, 'auto, ISO-8859-1', TRUE);
			$im = str_replace('?', '&qmark;', $str);
			if ($encoding !== FALSE)
			{
				$dst = mb_convert_encoding($im, 'UTF-8', $encoding);
			}
			else
			{
				$dst = mb_convert_encoding($im, 'UTF-8');
			}
			//$dst = utf8_encode($im);
			//$dst = getid3_lib::iconv_fallback('ISO-8859-1', 'UTF-8', $im);

			if (!mb_check_encoding($dst, 'UTF-8') || $dst !== mb_convert_encoding(mb_convert_encoding($dst, 'UTF-32', 'UTF-8'), 'UTF-8', 'UTF-32') || strpos($dst, '?') !== FALSE)
			{
				// not UTF8 yet... try them all
				$encs = mb_list_encodings();
				foreach ($encs as $encoding)
				{
					$dst = mb_convert_encoding($im, 'UTF-8', $encoding);
					if (mb_check_encoding($dst, 'UTF-8') && $dst === mb_convert_encoding(mb_convert_encoding($dst, 'UTF-32', 'UTF-8'), 'UTF-8', 'UTF-32') && strpos($dst, '?') === FALSE)
					{
						return str_replace('&qmark;', '?', $dst);
					}
				}

				// when we get here, it's pretty hopeless. Strip ANYTHING that's non-ASCII:
				return preg_replace("/[^ -~\t\r\n]/", '?', $str);
			}

			// UTF8 cannot contain low-ASCII values; at least WE do not allow that!
			if (preg_match("/[^ -\xFF\n\r\t]/", $dst))
			{
				// weird output that's not legible anyhow, so strip ANYTHING that's non-ASCII:
				return preg_replace("/[^ -~\t\r\n]/", '?', $str);
			}
			return str_replace('&qmark;', '?', $dst);
		}
		return $str;
	}



	/**
	 * Safe replacement of dirname(); does not care whether the input has a trailing slash or not.
	 *
	 * Return FALSE when the path is attempting to get the parent of '/'
	 */
	public static function getParentDir($path)
	{
		/*
		 * on Windows, you get:
		 *
		 * dirname("/") = "\"
		 * dirname("y/") = "."
		 * dirname("/x") = "\"
		 *
		 * so we'd rather not use dirname()   :-(
		 */
		if (!is_string($path))
			return FALSE;
		$path = rtrim($path, '/');
		// empty directory or a path with only 1 character in it cannot be a parent+child: that would be 2 at the very least when it's '/a': parent is root '/' then:
		if (strlen($path) <= 1)
			return FALSE;

		$p2 = strrpos($path, '/' /* , -1 */ );  // -1 as extra offset is not good enough? Nope. At least not for my Win32 PHP 5.3.1. Yeah, sounds like a PHP bug to me. So we rtrim() now...
		if ($p2 === FALSE)
		{
			return FALSE; // tampering!
		}
		$prev = substr($path, 0, $p2 + 1);
		return $prev;
	}

	/**
	 * Return the URI absolute path to the script pointed at by the current URI request.
	 * For example, if the request was 'http://site.org/dir1/dir2/script.php', then this method will
	 * return '/dir1/dir2/script.php'.
	 *
	 * By default, this is equivalent to $_SERVER['SCRIPT_NAME'].
	 *
	 * This default can be overridden by specifying the options['URIpath4RequestScript'] when invoking the constructor.
	 */
	public function getURIpath4RequestScript()
	{
		if (!empty($this->options['URIpath4RequestScript']))
		{
			return $this->options['URIpath4RequestScript'];
		}

		// see also: http://php.about.com/od/learnphp/qt/_SERVER_PHP.htm
		$path = strtr($_SERVER['SCRIPT_NAME'], '\\', '/');

		return $path;
	}

	/**
	 * Return the URI absolute path to the directory pointed at by the current URI request.
	 * For example, if the request was 'http://site.org/dir1/dir2/script', then this method will
	 * return '/dir1/dir2/'.
	 *
	 * Note that the path is returned WITH a trailing slash '/'.
	 */
	public function getRequestPath()
	{
		$path = self::getParentDir($this->getURIpath4RequestScript());
		$path = self::enforceTrailingSlash($path);
		return $path;
	}

	/**
	 * Normalize an absolute path by converting all slashes '/' and/or backslashes '\' and any mix thereof in the
	 * specified path to UNIX/MAC/Win compatible single forward slashes '/'.
	 *
	 * Also roll up any ./ and ../ directory elements in there.
	 *
	 * Throw an exception when the operation failed to produce a legal path.
	 */
	public function normalize($path)
	{
		$path = preg_replace('/(\\\|\/)+/', '/', $path);

		/*
		 * fold '../' directory parts to prevent malicious paths such as 'a/../../../../../../../../../etc/'
		 * from succeeding
		 *
		 * to prevent screwups in the folding code, we FIRST clean out the './' directories, to prevent
		 * 'a/./.././.././.././.././.././.././.././.././../etc/' from succeeding:
		 */
		$path = preg_replace('#/(\./)+#', '/', $path);
		// special fix: now strip trailing '/.' section; MUST replace by '/' (trailing) or path won't be accepted as legal when this is the '.' requested for root '/'
		$path = preg_replace('#/\.$#', '/', $path);

		// now temporarily strip off the leading part up to the colon to prevent entries like '../d:/dir' to succeed when the site root is 'c:/', for example:
		$lead = '';
		// the leading part may NOT contain any directory separators, as it's for drive letters only.
		// So we must check in order to prevent malice like /../../../../../../../c:/dir from making it through.
		if (preg_match('#^([A-Za-z]:)?/(.*)$#', $path, $matches))
		{
			$lead = $matches[1];
			$path = '/' . $matches[2];
		}

		while (($pos = strpos($path, '/..')) !== FALSE)
		{
			$prev = substr($path, 0, $pos);
			/*
			 * on Windows, you get:
			 *
			 * dirname("/") = "\"
			 * dirname("y/") = "."
			 * dirname("/x") = "\"
			 *
			 * so we'd rather not use dirname()
			 */
			$p2 = strrpos($prev, '/');
			if ($p2 === FALSE)
			{
				throw new Exception('path_tampering:' . $path);
			}
			$prev = substr($prev, 0, $p2);
			$next = substr($path, $pos + 3);
			if ($next && $next[0] !== '/')
			{
				throw new Exception('path_tampering:' . $path);
			}
			$path = $prev . $next;
		}

		$path = $lead . $path;

		/*
		 * iff there was such a '../../../etc/' attempt, we'll know because there'd be an exception thrown in the loop above.
		 */

		return $path;
	}


	/**
	 * Accept a URI relative or absolute path and transform it to an absolute URI path, i.e. rooted against DocumentRoot.
	 *
	 * Returns a fully normalized URI absolute path.
	 */
	public function rel2abs_url_path($path)
	{
		$path = strtr($path, '\\', '/');
		if ( ! $this->startsWith($path, '/'))
		{
			$based = $this->getRequestPath();
			$path = $based . $path;
		}
		return $this->normalize($path);
	}

	/**
	 * Accept an absolute URI path, i.e. rooted against DocumentRoot, and transform it to a LEGAL URI absolute path, i.e. rooted against options['filesDir'].
	 *
	 * Returns a fully normalized LEGAL URI path.
	 *
	 * Throws an Exception when the given path cannot be converted to a LEGAL URL, i.e. when it resides outside the options['filesDir'] subtree.
	 */
	public function abs2legal_url_path($path)
	{
		$root = $this->options['filesDir'];

		$path = $this->rel2abs_url_path($path);

		// but we MUST make sure the path is still a LEGAL URI, i.e. sitting inside options['filesDir']:
		if (strlen($path) < strlen($root))
			$path = self::enforceTrailingSlash($path);

		if ( ! $this->startsWith($path, $root))
		{
			throw new Exception('path_tampering:' . $path);
		}

		$path = str_replace($root, '/', $path);

		return $path;
	}

	/**
	 *
	 * Returns a fully normalized URI path.
	 */
	public function get_legal_path($path)
	{
		$path = $this->get_legal_url($path);
		$root = $this->options['filesDir'];
		$path = substr($root, 0, -1) . $path;

		return $path;
	}

	/**
	 *
	 * Returns a fully normalized LEGAL URI
	 */
	public function get_legal_url($path)
	{
		$path = strtr($path, '\\', '/');
		if ( ! $this->startsWith($path, '/'))
		{
			$path = '/' . $path;
		}

		$path = $this->normalize($path);

		return $path;
	}

	/**
	 * Return the filesystem absolute path for the relative or absolute URI path.
	 *
	 * Note: as it uses normalize(), any illegal path will throw an Exception
	 *
	 * Returns a fully normalized filesystem absolute path.
	 */
	public function url_path2file_path($url_path)
	{
		$url_path = $this->rel2abs_url_path($url_path);
		$path = $this->options['documentRoot'] . $url_path;

		return $path;
	}

	/**
	 * Return the filesystem absolute path for the relative or absolute LEGAL URI path.
	 *
	 * Note: as it uses normalize(), any illegal path will throw an Exception
	 *
	 * Returns a fully normalized filesystem absolute path.
	 */
	public function get_full_path($url_path)
	{
		$path = $this->get_legal_url($url_path);

		$path = substr($this->managedBaseDir, 0, -1) . $path;

		return $path;
	}

	public static function enforceTrailingSlash($string)
	{
		return (strrpos($string, '/') === strlen($string) - 1 ? $string : $string . '/');
	}





	/**
	 * Produce minimized HTML output; used to cut don't on the content fed
	 * to JSON_encode() and make it more readable in raw debug view.
	 */
	public static function compressHTML($str)
	{
		// brute force: replace tabs by spaces and reduce whitespace series to a single space.
		//$str = preg_replace('/\s+/', ' ', $str);

		return $str;
	}


	protected function modify_json4exception(&$jserr, $emsg, $target_info = NULL)
	{
		if (empty($emsg))
			return;

		// only set up the new json error report array when this is the first exception we got:
		if (empty($jserr['error']))
		{
			// check the error message and see if it is a translation code word (with or without parameters) or just a generic error report string
			$e = explode(':', $emsg, 2);
			if (preg_match('/[^A-Za-z0-9_-]/', $e[0]))
			{
				// generic message. ouch.
				$jserr['error'] = $emsg;
			}
			else
			{
				$extra1 = (!empty($e[1]) ? $this->mkSafe4Display($e[1]) : '');
				$extra2 = (!empty($target_info) ? ' (' . $this->mkSafe4Display($target_info) . ')' : '');
				$jserr['error'] = $emsg = '${backend.' . $e[0] . '}';
				if ($e[0] != 'disabled')
				{
					// only append the extra data when it's NOT the 'disabled on this server' message!
					$jserr['error'] .=  $extra1 . $extra2;
				}
				else
				{
					$jserr['error'] .=  ' (${' . $extra1 . '})';
				}
			}
			$jserr['status'] = 0;
		}
	}


	/**
	 * @param $filename
	 *
	 * @return bool
	 */
	public function isAllowedExtension($filename)
	{
		if ($this->options['safe'])
		{
			$fi = pathinfo($filename);
			if ( ! isset($fi['extension'])) return FALSE;
			if ( ! in_array(strtolower($fi['extension']), $this->options['allowed_extensions']))
				return FALSE;
		}
		return TRUE;
	}


	/*
	public function getAllowedMimeTypes($mime_filter = NULL)
	{
		$mimeTypes = array();

		if (empty($mime_filter)) return NULL;
		$mset = explode(',', $mime_filter);
		for($i = count($mset) - 1; $i >= 0; $i--)
		{
			if (strpos($mset[$i], '/') === FALSE)
				$mset[$i] .= '/';
		}

		$mimes = $this->getMimeTypeDefinitions();

		foreach ($mimes as $k => $mime)
		{
			if ($k === '.')
				continue;

			foreach($mset as $filter)
			{
				if ($this->startsWith($mime, $filter))
					$mimeTypes[] = $mime;
			}
		}

		return $mimeTypes;
	}
	*/

	public function getMimeTypeDefinitions()
	{
		$pref_ext = array();
		$mimes2 = $this->options['mimes'];
		$mimes_result = array();

		if (is_array($mimes2))
		{
			foreach($mimes2 as $values)
			{
				foreach($values as $k => $v)
				{
					if ( ! is_array($v)) $v = array($v);

					$mimes_result[$k] = $v[0];
					$p = NULL;
					if ( ! empty($v[1]))
					{
						$p = trim($v[1]);
					}
					// is this the preferred extension for this mime type? Or is this the first known extension for the given mime type?
					if ($p === '*' || !array_key_exists($v[0], $pref_ext))
					{
						$pref_ext[$v[0]] = $k;
					}
				}
			}
			// stick the mime-to-extension map into an 'illegal' index:
			$mimes_result['.'] = $pref_ext;
		}

		return $mimes_result;
	}

	/*
	public function IsAllowedMimeType($mime_type, $mime_filters)
	{
		if (empty($mime_type))
			return FALSE;
		if (!is_array($mime_filters))
			return TRUE;

		return in_array($mime_type, $mime_filters);
	}
	*/

	/**
	 * Returns (if possible) the mimetype of the given file
	 *
	 * @param string $file		Physical filesystem path of the file for which we wish to know the mime type.
	 *
	 * @return null|string
	 */
	public function getMimeFromExtension($file)
	{
		$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

		$mime = NULL;
		if (MTFM_USE_FINFO_OPEN)
		{
			$ini = error_reporting(0);
			if (function_exists('finfo_open') && $f = finfo_open(FILEINFO_MIME, getenv('MAGIC')))
			{
				$mime = finfo_file($f, $file);
				// some systems also produce the character encoding with the mime type; strip if off:
				$ma = explode(';', $mime);
				$mime = $ma[0];
				finfo_close($f);
			}
			error_reporting($ini);
		}

		if ((empty($mime) || $mime === 'application/octet-stream') && strlen($ext) > 0)
		{
			$ext2mimetype_arr = $this->getMimeTypeDefinitions();

			if (array_key_exists($ext, $ext2mimetype_arr))
				$mime = $ext2mimetype_arr[$ext];
		}

		if (empty($mime))
		{
			$mime = 'application/octet-stream';
		}

		return $mime;
	}

	/**
	 * Return the first known extension for the given mime type.
	 *
	 * Return NULL when no known extension is found.
	public function getExtensionFromMime($mime)
	{
		$ext2mimetype_arr = $this->getMimeTypeDefinitions();
		$mime2ext_arr = $ext2mimetype_arr['.'];

		if (array_key_exists($mime, $mime2ext_arr))
			return $mime2ext_arr[$mime];

		return NULL;
	}
	*/

	/**
	 * Returns (if possible) all info about the given file, mimetype, dimensions, the works
	 *
	 * @param string $file       physical filesystem path to the file we want to know all about
	 *
	 * @param string $legal_url
	 *                           'legal URL path' to the file; used as the key to the corresponding
	 *                           cache storage record: getFileInfo() will cache the
	 *                           extracted info alongside the thumbnails in a cache file with
	 *                           '.nfo' extension.
	 *
	 * @return the info array as produced by getID3::analyze(), as part of a MTFMCacheEntry reference
	 */
	public function getFileInfo($file, $legal_url, $force_recheck = FALSE)
	{
		// when hash exists in cache, return that one:
		$meta = &$this->getid3_cache->pick($legal_url, $this);
		assert($meta != NULL);
		$mime_check = $meta->fetch('mime_type');
		if (empty($mime_check) || $force_recheck)
		{
			// cache entry is not yet filled: we'll have to do the hard work now and store it.
			if (is_dir($file))
			{
				$meta->store('mime_type', 'text/directory', FALSE);
				$meta->store('analysis', NULL, FALSE);
			}
			else
			{
				$this->getid3->analyze($file);

				$rv = $this->getid3->info;
				if (empty($rv['mime_type']))
				{
					// guarantee to produce a mime type, at least!
					$meta->store('mime_type', $this->getMimeFromExtension($file));     // guestimate mimetype when content sniffing didn't work
				}
				else
				{
					$meta->store('mime_type', $rv['mime_type']);
				}
				$meta->store('analysis', $rv);
			}
		}

		return $meta;
	}





	protected /* static */ function getGETparam($name, $default_value = NULL)
	{
		if (is_array($_GET) && !empty($_GET[$name]))
		{
			$rv = $_GET[$name];

			// see if there's any stuff in there which we don't like
			if (!preg_match('/[^A-Za-z0-9\/~!@#$%^&*()_+{}[]\'",.?]/', $rv))
			{
				return $rv;
			}
		}
		return $default_value;
	}

	protected function getPOST($name, $default_value = NULL)
	{
		if (is_array($_POST) && !empty($_POST[$name]))
		{
			$value = $_POST[$name];

			// see if there's any stuff in there which we don't like
			if (!preg_match('/[^A-Za-z0-9\/~!@#$%^&*()_+{}[]\'",.?]/', $value))
				return $value;
		}
		return $default_value;
	}


	protected function getDocumentRoot()
	{
		if ( ! empty($this->options['documentRoot']))
			$document_root = realpath($this->options['documentRoot']);

		if (empty($document_root))
			$document_root = realpath($_SERVER['DOCUMENT_ROOT']);

		$document_root = strtr($document_root, '\\', '/');
		$document_root = rtrim($document_root, '/');

		return  $document_root;
	}

	/**
	 *
	 * Convert to bytes a information scale
	 *
	 * @param	string	Information scale
	 * @return	integer	Size in bytes
	 *
	 */
	protected function convert_size($val)
	{
		$val = trim($val);
		$last = strtolower($val[strlen($val) - 1]);

		switch ($last) {
			case 'g': $val *= 1024;

			case 'm': $val *= 1024;

			case 'k': $val *= 1024;
		}

		return $val;
	}

	protected function rawurlencode_path($path)
	{
		$encoded_url = str_replace('%2F', '/', rawurlencode($path));
		$encoded_url = str_replace('%3A', ':', $encoded_url);

		return $encoded_url;
	}

	protected function endsWith($string, $look)
	{
		return strrpos($string, $look) === strlen($string) - strlen($look);
	}

	protected function startsWith($string, $look)
	{
		return strpos($string, $look) === 0;
	}

	protected function cleanFilename($str, $replace=array(), $delimiter='-')
	{
		setlocale(LC_ALL, 'en_US');

		if( !empty($replace) ) {
			$str = str_replace((array)$replace, ' ', $str);
		}

		$fi = pathinfo($str);
		$filename = $fi['filename'];
		$ext = (! empty($fi['extension'])) ? strtolower($fi['extension']) : '';

		$clean = @iconv('UTF-8', 'ASCII//IGNORE', $filename);
		$clean = preg_replace("/[^a-zA-Z0-9\/_.|+ -]/", '_', $clean);
		$clean = strtolower(trim($clean, '-. '));
		$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);
		$clean = rtrim($clean, '_-. ');
		if ( ! empty($ext))
			$clean .= '.'.$ext;

		return $clean;
	}


	/**
	 * @param $str
	 *
	 * @return bool
	 */
	protected function isBinary($str)
	{
		for($i = 0; $i < strlen($str); $i++)
		{
			$c = ord($str[$i]);
			// do not accept ANY codes below SPACE, except TAB, CR and LF.
			if ($c == 255 || ($c < 32 /* SPACE */ && $c != 9 && $c != 10 && $c != 13)) return TRUE;
		}

		return FALSE;
	}


	/**
	 * @param      $data
	 * @param null $options
	 * @param null $extra_allowed_chars
	 * @param null $trim_chars
	 *
	 * @return string
	 */
	protected function pagetitle($data, $options = NULL, $extra_allowed_chars = NULL, $trim_chars = NULL)
	{
		static $regex;
		if (!$regex){
			$regex = array(
				explode(' ', 'Æ æ Œ œ ß Ü ü Ö ö Ä ä À Á Â Ã Ä Å &#260; &#258; Ç &#262; &#268; &#270; &#272; Ð È É Ê Ë &#280; &#282; &#286; Ì Í Î Ï &#304; &#321; &#317; &#313; Ñ &#323; &#327; Ò Ó Ô Õ Ö Ø &#336; &#340; &#344; Š &#346; &#350; &#356; &#354; Ù Ú Û Ü &#366; &#368; Ý Ž &#377; &#379; à á â ã ä å &#261; &#259; ç &#263; &#269; &#271; &#273; è é ê ë &#281; &#283; &#287; ì í î ï &#305; &#322; &#318; &#314; ñ &#324; &#328; ð ò ó ô õ ö ø &#337; &#341; &#345; &#347; š &#351; &#357; &#355; ù ú û ü &#367; &#369; ý ÿ ž &#378; &#380;'),
				explode(' ', 'Ae ae Oe oe ss Ue ue Oe oe Ae ae A A A A A A A A C C C D D D E E E E E E G I I I I I L L L N N N O O O O O O O R R S S S T T U U U U U U Y Z Z Z a a a a a a a a c c c d d e e e e e e g i i i i i l l l n n n o o o o o o o o r r s s s t t u u u u u u y y z z z'),
			);
		}

		if (empty($data))
		{
			return (string)$data;
		}

		// fixup $extra_allowed_chars to ensure it's suitable as a character sequence for a set in a regex:
		//
		// Note:
		//   caller must ensure a dash '-', when to be treated as a separate character, is at the very end of the string
		if (is_string($extra_allowed_chars))
		{
			$extra_allowed_chars = str_replace(']', '\]', $extra_allowed_chars);
			if (strpos($extra_allowed_chars, '-') === 0)
			{
				$extra_allowed_chars = substr($extra_allowed_chars, 1) . (strpos($extra_allowed_chars, '-') != strlen($extra_allowed_chars) - 1 ? '-' : '');
			}
		}
		else
		{
			$extra_allowed_chars = '';
		}
		// accepts dots and several other characters, but do NOT tolerate dots or underscores at the start or end, i.e. no 'hidden file names' accepted, for example!
		$data = preg_replace('/[^A-Za-z0-9' . $extra_allowed_chars . ']+/', '_', str_replace($regex[0], $regex[1], $data));
		$data = trim($data, '_. ' . $trim_chars);

		return !empty($options) ? $this->checkTitle($data, $options) : $data;
	}

	/**
	 * @param       $data
	 * @param array $options
	 * @param int   $i
	 *
	 * @return string
	 *
	 */
	protected function checkTitle($data, $options = array(), $i = 0)
	{
		if (!is_array($options)) return $data;

		$lwr_data = strtolower($data);

		foreach ($options as $content)
			if ($content && strtolower($content) == $lwr_data . ($i ? '_' . $i : ''))
				return $this->checkTitle($data, $options, ++$i);

		return $data.($i ? '_' . $i : '');
	}


	/**
	 * @param      $variable
	 * @param bool $wrap_in_td
	 * @param bool $show_types
	 * @param int  $level
	 *
	 * @return string
	 */
	public static function table_var_dump(&$variable, $wrap_in_td = FALSE, $show_types = FALSE, $level = 0)
	{
		$returnstring = '';
		if (is_array($variable))
		{
			$returnstring .= ($wrap_in_td ? '' : '');
			$returnstring .= '<ul class="dump_array dump_level_' . sprintf('%02u', $level) . '">';
			foreach ($variable as $key => &$value)
			{
				// Assign an extra class representing the (rounded) width in number of characters 'or more':
				// You can use this as a width approximation in pixels to style (very) wide items. It saves
				// a long run through all the nodes in JS, just to measure the actual width and correct any
				// overlap occurring in there.
				$keylen = strlen($key);
				$threshold = 10;
				$overlarge_key_class = '';
				while ($keylen >= $threshold)
				{
					$overlarge_key_class .= ' overlarger' . sprintf('%04d', $threshold);
					$threshold *= 1.6;
				}

				$returnstring .= '<li><span class="key' . $overlarge_key_class . '">' . $key . '</span>';
				$tstring = '';
				if ($show_types)
				{
					$tstring = '<span class="type">'.gettype($value);
					if (is_array($value))
					{
						$tstring .= '&nbsp;('.count($value).')';
					}
					elseif (is_string($value))
					{
						$tstring .= '&nbsp;('.strlen($value).')';
					}
					$tstring = '</span>';
				}

				switch ((string)$key)
				{
					case 'filesize':
						$returnstring .= '<span class="dump_seconds">' . $tstring . self::fmt_bytecount($value) . ($value >= 1024 ? ' (' . $value . ' bytes)' : '') . '</span></li>';
						continue 2;

					case 'playtime seconds':
						$returnstring .= '<span class="dump_seconds">' . $tstring . number_format($value, 1) . ' s</span></li>';
						continue 2;

					case 'compression ratio':
						$returnstring .= '<span class="dump_compression_ratio">' . $tstring . number_format($value * 100, 1) . '%</span></li>';
						continue 2;

					case 'bitrate':
					case 'bit rate':
					case 'avg bit rate':
					case 'max bit rate':
					case 'max bitrate':
					case 'sample rate':
					case 'sample rate2':
					case 'samples per sec':
					case 'avg bytes per sec':
						$returnstring .= '<span class="dump_rate">' . $tstring . self::fmt_bytecount($value) . '/s</span></li>';
						continue 2;

					case 'bytes per minute':
						$returnstring .= '<span class="dump_rate">' . $tstring . self::fmt_bytecount($value) . '/min</span></li>';
						continue 2;
				}
				$returnstring .= self::table_var_dump($value, TRUE, $show_types, $level + 1) . '</li>';
			}
			$returnstring .= '</ul>';
			$returnstring .= ($wrap_in_td ? '' : '');
		}
		else if (is_bool($variable))
		{
			$returnstring .= ($wrap_in_td ? '<span class="dump_boolean">' : '').($variable ? 'TRUE' : 'FALSE').($wrap_in_td ? '</span>' : '');
		}
		else if (is_int($variable))
		{
			$returnstring .= ($wrap_in_td ? '<span class="dump_integer">' : '').$variable.($wrap_in_td ? '</span>' : '');
		}
		else if (is_float($variable))
		{
			$returnstring .= ($wrap_in_td ? '<span class="dump_double">' : '').$variable.($wrap_in_td ? '</span>' : '');
		}
		else if (is_object($variable) && isset($variable->id3_procsupport_obj))
		{
			if (isset($variable->metadata) && isset($variable->imagedata))
			{
				// an embedded image (MP3 et al)
				$returnstring .= ($wrap_in_td ? '<div class="dump_embedded_image">' : '');
				$returnstring .= '<table class="dump_image">';
				$returnstring .= '<tr><td><b>type</b></td><td>'.getid3_lib::ImageTypesLookup($variable->metadata[2]).'</td></tr>';
				$returnstring .= '<tr><td><b>width</b></td><td>'.number_format($variable->metadata[0]).' px</td></tr>';
				$returnstring .= '<tr><td><b>height</b></td><td>'.number_format($variable->metadata[1]).' px</td></tr>';
				$returnstring .= '<tr><td><b>size</b></td><td>'.number_format(strlen($variable->imagedata)).' bytes</td></tr></table>';
				$returnstring .= '<img src="data:'.$variable->metadata['mime'].';base64,'.base64_encode($variable->imagedata).'" width="'.$variable->metadata[0].'" height="'.$variable->metadata[1].'">';
				$returnstring .= ($wrap_in_td ? '</div>' : '');
			}
			else if (isset($variable->binarydata_mode))
			{
				$returnstring .= ($wrap_in_td ? '<span class="dump_binary_data">' : '');
				if ($variable->binarydata_mode == 'procd')
				{
					$returnstring .= '<i>' . self::table_var_dump($variable->binarydata, FALSE, FALSE, $level + 1) . '</i>';
				}
				else
				{
					$temp = unpack('H*', $variable->binarydata);
					$temp = str_split($temp[1], 8);
					$returnstring .= '<i>' . self::table_var_dump(implode(' ', $temp), FALSE, FALSE, $level + 1) . '</i>';
				}
				$returnstring .= ($wrap_in_td ? '</span>' : '');
			}
			else
			{
				$returnstring .= ($wrap_in_td ? '<span class="dump_object">' : '').print_r($variable, TRUE).($wrap_in_td ? '</span>' : '');
			}
		}
		else if (is_object($variable))
		{
			$returnstring .= ($wrap_in_td ? '<span class="dump_object">' : '').print_r($variable, TRUE).($wrap_in_td ? '</span>' : '');
		}
		else if (is_null($variable))
		{
			$returnstring .= ($wrap_in_td ? '<span class="dump_null">' : '').'(null)'.($wrap_in_td ? '</span>' : '');
		}
		else if (is_string($variable))
		{
			$variable = strtr($variable, "\x00", ' ');
			$varlen = strlen($variable);
			for ($i = 0; $i < $varlen; $i++)
			{
				$returnstring .= htmlentities($variable{$i}, ENT_QUOTES, 'UTF-8');
			}
			$returnstring = ($wrap_in_td ? '<span class="dump_string">' : '').nl2br($returnstring).($wrap_in_td ? '</span>' : '');
		}
		else
		{
			$returnstring .= ($wrap_in_td ? '<span class="dump_other">' : '').nl2br(htmlspecialchars(strtr($variable, "\x00", ' '))).($wrap_in_td ? '</span>' : '');
		}
		return $returnstring;
	}
}


/**
 *
 * Support class for the getID3 info and embedded image extraction
 *
 */
class EmbeddedImageContainer
{
	public $metadata;
	public $imagedata;
	public $id3_procsupport_obj;

	public function __construct($meta, $img)
	{
		$this->metadata = $meta;
		$this->imagedata = $img;
		$this->id3_procsupport_obj = TRUE;
	}

	public static function __set_state($arr)
	{
		$obj = new EmbeddedImageContainer($arr['metadata'], $arr['imagedata']);
		return $obj;
	}
}


/**
 *
 */
class BinaryDataContainer
{
	public $binarydata;
	public $binarydata_mode;
	public $id3_procsupport_obj;

	public function __construct($data, $mode = 'procd')
	{
		$this->binarydata_mode = $mode;
		$this->binarydata = $data;
		$this->id3_procsupport_obj = TRUE;
	}

	public static function __set_state($arr)
	{
		$obj = new BinaryDataContainer($arr['binarydata'], $arr['binarydata_mode']);
		return $obj;
	}
}

