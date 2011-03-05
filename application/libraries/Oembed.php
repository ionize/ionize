<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* CodeIgniter library to use the Oembed Format (http://www.oembed.com/) for Videos (YouTube, Hulu, Viddler, Vimeo, Revison3, Qik)
*
* Example Usage:
*
*   Without config
*   $this->load->library('oembed');
*
*   With config
*   $params = array('type' => 'json', 'cache' => TRUE);
*   $this->load->library('oembed', $params);
*
*   $this->oembed->call('vimeo', 'http://vimeo.com/6972240');
*   $this->oembed->call('hulu', 'http://www.hulu.com/watch/105905/the-daily-show-with-jon-stewart-recap-week-of-oct-26-2009');
*   $this->oembed->call('revision3', 'http://revision3.com/appjudgment/an_gvoice');
*   $this->oembed->call('qik', 'http://qik.com/video/3420556');
*   $this->oembed->call('viddler', 'http://www.viddler.com/explore/BTTradespace/videos/271/');
*   $this->oembed->call('youtube', 'http://www.youtube.com/watch?v=nKu60YKqsvs&feature=rec-LGOUT-exp_stronger_r2-HM');
*   ---ONLY JSON FOR MORE VISIT http://oohembed.com/--- $this->oembed->call('oohembed', 'http://pycon.blip.tv/file/2058801/');
*
* REQUIRES: curl, json_decode or simplexml_load_string
* OPTIONAL: KhCache library
*
* Methods return a array(provider_url, title, html, author_name, height, width, version, author_url, provider_name, type[photo/video])
*
* @author I.M.G <img@public-files.de>
*
* @version: 1.3
* @license GNU GENERAL PUBLIC LICENSE - Version 2, June 1991
*
**/

class Oembed {
	
    var $type = 'json';
    var $cache = FALSE;

    function Oembed($params = array())
    {
        if (count($params) > 0)
        {
            $this->_initialize($params);
        }
        log_message('debug', "Oembed Lib Initialized");
    }


    function call($api, $url)
    {
                if($site = $this->_get_api($api))
                {
                    $uri = $site['http'].rawurlencode($url).$site['param'];

                    if($this->cache)
                    {
                        //SuperObject laden
                        $CI =& get_instance();
                        //KhCache Lib laden
                        $CI->load->library('khcache');
                        //Cache Key erzeugen
                        $key = $CI->khcache->generatekey($uri);

                        if (($data = $CI->khcache->fetch($key)) === false)
                        {
                            $data = $this->_fetch($uri);
                            //Cache-File erzeugen
                            $CI->khcache->store($key, $data);
                        }
                        return $data;
                    }
                    return $this->_fetch($uri);
                }
                return show_error('Oembed - The '.$api.' API is unsupported!');
    }

	function _fetch($url)
    {
		if (!function_exists('curl_init'))
        {
			if(function_exists('log_message'))
            {
				log_message('error', 'Oembed - PHP was not built with cURL enabled. Rebuild PHP with --with-curl to use cURL.') ;
			}
			return FALSE;
		}

    	$curl = curl_init($url);
    	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($curl, CURLOPT_TIMEOUT, 30);
    	$return = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

		if ($status == '200')
        {
            return $this->_parse_returned($return);
		}
		return FALSE;
	}

    function _get_api($api)
    {
        switch (strtolower($api))
        {
			case 'youtube':
                return array('http' => 'http://www.youtube.com/oembed?url=', 'param' => '&format='.$this->type);
				break;
			case 'viddler':
                return array('http' => 'http://lab.viddler.com/services/oembed/?url=', 'param' => '&format='.$this->type);
				break;
			case 'qik':
                return array('http' => 'http://qik.com/api/oembed.'.$this->type.'?url=','param' => '');
				break;
			case 'revision3':
                return array('http' => 'http://revision3.com/api/oembed/?url=', 'param' => '&format='.$this->type);
				break;
			case 'hulu':
                return array('http' => 'http://www.hulu.com/api/oembed.'.$this->type.'?url=','param' => '');
				break;
			case 'vimeo':
                return array('http' => 'http://www.vimeo.com/api/oembed.'.$this->type.'?url=',    'param' => '');
				break;
			case 'oohembed':
                return array('http' => 'http://oohembed.com/oohembed/?url=',    'param' => '');
				break;
            default:
                return FALSE;
                break;
		}
    }
	function _parse_returned($data)
    {
		if(empty($data)) return FALSE;

		switch ($this->type)
        {
			case 'xml':
                return $this->_build_xml($data);
				break;
			case 'json':
				return json_decode($data);
				break;
		}
	}

	function _build_xml($data)
    {
        if ($this->type == 'xml')
        {
            $data = simplexml_load_string($data);

            $keys = array();

            foreach($data as $key => $value)
            {
                if ($key !== '@attributes')
                {
                    $keys[] = $key;
                }
            }
            if (count($keys) == 1)
            {
                return $data->$keys[0];
            }
        }
        return $data;
	}
	
    function _initialize($params = array())
    {
        if (count($params) > 0)
        {
            foreach ($params as $key => $val)
            {
                if (isset($this->$key))
                {
                    $this->$key = strtolower($val);
                }
            }
        }
    }
}
