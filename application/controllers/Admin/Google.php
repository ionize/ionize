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

	/**
	 *
	 */
	public function get_dashboard_report()
	{
		if (self::$should_access)
		{
			try
			{
				$dataRows = array();

				$ga = new gapi(self::$ga_email, self::$ga_password);

				// Main data
				$ga->requestReportData(
					self::$ga_profile_id,
					'pagePath',
					array(
						'pageviews',
						'uniquePageviews',
						'exitRate',
						'avgTimeOnPage',
						'entranceBounceRate',
					),
					null,
					'pagePath == /'
				);
				$mainData = $ga->getResults();

				// Visits data
				$ga->requestReportData(
					self::$ga_profile_id,
					'date',
					array('visitors', 'newVisits','visits'),
					'date'
				);

				$visitorData = $ga->getResults();

				if ( ! empty($mainData[0]))
				{
					$result = $mainData[0];

					$data = array(
						'pageViews' => number_format($result->getPageviews(),0,null,' '),
						'uniquePageViews' => number_format($result->getUniquePageviews(),0,null,' '),
						'avgTimeOnPage' => $this->secondMinute($result->getAvgtimeOnpage()),
						'bounceRate' => round($result->getEntranceBounceRate(), 2) . '%'
					);

					$data['visitors'] = 0;
					$data['visits'] = 0;
					$data['newVisits'] = 0;

					if ( ! empty($visitorData))
					{
						foreach ($visitorData as $vd)
						{
							$data['visitors'] += $vd->getVisitors();
							$data['visits'] += $vd->getVisits();
							$data['newVisits'] += $vd->getNewVisits();

							$dataRows[] = array(
								date('M j', strtotime($vd->getDate())),
								$vd->getVisits(),
								$vd->getNewVisits(),
							);
						}
						$data['visitors'] = number_format($data['visitors'],0,null,' ');
						$data['visits'] = number_format($data['visits'],0,null,' ');
						$data['newVisits'] = number_format($data['newVisits'],0,null,' ');
					}

					$this->template['data'] = $data;
				}

				// Get the Last 30 days data
				/*
				$ga->requestReportData(
					self::$ga_profile_id,
					array('date'),
					array('pageviews','uniquePageviews'),
					'date',
					'pagePath == /'
				);
				$chartResults = $ga->getResults();
				foreach($chartResults as $result)
				{
					$dataRows[] = array(
						date('M j', strtotime($result->getDate())),
						$result->getPageviews(),
						$result->getUniquePageviews()
					);
				}
				*/

				$this->template['dataRows'] = json_encode($dataRows, true);

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
