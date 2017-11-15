<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize, creative CMS
 * Log Controller
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 1.0.8
 */

class Log extends MY_admin
{

	private $_log_path = '';

	private $_enabled = TRUE;

	private $_file_path = '';


	// ------------------------------------------------------------------------


	public function __construct()
	{
		parent::__construct();

		$this->config->set_item('log_threshold', '0');

		$config =& get_config();

		$this->_log_path = ($config['log_path'] != '') ? $config['log_path'] : APPPATH.'logs/';

		if ( ! is_dir($this->_log_path) OR ! is_really_writable($this->_log_path))
		{
			$this->_enabled = FALSE;
		}

		if ($config['log_date_format'] != '')
		{
			$this->_date_fmt =$config['log_date_format'];
		}

		$this->_file_name = 'log-'.date('Y-m-d').'.php';
		$this->_file_path = $this->_log_path.$this->_file_name;
	}


	// ------------------------------------------------------------------------


	public function index()
	{
		// Disable xhr protection on index
		$this->disable_xhr_protection();

		$this->output('log/index');
	}


	// ------------------------------------------------------------------------


	/**
	 *
	 *
	 */
	public function get_logs()
	{
		if ($this->_enabled)
		{
			$str = $this->tailCustom($this->_file_path, config_item('log_nb_lines'));

			if (empty($str))
			{
				$this->xhr_output(array(
					'error' => 1,
					'message' => 'No log file or log file is empty.'
				));
			}
			$str = nl2br($str);

			$this->xhr_output(array(
				'error' => 0,
				'lines' => $str,
				'file_name' => Settings::get('site_title') . ' > ' . $this->_file_name
			));
		}
		else
		{
			$this->xhr_output(array(
				'error' => 1,
				'message' => 'NO LOG : Log is not enabled.'
			));
		}
	}


	// ------------------------------------------------------------------------


	/**
	 *
	 * See : http://stackoverflow.com/questions/15025875/what-is-the-best-way-in-php-to-read-last-lines-from-a-file
	 * 		 https://gist.github.com/lorenzos/1711e81a9162320fde20
	 *
	 * @param $filepath
	 * @param int $lines
	 * @param bool $adaptive
	 * @return bool|string
	 */
	function tailCustom($filepath, $lines = 1, $adaptive = true) {

		// Open file
		$f = @fopen($filepath, "rb");
		if ($f === false) return false;

		// Sets buffer size
		if (!$adaptive) $buffer = 4096;
		else $buffer = ($lines < 2 ? 64 : ($lines < 10 ? 512 : 4096));

		// Jump to last character
		fseek($f, -1, SEEK_END);

		// Read it and adjust line number if necessary
		// (Otherwise the result would be wrong if file doesn't end with a blank line)
		if (fread($f, 1) != "\n") $lines -= 1;

		// Start reading
		$output = '';
		$chunk = '';

		// While we would like more
		while (ftell($f) > 0 && $lines >= 0)
		{
			// Figure out how far back we should jump
			$seek = min(ftell($f), $buffer);

			// Do the jump (backwards, relative to where we are)
			fseek($f, -$seek, SEEK_CUR);

			// Read a chunk and prepend it to our output
			$output = ($chunk = fread($f, $seek)) . $output;

			// Jump back to where we started reading
			fseek($f, -mb_strlen($chunk, '8bit'), SEEK_CUR);

			// Decrease our line counter
			$lines -= substr_count($chunk, "\n");
		}

		// While we have too many lines
		// (Because of buffer size we might have read too many)
		while ($lines++ < 0)
		{
			// Find first newline and remove all text before that
			$output = substr($output, strpos($output, "\n") + 1);
		}

		// Close file and return
		fclose($f);
		return trim($output);
	}
}
