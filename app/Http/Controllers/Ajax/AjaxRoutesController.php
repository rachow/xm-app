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

        if (empty($inputs['region'])) {
            $inputs['region'] = 'US';
        }

        /**
         * serious caching needed here!
         *  - Network latency
         *  - TCP Bottlenecks
         *  - Offload OHLCV data fetching from intermediary proxy.
         *      - Cache data to AWS RDS/RDBMS / NoSQL / Time-Series DB
         *      - Schedule processes to load data into DB
         *      - Track (TPS/QPS)
         *  - Any Broker?
        */

        $yahoo_api_url = config('app.yahoo_api_url'); 
        $yahoo_api_key = config('app.yahoo_api_key');

        $yahoo_url_parts = parse_url($yahoo_api_url);

        /*
        $response = Http::withHeaders([
            'X-RapidAPI-Key'  => $yahoo_api_key,
            'X-RapidAPI-Host' => $yahoo_url_parts['host'],
        ])->get($yahoo_api_url, [
            'symbol' => $inputs['symbol'],
            'region' => $inputs['region'],
        ]);
         */
        // lets skip the SSL certificate checks
        $response = Http::withoutVerifying()->withHeaders([
            'X-RapidAPI-Key'  => $yahoo_api_key,
            'X-RapidAPI-Host' => $yahoo_url_parts['host'],
        ])->get($yahoo_api_url, [
            'symbol' => $inputs['symbol'],
            'region' => $inputs['region'],
        ]);

        // Any transformer ?
        // https://fractal.thephpleague.com/transformers/

        $json = $response->json();
        return $this->successResponse($json);
    }

    private function getSymbolValidation(Request $request)
    {
        // we could create a Form Request to sparate the logics
        // for validation.

        $attribute_msgs = [
            'symbol.min'    => '3 letter symbol is required',
            'symbol.max'    => '3 letter symbol is required',
            'email.dns'     => ':attribute does not exist, according to MX records',
            'email.rfc'     => ':attribute does not exist or is invalid',
        ];

        // see - valcanbuild.tech/laravel-dns-email-validation
        // use $faker->freeEmail to get green unit tests for MX record checks!

        $validator = Validator::make($request->all(),[
            'symbol' => 'required|min:3|max:3',
            'startdate' => ['required', function($attribute, &$value, $fail){
                    if (preg_match("/(\d){2}\/(\d){2}\/(\d){4}/", $value)) {
                        $date = str_replace('/', '-', $value);
                        $date_parts = explode('-', $date);
                        $value = $date_parts[2] . '-' . $date_parts[1] . '-' . $date_parts[0]; 
                    } elseif (preg_match("/(\d){4}\/(\d){2}\/(\d){2}/", $value)) {
                        $date = str_replace('/', '-', $value);
                        $value = $date; 
                    } else {
                        $fail($attribute . ' is invalid format yyyy/mm/dd');
                    }
            }],
            'enddate' => [],
            'email' => 'required'
        ]);

        return $validator;
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
