<?php

if (!defined('FILEMANAGER_CODE')) { header('HTTP/1.0 403 Forbidden', true, 403); die('illegal entry point'); }


/*
 * Set to nonzero, e.g. 01, when your site uses mod_alias, mod_vhost_alias or any other form of path aliasing, where
 * one or more of these assumptions do not apply any longer as they would for a 'regular' site:
 *
 * - SERVER['DOCUMENT_ROOT'] correctly points at the physical filesystem path equivalent of the '/' URI path (~ 'http://your-site.com/')
 *
 * - every subdirectory of SERVER['DOCUMENT_ROOT'] is also a subdirectory in URI space, i.e. URI path '/media/Files/'
 *   (~ 'http://your-site.com/media/Files/') would point at the physical filesystem path SERVER['DOCUMENT_ROOT'].'/media/Files/'
 *
 * Edit the 'Aliases' sub-array below to mimic your local site setup; see the notes there for a few hints.
 */
if (!defined('SITE_USES_ALIASES')) define('SITE_USES_ALIASES', 0);



if (!defined('DEVELOPMENT')) define('DEVELOPMENT', 0);   // set to 01 / 1 / nonzero value to enable logging of each incoming event request.

// when ON, show the sneaky 'reject on size' filter in the auth callback handler
if (!defined('SHOW_CUSTOM_CALLBACK_WORK')) define('SHOW_CUSTOM_CALLBACK_WORK', DEVELOPMENT && 0);


if (!SITE_USES_ALIASES)
{
	require(strtr(dirname(__FILE__), '\\', '/') . '/../Assets/Connector/FileManager.php');
}
else
{
	// you don't need the additional sophistication of this one when you don't need path mapping support
	require(strtr(dirname(__FILE__), '\\', '/') . '/../Assets/Connector/FMgr4Alias.php');
}









/**
defines for dump_request_to_logfile():
*/
define('DUMP2LOG_SERVER_GLOBALS',  0x0001);
define('DUMP2LOG_ENV_GLOBALS',     0x0002);
define('DUMP2LOG_SESSION_GLOBALS', 0x0004);
define('DUMP2LOG_POST_GLOBALS',    0x0008);
define('DUMP2LOG_GET_GLOBALS',     0x0010);
define('DUMP2LOG_REQUEST_GLOBALS', 0x0020);
define('DUMP2LOG_FILES_GLOBALS',   0x0040);
define('DUMP2LOG_COOKIE_GLOBALS',  0x0080);
define('DUMP2LOG_STACKTRACE',      0x0400);

define('DUMP2LOG_SORT',                            0x0100000);
define('DUMP2LOG_FORMAT_AS_HTML',                  0x0400000);
define('DUMP2LOG_WRITE_TO_FILE',                   0x0800000);
define('DUMP2LOG_WRITE_TO_STDOUT',                 0x1000000);











/*
 * Derived from code by phella.net:
 *
 *   http://nl3.php.net/manual/en/function.var-dump.php
 */
function var_dump_ex($value, $level = 0, $sort_before_dump = 0, $show_whitespace = true, $max_subitems = 0x7FFFFFFF)
{
	if ($level == -1)
	{
		$trans = array();
		if ($show_whitespace)
		{
			$trans[' '] = '&there4;';
			$trans["\t"] = '&rArr;';
			$trans["\n"] = '&para;';
			$trans["\r"] = '&lArr;';
			$trans["\0"] = '&oplus;';
		}
		return strtr(htmlspecialchars($value, ENT_COMPAT, 'UTF-8'), $trans);
	}

	$rv = '';
	if ($level == 0)
	{
		$rv .= '<pre>';
	}
	$type = gettype($value);
	$rv .= $type;

	switch ($type)
	{
	case 'string':
		$rv .= '(' . strlen($value) . ')';
		$value = var_dump_ex($value, -1, 0, $show_whitespace, $max_subitems);
		break;

	case 'boolean':
		$value = ($value ? 'true' : 'false');
		break;

	case 'object':
		$props = get_object_vars($value);
		if ($sort_before_dump > $level)
		{
			ksort($props);
		}
		$rv .= '(' . count($props) . ') <u>' . get_class($value) . '</u>';
		foreach($props as $key => $val)
		{
			$rv .= "\n" . str_repeat("\t", $level + 1) . var_dump_ex($key, -1, 0, $show_whitespace, $max_subitems) . ' => ';
			$rv .= var_dump_ex($value->{$key}, $level + 1, $sort_before_dump, $show_whitespace, $max_subitems);
		}
		$value = '';
		break;

	case 'array':
		if ($sort_before_dump > $level)
		{
			$value = array_merge($value); // fastest way to clone the input array
			ksort($value);
		}
		$rv .= '(' . count($value) . ')';
		$count = 0;
		foreach($value as $key => $val)
		{
			$rv .= "\n" . str_repeat("\t", $level + 1) . var_dump_ex($key, -1, 0, $show_whitespace, $max_subitems) . ' => ';
			$rv .= var_dump_ex($val, $level + 1, $sort_before_dump, $show_whitespace, $max_subitems);
			$count++;
			if ($count >= $max_subitems)
			{
				$rv .= "\n" . str_repeat("\t", $level + 1) . '<i>(' . (count($value) - $count) . ' more entries ...)</i>';
				break;
			}
		}
		$value = '';
		break;

	default:
		break;
	}
	$rv .= ' <b>' . $value . '</b>';
	if ($level == 0)
	{
		$rv .= '</pre>';
	}
	return $rv;
}




/**
 * Generate a dump of the optional $extra values and/or the global variables $ccms[], $cfg[] and the superglobals.
 *
 * @param array $filename_options (optional) specifies a few pieces of the filename which will be generated to write
 *                                the dump to:
 *
 *                                'namebase': the leading part of the filename,
 *                                'origin-section': follows the timestamp encoded in the filename,
 *                                'extension': the desired filename extension (default: 'html' for HTML dumps, 'log' for plain dumps)
 *
 * @return the generated dump in the format and carrying the content as specified by the $dump_options.
 */
define('__DUMP2LOG_DEFAULT_OPTIONS', -1 ^ DUMP2LOG_WRITE_TO_STDOUT);
function dump_request_to_logfile($extra = null, $dump_options = __DUMP2LOG_DEFAULT_OPTIONS, $filename_options = null)
{
	global $_SERVER;
	global $_ENV;
	global $_COOKIE;
	global $_SESSION;
	static $sequence_number;

	if (!$sequence_number)
	{
		$sequence_number = 1;
	}
	else
	{
		$sequence_number++;
	}

	$sorting = ($dump_options & DUMP2LOG_SORT);
	$show_WS = ($dump_options & DUMP2LOG_FORMAT_AS_HTML);

	$rv = '<html><body>';

	if (!empty($_SESSION['dbg_last_dump']) && ($dump_options & DUMP2LOG_FORMAT_AS_HTML))
	{
		$rv .= '<p><a href="' . $_SESSION['dbg_last_dump'] . '">Go to previous dump</a></p>' . "\n";
	}

	$now = microtime(true);
	if (!empty($_SERVER['REQUEST_TIME']))
	{
		$start = $_SERVER['REQUEST_TIME'];
		$diff = $now - $start;

		$rv .= '<p>Time elapses since request start: ' . number_format($diff, 3) . ' seconds</p>' . "\n";
	}

	if (!empty($extra))
	{
		$rv .= '<h1>EXTRA</h1>';
		$rv .= "<pre>";
		$rv .= var_dump_ex($extra, 0, $sorting, $show_WS, 500);
		$rv .= "</pre>";
	}

	if ($dump_options & DUMP2LOG_ENV_GLOBALS)
	{
		$rv .= '<h1>$_ENV</h1>';
		$rv .= "<pre>";
		$rv .= var_dump_ex($_ENV, 0, $sorting, $show_WS);
		$rv .= "</pre>";
	}
	if ($dump_options & DUMP2LOG_SESSION_GLOBALS)
	{
		$rv .= '<h1>$_SESSION</h1>';
		$rv .= "<pre>";
		$rv .= var_dump_ex($_SESSION, 0, $sorting, $show_WS);
		$rv .= "</pre>";
	}
	if ($dump_options & DUMP2LOG_POST_GLOBALS)
	{
		$rv .= '<h1>$_POST</h1>';
		$rv .= "<pre>";
		$rv .= var_dump_ex($_POST, 0, $sorting, $show_WS);
		$rv .= "</pre>";
	}
	if ($dump_options & DUMP2LOG_GET_GLOBALS)
	{
		$rv .= '<h1>$_GET</h1>';
		$rv .= "<pre>";
		$rv .= var_dump_ex($_GET, 0, $sorting, $show_WS);
		$rv .= "</pre>";
	}
	if ($dump_options & DUMP2LOG_FILES_GLOBALS)
	{
		$rv .= '<h1>$_FILES</h1>';
		$rv .= "<pre>";
		$rv .= var_dump_ex($_FILES, 0, $sorting, $show_WS);
		$rv .= "</pre>";
	}
	if ($dump_options & DUMP2LOG_COOKIE_GLOBALS)
	{
		$rv .= '<h1>$_COOKIE</h1>';
		$rv .= "<pre>";
		$rv .= var_dump_ex($_COOKIE, 0, $sorting, $show_WS);
		$rv .= "</pre>";
	}
	if ($dump_options & DUMP2LOG_REQUEST_GLOBALS)
	{
		$rv .= '<h1>$_REQUEST</h1>';
		$rv .= "<pre>";
		$rv .= var_dump_ex($_REQUEST, 0, $sorting, $show_WS);
		$rv .= "</pre>";
	}

	if ($dump_options & DUMP2LOG_SERVER_GLOBALS)
	{
		$rv .= '<h1>$_SERVER</h1>';
		$rv .= "<pre>";
		$rv .= var_dump_ex($_SERVER, 0, $sorting, $show_WS);
		$rv .= "</pre>";
	}

	if ($dump_options & DUMP2LOG_STACKTRACE)
	{
		$st = debug_backtrace(false);
		$rv .= '<h1>Stack Trace:</h1>';
		$rv .= "<pre>";
		$rv .= var_dump_ex($st, 0, 0, $show_WS);
		$rv .= "</pre>";
	}

	$rv .= '</body></html>';

	$tstamp = date('Y-m-d.His') . '.' . sprintf('%07d', fmod($now, 1) * 1E6);

	$filename_options = array_merge(array(
			'namebase'       => 'LOG-',
			'origin-section' => substr($_SERVER['REQUEST_URI'], 0, -42),
			'extension'      => (($dump_options & DUMP2LOG_FORMAT_AS_HTML) ? 'html' : 'log')
		), (is_array($filename_options) ? $filename_options : array()));

	$fname = $filename_options['namebase'] . $tstamp . '.' . sprintf('%03u', $sequence_number) . '-' . $filename_options['origin-section'];
	$fname = substr(preg_replace('/[^A-Za-z0-9_.-]+/', '_', $fname), 0, 46) . '.' . substr(preg_replace('/[^A-Za-z0-9_.-]+/', '_', $filename_options['extension']), 0, 9);    // make suitable for filesystem
	if (isset($_SESSION))
	{
		$_SESSION['dbg_last_dump'] = $fname;
	}

	if (!($dump_options & DUMP2LOG_FORMAT_AS_HTML))
	{
		$rv = preg_replace('/^.*?<body>(.+)<\/body>.*?$/sD', '\\1', $rv);

		$trans['<h1>'] = "\n\n*** ";
		$trans['</h1>'] = " ***\n";
		$rv = strtr($rv, $trans);

		$rv = html_entity_decode(strip_tags($rv), ENT_NOQUOTES, 'UTF-8');
	}

	if ($dump_options & DUMP2LOG_WRITE_TO_FILE)
	{
		$fname = strtr(dirname(__FILE__), '\\', '/') . '/' . $fname;

		if (@file_put_contents($fname, $rv) === false)
		{
			throw new Exception('b0rk at ' . $fname);
		}
	}

	if ($dump_options & DUMP2LOG_FORMAT_AS_HTML)
	{
		$rv = preg_replace('/^.*?<body>(.+)<\/body>.*?$/sD', '\\1', $rv);
	}

	if ($dump_options & DUMP2LOG_WRITE_TO_STDOUT)
	{
		echo $rv;
	}

	return array('filename' => $fname, 'content' => $rv);
}















/**
 * dumper useful in development
 */
define('__MTFM_VARDUMP_DEFAULT_OPTIONS', (0
										| DUMP2LOG_SERVER_GLOBALS
										| DUMP2LOG_ENV_GLOBALS
										| DUMP2LOG_SESSION_GLOBALS
										| DUMP2LOG_POST_GLOBALS
										| DUMP2LOG_GET_GLOBALS
										| DUMP2LOG_REQUEST_GLOBALS
										| DUMP2LOG_FILES_GLOBALS
										| DUMP2LOG_COOKIE_GLOBALS
										//| DUMP2LOG_STACKTRACE
										| DUMP2LOG_SORT
										//| DUMP2LOG_FORMAT_AS_HTML
										| DUMP2LOG_WRITE_TO_FILE
										//| DUMP2LOG_WRITE_TO_STDOUT
									));
function FM_vardumper($mgr = null, $action = null, $info = null, $extra = null, $dump_options = __MTFM_VARDUMP_DEFAULT_OPTIONS)
{
	if (DEVELOPMENT)
	{
		if ($mgr)
			$settings = $mgr->getSettings();
		else
			$settings = null;

		//$mimetdefs = $mgr->getMimeTypeDefinitions();

		// log request data:
		$data = array(
				"FileManager::action" => $action,
				"FileManager::info" => $info,
				"FileManager::settings" => $settings
			);
		if (!empty($extra))
		{
			$data['extra'] = $extra;
		}

		dump_request_to_logfile($data, $dump_options, array(
				'origin-section' => basename($_SERVER['REQUEST_URI']) . '-' . $action
			));
	}
}















/**
 * Just a simple wrapper around the FileManager class constructor. Assumes a series of option defaults for the Demos,
 * which you may override by providing your own in $options.
 *
 * Returns an instantiated FileManager instance, which you can use to process the incoming request.
 */
function mkNewFileManager($options = null)
{
	$Aliases = array();

	if (SITE_USES_ALIASES)
	{
		//
		// http://httpd.apache.org/docs/2.2/mod/mod_alias.html -- we emulate the Alias statement. Sort of.
		//
		// In principle each entry in this array should copy a Alias/VhostAlias/... web server configuration line.
		//
		// When filesystem paths are 'real time constructed', e.g. through complex regex manipulations, you will need
		// to derive your own class from FileManagerWithAliasSupport or FileManager and implement/override
		// the offending member functions in there, using the FileManagerWithAliasSupport implementation as a guide.
		//
		// NOTE that the above caveat applies to very complex rigs only, e.g. where a single URL points at different
		//      physical locations, depending on who's logged in, or where the request is originating from.
		//
		//      As long as you can construct a static URI path to disk mapping, you are good to go using the Aliases[]
		//      array below!
		//
		$Aliases = array(
				'/c/lib/includes/js/mootools-filemanager/Demos/Files/alias' => "D:/xxx",
				'/c/lib/includes/js/mootools-filemanager/Demos/Files/d' => "D:/xxx.tobesorted",
				'/c/lib/includes/js/mootools-filemanager/Demos/Files/u' => "D:/websites-uploadarea",

				'/c/lib/includes/js/mootools-filemanager/Demos/Files' => "D:/experiment"
			);
	}

	$options = array_merge(array(
			//'directory' => $fm_basedir . 'Files/',   // absolute paths: as the relative ones, they sit in URI space, i.e. assume DocumentRoot is root '/'

			'directory' => 'Files/',                   // relative paths: are relative to the URI request script path, i.e. dirname(__FILE__) or rather: $_SERVER['SCRIPT_NAME']
			'thumbnailPath' => 'Files/Thumbnails/',
			'assetBasePath' => '../Assets',
			'chmod' => 0777,
			//'maxUploadSize' => 1024 * 1024 * 5,
			//'upload' => false,
			//'destroy' => false,
			//'create' => false,
			//'move' => false,
			//'download' => false,
			//'filter' => 'image/',
			'allowExtChange' => true,                  // allow file name extensions to be changed; the default however is: NO (FALSE)
			'UploadIsAuthorized_cb' => 'FM_IsAuthorized',
			'DownloadIsAuthorized_cb' => 'FM_IsAuthorized',
			'CreateIsAuthorized_cb' => 'FM_IsAuthorized',
			'DestroyIsAuthorized_cb' => 'FM_IsAuthorized',
			'MoveIsAuthorized_cb' => 'FM_IsAuthorized',
			'ViewIsAuthorized_cb' => 'FM_IsAuthorized',
			'DetailIsAuthorized_cb' => 'FM_IsAuthorized',
			'ThumbnailIsAuthorized_cb' => 'FM_IsAuthorized',

			// FileManagerWithAliasSupport-specific options:
			'Aliases' => $Aliases,
			'RequestScriptURI' => strtr($_SERVER['SCRIPT_NAME'], '\\', '/')   // or whatever URL you fancy. As long as the run-time ends up invoking the $browser class instantiated below on each request
	), (is_array($options) ? $options : array()));

	if (SITE_USES_ALIASES)
	{
		$browser = new FileManagerWithAliasSupport($options);
	}
	else
	{
		$browser = new FileManager($options);
	}
	return $browser;
}










/**
Start the session, whether the session ID is passed through the URL query section or as a cookie.
*/
function session_start_ex($override = false)
{
	/*
	 * Load session if not already done by CCMS!
	 */
	if (empty($_SESSION))
	{
		$sesid = session_id();
		$sesname = session_name();
		// the SWF.Upload / FancyUpload FLASH components do pass along the cookies, but as extra URL query entities:
		if (!empty($_POST[$sesname]))
		{
			// legalize the sessionID; just a precaution for malicious intent
			$alt_sesid = preg_replace('/[^A-Za-z0-9]/', 'X', $_POST[$sesname]);

			/*
			 * Before we set the sessionID, we'd better make darn sure it's a legitimate request instead of a hacker trying to get in:
			 *
			 * however, before we can access any $_SESSION[] variables do we have to load the session for the given ID.
			 */
			session_id($alt_sesid);
			if (!session_start())
			{
				header('HTTP/1.0 403 Forbidden', true, 403);
				die('session_start_ex() failed');
			}

			/*
			check the 'secret' value to doublecheck the legality of the session: did it originate from one of the demo entry pages?
			*/
			if (empty($_SESSION['FileManager']) || $_SESSION['FileManager'] !== 'DemoMagick')
			{
				//echo " :: illegal session override! IGNORED! \n";

				// do not nuke the first session; this might have been a interloper trying a attack... let it all run its natural course.
				if (0)
				{
					if (ini_get('session.use_cookies'))
					{
						$params = session_get_cookie_params();
						if (!empty($params['ccms_userID']))
						{
							setcookie(session_name(), '', time() - 42000,
								$params['path'], $params['domain'],
								$params['secure'], $params['httponly']
							);
						}
					}

					// Generate a new session_id
					session_regenerate_id();

					// Finally, destroy the session.
					if(session_destroy())
					{
						header('HTTP/1.0 403 Forbidden', true, 403);
						die('session_start_ex() failed');
					}
				}
				session_write_close();
				session_regenerate_id();
				session_id($sesid);
			}
		}
		else
		{
			if (!session_start())
			{
				header('HTTP/1.0 403 Forbidden', true, 403);
				die('session_start_ex(ALT) failed');
			}
		}
	}
	else if ($override)
	{
		$sesid = session_id();
		$sesname = session_name();
		// the SWF.Upload / FancyUpload FLASH components do pass along the cookies, but as extra URL query entities:
		if (!empty($_POST[$sesname]))
		{
			// legalize the sessionID; just a precaution for malicious intent
			$alt_sesid = preg_replace('/[^A-Za-z0-9]/', 'X', $_POST[$sesname]);

			// did we already activate this session?
			if ($sesid === $alt_sesid)
			{
				// yep. We're done!
				return;
			}
			else
			{
				// close running session:
				session_regenerate_id();
				//session_destroy();
				session_write_close();
				unset($_SESSION);

				/*
				 * Before we set the sessionID, we'd better make darn sure it's a legitimate request instead of a hacker trying to get in:
				 *
				 * however, before we can access any $_SESSION[] variables do we have to load the session for the given ID.
				 */
				session_id($alt_sesid);
				if (!session_start())
				{
					header('HTTP/1.0 403 Forbidden', true, 403);
					die('session_start_ex() failed');
				}

				/*
				check the 'secret' value to doublecheck the legality of the session: did it originate from one of the demo entry pages?
				*/
				if (empty($_SESSION['FileManager']) || $_SESSION['FileManager'] !== 'DemoMagick')
				{
					//echo " :: illegal session override! IGNORED! \n";

					// do not nuke the first session; this might have been a interloper trying a attack... let it all run its natural course.
					if (0)
					{
						if (ini_get('session.use_cookies'))
						{
							$params = session_get_cookie_params();
							if (!empty($params['ccms_userID']))
							{
								setcookie(session_name(), '', time() - 42000,
									$params['path'], $params['domain'],
									$params['secure'], $params['httponly']
								);
							}
						}

						// Generate a new session_id
						session_regenerate_id();

						// Finally, destroy the session.
						if(session_destroy())
						{
							header('HTTP/1.0 403 Forbidden', true, 403);
							die('session_start_ex() failed');
						}
					}
					session_write_close();
					session_regenerate_id();
					session_id($sesid);
				}
			}
		}
	}
}











/*
 * FileManager event callback: Please add your own authentication / authorization here.
 *
 * Note that this function serves as a custom callback for all FileManager
 * authentication/authorization requests, but you may of course provide
 * different functions for each of the FM callbacks.
 *
 * Return TRUE when the session/client is authorized to execute the action, FALSE
 * otherwise.
 *
 * NOTE: the customer code in here may edit the $fileinfo items and have those edits picked up by FM.
 *       E.g. changing the filename on write/move, fixing filename extensions based on file content sniffed mimetype, etc.
 *
 * See the Assets/Connector/FileManager.php file for extended info about this callback and the parameters passed into it.
 *
 *
 * Notes:
 *
 *     Just for the sake of the demo, did we include exceptions being thrown in here; in a real situation you wouldn't
 *     need those in the circumstances they are used right now. You /may/ use exceptions to signal other faults, though.
 */
function FM_IsAuthorized($mgr, $action, &$info)
{
	// Start session, if not already started
	session_name('alt_session_name');
	session_start_ex();

	//$settings = $mgr->getSettings();
	//$mimetdefs = $mgr->getMimeTypeDefinitions();

	// log request data:
	FM_vardumper($mgr, $action, $info);

	// when the session, started in the demo entry pages, doesn't exist or is not valid, we do not allow ANYTHING any more:
	if (empty($_SESSION))
	{
		session_write_close();
		throw new FileManagerException('authorized: The session is non-existent.');
		return false;
	}

	if (empty($_SESSION['FileManager']) || $_SESSION['FileManager'] !== 'DemoMagick')
	{
		session_write_close();
		throw new FileManagerException('authorized: The session is illegal, as it does not contain the mandatory magic value set up by the demo entry pages.');
		return false;
	}


	/*
	 * authenticate / authorize:
	 * this sample is a bogus authorization, but you can perform simple to highly
	 * sophisticated authentications / authorizations here, e.g. even ones which also check permissions
	 * related to what is being uploaded right now (different permissions required for file mimetypes,
	 * e.g. images: any authorized user; while other file types which are more susceptible to carrying
	 * illicit payloads requiring at least 'power/trusted user' permissions, ...)
	 */

	$rv = false;
	switch ($action)
	{
	case 'upload':
		/*
		 * Note that the TinyMCE demo currently has this sestting set to 'NO' to simulate an UNauthorized user, for the sake of the demo.
		 */
		$rv = ($_SESSION['UploadAuth'] == 'yes');
		break;

	case 'download':
		$rv = true;
		break;

	case 'create': // create directory
	case 'destroy':
	case 'move':  // move or copy!
	case 'view':
		$rv = true;
		break;

	case 'detail':
		/*
		 * For the demo, we deny generation of thumbnails for images in a certain size range: 500KB - 2MB, jpeg only.
		 *
		 * To showcase the nasty/cool (depending on your disposition) things you can do in this callback, we
		 * force the thumbnail to become a thumbnail of the 'nuke':
		 */
		$fsize = @filesize($info['file']);
		/*
		 * When the thumbnail request is made, the demo will error on
		 *   bison-head-with-horns (Ray Rauch, U.S. Fish and Wildlife Service).jpg
		 *   fruits-vegetables-milk-and-yogurt (Peggy Greb, U.S. Department of Agriculture).jpg
		 * intentionally with the next bit of code; just to give you an idea what can be done in here.
		 *
		 * you can do a similar thing for any other request and have a good file fail or a bad file recover and succeed,
		 * simply by patching the $info[] items.
		 */
		if (SHOW_CUSTOM_CALLBACK_WORK && $info['mime'] == 'image/jpeg' && $fsize >= 180 * 1024 && $fsize <= 200 * 1024)
		{
			// force the manager to fetch the 'nuke' icon:
			$info['filename'] = 'is.default-error';

			// and nuke the mimetype to make sure it does go for the icon, always:
			$info['mime'] = 'icon/icon';

			// and act as if we authorized the action. Meanwhile, we just nuked it.
		}
		$rv = true;
		break;

	default:
		// unknown operation. Internal server error.
		$rv = false;
		break;
	}

	// make sure the session is closed (and unlocked) before the bulk of the work is performed: better parallelism server-side.
	session_write_close();

	return $rv;
}







// Do *NOT* add a <?php ?-> close tag here! Any whitespace after that makes PHP output both a Content-Type: test/html header AND the whitespace as content.
// This BREAKS any operation (such as mootools-filemanager::event=thumbnail) which outputs BINARY DATA (in that particular case, PHP spits out an image)
// The safest way to prevent ANY PHP file from producing undesirable [whitespace] output is to never add that ?-> close tag.
