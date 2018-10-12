<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use DB;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
    * Function Name : testFunction
    * Function Description : Test function
    *
    * @param : income
    *
    * @throws : Records into Log, if any error takes place during process.
    * @author : Murat OZENC - Optimum7.com <murat.ozenc@optimum7.com>
    * @return : status
    */
        
    public function testFunction () {
        
        $users = DB::connection('sqlsrv')->table('P21Play3.dbo.inv_mast')->select('inv_mast_uid')->take(10)->get();
        //$users = DB::connection('sqlsrv')->statement('SELECT inv_mast_uid, item_id, FROM P21Play3.dbo.inv_mast');;

        dd($users);

        /*
        try {

            $con = new \PDO("sqlsrv:Server=sql01.safewareinc.com;Database=P21Play3", "optimum7read", "sF+tfD3\Aa#gQPFyWIq&");
            $con->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            $datas = $con->exec('SELECT TOP 1 PERCENT * FROM P21Play3.dbo.inv_mast');

            foreach ($datas as $data){

                print_r($data);

            }
            
        } catch (\PDOException $e) {

            echo "No connection: " . $e->getMessage();
            exit;

        }
        */
    }
}
