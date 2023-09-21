<?php
/**
 *  @author: $rachow
 *  @copyright: XM App 2023
 *
 *  Handles all XHR Routes
*/

namespace App\Http\Controllers\Ajax;

use Log;
use Cache;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Ajax\AjaxController;

class AjaxRoutesController extends AjaxController
{
    /**
     * Grab the symbols historical data from API.
     *
     * @param  Illuminate\Http\Request
     * @return Illuminate\Http\JsonResponse
     */
    public function getSymbolHistoricalData(Request $request)
    {
        $inputs = $request->all();
        $validator = Validator::make([],[]);           
        if (empty($inputs['symbol'])) {
            $validator->errors()->add('symbol', 'you must provide a valid symbol.');
            $errors = $validator->errors();
            return $this->errorResponse($errors);
        }
    } 

    /**
     * Grab all the available symbols.
     * Load from cache where possible.
     */   
    public function getSymbols(Request $request)
    {
        $ssl = config('app.ssl_cert');
        $url = config('app.symbol_url');

        //$cache_dir = storage_path() . '/app/public';
        //$cache_symbol_url = 'http://localhost:9090/storage/' . $symbol_file;
        //$symbols = file_get_contents($cache_symbol_url);

        $symbols = false;
        $ttl = 3600; # 1 hour epoch

        if (Cache::has('symbols_data')) {
            $symbols = Cache::get('symbols_data');
        } else { 
            $response = Http::withOptions([
                'ssl_key' => (file_exists($ssl) && is_readable($ssl)) ? $ssl : '',
                'verify'  => false, // issue > ?
            ])->get($url);

            $symbols = $response->json();
            Cache::put('symbols_data', $symbols, $ttl);
        }

        return $this->successResponse($symbols);

    }

}
