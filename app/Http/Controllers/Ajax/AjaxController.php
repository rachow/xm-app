<?php
/*
*   @author: $rachow
* 	@copyright: XM App 2023
*
* 	XHR Requests handler
*/

namespace App\Http\Controllers\Ajax;

use Log;
use Auth;
use Exception;
use App\Traits\AjaxResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Classes\Singleton\RequestEventStorable;

class AjaxController extends Controller
{
	use AjaxResponse;

	/*
	* Creates an instance
	* @return void
	*/
	public function __construct($authorize = false)
    {
        $this->init($authorize);
	}

    /**
     * Allow child to invoke method basis.
     * @return mixed
    */
    protected function init($authorize = false)
    {
        $request = request();
        $this->__initialise($request, $authorize);        
    }

	/*
	* Initialise the Ajax Request
	* @param void
	*/
	private function __initialise(Request $request, $authorize)
	{
		/**
		 * ensure json is expected and not in console.
		 * added the get content type because our frontend beacon was fired and 
		 * server was choking on this! - $rachow
		*/
		if (!$request->expectsJson() && !($request->headers->get('content-type') == 'application/json') && !app()->runningInConsole()) {
			$error = 'Your request is invalid.';
			throw new Exception($error);
		}
		
		/*
		* authenticate requests unless overidden.
		*/
		if ((bool) $authorize) {
			$this->middleware('auth');
        }
        /**
         * we are in the phase to collect all XHR request
         * event data for logging service.
         */
        
        $rstore = RequestEventStorable::getStorableRequestEvent($request);
        Log::info($rstore);
        Log::info(RequestEventStorable::getStorableRequestIPGeoData($request));
	}
}

