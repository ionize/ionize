<?php
/**
 * tiny_mce_gzip.php
 *
 * Copyright 2010, Moxiecode Systems AB
 * Released under LGPL License.
 *
 * License: http://tinymce.moxiecode.com/license
 * Contributing: http://tinymce.moxiecode.com/contributing
 */

// Handle incoming request if it's a script call
if (TinyMCE_Compressor::getParam("js")) {
	// Default settings
	$tinyMCECompressor = new TinyMCE_Compressor(array(
		"plugins" => "",
		"themes" => "",
		"languages" => "",
		"disk_cache" => true,
		"expires" => "30d",
		"cache_dir" => dirname(__FILE__),
		"compress" => true,
		"suffix" => ""
	));

	// Add custom scripts here
	//$tinyMCECompressor->addFile("somescript.js");

	// Handle request, compress and stream to client
	$tinyMCECompressor->handleRequest();
}

/**
 * This class combines and compresses the TinyMCE core, plugins, themes and
 * language packs into one disk cached gzipped request. It improves the loading speed of TinyMCE dramatically but
 * still provides dynamic initialization.
 *
 * Example of direct usage:
 * require_once("../jscripts/tiny_mce/tiny_mce_gzip.php");
 *
 * // Renders script tag with compressed scripts
 * TinyMCE_Compressor::renderTag(array(
 *    "url" => "../jscripts/tiny_mce/tiny_mce_gzip.php",
 *    "plugins" => "pagebreak,style",
 *    "themes" => "advanced",
 *    "languages" => "en"
 * ));
 */
class TinyMCE_Compressor {
	private $files, $settings;

	/**
	 * Constructs a new compressor instance.
	 *
	 * @param Array $settings Name/value array with settings for the compressor instance.
	 */
	public function __construct($settings = array()) {
		$this->files = array();
		$this->settings = $settings;
	}

	/**
	 * Adds a file to the concatenation/compression process.
	 *
	 * @param String $path Path to the file to include in the compressed package/output.
	 */
	public function &addFile($file) {
		$this->files[] = $file;

		return $this;
	}

	/**
	 * Handles the incoming HTTP request and sends back a compressed script depending on settings and client support.
	 */
	public function handleRequest() {
		$files = array();
		$supportsGzip = false;
		$expiresOffset = $this->parseTime($this->settings["expires"]);
		$tinymceDir = dirname(__FILE__);

		// Override settings with querystring params
		$plugins = self::getParam("plugins");
		if ($plugins)
			$this->settings["plugins"] = $plugins;

		$themes = self::getParam("themes");
		if ($themes)
			$this->settings["themes"] = $themes;

		$languages = self::getParam("languages");
		if ($languages)
			$this->settings["languages"] = $languages;

		$diskCache = self::getParam("diskcache");
		if ($diskCache)
			$this->settings["disk_cache"] = $diskCache === "true";

		$languages = explode(',', $this->settings["languages"]);

		// Add core
		$files[] = "tiny_mce.js";
		foreach ($languages as $language)
			$files[] = "langs/" . $language . ".js";

		// Add plugins
		$plugins = explode(',', $this->settings["plugins"]);
		foreach ($plugins as $plugin) {
			$files[] = "plugins/" . $plugin . "/editor_plugin.js";

			foreach ($languages as $language)
				$files[] = "plugins/" . $plugin . "/langs/" . $language . ".js";
		}

		// Add themes
		$themes = explode(',', $this->settings["themes"]);
		foreach ($themes as $theme) {
			$files[] = "themes/" . $theme . "/editor_template.js";

			foreach ($languages as $language)
				$files[] = "themes/" . $theme . "/langs/" . $language . ".js";
		}

		// Generate hash for all files
		$hash = "";
		foreach ($files as $file)
			$hash .= $file;
		$hash = md5($hash);

		// Set basic headers
		header("Content-type: text/javascript");
		header("Vary: Accept-Encoding");  // Handle proxies

		// Check if it supports gzip
		if (isset($_SERVER['HTTP_ACCEPT_ENCODING']))
			$encodings = explode(',', strtolower(preg_replace("/\s+/", "", $_SERVER['HTTP_ACCEPT_ENCODING'])));

		// Check if the server and client supports gzip compression
		if ($this->settings['compress'] && (in_array('gzip', $encodings) || in_array('x-gzip', $encodings) || isset($_SERVER['---------------'])) && function_exists('gzencode') && !ini_get('zlib.output_compression')) {
			header("Content-Encoding: " . (in_array('x-gzip', $encodings) ? "x-gzip" : "gzip"));
			$cacheFile = $this->settings["cache_dir"] . "/" . $hash . ".gz";
			$supportsGzip = true;
		} else
			$cacheFile = $this->settings["cache_dir"] . "/" . $hash . ".js";

		header("Expires: " . gmdate("D, d M Y H:i:s", time() + $expiresOffset) . " GMT");
		header("Cache-Control: public, max-age=" . $expiresOffset);

		// Use cached file
		if ($this->settings['disk_cache'] && file_exists($cacheFile)) {
			readfile($cacheFile);
			return;
		}

		// Set base URL for where tinymce is loaded from
		$buffer = "var tinyMCEPreInit={base:'" . dirname($_SERVER["SCRIPT_NAME"]) . "',suffix:''};";

		// Load all tinymce script files into buffer
		foreach ($files as $file)
			$buffer .= $this->getFileContents($tinymceDir . "/" . $file);

		// Mark all themes, plugins and languages as done
		$buffer .= 'tinymce.each("' . implode(',', $files) . '".split(","),function(f){tinymce.ScriptLoader.markDone(tinyMCE.baseURL+"/"+f);});';

		// Load any custom scripts into buffer
		foreach ($this->files as $file)
			$buffer .= $this->getFileContents($file);

		// Compress data
		if ($supportsGzip)
			$buffer = gzencode($buffer, 9, FORCE_GZIP);

		// Write cached file
		if ($this->settings["disk_cache"])
			@file_put_contents($cacheFile, $buffer);	

		// Stream contents to client
		echo $buffer;
	}

	/**
	 * Renders a script tag that loads the TinyMCE script.
	 *
	 * @param Array $settings Name/value array with settings for the script tag.
	 */
	public static function renderTag($settings) {
		$scriptSrc = $settings["url"] . "?js=1";

		// Add plugins
		if (isset($settings["plugins"]))
			$scriptSrc .= "&plugins=" . (is_array($settings["plugins"]) ? implode(',', $settings["plugins"]) : $settings["plugins"]);

		// Add themes
		if (isset($settings["themes"]))
			$scriptSrc .= "&themes=" . (is_array($settings["themes"]) ? implode(',', $settings["themes"]) : $settings["themes"]);

		// Add languages
		if (isset($settings["languages"]))
			$scriptSrc .= "&languages=" . (is_array($settings["languages"]) ? implode(',', $settings["languages"]) : $settings["languages"]);

		// Add disk_cache
		if (isset($settings["disk_cache"]))
			$scriptSrc .= "&diskcache=" . ($settings["disk_cache"] === true ? "true" : "false");

		echo '<script type="text/javascript" src="' . htmlspecialchars($scriptSrc) . '"></script>';
	}

	/**
	 * Returns a sanitized query string parameter.
	 *
	 * @param String $name Name of the query string param to get.
	 * @param String $default Default value if the query string item shouldn't exist.
	 * @return String Sanitized query string parameter value.
	 */
	public static function getParam($name, $default = "") {
		if (!isset($_GET[$name]))
			return $default;

		return preg_replace("/[^0-9a-z\-_,]+/i", "", $_GET[$name]); // Sanatize for security, remove anything but 0-9,a-z,-_,
	}

	/**
	 * Parses the specified time format into seconds. Supports formats like 10h, 10d, 10m.
	 *
	 * @param String $time Time format to convert into seconds.
	 * @return Int Number of seconds for the specified format.
	 */
	private function parseTime($time) {
		$multipel = 1;

		// Hours
		if (strpos($time, "h") > 0)
			$multipel = 3600;

		// Days
		if (strpos($time, "d") > 0)
			$multipel = 86400;

		// Months
		if (strpos($time, "m") > 0)
			$multipel = 2592000;

		// Trim string
		return intval($time) * $multipel;
	}

	/**
	 * Returns the contents of the script file if it exists and removes the UTF-8 BOM header if it exists.
	 *
	 * @param String $file File to load.
	 * @return String File contents or empty string if it doesn't exist.
	 */
	private function getFileContents($file) {
		if (file_exists($file)) {
			$content = file_get_contents($file);

			// Remove UTF-8 BOM
			if (substr($content, 0, 3) === pack("CCC", 0xef, 0xbb, 0xbf))
				$content = substr($content, 3);
		} else
			$content = "";

		return $content;
	}
}
?>