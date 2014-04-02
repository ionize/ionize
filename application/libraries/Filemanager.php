<?php
require(strtr(dirname(__FILE__), '\\', '/') . '/Filemanager/Tooling.php');
require(strtr(dirname(__FILE__), '\\', '/') . '/Filemanager/Image.class.php');
require(strtr(dirname(__FILE__), '\\', '/') . '/getid3/getid3.php');

//-------------------------------------------------------------------------------------------------------------

// Execution Environment. Can be 'development', 'testing', 'production'
// If not defined by your framework, define it here
// if (!defined('ENVIRONMENT')) define ('ENVIRONMENT', 'development');



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

	/**
	 * Number of directory levels in the thumbnail cache; set to 2 when you expect to handle huge image collections.
	 * Note that each directory level distributes the files evenly across 256 directories; hence, you may set this
	 * level count to 2 when you expect to handle more than 32K images in total -- as each image will have two thumbnails:
	 * a 48px small one and a 250px large one.
	 * when you expect to manage a really HUGE file collection from FM, you may dial up the
	 * $number_of_dirlevels_for_cache define to 2.
	 *
	 * @var int
	 */
	public static $number_of_dirlevels_for_cache = 2;

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
		for ($i = 0; $i < self::$number_of_dirlevels_for_cache; $i++)
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

			if ( ! is_array($this->fstat) && file_exists($this->file))
			{
				$this->fstat = @stat($this->file);
			}
			if (file_exists($this->cache_file))
			{
				include($this->cache_file);  // unserialize();

				if (   isset($statdata)
					&& isset($data)
					&& is_array($data)
					&& is_array($this->fstat) && is_array($statdata)
					&& $statdata[10] == $this->fstat[10]	// ctime
					&& $statdata[9]  == $this->fstat[9]		// mtime
					&& $statdata[7]  == $this->fstat[7]		// size
				   )
				{
					if (ENVIRONMENT != 'development')
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
			for ($i = 0; $i < self::$number_of_dirlevels_for_cache; $i++)
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
	protected $relativeBaseUrl;

	public static $thumb_jpeg_quality = 90;

	/**
	 * Allows to use finfo_open() to help to produce mime types for files.
	 * This is slower than the basic file extension to mimetype mapping
	 * @var bool
	 */
	public static $use_finfo_open = FALSE;

	/**
	 * Minimum of cached getID3 results.
	 * Cache is automatically pruned
	 * @var int
	 */
	public static $min_getid3_cachesize = 16;


	/**
	 * @param $options
	 */
	public function __construct($options)
	{
		$this->options = array_merge(array
		(
			'filesDir' => NULL,
			'assetsDir' => NULL,
			'thumbsDir' => NULL,				// with trailing slash
			'baseUrl' => self::getHostUrl(),

			'thumbSmallSize' => 48,
			'thumbBigSize' => 250,

			// Path to the Mimes pure PHP file.
			// Mimes must be set in one $mimes array.
			'mimesPath' => strtr(dirname(__FILE__), '\\', '/') . '/Filemanager/mimes.php',												// Mimes pure PHP array.
			'documentRoot' => $_SERVER['DOCUMENT_ROOT'],
			'dateFormat' => 'j M Y - H:i',
			'maxUploadSize' => 2600 * 2600 * 3,

			// Extract additional data by using ID3 ?
			'useGetID3' => FALSE,

			// Allow to specify the "Resize Large Images" tolerance level.
			'maxImageDimension' => array('width' => 1024, 'height' => 768),
			'upload' => FALSE,
			'destroy' => FALSE,
			'create' => FALSE,
			'move' => FALSE,
			'download' => FALSE,

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
			'cleanFileNameDelimiter' => '_',
			'hashMethod' => 'sha256',

			'DetailIsAuthorized_cb' => NULL,
			'UploadIsAuthorized_cb' => NULL,
			'DownloadIsAuthorized_cb' => NULL,
			'CreateIsAuthorized_cb' => NULL,
			'MoveIsAuthorized_cb' => NULL,
			'DestroyIsAuthorized_cb' => NULL,

			'showHiddenFoldersAndFiles' => FALSE      // Hide dot dirs/files ?
		), (is_array($options) ? $options : array()));

		$this->managedBaseDir = $this->url_path2file_path($this->options['filesDir']);

		// Base URL, relative, set as option to be available by MTFMCacheItem
		$this->options['relativeBaseUrl'] = $this->relativeBaseUrl = $this->getRelativeBaseUrl();

		// Precalculates Cache dirs
		$this->thumbnailCacheDir = $this->url_path2file_path($this->options['thumbsDir']);
		$this->thumbnailCacheParentDir = $this->url_path2file_path(self::getParentDir($this->options['thumbsDir']));

		// Mimes
		$mimes = NULL;
		if (is_file($this->options['mimesPath']))
			include($this->options['mimesPath']);
		$this->options['mimes'] = $mimes;
		unset($mimes);

		// getID3 is slower as it *copies* the image to the temp dir before processing: see GetDataImageSize().
		// This is done as getID3 can also analyze *embedded* images, for which this approach is required.
		$this->getid3 = new getID3();
		$this->getid3->setOption(array('encoding' => 'UTF-8'));

		$this->getid3_cache = new MTFMCache(self::$min_getid3_cachesize);

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
			// 'root' => substr($this->options['filesDir'], 1),
			'root' => $this->options['filesDir'],
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
		$json = array(
				'status' => 1
			);

		$mime_filter = $this->getPOST('filter', $this->options['filter']);
		$legal_url = NULL;

		try
		{
			$dir_arg = $this->getPOST('directory');
			$legal_url = $this->get_legal_url($dir_arg . '/');
			$file_preselect_arg = $this->getPOST('file_preselect');

			/*
			 * Partikule, 2013.01.28
			 * TODO : Rewrite this part :
			 * - Remove abs2legal_url_pat* call
			 *
			 */
			try
			{
				/*
				if ( ! empty($file_preselect_arg))
				{
					// check if this a path instead of just a basename, then convert to legal_url and split across filename and directory.
					if (strpos($file_preselect_arg, '/') !== FALSE)
					{
						// this will also convert a relative path to an absolute path before transforming it to a LEGAL URI path:
						// $legal_presel = $this->abs2legal_url_path($file_preselect_arg);

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
				*/
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
				$rv = $this->_onView($legal_url, $json, $mime_filter, $file_preselect_arg);

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

			$json['status']++;

		} while ($legal_url !== FALSE);

		$this->modify_json4exception($json, $emsg, 'path = ' . $original_legal_url);
		$this->sendHttpHeaders('Content-Type: application/json');
		echo json_encode($json);
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
		$json = array('status' => 1);

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
				$legal_url = $this->get_legal_url($legal_url . $filename);
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
				'preliminary_json' => $json,
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
			$json = $this->extractDetailInfo($json, $legal_url, $meta, $mode);

			$this->sendHttpHeaders('Content-Type: application/json');

			echo json_encode($json);
			return;
		}
		catch(Exception $e)
		{
			$emsg = $e->getMessage();
		}

		$this->modify_json4exception($json, $emsg, 'file = ' . $file_arg . ', path = ' . $legal_url);
		echo json_encode($json);
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
		$json = array('status' => 1);

		try
		{
			if (!$this->options['destroy'])
				throw new Exception('disabled:destroy');

			$file_arg = $this->getPOST('file');
			$dir_arg = $this->getPOST('directory');

			$legal_url = $this->get_legal_url($dir_arg . '/');

			$is_dir = is_dir($legal_url);

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

			// Destroy Authorization callback
			if (
				! empty($this->options['DestroyIsAuthorized_cb'])
				&& is_callable($this->options['DestroyIsAuthorized_cb'])
				&& FALSE == call_user_func_array($this->options['DestroyIsAuthorized_cb'],array($this, 'destroy', $file))
			)
			{
				throw new Exception('validation_failure');
			}


			if ( ! $this->unlink($legal_url))
			{
				throw new Exception('unlink_failed:' . $legal_url);
			}

			// Event
			Event::fire(
				'Filemanager.destroy.success',
				array(
					'path' => $this->get_full_path($legal_url),
					'is_dir'=>$is_dir
				)
			);


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

		$json['status'] = 0;
		$this->modify_json4exception($json, $emsg, 'file = ' . $file_arg . ', path = ' . $legal_url);
		$this->sendHttpHeaders('Content-Type: application/json');
		echo json_encode($json);
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
		$json = array('status' => 1);

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
				$filename = $this->cleanFilename($filename, '_', TRUE);

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
			$rv = $this->_onView($legal_url . $file . '/', $json, $mime_filter);

			echo json_encode($rv);
			return;
		}
		catch(Exception $e)
		{
			// catching other severe failures; since this can be anything and should only happen in the direst of circumstances, we don't bother translating
			$emsg = $e->getMessage();
			$json['status'] = 0;

			// and fall back to showing the PARENT directory
			try
			{
				$json = $this->_onView($legal_url, $json, $mime_filter);
			}
			catch (Exception $e)
			{
				// and fall back to showing the BASEDIR directory
				try
				{
					$legal_url = $this->options['filesDir'];
					$json = $this->_onView($legal_url, $json, $mime_filter);
				}
				// when we fail here, it's pretty darn bad and nothing to it.
				catch (Exception $e){}
			}
		}

		$this->modify_json4exception($json, $emsg, 'directory = ' . $file_arg . ', path = ' . $legal_url);
		$this->sendHttpHeaders('Content-Type: application/json');
		echo json_encode($json);
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
		$json = array('status' => 1);

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
		header('x', TRUE, 403);

		$this->modify_json4exception($json, $emsg, 'file = ' . $this->mkSafe4Display($file_arg . ', destination path = ' . $file));

		// Safer for iframes: the 'application/json' mime type would cause FF3.X to pop up a save/view dialog when transmitting these error reports!
		$this->sendHttpHeaders('Content-Type: text/plain');
		echo json_encode($json);
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

			if ($this->is_blob_upload())
			{
				$response = $this->blob_mode_upload();
				$this->sendHttpHeaders(array('Content-type: application/json'));
			}
			else
			{
				$response = $this->file_mode_upload();
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


	/**
	 * Upload one file in blob mode
	 *
	 * @return array
	 */
	public function blob_mode_upload()
	{
		$max_upload = self::convert_size(ini_get('upload_max_filesize'));
		$max_post = self::convert_size(ini_get('post_max_size'));
		$memory_limit = self::convert_size(ini_get('memory_limit'));
		$limit = min($max_upload, $max_post, $memory_limit);

		$headers = $this->getHttpHeaders();
		$directory = ! empty($headers['X-Directory']) ? $headers['X-Directory'] : '';

		$resize = (bool) ! empty($headers['X-Resize']) ? $headers['X-Resize'] : FALSE;
		$resume_flag = ! empty($headers['X-File-Resume']) ? FILE_APPEND : 0;
		$replace = (! empty($headers['X-Replace']) && $headers['X-Replace'] == 1 ) ? TRUE : FALSE;

		// Prepare the response
		$response = array(
			'method'	=> 'html5',
			'id'    	=> ! empty($headers['X-File-Id']) ? $headers['X-File-Id'] : NULL,
			'name'  	=> basename($headers['X-File-Name']),
			'directory' => $directory,
			'files_dir' => $this->options['filesDir'],
			'size'  	=> ! empty($headers['Content-Length']) ? $headers['Content-Length'] : '0',
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
			{
				throw new Exception('${backend.extension}');
			}

			// Full dir path
			$legal_dir_url = $this->get_legal_url($directory . '/');
			$dir = $this->get_full_path($legal_dir_url);

			// No resume, no replace : Get one unique filename
			if ( ! $resume_flag && ! $replace)
			{
				$filename = $this->getUniqueName($response['name'], $dir);
			}

			// Authorization callback
			$fileinfo = array(
				'legal_dir_url' => $legal_dir_url,
				'dir' => $dir,
				'filename' => $filename,
				'size' => $response['size'],
				'maxsize' => $limit,
				'overwrite' => FALSE,
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
			{
				$filename = $this->cleanFilename($filename);
			}

			// clean is finished, set the filename for DropZone
			$response['name'] = $filename;

			// Creates directory if it doesn't exists
			if ( ! is_dir($dir))
			{
				if ( ! @mkdir($dir, $this->options['chmod'], TRUE))
					throw new Exception('mkdir_failed:' . $dir);

				@chmod($dir, $this->options['chmod']);
			}

			// full file path
			$file_path = $dir . $filename;

			if (file_put_contents($file_path, file_get_contents('php://input'), $resume_flag) === FALSE)
			{
				throw new Exception('${backend.path_not_writable}');
			}
			else
			{
				// Upload finished ?
				if (filesize($file_path) >= $headers['X-File-Size'])
				{
					// Relative complete path, including "files" directory
					$path = $this->get_legal_path($legal_dir_url) . $filename;

					$response['finish'] = TRUE;
					$response['original_name'] = $response['name'];
					$response['name'] = $filename;
					$response['path'] = $path;
					// $response['path_hash'] = $this->getHash($path);

					// Event
					Event::fire('Filemanager.upload.success', $response);

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

			// Event
			Event::fire('Filemanager.upload.error', $response);
		}

		return $response;

	}

	/**
	 * File Mode upload
	 *
	 * @return array
	 */
	public function file_mode_upload()
	{
		$headers = $this->getHttpHeaders();

		$file_input_prefix = isset($_POST['file_input_prefix']) ? $_POST['file_input_prefix'] : $headers['X-File-Input-Prefix'];
		$directory = isset($_POST['directory']) ? ( ! empty($_POST['directory']) ? $_POST['directory'] : '') : ( ! empty($headers['X-Directory']) ? $headers['X-Directory'] : '');
		$resize = (bool) isset($_POST['resize']) ? ! empty($_POST['resize']) : ! empty($headers['X-Resize']);
		$replace = (bool) isset($_POST['replace']) ? ! empty($_POST['replace']) : ! empty($headers['X-Replace']);

		// Upload file using traditional method
		$response = array();

		try
		{
			foreach ($_FILES as $k => $file)
			{
				$response = array(
					'method' => 		'html4',
					'key' => 			(int)substr($k, strpos($k, $file_input_prefix) + strlen($file_input_prefix)),
					'name' => 			basename($file['name']),
					'directory' => 		$directory,
					'files_dir' => 		$this->options['filesDir'],
					'size' => 			$file['size'],
					'error' => 			$file['error'],
					'finish' => 		FALSE,
				);

				if ($response['error'] == 0)
				{
					// Full dir path
					$legal_dir_url = $this->get_legal_url($directory . '/');
					$dir = $this->get_full_path($legal_dir_url);

					$filename = $response['name'];

					// Replace the file ?
					if ( ! $replace)
						$filename = $this->getUniqueName($response['name'], $dir);

                    // Creates safe file names
                    if ($this->options['cleanFileName'])
                        $filename = $this->cleanFilename($file['name'], '_');

					// Allowed extension ?
					if ( ! $this->isAllowedExtension($filename))
						throw new Exception('${backend.extension}');

					// Creates directory if it doesn't exists
					if ( ! is_dir($dir))
					{
						if ( ! @mkdir($dir, $this->options['chmod'], TRUE))
							throw new Exception('mkdir_failed:' . $dir);

						@chmod($dir, $this->options['chmod']);
					}

					$file_path = $dir . $filename;

					if (move_uploaded_file($file['tmp_name'], $file_path) === FALSE)
					{
						throw new Exception('${backend.path_not_writable}');
					}
					else
					{
						// Relative complete path, including "files" directory
						$path = $this->get_legal_path($legal_dir_url) . $filename;

						$response['finish'] = TRUE;
						$response['original_name'] = $response['name'];
						$response['name'] = $filename;
						$response['path'] = $path;
						// $response['path_hash'] = $this->getHash($path);

						// Event
						Event::fire('Filemanager.upload.success', $response);

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
					$response['error'] = '1';
					$response['finish'] = TRUE;

					// Event
					Event::fire('Filemanager.upload.error', $response);
				}
			}
		}
		catch(Exception $e)
		{
			$response['error'] = $e->getMessage();
			$response['finish'] = TRUE;

			// Event
			Event::fire('Filemanager.upload.error', $response);
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
	 * Detect if the upload request
	 * should be one blob or not
	 *
	 * @return	boolean
	 *
	 */
	public function is_blob_upload()
	{
		return (empty($_FILES) && empty($_POST));
	}

	public function getHttpHeaders()
	{
        $headers = array();

        foreach ($_SERVER as $name => $value)
        {
            if (substr($name, 0, 5) == 'HTTP_')
            {
                $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                $headers[$name] = $value;
            } else if ($name == "CONTENT_TYPE") {
                $headers["Content-Type"] = $value;
            } else if ($name == "CONTENT_LENGTH") {
                $headers["Content-Length"] = $value;
            }
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
		$json = array('status' => 1);

		try
		{
			if ( ! $this->options['move'])
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
				'preliminary_json' => $json,
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

			// Event
			Event::fire(
				'Filemanager.move.success',
				array(
					'old_path' => $path,
					'new_path' =>$newpath,
					'is_dir' => $is_dir
				)
			);

			// Json response
			$this->sendHttpHeaders('Content-Type: application/json');
			$json['name'] = $newname;
			echo json_encode($json);
			return;
		}
		catch(Exception $e)
		{
			$emsg = $e->getMessage();
		}

		$this->modify_json4exception($json, $emsg, 'file = ' . $file_arg . ', path = ' . $legal_url . ', destination path = ' . $newpath);
		$this->sendHttpHeaders('Content-Type: application/json');
		echo json_encode($json);
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
	 *
	 */
	public function extractDetailInfo($json_in, $legal_url, &$meta, $mode)
	{
		$auto_thumb_gen_mode = ! in_array('direct', $mode, TRUE);

		$url = $this->get_legal_path($legal_url);
		$filename = basename($url);

		// must transform here so alias/etc. expansions inside url_path2file_path() get a chance:
		$file = $this->url_path2file_path($url);

		$mime = NULL;

		// only perform the (costly) getID3 scan when it hasn't been done before,
		// i.e. can we re-use previously obtained data or not?
		if ( ! is_object($meta))
		{
			$meta = $this->getFileInfo($file, $legal_url);
		}
		// File
		if ( is_file($file))
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
			$iconspec = 'is.directory';
		}
		else
		{
			throw new Exception('nofile');
		}

		// it's an internal error when this entry do not exist in the cache store by now!
		$fi = $meta->fetch('analysis');

		$icon48 = $this->getIcon($iconspec, FALSE);
		$icon = $this->getIcon($iconspec, TRUE);

		$thumb250 = $meta->fetch('thumb250_direct');
		$thumb48 = $meta->fetch('thumb48_direct');

		$tstamp_str = date($this->options['dateFormat'], @filemtime($file));
		$fsize = @filesize($file);

		$json = array_merge(array(
			'content' => '<div class="margin">${nopreview}</div>'
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
		$postdiag_HTML = '';
		$thumbnails_done_or_deferred = FALSE;   // TRUE: mark our thumbnail work as 'done'; any NULL thumbnails represent deferred generation entries!
		$check_for_embedded_img = FALSE;

		$mime_els = explode('/', $mime);
		for(;;) // bogus loop; only meant to assist the [mime remapping] state machine in here
		{
			switch ($mime_els[0])
			{
				case 'image':
					$emsg = NULL;
					try
					{
						if (empty($thumb250))
							$thumb250 = $this->getThumb($meta, $file, $this->options['thumbBigSize'], $this->options['thumbBigSize'], $auto_thumb_gen_mode);

						if (empty($thumb48))
							$thumb48 = $this->getThumb($meta, (!empty($thumb250) ? $this->url_path2file_path($thumb250) : $file), $this->options['thumbSmallSize'], $this->options['thumbSmallSize'], $auto_thumb_gen_mode);

						if (empty($thumb48) || empty($thumb250))
						{
							// Partikule
							// TODO : See what to do with that
							$imginfo = Image::checkFileForProcessing($file);
						}
						$thumbnails_done_or_deferred = TRUE;
					}
					catch (Exception $e)
					{
						$emsg = $e->getMessage();
						$icon48 = $this->getIconForError($emsg, $legal_url, FALSE);
						$icon = $this->getIconForError($emsg, $legal_url, TRUE);
						// even cache the fail: that means next time around we don't suffer the failure
						// but immediately serve the error icon instead.
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

					// If thumb generation is delayed, we need to infer the thumbnail dimensions *anyway*!
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
							$json['thumb250_width'] = $this->options['thumbBigSize'];
							$json['thumb250_height'] = $this->options['thumbBigSize'];
						}
						else
						{
							// Failure before : we only will have the icons. Use them.
							$preview_HTML = '<a href="' . $this->getElementURL($url) . '" title="' . htmlentities($filename, ENT_QUOTES, 'UTF-8') . '">
												<img src="' . $this->getElementURL($icon48) . '" class="preview" alt="preview" />
											</a>';
						}
					}
					// else: defer the $preview_HTML production until we're at the end of this and have fetched the actual thumbnail dimensions

					if ( ! empty($emsg))
					{
						// use the abilities of modify_json4exception() to munge/format the exception message:
						$jsa = array('error' => '');
						$this->modify_json4exception($jsa, $emsg, 'path = ' . $url);

						if (strpos($emsg, 'img_will_not_fit') !== FALSE)
						{
							$earr = explode(':', $emsg, 2);
							$postdiag_HTML .= '<p class="tech_info">Estimated minimum memory requirements to create thumbnails for this image: ' . $earr[1] . '</p>';
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
								$preview_HTML =
									'<div class="txt">'
										. str_replace(array('$', "\t"), array('&#36;', '&nbsp;&nbsp;'), htmlentities($filecontent, ENT_NOQUOTES, 'UTF-8'))
									. '</div>'
								;
							}
							else
							{
								$mime_els[0] = 'unknown';
								continue 3;
							}
							break;
					}
					break;

				case 'application':
					switch ($mime_els[1])
					{
						case 'x-javascript':
							$mime_els[0] = 'text';
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
									$out[$isdir ? 0 : 1][$name] = '<li><a><img src="' . $this->getElementURL($this->getIcon($name, TRUE)) . '" alt="" /> ' . $name . '</a></li>';
								}
								natcasesort($out[0]);
								natcasesort($out[1]);
								$preview_HTML = '<ul>' . implode(array_merge($out[0], $out[1])) . '</ul>';
							}
							break;

						/*
						 * If fact, do we really care about swf ?
						 *
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
						*/

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

		if ( ! $content_dl_term)
		{
			$content .= '</dl>';
		}

		if ( ! empty($fi['error']))
		{
			$postdiag_HTML .= '<p class="tech_info">' . $this->mkSafeUTF8(implode(', ', $fi['error'])) . '</p>';
		}

		$emsgX = NULL;

		if (empty($thumb250))
		{
			if (!$thumbnails_done_or_deferred)
			{
				// check if we have stored a thumbnail for this file anyhow:
				$thumb250 = $this->getThumb($meta, $file, $this->options['thumbBigSize'], $this->options['thumbBigSize'], TRUE);

				if ( ! empty($thumb250))
				{
					try
					{
						$thumb48 = $this->getThumb($meta, $this->url_path2file_path($thumb250), $this->options['thumbSmallSize'], $this->options['thumbSmallSize'], FALSE);
						assert( ! empty($thumb48));
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
		// $thumb250 is not empty
		else
		{
			if (empty($thumb48))
			{
				try
				{
					$thumb48 = $this->getThumb($meta, $this->url_path2file_path($thumb250), $this->options['thumbSmallSize'], $this->options['thumbSmallSize'], FALSE);
					assert(!empty($thumb48));
				}
				catch (Exception $e)
				{
					$emsgX = $e->getMessage();
					$icon48 = $this->getIconForError($emsgX, $legal_url, FALSE);
					$icon = $this->getIconForError($emsgX, $legal_url, TRUE);
				}
			}
		}

		// also provide X/Y size info with each direct-access thumbnail file:
		if (!empty($thumb250))
		{
			$thumb250_uri = $this->getElementURL($thumb250);
			$json['thumb250'] = $thumb250_uri;
			$meta->store('thumb250_direct', $thumb250_uri);

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
					$preview_HTML = '<a target="_blank" href="' . $this->getElementURL($url) . '" title="' . htmlentities($filename, ENT_QUOTES, 'UTF-8') . '">
										<img src="' . $thumb250_uri . '" class="preview" alt="' . (!empty($emsgX) ? $this->mkSafe4HTMLattr($emsgX) : 'preview') . '" style="width: ' . $tnsize[0] . 'px; height: ' . $tnsize[1] . 'px;"	/>
						 			</a>'
					;
				}
			}
		}
		if (!empty($thumb48))
		{
			$thumb48_uri = $this->getElementURL($thumb48);

			$json['thumb48'] = $thumb48_uri;
			$meta->store('thumb48_direct', $thumb48_uri);

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
			$json['icon48'] = $this->getElementURL($icon48);

		if (!empty($icon))
			$json['icon'] = $this->getElementURL($icon);


		if ($preview_HTML === NULL)
			$preview_HTML = '<div class="center">${nopreview}</div>';

		if (!empty($preview_HTML))
		{
			$content =
				'<div class="filemanager-preview-content">'
					. $preview_HTML
				. '</div>'
				. $content
				. '<div>'
				. $postdiag_HTML
				. '</div>'
			;
		}

		$json['content'] = $content;
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
	public function getID3infoItem($getid3_info_obj, $default_value)
	{
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
	 * Delete a file or directory, including subdirectories and files.
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
	 * even while asked for, when the parent directory can be legally traversed by the FileManager
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
	 * Returns the URL icon image for the given file / directory.
	 *
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

		if( ! is_file($this->url_path2file_path($url_path)))
			$url_path = $this->options['assetsDir'] . 'images/icons/' . $largeDir . 'default.png';

		$url_path = $this->relativeBaseUrl . $url_path;

		$this->icon_cache[!$smallIcon][$ext] = $url_path;

		return $url_path;
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
			$img->resize($width, $height)->save($thumbPath, min(98, max(self::$thumb_jpeg_quality, self::$thumb_jpeg_quality + 0.15 * (250 - min($width, $height)))), TRUE);

			if (ENVIRONMENT == 'development')
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
	 * Test : in case someone want's to get really fancy: nuke the URLencoded '<'
	 */
	public function mkSafe4HTMLattr($str)
	{
		$str = str_replace('%3C', '?', $str);
		$str = strip_tags($str);
		return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
	}


	/**
	 * Inspired by http://nl3.php.net/manual/en/function.utf8-encode.php#102382;
	 * mix & mash to make sure the result is LEGAL UTF-8
	 *
	 * Introduced after the JSON encoder kept spitting out 'null' instead of a string value
	 * for a few choice French JPEGs with very non-UTF EXIF content. :-(
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

			if ( ! mb_check_encoding($dst, 'UTF-8') || $dst !== mb_convert_encoding(mb_convert_encoding($dst, 'UTF-32', 'UTF-8'), 'UTF-8', 'UTF-32') || strpos($dst, '?') !== FALSE)
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
				throw new Exception('path_tampering: ' . $path);
			}
			$prev = substr($prev, 0, $p2);
			$next = substr($path, $pos + 3);
			if ($next && $next[0] !== '/')
			{
				throw new Exception('path_tampering: ' . $path);
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
	 */
	public function url_path2file_path($url_path)
	{
		if (strpos($url_path, '/') === 0) $url_path = substr($url_path,1);
		$path = $this->options['documentRoot'] . $url_path;
		return $path;
	}

	/**
	 * Return the filesystem absolute path for the relative or absolute LEGAL URI path.
	 *
	 */
	public function get_full_path($url_path)
	{
		$path = $this->get_legal_url($url_path);
		$path = substr($this->managedBaseDir, 0, -1) . $path;
		return $path;
	}


	/**
	 * @param $string
	 *
	 * @return string
	 */
	public static function enforceTrailingSlash($string)
	{
		return (strrpos($string, '/') === strlen($string) - 1 ? $string : $string . '/');
	}



	protected function modify_json4exception(&$json, $emsg, $target_info = NULL)
	{
		if (empty($emsg))
			return;

		// only set up the new json error report array when this is the first exception we got:
		if (empty($json['error']))
		{
			// check the error message and see if it is a translation code word (with or without parameters) or just a generic error report string
			$e = explode(':', $emsg, 2);
			if (preg_match('/[^A-Za-z0-9_-]/', $e[0]))
			{
				// generic message. ouch.
				$json['error'] = $emsg;
			}
			else
			{
				$extra1 = (!empty($e[1]) ? $this->mkSafe4Display($e[1]) : '');
				$extra2 = (!empty($target_info) ? ' (' . $this->mkSafe4Display($target_info) . ')' : '');
				$json['error'] = $emsg = '${backend.' . $e[0] . '}';
				if ($e[0] != 'disabled')
				{
					// only append the extra data when it's NOT the 'disabled on this server' message!
					$json['error'] .=  $extra1 . $extra2;
				}
				else
				{
					$json['error'] .=  ' (${' . $extra1 . '})';
				}
			}
			$json['status'] = 0;
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
		if (self::$use_finfo_open)
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
	 * This method will cache the extracted info alongside the thumbnails in a cache file with
	 * '.nfo' extension.
	 * @param 	string $file       			physical filesystem path to the file we want to know all about
	 *
	 * @param 	string $legal_url			'legal URL path' to the file; used as the key to
	 * 										the corresponding .nfo file
	 * @param	boolean	 $force_recheck
	 *
	 * @return 	array	Info array as produced by getID3::analyze(), as part of a MTFMCacheEntry reference
	 *
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
			if ( is_dir($file))
			{
				$meta->store('mime_type', 'text/directory', FALSE);
				$meta->store('analysis', NULL, FALSE);
			}
			else
			{
				// Partikule
				// TODO : Check memory potential usage for opening the complete file
				// Do not retrieve info from ID3 if file size > memory limit
				// Replace by basic infos
				// $used_memory = memory_get_usage();
				$this->getid3->analyze($file);
				$rv = $this->getid3->info;

				if (empty($rv['mime_type']))
				{
					// guarantee to produce a mime type, at least!
					$meta->store('mime_type', $this->getMimeFromExtension($file));
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



	protected static function getHostUrl()
	{
		if(isset($_SERVER['HTTPS']))
			$protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
		else
			$protocol = 'http';

		return $protocol . "://" . $_SERVER['HTTP_HOST'] .'/';
	}

	protected function getRelativeBaseUrl()
	{
		$uri = str_replace(self::getHostUrl(), '', $this->options['baseUrl']);

		if ( ! empty($uri))
			return '/' . $uri;
		return '/';
	}


	/**
	 * @param string $url	URL relative to the document root.
	 *
	 * @return string		URL relative to the website
	 * 						If the website is installed in a subfolder
	 * 						$this->relativeBaseUrl will contains the path to the folder
	 */
	protected function getElementURL($url)
	{
		if ( ! empty($this->relativeBaseUrl) && strpos($url, $this->relativeBaseUrl) === FALSE)
		{
			if (strpos($url, '/') === 0) $url = substr($url,1);
			return $this->rawurlencode_path($this->relativeBaseUrl . $url);
		}
		else
		{
			if (strpos($url, '/') === 0) $url = substr($url,1);
			return $this->rawurlencode_path('/' . $url);
		}
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

    protected function cleanFilename($str, $delimiter='_', $is_dir=FALSE) {

        if($is_dir) {
            $filename = $str;
        }
        else {
        	$ext = end(explode('.', $str));

        	$filename = str_replace('.' . $ext, '', $str);
        }

        if (defined('ENVIRONMENT') AND is_file(APPPATH.'config/'.ENVIRONMENT.'/foreign_chars'.EXT))
            include(APPPATH.'config/'.ENVIRONMENT.'/foreign_chars'.EXT);
        elseif (is_file(APPPATH.'config/foreign_chars'.EXT))
            include(APPPATH.'config/foreign_chars'.EXT);

        $clean  = $filename;

        if(isset($foreign_characters))
            $clean  = preg_replace(array_keys($foreign_characters), array_values($foreign_characters), $clean);

        $clean  = strtolower($clean);
        $clean  = preg_replace('/[^a-zA-Z0-9\/_.|+ -]/', $delimiter, $clean);
        $clean  = strtolower(trim($clean, '-. '));
        $clean  = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);
        $clean  = rtrim($clean, '_-. ');
        if ( ! empty($ext) && $ext != $str && ! $is_dir)
            $clean  .= '.'.strtolower($ext);

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
	 * @param $str
	 *
	 * @return string
	 */
	protected function getHash($str)
	{
		return hash($this->options['hashMethod'], $str);
	}
}