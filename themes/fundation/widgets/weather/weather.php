<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Weather widget
 * Uses Yahoo weather webservice : http://developer.yahoo.com/weather/
 *
 * @author	Partikule Studio
 *
 * @usage	To be called from a view : 
 * 			<\ion:widget name="weather" id="FRXX0076" unit="c" />
 *
 *			id : 	Location. See http://weather.yahoo.com/
 *			unit : 	c : Celcius degrees
 *					f : Farenheit degrees
 *
 */
class Weather extends Widget
{
	function run($id, $unit) 
	{
		$data = array();
	
		$url = 'http://weather.yahooapis.com/forecastrss?p='.$id.'&u='.$unit;

		$xml = @simplexml_load_file($url);

		if ($xml)
		{
			// Location
			$location= $xml->xpath('//channel/yweather:location');
			$data['city'] = (String) $location[0]["city"];
			$data['region'] = (String) $location[0]["region"];
			$data['country'] = (String) $location[0]["country"];

			// Lattitude
			$lat= $xml->xpath('//channel/item/geo:lat');
			$data['lat'] = (String) $lat[0];

			// Longitude
			$long= $xml->xpath('//channel/item/geo:long');
			$data['long'] = (String) $long[0];

			// Condition
			$condition = $xml->xpath('//channel/item/yweather:condition');
			$data['code'] = (String) $condition[0]["code"];
			$data['temp'] = (String) $condition[0]["temp"];

			// Forecast today
			$forecast = $xml->xpath('//channel/item/yweather:forecast');
			$data['low'] = (String) $forecast[0]["low"];
			$data['high'] = (String) $forecast[0]["high"];

			return $this->render('weather', $data);
		}
		else
		{
			return $this->show_error(lang('widget_weather_url_not_reachable') . '<br/>' . $url);
		}
	}
} 