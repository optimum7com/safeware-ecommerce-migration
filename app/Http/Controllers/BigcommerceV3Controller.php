<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Bigcommerce\Api\Client as Bigcommerce;

class BigCommerceV3Controller extends Controller
{
    protected $bigcommerce;

    public function __construct(Bigcommerce $bigcommerce)
    {
        $this->bigcommerce = $bigcommerce;
    }

    /*
    * returns Collection
    */
    public function getPriceList()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.bigcommerce.com/stores/8zcngt4nvy/v3/pricelists",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'X-Auth-Client: 43p57yc8nr285dymjn2exejibxn6ujw',
                'X-Auth-Token: 1yw8e3xqmplfcgocdqfqen7y88kudxy',
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            print_r(json_decode($response));
        }
    }

    public function setPriceList(){

        $data1 = [
            'data1' => 'value_1',
            'data2' => 'value_2',
        ];
        
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.bigcommerce.com/stores/8zcngt4nvy/v3/pricelists",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data2),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'X-Auth-Client: 43p57yc8nr285dymjn2exejibxn6ujw',
                'X-Auth-Token: 1yw8e3xqmplfcgocdqfqen7y88kudxy',
            ),
        ));
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        
        curl_close($curl);
        
        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            print_r(json_decode($response));
        }

    }
}
