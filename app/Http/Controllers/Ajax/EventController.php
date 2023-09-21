<?php
/**
 *  @author: $rachow
 *  @copyright: XM App 2023
 *
 *  Capture events for sending to logging hub.
 *
 *  todo:
 *      1. Every app will send events.
 *      2. Event capturing needs to be central and can be invoked by systems.
 *      3. REST API - '/api/_events' or '/api/v1/_events' ??
 *      4. Based on growth, dedicate as a separate service.
 */

namespace App\Http\Controllers\Ajax;

use DB;
use Log;
use Carbon\Carbon;
use Jenssegers\Agent\Agent;
use Illuminate\Http\Request;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use App\Http\Controllers\Ajax\AjaxController;

class EventController extends AjaxController
{
	// @var - authentication
	protected $auth = false;

	/*
	* Creates an instance.
	* @return void
	*/
	public function __construct()
	{
		parent::__construct($this->auth);
	}

	/*
	* Capture the events and push to hub
	* @param Illuminate\Http\Request
	*
	*/
	public function storeCapturedEvents(Request $request)
	{
		/**
		* collect what is essential, collect metrics, TTFB etc.
		*/

		$payload 	= $request->getContent();
		$payload_arr 	= json_decode($payload, true);
		$payload_log 	= array();
		
		if(!empty($payload_arr)){
			foreach($payload_arr as $key => $val)
				$payload_log[$key] = $val;
		}

		$referer = $request->headers->get('referer');
		$agent	 = new Agent();
		$browser = $agent->browser();
		$os	 = $agent->platform();
		$version = $agent->version($browser);
		$locale	 = $agent->languages();
		$device	 = $agent->device();
		$ismob	 = (bool) $agent->isMobile();
		$istab   = (bool) $agent->isTablet();
		$desktop = (bool) $agent->isDesktop();
		$robot	 = (bool) $agent->isRobot();
		$ipv4	 = $request->getClientIp();
		$ttl	 = Carbon::now()->getTimestamp();

		$utm_source	= $payload_arr['utm_source'] ?? ''; 
		$utm_medium	= $payload_arr['utm_medium'] ?? '';
		$utm_campaign	= $payload_arr['utm_campaign'] ?? '';
		$utm_content	= $payload_arr['utm_content'] ?? '';
		$utm_term	= $payload_arr['utm_term'] ?? '';

		$json_log = json_encode(array_merge([
			'referer' => $referer,
			'agent'	  => $browser,
			'version' => $version,
			'os'	  => $os,
			'ipv4'	  => $ipv4,
			'locale'  => $locale,
			'device'  => $device,
			'ismob'	  => $ismob,
			'istab'	  => $istab,
			'desktop' => $desktop,
			'ttl'	  => $ttl,
			'marketing' => [
				'utm_source'   	=> $utm_source,
				'utm_medium'   	=> $utm_medium,
				'utm_campaign' 	=> $utm_campaign,
				'utm_content'	=> $utm_content,
				'utm_term'	=> $utm_term,
			]
		], $payload_log));

		// control the log data
		if(isset($payload_log['errorBag']) || (isset($payload_log['source']) && $payload['source'] == 'api'))
		{
			//Log::debug(json_encode(json_decode($json_log, true), JSON_PRETTY_PRINT));
			
			$logger = new Logger('events');
			$format = new LineFormatter(null, 'Y-m-d H:i:s', true, true);
			$handle = new StreamHandler(storage_path('logs/events.log')); 
			$handle->setFormatter($format);
			$logger->pushHandler($handle, Logger::DEBUG);

			$logger->debug(json_encode(json_decode($json_log, true), JSON_PRETTY_PRINT));
		}

		// beacon not captured for timing analysis, but we can.
		// these can fill up quite easily.
		if(!isset($payload_log['errorBag']) && !empty($payload_log)){
			$logger = new Logger('timing');
			$format = new LineFormatter(null, 'Y-m-d H:i:s', true, true);
			$handle = new StreamHandler(storage_path('logs/timing.log')); 
			$handle->setFormatter($format);
			$logger->pushHandler($handle, Logger::DEBUG);
			$logger->debug(json_encode(json_decode($json_log, true), JSON_PRETTY_PRINT));
		}

		// send quick OK response.
		return response()->json([
			'message' => "OK",
		]);
	}
}
