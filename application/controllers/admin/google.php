<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Ionize, creative CMS
 * Google Controller
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 1.0.0
 */

require_once APPPATH.'libraries/gapi.class.php';

class Google extends MY_admin
{
	private static $ga_email = NULL;
	private static $ga_password = NULL;
	private static $ga_profile_id = NULL;
	private static $ga_url = NULL;

	private static $should_access = FALSE;

	/**
	 * Constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct();

		self::$ga_email = Settings::get('google_analytics_email');
		self::$ga_password = Settings::get('google_analytics_password');
		self::$ga_profile_id = Settings::get('google_analytics_profile_id');
		self::$ga_url = Settings::get('google_analytics_url');

		if (self::$ga_email !='' && self::$ga_password !='' && self::$ga_profile_id !='' && self::$ga_url !='')
			self::$should_access = TRUE;
	}

	public function index()
	{
	}

	public function get_dashboard_report()
	{
		if (self::$should_access)
		{
			try
			{
				$ga = new gapi(self::$ga_email, self::$ga_password);

				$ga->requestReportData(
					self::$ga_profile_id,
					'pagePath',
					array('pageviews', 'uniquePageviews', 'exitRate', 'avgTimeOnPage', 'entranceBounceRate', 'newVisits'),
					null,
					'pagePath == /'
				);
				$mainData = $ga->getResults();

				if ( ! empty($mainData[0]))
				{
					$result = $mainData[0];

					$data = array(
						'pageViews' => number_format($result->getPageviews()),
						'uniquePageViews' => number_format($result->getUniquePageviews()),
						'avgTimeOnPage' => $this->secondMinute($result->getAvgtimeOnpage()),
						'bounceRate' => round($result->getEntranceBounceRate(), 2) . '%',
						'exitRate' => round($result->getExitRate(), 2) . '%',
					);

					$this->template['data'] = $data;
				}

				// Get the Last 30 days data
				$ga->requestReportData(
					self::$ga_profile_id,
					array('date'),
					array('pageviews'),
					'date',
					'pagePath == /'
				);
				$chartResults = $ga->getResults();
				$chartRows = array();
				foreach($chartResults as $result)
				{
					$chartRows[] = array(date('M j',strtotime($result->getDate())), $result->getPageviews());
				}

				$this->template['chartRows'] = json_encode($chartRows, true);

				$this->output('google/dashboard_report');
			}
			catch(Exception $e)
			{
				echo $e->getMessage();
			}
		}
	}


	private function secondMinute($seconds)
	{
		$minResult = floor($seconds/60);
		if($minResult < 10){$minResult = 0 . $minResult;}
		$secResult = ($seconds/60 - $minResult)*60;
		if($secResult < 10){$secResult = 0 . round($secResult);}
		else { $secResult = round($secResult); }
		return $minResult.":".$secResult;
	}

}
