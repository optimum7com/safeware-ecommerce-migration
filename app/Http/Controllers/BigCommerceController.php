<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Bigcommerce\Api\Client as Bigcommerce;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\CategoryHierarchy;
use App\Products;
use App\CountryCodes;
use App\ProductCategories;
use App\ProductOptions;
use App\Customer;
use App\Category;
use App\Item;
use App\InvMast;
use App\Brand;


class BigCommerceController extends Controller
{

    public function test(){

        $columns = DB::getSchemaBuilder()->getColumnListing('inv_mast');

        foreach ($columns as $column){
          echo Input::get($column) != null ? '<br>'.$column.' : '.Input::get($column):'';
        }

        dd($columns);
    }


    // Category functions

    public function createCategory(){

        $username = 'optimum7';
        $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
        $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
        $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

        Bigcommerce::configure(array(

            'store_url' => $store_url,
            'username'	=> $username,
            'api_key'	=> $API_Token

        ));

        Bigcommerce::failOnError();

        $identity = 1496;

        //$productRootCategories = ProductCategories::select('rc')->distinct()->get();

        //$categories = DB::table('job_contracter')->select('job_description')->groupBy('job_description')->where('exception',0)->take(3)->get();
        $categories = DB::table('job_contracter')->select(DB::raw('DISTINCT job_no'), 'job_no', 'job_description')->get();
        
        // echo '<pre>';
        // var_dump($categories);
        // echo '</pre>';

        echo count($categories);
        
        die();
        
        foreach ($categories as $category){

            //echo $category->job_no.'-'.(!empty($category->job_description) ? $category->job_description : 'N/A').'<br>';

            
            if ($category != null || $category != ""){

                /*
                 $lenght = strlen($category->job_no.'-'.(!empty($category->job_description) ? $category->job_description : $category->job_no.'NA'));

                 if($lenght>50){
                     echo '<br>'.$category->job_no.'-'.'<font color="red">'.$lenght.'</font>';
                 }
                */
                
                

                $categoryData = array(

                    'parent_id' => 1495,
                    'name' => $category->job_no.'-'.(!empty($category->job_description) ? $category->job_description : $category->job_no.'NA'),
                    'description' => '',
                    'sort_order' => 0,
                    'page_title' => (!empty($category->job_description) ? $category->job_description : $category->job_no.'NA'),
                    'meta_keywords' => '',
                    'meta_description' => '',
                    'layout_file' => 'category.html',
                    'url' => '/'.$this->slugCreator($category->job_no.'-'.(!empty($category->job_description) ? $category->job_description : $category->job_no.'NA')).'/'
                );

                //dd($categoryData);

                
                try {

                    $createCategory = Bigcommerce::createCategory((object)$categoryData);

                    if($createCategory){

                        $updateLocal = DB::table('job_contracter')->where('job_description',$category->job_description)->update(['BCCategoryId' => $identity]);
                        $identity++;

                    } else {

                        die ('There was an error on category adding process : '.$category->job_description);
                    }

                } catch (Bigcommerce\Api\Error $error) {

                    echo 'Error : '.$category->job_description;
                    echo '<br>';
                    print_r($e);
                    
                }
                
            } else {

                $updateLocalException = DB::table('job_contracter')->where('job_description',$category->job_description)->update(['exception' => 1]);

                if($updateLocalException){

                    echo 'There was a problem during excetion update<br>';
                }
            }
            
            /*
            $productPrimaryCategories = ProductCategories::select('pc')->distinct()->where('rc',$root->rc)->get();

            foreach ($productPrimaryCategories as $primary){

                if ( $primary->pc != null || $primary->pc != "" ){

                    echo '<br><br>&nbsp;&nbsp;&nbsp;'.$identity.'-'.$primary->pc.' (ParentID : '.$rootId.')<br>';

                    $updaterPc = ProductCategories::where('rc',$root->rc)->where('pc',$primary->pc)->update(['pc_parent_id' => $rootId, 'pc_id' => $identity]);

                    $primaryId = $identity;
                    $identity++;

                    /*
                    $categoryData = array(

                        'parent_id' => $rootId,
                        'name' => $primary->pc,
                        'description' => '',
                        'sort_order' => 0,
                        'page_title' => $primary->pc,
                        'meta_keywords' => $primary->pc,
                        'meta_description' => 'Information about '.$primary->pc. ' equipments.',
                        'layout_file' => 'category.html',
                        'url' => '/'.$this->slugCreator($root->rc).'/'.$this->slugCreator($primary->pc).'/'
                    );
                    
                    try {
    
                        if(Bigcommerce::createCategory($categoryData)){
    
                            $primaryId = $identity;
                            $identity++;
    
                        } else {
    
                            die ('There was an error on root category adding process : '.$primary->pc);
                        }
    
                    } catch (Exception $e) {
    
                        echo 'Error : '.$primary->pc;
                        echo '<br>';
                        print_r($e);
                        
                    }
                    
                }

                $productSecondaryCategories = ProductCategories::select('sc')->distinct()->where('rc',$root->rc)->where('pc',$primary->pc)->get();

                foreach ($productSecondaryCategories as $secondary){

                    if ( $secondary->sc != null || $secondary->sc != "" ){

                        echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$identity.'-'.$secondary->sc.' (ParentID : '.$primaryId.')';

                        $updaterSc = ProductCategories::where('rc',$root->rc)->where('pc',$primary->pc)->where('sc',$secondary->sc)->update(['sc_parent_id' => $primaryId, 'sc_id' => $identity]);

                        $secondaryId = $identity;
                        $identity++;

                        
                        $categoryData = array(

                            'parent_id' => $primaryId,
                            'name' => $secondary->sc,
                            'description' => '',
                            'sort_order' => 0,
                            'page_title' => $secondary->sc,
                            'meta_keywords' => $secondary->sc,
                            'meta_description' => 'Information about '.$secondary->sc. ' equipments.',
                            'layout_file' => 'category.html',
                            'url' => '/'.$this->slugCreator($root->rc).'/'.$this->slugCreator($primary->pc).'/'.$this->slugCreator($secondary->sc).'/'
                        );
                        
                        try {
        
                            if(Bigcommerce::createCategory($categoryData)){
        
                                $secondaryId = $identity;
                                $identity++;
        
                            } else {
        
                                die ('There was an error on root category adding process : '.$primary->pc);
                            }
        
                        } catch (Exception $e) {
        
                            echo 'Error : '.$secondary->sc;
                            echo '<br>';
                            print_r($e);
                            
                        }
                        
                    }
                }
            }*/
        }
    }

    public function getCategories(){

        $username = 'optimum7';
        $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
        $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
        $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

        Bigcommerce::configure(array(

            'store_url' => $store_url,
            'username'	=> $username,
            'api_key'	=> $API_Token

        ));

        Bigcommerce::failOnError();

        try {

            $categories = Bigcommerce::getCategories();

            if (count($categories) > 0){

                echo '<pre>';
                print_r($categories);
                echo '</pre>';

            } else {

                die('There is no category with this id'.$categoryId);
            }

        } catch(Bigcommerce\Api\Error $error) {

            echo $error->getCode();
            echo $error->getMessage();

        }
    }

    public function getSingleCategory(){

        $categoryId =Input::get('categoryId');

        if ($categoryId){

            $username = 'optimum7';
            $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
            $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
            $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

            Bigcommerce::configure(array(

                'store_url' => $store_url,
                'username'	=> $username,
                'api_key'	=> $API_Token

            ));

            Bigcommerce::failOnError();

            try {

                $category = Bigcommerce::getCategory($categoryId);

                echo '<pre>';
                print_r($category);
                echo '</pre>';

            } catch(Bigcommerce\Api\Error $error) {

                echo $error->getCode();
                echo $error->getMessage();

            }

        } else {

            die('There should be an ID value to get specific category');
        }
    }

    public function updateCategory(){
        //
    }

    public function deleteCategories(){


        $username = 'optimum7';
        $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
        $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
        $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';
        Bigcommerce::configure(array(
            'store_url' => $store_url,
            'username'	=> $username,
            'api_key'	=> $API_Token
        ));

        Bigcommerce::deleteAllCategories();
        Bigcommerce::deleteAllProducts();
    }

    public function deleteCategory(){

        $categoryId =Input::get('categoryId');

        if ($categoryId){

            $username = 'optimum7';
            $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
            $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
            $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

            Bigcommerce::configure(array(

                'store_url' => $store_url,
                'username'	=> $username,
                'api_key'	=> $API_Token

            ));

            Bigcommerce::failOnError();

            try {

                if (Bigcommerce::deleteCategory($categoryId)){

                    echo 'Category successfully deleted!';
                }

            } catch(Bigcommerce\Api\Error $error) {

                echo $error->getCode();
                echo $error->getMessage();

            }

        } else {

            die('There should be an ID value to delete category');
        }
    }

    
    // Customer Functions

    public function createCustomer(){

        $username = 'optimum7';
        $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
        $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
        $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

        Bigcommerce::configure(array(

            'store_url' => $store_url,
            'username'	=> $username,
            'api_key'	=> $API_Token

        ));

        Bigcommerce::failOnError();

        $customers = Customer::where('local_id','>',2762)->get();

        //dd($customers);

        $identity = 2776;

        foreach ($customers as $customer){

            if ($customer->Email == null || $customer->Email == " " || $customer->Email == ""){

            } else {

                if ($customer->Name == null || $customer->Name == " " || $customer->Name == ""){

                    $first_name = "N/A";
                    $second_name = "N/A";

                } else {

                    $full_name = $this->seperateNames($customer->Name);

                    $first_name = $full_name['first_name'];
                    $second_name = $full_name['second_name'];
                }

                
                /*
                echo $customer->CompanyName.'<br>';
                echo $full_name['first_name'].'<br>';
                echo $full_name['second_name'].'<br>';
                echo $customer->Email.'<br>';
                echo $customer->Phone.'<br>';
                echo $this->convertDateFromAmericanToStandard($customer->CreationDate).'<br>';
                echo date('Y-m-d H:i:s');
                echo '<hr>';
                */

                $customerDataArray = array(

                    'company'=>$customer->CompanyName,
                    'first_name'=>$first_name,//$full_name['first_name'],
                    'last_name'=>$second_name,//$full_name['second_name'],
                    'email'=>trim($customer->Email),
                    'phone'=>$customer->Phone
                    //'date_created'=>$this->convertDateFromAmericanToStandard($customer->CreationDate),
                    //'date_modified'=>date('Y-m-d H:i:s'),

                );

                try {

                    if (Bigcommerce::createCustomer($customerDataArray)){

                        $localIdUpdate = Customer::where('local_id', $customer->local_id)->update(['BCCustomerId' => $identity]);

                        $identity++;

                        /*
                        $customerAddressDataArray = array(

                            'customer_id'=>$identity,
                            'first_name'=>$full_name['first_name'],
                            'last_name'=>$full_name['second_name'],
                            'company'=>$customer->CompanyName,
                            'street_1'=>$customer->Addr1,
                            'street_2'=>$customer->Addr2,
                            'city'=>$customer->City,
                            'state'=>$customer->State,
                            'zip'=>$customer->Zip,
                            'country'=>$this->countryCodeToName($customer->Country),
                            //'country_iso2'=>$customer->Country,
                            'phone'=>$customer->Phone,
                            'address_type'=>strtolower($customer->ShipAddressType)
                        );
                        */
                        /*
                        try{
                        
                            if (Bigcommerce::createCustomerAddress($identity, $customerAddressDataArray)){

                                $identity++;
                            }
                
                            //$identity++;

                            print_r($customerDataArray);
                            echo '<hr>';
                            print_r($customerAddressDataArray);
                            echo '<hr><hr><hr><br>';

                        } catch (Exception $e) {

                            echo 'Error : '.$secondary->sc;
                            echo '<br>';
                            print_r($e);
                            
                        }*/

                    }

                } catch (Exception $e) {

                    echo 'Error : '.$secondary->sc;
                    echo '<br>';
                    print_r($e);
                    
                }
                
            }
        }

        //die();


        //dd($customers);
        
    }

    public function getCustomers(){

        $username = 'optimum7';
        $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
        $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
        $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

        Bigcommerce::configure(array(

            'store_url' => $store_url,
            'username'	=> $username,
            'api_key'	=> $API_Token

        ));

        Bigcommerce::failOnError();

        try {

            $customers = Bigcommerce::getCustomers();

            if (count($customers) > 0){

                echo '<pre>';
                print_r($customers);
                echo '</pre>';

            } else {

                die('There is no customer');
            }

        } catch(Bigcommerce\Api\Error $error) {

            echo $error->getCode();
            echo $error->getMessage();

        }
    }

    public function getSingleCustomer(){

        $customerId =Input::get('customerId');

        if ($customerId){

            $username = 'optimum7';
            $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
            $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
            $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

            Bigcommerce::configure(array(

                'store_url' => $store_url,
                'username'	=> $username,
                'api_key'	=> $API_Token

            ));

            Bigcommerce::failOnError();

            try {

                $customer = Bigcommerce::getCustomer($customerId);

                echo '<pre>';
                print_r($customer);
                echo '</pre>';

            } catch(Bigcommerce\Api\Error $error) {

                echo $error->getCode();
                echo $error->getMessage();

            }

        } else {

            die('There should be an ID value to get specific customer');
        }
    }

    public function updateCustomer(){
        //
    }

    public function deleteCustomers(){


        $username = 'optimum7';
        $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
        $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
        $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

        Bigcommerce::configure(array(
            'store_url' => $store_url,
            'username'	=> $username,
            'api_key'	=> $API_Token
        ));

        Bigcommerce::deleteAllCustomers();
    }

    public function deleteCustomer(){

        $customerId =Input::get('customerId');

        if ($customerId){

            $username = 'optimum7';
            $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
            $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
            $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

            Bigcommerce::configure(array(

                'store_url' => $store_url,
                'username'	=> $username,
                'api_key'	=> $API_Token

            ));

            Bigcommerce::failOnError();

            try {

                if (Bigcommerce::deleteCustomer($customerId)){

                    echo 'Category successfully deleted!';
                }

            } catch(Bigcommerce\Api\Error $error) {

                echo $error->getCode();
                echo $error->getMessage();

            }

        } else {

            die('There should be an ID value to delete customer');
        }
    }


    // Customer Address Functions

    public function createCustomerAddress(){

        $username = 'optimum7';
        $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
        $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
        $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

        Bigcommerce::configure(array(

            'store_url' => $store_url,
            'username'	=> $username,
            'api_key'	=> $API_Token

        ));

        Bigcommerce::failOnError();

        $customers = Customer::where('local_id','>',3915)->get();

        foreach ($customers as $customer){

            if ($customer->Zip == NULL || $customer->Zip == '' || $customer->Zip == ' '){ $customer->Zip = '00000'; }

            if ($customer->State == '' && $customer->Country == ''){ continue; }

            if ($customer->Adrr1 == '' || $customer->Addr1 == ' ' || $customer->Addr1 == NULL){ $customer->Addr1 = 'N/A';}

            if ($customer->Phone == '' || $customer->Phone == ' ' || $customer->Phone == NULL){ $customer->Phone = 'N/A';}

            if ($customer->ShipAddressType == '' || $customer->ShipAddressType == ' ' || $customer->ShipAddressType == NULL){ $customer->ShipAddressType = 'commercial';}

            $customerData = Bigcommerce::getCustomer($customer->BCCustomerId);

            $customerAddressDataArray = array(

                'customer_id'	=>$customerData->id,
                'first_name'	=>$customerData->first_name,
                'last_name'	=>$customerData->last_name,
                'company'	=>$customerData->company,
                'street_1'	=>$customer->Addr1,
                'street_2'	=>$customer->Addr2,
                'city'	=>$customer->City,
                'state'	=>$customer->State,
                'zip'	=>$customer->Zip,
                'country'	=>$this->countryCodeToName($customer->Country),
                //'country_iso2'	=>$customer->Country,
                'phone'	=>$customer->Phone,
                'address_type'	=>strtolower($customer->ShipAddressType)

            );

            //dd($customerAddressDataArray);

            try{
            
                if (Bigcommerce::createCustomerAddress($customerData->id, $customerAddressDataArray)){

                    echo 'Tamamlandı : '.$customer->name.' - '.$customer->id;
                    echo '<br>';

                } else {

                    echo 'TAMAMLANAMADI : '.$customer->name.' - '.$customer->id;
                    echo '<br>';
                }

            } catch(Bigcommerce\Api\Error $error) {
                echo $error->getCode();
                echo $error->getMessage();
            }
        }
    }

    public function getCustomerAddresses(){

        $customerId = Input::get('customerId');

        $username = 'optimum7';
        $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
        $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
        $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

        Bigcommerce::configure(array(

            'store_url' => $store_url,
            'username'	=> $username,
            'api_key'	=> $API_Token

        ));

        Bigcommerce::failOnError();

        try {

            $customerAddresses = Bigcommerce::getCustomerAddresses($customerId);

            if (count($customerAddresses) > 0){

                echo '<pre>';
                print_r($customerAddresses);
                echo '</pre>';

            } else {

                die('There is no address for this custumer');
            }

        } catch(Bigcommerce\Api\Error $error) {

            echo $error->getCode();
            echo $error->getMessage();

        }

    }

    public function getCustomerAddressCheck(){

        $username = 'optimum7';
        $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
        $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
        $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

        Bigcommerce::configure(array(

            'store_url' => $store_url,
            'username'	=> $username,
            'api_key'	=> $API_Token

        ));

        Bigcommerce::failOnError();

        $customers = Customer::where('local_id','>',0)->take(10)->get();

        foreach ($customers as $customer){

            $customerData = Bigcommerce::getCustomer($customer->BCCustomerId);

            // check customer addresses

            $customerAddresses = Bigcommerce::getCustomerAddresses(350);

            //dd($customerAddresses);

            if (count($customerAddresses) == 0){

                //die('none');

                echo 'There is no address data for user : LI->'.$customer->local_id.' BCID->'.$customer->BCCustomerId.'<br>';

            } else if (count($customerAddresses) == 1){

                //die('single');
                // update local DB table with address data on BC
                $customerToUpdate = Customer::where('local_id',$customer->local_id)->first();
                $customerToUpdate->AddressId = $customerAddresses[0]->id;

                if($customerToUpdate->save()){

                    echo 'Local DB Address Data Has Been Updated LI->'.$customer->local_id.'<br>';

                } else {

                    echo 'Local DB Address Data Update Failed LI->'.$customer->local_id.'<br>';
                }


            } else {

                die('multi');

                // update local DB table with address data on BC
                $customerToUpdate = Customer::where('local_id',$customer->local_id)->first();
                $customerToUpdate->AddressId = $customerAddresses[0]->id;

                if($customerToUpdate->save()){

                    echo 'Local DB Address Data Has Been Updated LI->'.$customer->local_id.'<br>';

                    try {

                        deleteCustomerAddress($customerData->id, $customerAddresses[1]->id);
    
                    } catch(Bigcommerce\Api\Error $error) {
    
                        echo $error->getCode();
                        echo $error->getMessage();
    
                    }

                } else {

                    echo 'Local DB Address Data Update Failed LI->'.$customer->local_id.'<br>';
                }
            }
        }
    }


    // Brand Functions

    public function createBrand(){

        $username = 'optimum7';
        $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
        $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
        $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

        Bigcommerce::configure(array(

            'store_url' => $store_url,
            'username'	=> $username,
            'api_key'	=> $API_Token

        ));

        Bigcommerce::failOnError();

        $brands = Brand::where('brand_id','>',902)->get();

        //dd($brands);

        $identity = 903;

        foreach ($brands as $brand){

            if ($brand->brand_name == null || $brand->brand_name == " " || $brand->brand_name == ""){
                continue;
            } else {

                $brandDataArray = array(

                    'name'=>$brand->brand_name,
                    'page_title'=>$brand->brand_name,
                    'meta_keywords'=>$brand->brand_name,
                    'meta_description'=>'Information about the brand '.$brand->brand_name,
                    'image_file'=>'',
                    'search_keywords'=>$brand->brand_name

                );

                try {

                    if (Bigcommerce::createBrand($brandDataArray)){

                        $localIdUpdate = Brand::where('brand_id', $brand->brand_id)->update(['BCBrandId' => $identity]);

                        if ($localIdUpdate){ $identity++; }

                    }

                } catch(Bigcommerce\Api\Error $error) {
                    echo $error->getCode();
                    echo $error->getMessage();
                }
            }
            //exit;
        }
    }

    public function getBrand(){

        $username = 'optimum7';
        $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
        $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
        $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

        Bigcommerce::configure(array(

            'store_url' => $store_url,
            'username'	=> $username,
            'api_key'	=> $API_Token

        ));

        Bigcommerce::failOnError();

        try {

            $brand = Bigcommerce::getBrand();

            if (count($customers) > 0){

                echo '<pre>';
                print_r($customers);
                echo '</pre>';

            } else {

                die('There is no customer');
            }

        } catch(Bigcommerce\Api\Error $error) {

            echo $error->getCode();
            echo $error->getMessage();

        }
    }

    public function getSingleBrand(){

        $customerId =Input::get('customerId');

        if ($customerId){

            $username = 'optimum7';
            $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
            $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
            $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

            Bigcommerce::configure(array(

                'store_url' => $store_url,
                'username'	=> $username,
                'api_key'	=> $API_Token

            ));

            Bigcommerce::failOnError();

            try {

                $customer = Bigcommerce::getCustomer($customerId);

                echo '<pre>';
                print_r($customer);
                echo '</pre>';

            } catch(Bigcommerce\Api\Error $error) {

                echo $error->getCode();
                echo $error->getMessage();

            }

        } else {

            die('There should be an ID value to get specific customer');
        }
    }

    public function updateBrand(){
        //
    }

    public function deleteAllBrands(){


        $username = 'optimum7';
        $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
        $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
        $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

        Bigcommerce::configure(array(
            'store_url' => $store_url,
            'username'	=> $username,
            'api_key'	=> $API_Token
        ));

        Bigcommerce::deleteAllCustomers();
    }

    public function deleteBrand(){

        $customerId =Input::get('customerId');

        if ($customerId){

            $username = 'optimum7';
            $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
            $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
            $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

            Bigcommerce::configure(array(

                'store_url' => $store_url,
                'username'	=> $username,
                'api_key'	=> $API_Token

            ));

            Bigcommerce::failOnError();

            try {

                if (Bigcommerce::deleteCustomer($customerId)){

                    echo 'Category successfully deleted!';
                }

            } catch(Bigcommerce\Api\Error $error) {

                echo $error->getCode();
                echo $error->getMessage();

            }

        } else {

            die('There should be an ID value to delete customer');
        }
    }


    // Product Functions

    public function createProduct(){

        $username = 'optimum7';
        $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
        $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
        $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

        Bigcommerce::configure(array(

            'store_url' => $store_url,
            'username'	=> $username,
            'api_key'	=> $API_Token

        ));

        Bigcommerce::failOnError();

        // main_item_id değerinde ANA barınanları seç.
        // item_id değeri varsa, o item_idye göre sorgula
        // yoksa bir sonraki id değerini alıp, yola devam et.

        $products = Products::where('product_id','>',0)->where('main_item_id', 'like', '%(ANA)%')->where('is_inserted_to_bc',0)->where('not_to_insert',0)->get();

        dd($products);

        $identity = 11933;

        foreach ($products as $product){

            //dd($product);

            $directProductId = '';
            $directProductItemId = '';
            $directProductItemDesc = '';
            $directProductFullWebCat = '';
            $directProductSuppName = '';

            if ($product->option_set == '' || $product->option_set == ' ' || $product->option_set == NULL ){ // ürün
                //echo 'ürün';

                //echo $product->product_id. ' bu direkt bir üründür : '.trim(str_replace('(ANA)','',$product->main_item_id)).'<br>';

                $directProductId = $product->product_id;
                $directProductItemId = trim(str_replace('(ANA)','',$product->main_item_id));
                $directProductItemDesc = $product->item_desc;
                $directProductFullWebCat = $product->new_full_web_category;
                $directProductSuppName = $product->supplier_name;

                // does product inserted before
                if ($directProductItemDesc != ''){

                    $isInsertedBeforeCount = Products::where('is_inserted_to_bc',1)->where('item_desc',$directProductItemDesc)->where('main_item_id', 'like', '%(ANA)%')->count();

                    $isInsertedBeforeData = Products::where('is_inserted_to_bc',1)->where('item_desc',$directProductItemDesc)->where('main_item_id', 'like', '%(ANA)%')->first();

                    if ($isInsertedBeforeCount > 0){

                        echo $directProductId.' idli ürün '. $directProductItemDesc . ' açıklamasına sahip.<br>';
                        echo $isInsertedBeforeData->product_id.' idli ürün '. $isInsertedBeforeData->item_desc . ' açıklamasına sahip.<br><br><br>';
                        echo '<hr>';

                        $localIdUpdate = Products::where('product_id', $product->product_id)->update(['duplicate_of'=>$isInsertedBeforeData->product_id]);
                        continue;

                    } else {

                        if($directProductFullWebCat == 'New Rescue cats and subs'){

                            $categoryIdOfProduct = 1398;

                        } else if ($directProductFullWebCat == 'Law Enforcement|CDU and Public Order|Outer Garments'){

                            $categoryIdOfProduct = 1399;

                        } else if ($directProductFullWebCat == 'Hazardous Materials Response'){

                            $categoryIdOfProduct = 1400;

                        } else if ($directProductFullWebCat == 'Industrial|Hand Protection|General Purpose Gloves - Coated'){

                            $categoryIdOfProduct = 1401;

                        } else if ($directProductFullWebCat == 'Law Enforcement|CDU and Public Order|Identification'){

                            $categoryIdOfProduct = 1402;

                        } else if ($directProductFullWebCat == 'Industrial|Emergency Responder|Accessories'){

                            $categoryIdOfProduct = 1404;

                        } else {

                            // Kategori
                            $categoryOfProduct = DB::table('product_categories_pure')->where('fp',$directProductFullWebCat)->first();

                            if ($categoryOfProduct->rc_id != 0  && $categoryOfProduct->pc_id != 0 && $categoryOfProduct->sc_id != 0){ $categoryIdOfProduct = $categoryOfProduct->sc_id;}
                            else if ($categoryOfProduct->rc_id != 0  && $categoryOfProduct->pc_id != 0 && $categoryOfProduct->sc_id == 0){ $categoryIdOfProduct = $categoryOfProduct->pc_id;}
                            else if ($categoryOfProduct->rc_id != 0  && $categoryOfProduct->pc_id == 0 && $categoryOfProduct->sc_id == 0){ $categoryIdOfProduct = $categoryOfProduct->pc_id;}
                            else {echo 'KATEGORİ HATA<br>'; dd($product);}

                        }

                        // Marka
                        if($directProductSuppName == '-' || $directProductSuppName == '' || $directProductSuppName == ' ' || $directProductSuppName == NULL){

                            $brandIdOfProduct = 0;

                        } else {

                            $brandOfProduct = DB::table('brands')->where('brand_name',$directProductSuppName)->first();
                            $brandIdOfProduct = $brandOfProduct->BCBrandId;

                        }

                        // Fiyat

                        $priceProduct = DB::table('inv_mast')->where('item_id',$directProductItemId)->first();

                        if (!is_null($priceProduct)){
                            $priceOfProduct = $priceProduct->price1;
                        }
                        else {
                            $priceOfProduct = 1;
                        }

                        if ($priceOfProduct>0){$currentPrice = $priceOfProduct;}
                        else {$currentPrice = 1;}

                        $productDataArray = array(

                            'name'=>$directProductItemDesc,
                            'type'=>'physical',
                            'sku'=>$directProductItemId,
                            'description'=>$directProductItemDesc,
                            'is_visible'=>true,
                            'weight' => '1.0000',
                            'width' => '1.0000',
                            'height' => '1.0000',
                            'depth' => '1.0000',
                            'price' => $currentPrice,
                            'cost_price' => '0.0000',
                            'retail_price' => '0.0000',
                            'sale_price' => '0.0000',
                            'availability' => 'available',
                            'categories'=>[$categoryIdOfProduct],
                            'brand_id'=> $brandIdOfProduct

                        );

                        //dd($productDataArray);

                        try {

                            $insertProductToBC = Bigcommerce::createProduct($productDataArray);

                            if ($insertProductToBC){

                                $localIdUpdate = Products::where('product_id', $product->product_id)->update(['BCProductId' => $identity, 'is_inserted_to_bc'=>1]);

                                if ($localIdUpdate){ $identity++; }

                            } else {

                                $error = Bigcommerce::getLastError();
                                echo $error->code;
                                echo $error->message;

                            }

                        } catch(Bigcommerce\Api\Error $error) {

                            echo $error->getCode();
                            echo $error->getMessage();

                        }

                    }
                }

            } else { // option

                //echo 'option';

                $otherProduct = Products::where('product_id','=',1+$product->product_id)->first();

                //dd($otherProduct);

                $directProductId = $otherProduct->product_id;
                $directProductItemId = $otherProduct->item_id;
                $directProductItemDesc = $otherProduct->item_desc;
                $directProductFullWebCat = $otherProduct->new_full_web_category;
                $directProductSuppName = $otherProduct->supplier_name;

                //echo $product->product_id. ' bu optionları olan bir üründür, bir sonraki ürün item_id değeri : '.$otherProduct->item_id.'<br>';

                // does product inserted before
                // does product inserted before
                if ($directProductItemDesc != ''){
                    $isInsertedBeforeCount = Products::where('is_inserted_to_bc',1)->where('item_desc',$directProductItemDesc)->where('main_item_id', 'like', '%(ANA)%')->count();

                    $isInsertedBeforeData = Products::where('is_inserted_to_bc',1)->where('item_desc',$directProductItemDesc)->where('main_item_id', 'like', '%(ANA)%')->first();

                    if ($isInsertedBeforeCount > 0){

                        echo $directProductId.' idli ürün '. $directProductItemDesc . ' açıklamasına sahip.<br>';
                        echo $isInsertedBeforeData->product_id.' idli ürün '. $isInsertedBeforeData->item_desc . ' açıklamasına sahip.<br><br><br>';
                        echo '<hr>';

                        $localIdUpdate = Products::where('product_id', $product->product_id)->update(['duplicate_of'=>$isInsertedBeforeData->product_id]);
                        continue;

                    } else {

                        if($directProductFullWebCat == 'New Rescue cats and subs'){

                            $categoryIdOfProduct = 1398;

                        } else if ($directProductFullWebCat == 'Law Enforcement|CDU and Public Order|Outer Garments'){

                            $categoryIdOfProduct = 1399;

                        } else if ($directProductFullWebCat == 'Hazardous Materials Response'){

                            $categoryIdOfProduct = 1400;

                        } else if ($directProductFullWebCat == 'Industrial|Hand Protection|General Purpose Gloves - Coated'){

                            $categoryIdOfProduct = 1401;

                        } else if ($directProductFullWebCat == 'Law Enforcement|CDU and Public Order|Identification'){

                            $categoryIdOfProduct = 1402;

                        } else if ($directProductFullWebCat == 'Industrial|Emergency Responder|Accessories'){

                            $categoryIdOfProduct = 1404;

                        } else {

                            // Kategori
                            $categoryOfProduct = DB::table('product_categories_pure')->where('fp',$directProductFullWebCat)->first();

                            if ($categoryOfProduct->rc_id != 0  && $categoryOfProduct->pc_id != 0 && $categoryOfProduct->sc_id != 0){ $categoryIdOfProduct = $categoryOfProduct->sc_id;}
                            else if ($categoryOfProduct->rc_id != 0  && $categoryOfProduct->pc_id != 0 && $categoryOfProduct->sc_id == 0){ $categoryIdOfProduct = $categoryOfProduct->pc_id;}
                            else if ($categoryOfProduct->rc_id != 0  && $categoryOfProduct->pc_id == 0 && $categoryOfProduct->sc_id == 0){ $categoryIdOfProduct = $categoryOfProduct->pc_id;}
                            else {echo 'KATEGORİ HATA<br>'; dd($product);}

                        }

                        // Marka
                        if($directProductSuppName == '-' || $directProductSuppName == '' || $directProductSuppName == ' ' || $directProductSuppName == NULL){

                            $brandIdOfProduct = 0;

                        } else {

                            $brandOfProduct = DB::table('brands')->where('brand_name',$directProductSuppName)->first();
                            $brandIdOfProduct = $brandOfProduct->BCBrandId;

                        }

                        // Fiyat

                        $priceProduct = DB::table('inv_mast')->where('item_id',$directProductItemId)->first();

                        if (!is_null($priceProduct)){
                            $priceOfProduct = $priceProduct->price1;
                        }
                        else {
                            $priceOfProduct = 1;
                        }

                        if ($priceOfProduct>0){$currentPrice = $priceOfProduct;}
                        else {$currentPrice = 1;}

                        $productDataArray = array(

                            'name'=>$directProductItemDesc,
                            'type'=>'physical',
                            'sku'=>$directProductItemId,
                            'description'=>$directProductItemDesc,
                            'is_visible'=>true,
                            'weight' => '1.0000',
                            'width' => '1.0000',
                            'height' => '1.0000',
                            'depth' => '1.0000',
                            'price' => $currentPrice,
                            'cost_price' => '0.0000',
                            'retail_price' => '0.0000',
                            'sale_price' => '0.0000',
                            'availability' => 'available',
                            'categories'=>[$categoryIdOfProduct],
                            'brand_id'=> $brandIdOfProduct

                        );

                        //dd($productDataArray);

                        try {

                            $insertProductToBC = Bigcommerce::createProduct($productDataArray);

                            if ($insertProductToBC){

                                $localIdUpdate = Products::where('product_id', $product->product_id)->update(['BCProductId' => $identity, 'is_inserted_to_bc'=>1]);

                                if ($localIdUpdate){ $identity++; }

                            } else {

                                $error = Bigcommerce::getLastError();
                                echo $error->code;
                                echo $error->message;

                            }

                        } catch(Bigcommerce\Api\Error $error) {

                            echo $error->getCode();
                            echo $error->getMessage();

                        }

                    }
                }
            }

            //exit;
        }
    }

    public function updateProduct(){

        $start = Input::get('start');

        if($start == 1){

            $username = 'optimum7';
            $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
            $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
            $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

            Bigcommerce::configure(array(

                'store_url' => $store_url,
                'username'	=> $username,
                'api_key'	=> $API_Token

            ));

            Bigcommerce::failOnError();

            $products = ProductOptions::where('BCProductId','!=',null)->where('is_inserted_to_bc',1)->where('BCOptionSetId','!=',0)->get();
            //$products = ProductOptions::where('BCProductId',9919)->get();

            //echo count($products);

            //die();

            foreach ($products as $product){

                $currentProduct = BigCommerce::getProduct($product->BCProductId);

                //if (isset($currentProduct->price) && ($currentProduct->price == "1" || $currentProduct->price == "1.0000")){
                    //echo $currentProduct->price.'<br>';
                    /*
                    $updateObject = array(
                        "availability" => "disabled",
                        "is_price_hidden" => true,
                        "price_hidden_label" => "Call for Price : 800-331-6707"
                    );
                    */

                    $updateObject = array(
                        "option_set_id"=>$product->BCOptionSetId
                    );

                    try{

                        $updateStatus = BigCommerce::updateProduct($product->BCProductId, (object)$updateObject);

                        if ($updateStatus){
                            echo 'update successful '.$product->BCProductId.'<br>';
                        } else {
                            echo 'update FAILED '.$product->BCProductId.'<br>';
                        }

                    } catch(Bigcommerce\Api\Error $error) {

                        echo $error->getCode();
                        echo $error->getMessage();

                    }

                //} //else {
                   //echo 'N/A<br>';
                //}
            }
        }
    }

    public function deleteProduct(){

        $productId =Input::get('productId');

        if ($productId){

            $username = 'optimum7';
            $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
            $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
            $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

            Bigcommerce::configure(array(

                'store_url' => $store_url,
                'username'	=> $username,
                'api_key'	=> $API_Token

            ));

            Bigcommerce::failOnError();

            try {

                if (Bigcommerce::deleteProduct($productId)){

                    echo 'Product successfully deleted!';
                }

            } catch(Bigcommerce\Api\Error $error) {

                echo $error->getCode();
                echo $error->getMessage();

            }

        } else {

            die('There should be an ID value to delete product');
        }
    }

    public function getProducts(){

        $username = 'optimum7';
        $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
        $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
        $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

        Bigcommerce::configure(array(

            'store_url' => $store_url,
            'username'	=> $username,
            'api_key'	=> $API_Token

        ));

        Bigcommerce::failOnError();

        try {

            $products = Bigcommerce::getProducts();

            if (count($products) > 0){

                echo '<pre>';
                print_r($products);
                echo '</pre>';

            } else {

                die('There is no product');
            }

        } catch(Bigcommerce\Api\Error $error) {

            echo $error->getCode();
            echo $error->getMessage();

        }
    }

    public function getSingleProduct(){

        $productId =Input::get('productId');

        if ($productId){

            $username = 'optimum7';
            $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
            $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
            $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

            Bigcommerce::configure(array(

                'store_url' => $store_url,
                'username'	=> $username,
                'api_key'	=> $API_Token

            ));

            Bigcommerce::failOnError();

            try {

                $product = Bigcommerce::getProduct($productId);

                echo '<pre>';
                print_r($product);
                echo '</pre>';

            } catch(Bigcommerce\Api\Error $error) {

                echo $error->getCode();
                echo $error->getMessage();

            }

        } else {

            die('There should be an ID value to get specific category');
        }
    }


    // Useful functions

    public function slugCreator($text){

        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        // remove paranthesis
        $text = str_replace(array('(',')'),array('-',''), $text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;

    }

    public function convertDateFromAmericanToStandard($date){

        $dateTimeDataArray = explode(" ",$date);

        $currentDate = $dateTimeDataArray[0];
        $currentTime = $dateTimeDataArray[1];
        $amOrPm = $dateTimeDataArray[2]; 

        $dateData = explode("/",$currentDate);
        $newDate = $dateData[2].'-'.$dateData[0].'-'.$dateData[1];

        if($amOrPm == 'PM'){

            $timeData = explode(":",$currentTime);
            $newTime = ($timeData[0]+12).':'.$timeData[1].':'.$timeData[2];
        }

        return $newDate.' '.$newTime;
    }

    public function seperateNames($name){

        for ($i=0; $i<5; $i++){

            $name = str_replace("  "," ",$name);
        }

        $currentNameArray = explode(" ",$name);

        if (count($currentNameArray) > 2){

            $first_name = $currentNameArray[0].' '.$currentNameArray[1];
            $second_name = $currentNameArray[2];

        } else if (count($currentNameArray) == 2){

            $first_name = $currentNameArray[0];
            $second_name = $currentNameArray[1];

        } else if (count($currentNameArray) == 1){

            $first_name = $currentNameArray[0];
            $second_name = $currentNameArray[0];

        } else {

            return 'there is no name!';
            exit;
        }

        $returnData = array(
            'first_name'=>$first_name,
            'second_name'=>$second_name
        );

        return $returnData;

    }

    public function countryCodeToName($code){

        $countryData = CountryCodes::where('alpha2',trim($code))->first();

        return $countryData->country;
    }


    // Option functions

    public function getOptions($filter = array()){

        $username = 'optimum7';
        $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
        $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
        $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

        Bigcommerce::configure(array(

            'store_url' => $store_url,
            'username'	=> $username,
            'api_key'	=> $API_Token

        ));

        Bigcommerce::failOnError();

        try {

            $options = Bigcommerce::getOptions($filter);

            dd($options);

        } catch(Bigcommerce\Api\Error $error) {

            echo $error->getCode();
            echo $error->getMessage();

        }

        //$filter = Filter::create($filter);
        //return self::getCollection('/options' . $filter->toQuery(), 'Option');
    }

    public function createOption($object){

        $username = 'optimum7';
        $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
        $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
        $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

        Bigcommerce::configure(array(

            'store_url' => $store_url,
            'username'	=> $username,
            'api_key'	=> $API_Token

        ));

        Bigcommerce::failOnError();

        try {

            $createOption = Bigcommerce::createOption($object);

            if ($createOption){

                echo 'Option has been created!';

            } else {

                echo 'Option creation failed!';

            }

        } catch(Bigcommerce\Api\Error $error) {

            echo $error->getCode();
            echo $error->getMessage();

        }

        //return self::createResource('/options', $object);
    }

    public function updateOption($id, $object){

        $username = 'optimum7';
        $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
        $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
        $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

        Bigcommerce::configure(array(

            'store_url' => $store_url,
            'username'	=> $username,
            'api_key'	=> $API_Token

        ));

        Bigcommerce::failOnError();

        try {

            $updateOption = Bigcommerce::updateOption($id, $object);

            if ($updateOption){

                echo 'Option has been updated!';

            } else {

                echo 'Option update failed!';
                
            }

        } catch(Bigcommerce\Api\Error $error) {

            echo $error->getCode();
            echo $error->getMessage();

        }

        //return self::updateResource('/options/' . $id, $object);
    }

    public function getOptionsCount(){

        $username = 'optimum7';
        $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
        $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
        $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

        Bigcommerce::configure(array(

            'store_url' => $store_url,
            'username'	=> $username,
            'api_key'	=> $API_Token

        ));

        Bigcommerce::failOnError();

        try {

            $getOptionsCount = Bigcommerce::getOptionsCount();

            echo 'Options Count : '.$getOptionsCount;

        } catch(Bigcommerce\Api\Error $error) {

            echo $error->getCode();
            echo $error->getMessage();

        }

        //return self::getCount('/options/count');
    }

    public function getOption(){

        $id = Input::get('id');

        $username = 'optimum7';
        $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
        $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
        $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

        Bigcommerce::configure(array(

            'store_url' => $store_url,
            'username'	=> $username,
            'api_key'	=> $API_Token

        ));

        Bigcommerce::failOnError();

        try {

            $getOption = Bigcommerce::getOption($id);

            dd($getOption);

        } catch(Bigcommerce\Api\Error $error) {

            echo $error->getCode();
            echo $error->getMessage();

        }

        //return self::getResource('/options/' . $id, 'Option');
    }

    public function deleteOption(){

        $id = Input::get('id');

        $username = 'optimum7';
        $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
        $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
        $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

        Bigcommerce::configure(array(

            'store_url' => $store_url,
            'username'	=> $username,
            'api_key'	=> $API_Token

        ));

        Bigcommerce::failOnError();

        try {

            $deleteOption = Bigcommerce::deleteOption($id);

           if ($deleteOption){

                echo 'Option has been deleted!';

           } else {

                echo 'Option deletion failed';

           }

        } catch(Bigcommerce\Api\Error $error) {

            echo $error->getCode();
            echo $error->getMessage();

        }

        //return self::deleteResource('/options/' . $id);
    }

    public function getOptionValue(){

        $id = Input::get('id');
        $option_id = Input::get('option_id');

        $username = 'optimum7';
        $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
        $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
        $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

        Bigcommerce::configure(array(

            'store_url' => $store_url,
            'username'	=> $username,
            'api_key'	=> $API_Token

        ));

        Bigcommerce::failOnError();

        try {

            $getOptionValue = Bigcommerce::getOptionValue($option_id, $id);

           dd($getOptionValue);

        } catch(Bigcommerce\Api\Error $error) {

            echo $error->getCode();
            echo $error->getMessage();

        }

        //return self::getResource('/options/' . $option_id . '/values/' . $id, 'OptionValue');
    }

    public function getOptionValues(){

        $optionId = Input::get('optionId');

        $username = 'optimum7';
        $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
        $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
        $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

        Bigcommerce::configure(array(

            'store_url' => $store_url,
            'username'	=> $username,
            'api_key'	=> $API_Token

        ));

        Bigcommerce::failOnError();

        try {

            $getOptionValues = Bigcommerce::getOptionValues((object)array('option_id'=>$optionId));

            dd($getOptionValues);

        } catch(Bigcommerce\Api\Error $error) {

            echo $error->getCode();
            echo $error->getMessage();

        }

        //$filter = Filter::create($filter);
        //return self::getCollection('/options/values' . $filter->toQuery(), 'OptionValue');
    }

    public function getOptionSets($filter = array()){

        $username = 'optimum7';
        $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
        $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
        $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

        Bigcommerce::configure(array(

            'store_url' => $store_url,
            'username'	=> $username,
            'api_key'	=> $API_Token

        ));

        Bigcommerce::failOnError();

        try {

            $getOptionSets = Bigcommerce::getOptionSets($filter);

            dd($getOptionSets);

        } catch(Bigcommerce\Api\Error $error) {

            echo $error->getCode();
            echo $error->getMessage();

        }

        //$filter = Filter::create($filter);
        //return self::getCollection('/optionsets' . $filter->toQuery(), 'OptionSet');
    }

    public function getOptionSet(){

        $id = Input::get('id');

        $username = 'optimum7';
        $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
        $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
        $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

        Bigcommerce::configure(array(

            'store_url' => $store_url,
            'username'	=> $username,
            'api_key'	=> $API_Token

        ));

        Bigcommerce::failOnError();

        try {

            $getOptionSets = Bigcommerce::getOptionSet($id);

            dd($getOptionSets);

        } catch(Bigcommerce\Api\Error $error) {

            echo $error->getCode();
            echo $error->getMessage();

        }

        //$filter = Filter::create($filter);
        //return self::getCollection('/optionsets' . $filter->toQuery(), 'OptionSet');
    }

    public function getOptionSetOptions(){

        $id = Input::get('id');

        $username = 'optimum7';
        $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
        $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
        $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

        Bigcommerce::configure(array(

            'store_url' => $store_url,
            'username'	=> $username,
            'api_key'	=> $API_Token

        ));

        Bigcommerce::failOnError();

        try {

            $getOptionSets = Bigcommerce::getOptionSetOptions($id);

            dd($getOptionSets);

        } catch(Bigcommerce\Api\Error $error) {

            echo $error->getCode();
            echo $error->getMessage();

        }

        //$filter = Filter::create($filter);
        //return self::getCollection('/optionsets' . $filter->toQuery(), 'OptionSet');
    }

    public function createOptionSet($object){

        $username = 'optimum7';
        $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
        $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
        $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

        Bigcommerce::configure(array(

            'store_url' => $store_url,
            'username'	=> $username,
            'api_key'	=> $API_Token

        ));

        Bigcommerce::failOnError();

        try {

            $createOptionSet = Bigcommerce::createOptionSet($object);

            if ($createOptionSet){

                echo 'Option set has been created!';
                return true;

            } else {

                echo 'Option set creation has been failed!';
                return false;

            }

        } catch(Bigcommerce\Api\Error $error) {

            echo $error->getCode();
            echo $error->getMessage();

        }

        //return self::createResource('/optionsets', $object);
    }

    public function createOptionSetOption(){

        $start = Input::get('start');

        if($start==1){

            $username = 'optimum7';
            $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
            $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
            $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

            Bigcommerce::configure(array(

                'store_url' => $store_url,
                'username'	=> $username,
                'api_key'	=> $API_Token

            ));

            Bigcommerce::failOnError();

            $products = ProductOptions::where('BCProductId','!=',null)->where('is_inserted_to_bc',1)->where('BCOptionSetId','!=',0)->get();

            foreach ($products as $product){

                $object = array(
                    "option_id" => $product->BCOptionId
                );
        
                try {
        
                    $createOptionSetOption = Bigcommerce::createOptionSetOption($product->BCOptionSetId, $object);

                    if ($createOptionSetOption){
                        echo 'update successful '.$product->BCProductId.'<br>';
                    } else {
                        echo 'update FAILED '.$product->BCProductId.'<br>';
                    }
        
                } catch(Bigcommerce\Api\Error $error) {
        
                    echo $error->getCode();
                    echo $error->getMessage();
        
                }

            }

        } else {
            echo 'is started?';
        }

        //return self::createResource('/optionsets/' . $id . '/options', $object);
    }

    

    public function getOptionSetsCount(){

        $username = 'optimum7';
        $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
        $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
        $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

        Bigcommerce::configure(array(

            'store_url' => $store_url,
            'username'	=> $username,
            'api_key'	=> $API_Token

        ));

        Bigcommerce::failOnError();

        try {

            $getOptionSetsCount = Bigcommerce::getOptionSetsCount();

            dd($getOptionSetsCount);

        } catch(Bigcommerce\Api\Error $error) {

            echo $error->getCode();
            echo $error->getMessage();

        }

        //return self::getCount('/optionsets/count');
    }

    

    public function updateOptionSet(){

        $username = 'optimum7';
        $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
        $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
        $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

        Bigcommerce::configure(array(

            'store_url' => $store_url,
            'username'	=> $username,
            'api_key'	=> $API_Token

        ));

        Bigcommerce::failOnError();

        $products = ProductOptions::where('BCProductId',9919)->get();

        //dd($products);

        foreach ($products as $product){

            $optionSetUpdateObject = array(
                "product_id"=> $product->BCProductId
            );
    
            try {
    
                $updateOptionSet = Bigcommerce::updateOptionSet($product->BCOptionSetId, $optionSetUpdateObject);
    
                if ($updateOptionSet){
    
                    echo 'LocalId : '.$product->product_id.' BCProductId : '.$product->BCProductId.' BCOptionSetId : '.$product->BCOptionSetId.' is successful!';
    
                } else {
    
                    echo 'LocalId : '.$product->product_id.' BCProductId : '.$product->BCProductId.' BCOptionSetId : '.$product->BCOptionSetId.' is FAILED!';
    
                }
    
            } catch(Bigcommerce\Api\Error $error) {
    
                echo $error->getCode();
                echo $error->getMessage();
    
            }


        }
        //return self::updateResource('/optionsets/' . $id, $object);
    }

    public function deleteOptionSet(){

        $id = Input::get('id');

        $username = 'optimum7';
        $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
        $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
        $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

        Bigcommerce::configure(array(

            'store_url' => $store_url,
            'username'	=> $username,
            'api_key'	=> $API_Token

        ));

        Bigcommerce::failOnError();

        try {

            $deleteOptionSet = Bigcommerce::deleteOptionSet($id);

            if ($deleteOptionSet){

                echo 'Option Set has been deleted!';

            } else {

                echo 'Option Set deletion has been failed';

            }

        } catch(Bigcommerce\Api\Error $error) {

            echo $error->getCode();
            echo $error->getMessage();

        }

        //Client::deleteResource('/optionsets/' . $id);
    }

    public function deleteAllOptions(){

        $username = 'optimum7';
        $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
        $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
        $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

        Bigcommerce::configure(array(

            'store_url' => $store_url,
            'username'	=> $username,
            'api_key'	=> $API_Token

        ));

        Bigcommerce::failOnError();

        try {

            $deleteAllOptions = Bigcommerce::deleteAllOptions();

            if ($deleteAllOptions){

                echo 'All options has been deleted!';

            } else {

                echo 'Deletion of all options has been failed!';

            }

        } catch(Bigcommerce\Api\Error $error) {

            echo $error->getCode();
            echo $error->getMessage();

        }

        //return self::deleteResource('/options');
    }

    public function getProductOptions(){

        $productId = Input::get('productId');

        $username = 'optimum7';
        $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
        $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
        $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

        Bigcommerce::configure(array(

            'store_url' => $store_url,
            'username'	=> $username,
            'api_key'	=> $API_Token

        ));

        Bigcommerce::failOnError();

        try {

            $getProductOptions = Bigcommerce::getProductOptions($productId);

            dd($getProductOptions);

        } catch(Bigcommerce\Api\Error $error) {

            echo $error->getCode();
            echo $error->getMessage();

        }

        //return self::getCollection('/products/' . $productId . '/options');
    }

    public function getProductOption(){

        $productId = Input::get('productId');
        $productOptionId = Input::get('productOptionId');

        $username = 'optimum7';
        $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
        $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
        $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

        Bigcommerce::configure(array(

            'store_url' => $store_url,
            'username'	=> $username,
            'api_key'	=> $API_Token

        ));

        Bigcommerce::failOnError();

        try {

            $getProductOption = Bigcommerce::getProductOption($productId, $productOptionId);

            dd($getProductOption);

        } catch(Bigcommerce\Api\Error $error) {

            echo $error->getCode();
            echo $error->getMessage();

        }

        //return self::getResource('/products/' . $productId . '/options/' . $productOptionId);
    }

    public function createOptionValue($optionId, $object){

        $username = 'optimum7';
        $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
        $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
        $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

        Bigcommerce::configure(array(

            'store_url' => $store_url,
            'username'	=> $username,
            'api_key'	=> $API_Token

        ));

        Bigcommerce::failOnError();

        try {

            $createOptionValue = Bigcommerce::createOptionValue($optionId, $object);

            if ($createOptionValue){

                echo 'Creation of Option value has been successfull';

            } else {

                echo 'Creation of Option value has been failed!';

            }

        } catch(Bigcommerce\Api\Error $error) {

            echo $error->getCode();
            echo $error->getMessage();

        }

        //return self::createResource('/options/' . $optionId . '/values', $object);
    }

    public function deleteAllOptionSets(){

        $username = 'optimum7';
        $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
        $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
        $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

        Bigcommerce::configure(array(

            'store_url' => $store_url,
            'username'	=> $username,
            'api_key'	=> $API_Token

        ));

        Bigcommerce::failOnError();

        try {

            $deleteAllOptionSets = Bigcommerce::deleteAllOptionSets();

            if ($deleteAllOptionSets){

                echo 'Deletion of all option sets has been successfull';

            } else {

                echo 'Deletion of all option sets has been failed!';

            }

        } catch(Bigcommerce\Api\Error $error) {

            echo $error->getCode();
            echo $error->getMessage();

        }

        //return self::deleteResource('/optionsets');
    }

    public function updateOptionValue($optionId, $optionValueId, $object){

        $username = 'optimum7';
        $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
        $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
        $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

        Bigcommerce::configure(array(

            'store_url' => $store_url,
            'username'	=> $username,
            'api_key'	=> $API_Token

        ));

        Bigcommerce::failOnError();

        try {

            $updateOptionValue = Bigcommerce::updateOptionValue($optionId, $optionValueId, $object);

            if ($updateOptionValue){

                echo 'Update of option value has been successfull';

            } else {

                echo 'Update of option value has been failed!';

            }

        } catch(Bigcommerce\Api\Error $error) {

            echo $error->getCode();
            echo $error->getMessage();

        }

        /*
        return self::updateResource(
            '/options/' . $optionId . '/values/' . $optionValueId,
            $object
        );
        */
    }

    public function optionSetProcess(){

        $username = 'optimum7';
        $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
        $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
        $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

        Bigcommerce::configure(array(

            'store_url' => $store_url,
            'username'	=> $username,
            'api_key'	=> $API_Token

        ));

        Bigcommerce::failOnError();

        //$products = ProductOptions::where('product_id','>',0)->where('main_item_id', 'like', '%(ANA)%')->where('BCProductId','!=',0)->where('not_to_insert',0)->take(10)->get();
        $productSets = ProductOptions::where('product_id','>',5313)->where('product_id','<',5318)->where('is_option_root',1)->where('not_to_insert',0)->get();

        //dd($productSets);

        $lastOptionSetId = 735;

        foreach ($productSets as $productSet){

            // OptionSet için option var mı?
            $currentProductOptions = ProductOptions::where('option_of',$productSet->BCProductId)->where('is_option',1)->where('not_to_insert',0)->get();
            
            if(count($currentProductOptions) > 0){ // Option Set'e ait optionlar mevcut

                //echo 'option var : '.count($currentProductOptions);

                $optionSetName = $productSet->option_set.' '.'('.trim(str_replace('(ANA)','',$productSet->main_item_id)).')';
                $optionSet = (object)array('name'=>$optionSetName);

                // create option set

                if ($this->createOptionSet($optionSet)){ // option set oluştuysa
                    
                    $isOptionSetLocallyInserted = ProductOptions::where('product_id', $productSet->product_id)->update(['BCOptionSetId' => $lastOptionSetId]);

                    if ($isOptionSetLocallyInserted){

                        $lastOptionSetId++;

                    } else {

                        echo 'Local BCOptionSetId could not be updated! | Local Product Id : '.$productSet->product_id.'<br>';

                    }
                    
                } else { // if optionset could not be created

                    echo 'Option Set could not be created on BC : '.$productSet->product_id.'<br>';

                }

            } else { // if there is no option for this option set

                echo 'There is no option for this option set : '.$productSet->product_id.'<br>';

            }

        }

        /*
        foreach ($products as $product){

            if ($product->option_set != '' || $product->option_set != null){

                if (strpos($product->option_set,'–')>0){ // Option Set

                    $currentProductOptions = ProductOptions::where('option_of',$product->BCProductId)->where('not_to_insert',0)->take(15)->get();

                    dd($currentProductOptions);

                    $optionSetName = $product->option_set.' '.'('.trim(str_replace('(ANA)','',$product->main_item_id)).')';

                    $optionSet = array('name'=>$optionSetName);

                    $optionSet = (object)$optionSet;

                    if ($this->createOptionSet($optionSet)){
                        echo 'ok';
                    }
    
                } else if (strpos($product->option_set,'–')==0){ // Option Itself
    
                    echo $product->option_set.'<br>';
    
                }

            }

        }
        */

        //$this->createOptionSet();
        //$this->createOptionSetOption();
    }

    public function optionProcess(){

        $username = 'optimum7';
        $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
        $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
        $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

        Bigcommerce::configure(array(

            'store_url' => $store_url,
            'username'	=> $username,
            'api_key'	=> $API_Token

        ));

        Bigcommerce::failOnError();

        $productSets = ProductOptions::where('product_id','>',0)->where('BCOptionSetId','!=',0)->where('is_option_root',1)->where('not_to_insert',0)->take(1)->get();

        //dd($productSets);

        //$lastOptionId = 8;
        //$lastOptionValueId = 29;

        foreach ($productSets as $productSet){

            $localProductOptions = ProductOptions::where('option_of',$productSet->BCProductId)->where('is_option',1)->where('not_to_insert',0)->get();

            if (count($localProductOptions)){

                //dd($localProductOptions);

                echo $productSet->BCOptionSetId.'<br>';
                echo $productSet->BCProductId.'<br>';
                
                $optionTypeArray = explode(' – ',$productSet->option_set);
                $optionType = $optionTypeArray[0];

                // create options in BC for specific product

                $optionArray = array(
                    "name" => "Color",
                    "display_name" => "Color",
                    "type" => "CS"
                );



                // create option values and combine them with options
                // add option to optionset created fot the product
                // relate optionset with product 

                if ($optionType == 'RT'){ // Size


                } else if ($optionType == 'CS'){ // Color



                } else if ($optionType == 'C'){ // Custom


                } else {



                }

                //dd($localProductOptions);

                foreach ($localProductOptions as $localProductOption){

                    dd($localProductOption);

                }

            } else {

                continue;

            }
        }
    }

    public function getProductCustomFields(){

        $id=Input::get('id');

        $username = 'optimum7';
        $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
        $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
        $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

        Bigcommerce::configure(array(

            'store_url' => $store_url,
            'username'	=> $username,
            'api_key'	=> $API_Token

        ));

        //Bigcommerce::failOnError();

        $customFields = Bigcommerce::getProductCustomFields($id);

        dd($customFields);

    }

    public function deleteProductCustomField(){

        $start = Input::get('start');
        $end = Input::get('end');

        $product_id=8762;

        $username = 'optimum7';
        $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
        $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
        $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

        Bigcommerce::configure(array(

            'store_url' => $store_url,
            'username'	=> $username,
            'api_key'	=> $API_Token

        ));

        //Bigcommerce::failOnError();

        for ($i=$start; $i<=$end; $i++){

            $deleteCustomFields = Bigcommerce::deleteProductCustomField($product_id, $i);

            if ($deleteCustomFields){
                echo $i.' numaralı custom field silindi.';
            } else {
                echo $i.' numaralı custom field SİLİNEMEDİ.';
            }

        }

        

        //dd($customFields);
    }

    public function createProductCustomField(){

        $start = Input::get('start');

        if ($start == 1){

            $username = 'optimum7';
            $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
            $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
            $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

            Bigcommerce::configure(array(

                'store_url' => $store_url,
                'username'	=> $username,
                'api_key'	=> $API_Token

            ));

            //Bigcommerce::failOnError();

            $products = ProductOptions::where('product_id','>',0)->where('BCProductId','!=','')->get();

            foreach ($products as $product){

                $checkSecondPrice = InvMast::where('item_id',trim(str_replace('(ANA)','',$product->main_item_id)))->first();
                
                if($checkSecondPrice != null){

                    $customFieldData = array(
                        "name"=>"price2",
                        "text"=>$checkSecondPrice->price2
                    );

                    try {

                        $customFieldProcess = Bigcommerce::createProductCustomField($product->BCProductId, (object)$customFieldData);
                        
                        if($customFieldProcess){

                            echo $product->BCProductId.' numaralı ürün (ana) için custom field oluşturuldu '.$checkSecondPrice->price2.'<br>';
                            continue;
        
                        } else {
        
                            echo $product->BCProductId.' numaralı ürün (ana) için custom field OLUŞTURULAMADI<br>';
                            continue;
        
                        }
            
                    } catch(Bigcommerce\Api\Error $error) {
            
                        echo $error->getCode();
                        echo $error->getMessage();
            
                    }

                } else {

                    $productOptions = ProductOptions::where('option_of',$product->BCProductId)->get();

                    foreach ($productOptions as $productOption){

                        $checkSecondPriceForProductOptions = InvMast::where('item_id',$productOption->item_id)->first();

                        if ($checkSecondPriceForProductOptions != null){

                            $customFieldData = array(
                                "name"=>"price2",
                                "text"=>$checkSecondPriceForProductOptions->price2
                            );

                            try {

                                $customFieldProcess = Bigcommerce::createProductCustomField($product->BCProductId, (object)$customFieldData);
                                
                                if($customFieldProcess){

                                    echo $product->BCProductId.' numaralı ürün (option) için custom field oluşturuldu '.$checkSecondPriceForProductOptions->price2.'<br>';
                                    break;
                
                                } else {
                
                                    echo $product->BCProductId.' numaralı ürün (option) için custom field OLUŞTURULAMADI<br>';
                                    continue;
                
                                }
                    
                            } catch(Bigcommerce\Api\Error $error) {
                    
                                echo $error->getCode();
                                echo $error->getMessage();
                    
                            }

                        } else {

                            continue;
                            
                        }
                    }
                }
            }
        }
    }

    public function createProductCustomFieldProductType(){

        $start = Input::get('start');

        if ($start == 1){

            $username = 'optimum7';
            $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
            $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
            $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

            Bigcommerce::configure(array(

                'store_url' => $store_url,
                'username'	=> $username,
                'api_key'	=> $API_Token

            ));

            //Bigcommerce::failOnError();

            $products = ProductOptions::where('product_id','>',0)->where('BCProductId','!=','')->get();

            foreach ($products as $product){

                $checkSecondPrice = InvMast::where('item_id',trim(str_replace('(ANA)','',$product->main_item_id)))->first();
                
                if($checkSecondPrice != null){

                    $customFieldData = array(
                        "name"=>"product_type",
                        "text"=>$checkSecondPrice->product_type
                    );

                    try {

                        $customFieldProcess = Bigcommerce::createProductCustomField($product->BCProductId, (object)$customFieldData);
                        
                        if($customFieldProcess){

                            echo $product->BCProductId.' numaralı ürün (ana) için custom field oluşturuldu '.$checkSecondPrice->product_type.'<br>';
                            continue;
        
                        } else {
        
                            echo $product->BCProductId.' numaralı ürün (ana) için custom field OLUŞTURULAMADI<br>';
                            continue;
        
                        }
            
                    } catch(Bigcommerce\Api\Error $error) {
            
                        echo $error->getCode();
                        echo $error->getMessage();
            
                    }

                } else {

                    $productOptions = ProductOptions::where('option_of',$product->BCProductId)->get();

                    foreach ($productOptions as $productOption){

                        $checkSecondPriceForProductOptions = InvMast::where('item_id',$productOption->item_id)->first();

                        if ($checkSecondPriceForProductOptions != null){

                            $customFieldData = array(
                                "name"=>"product_type",
                                "text"=>$checkSecondPriceForProductOptions->product_type
                            );

                            try {

                                $customFieldProcess = Bigcommerce::createProductCustomField($product->BCProductId, (object)$customFieldData);
                                
                                if($customFieldProcess){

                                    echo $product->BCProductId.' numaralı ürün (option) için custom field oluşturuldu '.$checkSecondPriceForProductOptions->product_type.'<br>';
                                    break;
                
                                } else {
                
                                    echo $product->BCProductId.' numaralı ürün (option) için custom field OLUŞTURULAMADI<br>';
                                    continue;
                
                                }
                    
                            } catch(Bigcommerce\Api\Error $error) {
                    
                                echo $error->getCode();
                                echo $error->getMessage();
                    
                            }

                        } else {

                            continue;
                            
                        }
                    }
                }
            }
        }
    }

    public function createProductCustomFieldUom(){

        $start = Input::get('start');

        if ($start == 1){

            $username = 'optimum7';
            $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
            $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
            $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

            Bigcommerce::configure(array(

                'store_url' => $store_url,
                'username'	=> $username,
                'api_key'	=> $API_Token

            ));

            //Bigcommerce::failOnError();

            $products = ProductOptions::where('product_id','>',0)->where('BCProductId','!=','')->get();

            foreach ($products as $product){

                $checkSecondPrice = InvMast::where('item_id',trim(str_replace('(ANA)','',$product->main_item_id)))->first();
                
                if($checkSecondPrice != null){

                    $customFieldData = array(
                        "name"=>"Unit (UOM)",
                        "text"=>$checkSecondPrice->default_selling_unit
                    );

                    try {

                        $customFieldProcess = Bigcommerce::createProductCustomField($product->BCProductId, (object)$customFieldData);
                        
                        if($customFieldProcess){

                            echo $product->BCProductId.' numaralı ürün (ana) için custom field oluşturuldu '.$checkSecondPrice->product_type.'<br>';
                            continue;
        
                        } else {
        
                            echo $product->BCProductId.' numaralı ürün (ana) için custom field OLUŞTURULAMADI<br>';
                            continue;
        
                        }
            
                    } catch(Bigcommerce\Api\Error $error) {
            
                        echo $error->getCode();
                        echo $error->getMessage();
            
                    }

                } else {

                    $productOptions = ProductOptions::where('option_of',$product->BCProductId)->get();

                    foreach ($productOptions as $productOption){

                        $checkSecondPriceForProductOptions = InvMast::where('item_id',$productOption->item_id)->first();

                        if ($checkSecondPriceForProductOptions != null){

                            $customFieldData = array(
                                "name"=>"Unit (UOM)",
                                "text"=>$checkSecondPriceForProductOptions->default_selling_unit
                            );

                            try {

                                $customFieldProcess = Bigcommerce::createProductCustomField($product->BCProductId, (object)$customFieldData);
                                
                                if($customFieldProcess){

                                    echo $product->BCProductId.' numaralı ürün (option) için custom field oluşturuldu '.$checkSecondPriceForProductOptions->product_type.'<br>';
                                    break;
                
                                } else {
                
                                    echo $product->BCProductId.' numaralı ürün (option) için custom field OLUŞTURULAMADI<br>';
                                    continue;
                
                                }
                    
                            } catch(Bigcommerce\Api\Error $error) {
                    
                                echo $error->getCode();
                                echo $error->getMessage();
                    
                            }

                        } else {

                            continue;
                            
                        }
                    }
                }
            }
        }
    }

    public function createNewOptions(){

        $start = Input::get('start');

        if ($start == 1){

            $username = 'optimum7';
            $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
            $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
            $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

            Bigcommerce::configure(array(

                'store_url' => $store_url,
                'username'	=> $username,
                'api_key'	=> $API_Token

            ));

            Bigcommerce::failOnError();

           
            $mainOptions = ProductOptions::where('BCProductId','!=',NULL)->where('is_option_root',1)->where('is_inserted_to_bc',1)->get(); // get the option root

            //dd($mainOptions);
            //echo count($mainOptions).'<br>';

            $currentOptionId = 589;

            foreach ($mainOptions as $mainOption){

                //echo($mainOption->BCProductId).'<br>';

                //$justOptions = ProductOptions::where('option_of',$mainOption->BCProductId)->where('is_option_root',0)->where('is_option',1)->where('is_inserted_to_bc',0)->get(); // get the option root

                //echo $mainOption->option_set.'<br>';

                //foreach ($justOptions as $justOption){
                //    echo '--'.$justOption->option_set.'<br>';
                //}

                $optionSetArray = explode(' – ',$mainOption->option_set);

                $optionType = trim($optionSetArray[0]);

                /*
                if ($optionType == 'Size'){

                    $currentOptionType = 'RT'; 
                    $currentOptionName = 'Size';

                } else if ($optionType == 'Color'){

                    $currentOptionType = 'CS'; 
                    $currentOptionName = 'Color';

                } else {

                    $currentOptionType = 'S'; 
                    $currentOptionName = $optionType;
                }
                */

                $currentOptionType = 'S'; 
                $currentOptionName = $optionType;

                $optionOptions = array(
                    "name"=> $currentOptionName.'-'.$mainOption->BCProductId,
                    "display_name"=> $currentOptionName,
                    "type"=> $currentOptionType
                );

                //dump($optionOptions);

                try {

                    $optionProcess = Bigcommerce::createOption((object)$optionOptions);
                    
                    if($optionProcess){

                        $enterOption = ProductOptions::where('product_id',$mainOption->product_id)->update(['BCOptionId' => $currentOptionId]);
                        
                        if ($enterOption){

                            echo $mainOption->BCProductId.' için option girildi, db update edildi<br>';
                            $currentOptionId++;
                            continue;

                        } else {

                            echo $mainOption->BCProductId.' için option girildi, db update EDİLEMEDİ!<br>';

                        }

                    } else {
    
                        echo $mainOption->BCProductId.' için option girilemedi<br>';
                        continue;
    
                    }
        
                } catch(Bigcommerce\Api\Error $error) {
        
                    echo $error->getCode();
                    echo $error->getMessage();
        
                }
            }
        }
    }

    public function createNewOptionValues(){

        $start = Input::get('start');

        if ($start == 1){

            $username = 'optimum7';
            $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
            $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
            $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

            Bigcommerce::configure(array(

                'store_url' => $store_url,
                'username'	=> $username,
                'api_key'	=> $API_Token

            ));

            Bigcommerce::failOnError();
           
            $optionValues = ProductOptions::where('product_id','>',5272)->where('option_of','!=',0)->where('is_option',1)->where('not_to_insert',0)->get(); // get the option root

            //dd($optionValues);
            //echo count($mainOptions).'<br>';

            $currentOptionValueId = 2207;

            foreach ($optionValues as $optionValue){

                //dd($optionValue);

                //echo $optionValue->option_of.'<br>';

                $relatedOption = ProductOptions::where('BCProductId','=',$optionValue->option_of)->where('not_to_insert',0)->first(); // get the option root

                //dump($relatedOption);

                //echo $optionValue->option_set.'<br>';

                $optionValueOptions = array(
                    "label"=> $optionValue->option_set,
                    "sort_order"=> 0,
                    "value"=> $optionValue->option_set,
                    "is_default"=> false
                );

                //dump($optionValueOptions);

                //die();
                
                try {

                    $optionValueProcess = Bigcommerce::createOptionValue($relatedOption->BCOptionId, (object)$optionValueOptions);
                    
                    if($optionValueProcess){

                        $enterOption = ProductOptions::where('product_id',$optionValue->product_id)->update(['BCOptionValueId' => $currentOptionValueId]);
                        
                        if ($enterOption){

                            echo $optionValue->BCProductId.' için option value girildi, db update edildi<br>';
                            $currentOptionValueId++;
                            continue;

                        } else {

                            echo $optionValue->BCProductId.' için option value girildi, db update EDİLEMEDİ!<br>';
                            $currentOptionValueId++;
                            continue;
                        }

                    } else {
    
                        echo $optionValue->BCProductId.' için option value girilemedi<br>';
                        continue;
    
                    }
        
                } catch(Bigcommerce\Api\Error $error) {
        
                    echo $error->getCode();
                    echo $error->getMessage();
        
                }
            }
        }
    }



    // Option value and option set functions
    public function createOptionSetOptions (){

        $start = Input::get('start');

        if ($start == 1){

            $username = 'optimum7';
            $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
            $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
            $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

            Bigcommerce::configure(array(

                'store_url' => $store_url,
                'username'	=> $username,
                'api_key'	=> $API_Token

            ));

            Bigcommerce::failOnError();
           
            $optionValues = ProductOptions::where('product_id','>',5272)->where('option_of','!=',0)->where('is_option',1)->where('not_to_insert',0)->get(); // get the option root

            foreach ($optionValues as $optionValue){

                
            }
        }

    }

    public function updateOptionSetOptions (){

        $start = Input::get('start');

        if ($start == 1){

            $username = 'optimum7';
            $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
            $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
            $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

            Bigcommerce::configure(array(

                'store_url' => $store_url,
                'username'	=> $username,
                'api_key'	=> $API_Token

            ));

            //Bigcommerce::failOnError();
           
            $optionSetOptions = ProductOptions::where('BCOptionSetId','!=',0)->get(); // get the option root

            //dd($optionSetOptions);

            foreach ($optionSetOptions as $optionSetOption){

                $optionsOfThisSet = Bigcommerce::getOptionSetOptions($optionSetOption->BCOptionSetId);

                $optionSetOptionValues = array(
                    'is_required'=>true
                );

                if (!empty($optionSetOption->BCOptionSetId) && !empty($optionsOfThisSet->id)){

                    try{
                    
                        $status = Bigcommerce::updateOptionSetOption($optionSetOption->BCOptionSetId, $optionsOfThisSet->id, $optionSetOptionValues);
    
                        if ($status){
                            echo 'Make it required has failed<br>';
                        } else {
                            echo 'Make it required has failed<br>';
                        }
    
                    } catch(Bigcommerce\Api\Error $error) {
    
                        echo $error->getCode();
                        echo $error->getMessage();
    
                    }

                }
            }
        }

    }


    // Order Processes
    public function createOrder(){

        $start = Input::get('start');

        if ($start == 1){

            $username = 'optimum7';
            $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
            $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
            $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

            Bigcommerce::configure(array(

                'store_url' => $store_url,
                'username'	=> $username,
                'api_key'	=> $API_Token

            ));

            Bigcommerce::failOnError();
           
            $optionValues = ProductOptions::where('product_id','>',5272)->where('option_of','!=',0)->where('is_option',1)->where('not_to_insert',0)->get(); // get the option root

            foreach ($optionValues as $optionValue){

                
            }
        }
    }

    public function deleteOrder($orderId){

        $start = Input::get('start');

        if ($start == 1){

            $username = 'optimum7';
            $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
            $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
            $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

            Bigcommerce::configure(array(

                'store_url' => $store_url,
                'username'	=> $username,
                'api_key'	=> $API_Token

            ));

            Bigcommerce::failOnError();
           
            $optionValues = ProductOptions::where('product_id','>',5272)->where('option_of','!=',0)->where('is_option',1)->where('not_to_insert',0)->get(); // get the option root

            foreach ($optionValues as $optionValue){

                
            }
        }
    }

    public function updateOrder(){

        $start = Input::get('start');

        if ($start == 1){

            $username = 'optimum7';
            $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
            $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
            $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

            Bigcommerce::configure(array(

                'store_url' => $store_url,
                'username'	=> $username,
                'api_key'	=> $API_Token

            ));

            Bigcommerce::failOnError();
           
            $optionValues = ProductOptions::where('product_id','>',5272)->where('option_of','!=',0)->where('is_option',1)->where('not_to_insert',0)->get(); // get the option root

            foreach ($optionValues as $optionValue){

                
            }
        }
    }

    public function getOrder($orderId){

        $start = Input::get('start');

        if ($start == 1){

            $username = 'optimum7';
            $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
            $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
            $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

            Bigcommerce::configure(array(

                'store_url' => $store_url,
                'username'	=> $username,
                'api_key'	=> $API_Token

            ));

            Bigcommerce::failOnError();
           
            $optionValues = ProductOptions::where('product_id','>',5272)->where('option_of','!=',0)->where('is_option',1)->where('not_to_insert',0)->get(); // get the option root

            foreach ($optionValues as $optionValue){

                
            }
        }
    }

    public function getOrders(){

        $start = Input::get('start');

        if ($start == 1){

            $username = 'optimum7';
            $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
            $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
            $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

            Bigcommerce::configure(array(

                'store_url' => $store_url,
                'username'	=> $username,
                'api_key'	=> $API_Token

            ));

            Bigcommerce::failOnError();
           
            $optionValues = ProductOptions::where('product_id','>',5272)->where('option_of','!=',0)->where('is_option',1)->where('not_to_insert',0)->get(); // get the option root

            foreach ($optionValues as $optionValue){

                
            }
        }
    }


    public function jobContractProductsToCategories(){

        $start = Input::get('start');

        if($start == 1){

            $username = 'optimum7';
            $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
            $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
            $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

            Bigcommerce::configure(array(

                'store_url' => $store_url,
                'username'	=> $username,
                'api_key'	=> $API_Token

            ));

            Bigcommerce::failOnError();

            $products = ProductOptions::where('BCOptionValueId','!=',null)->get();

            $var = 0;
            $yok = 0;

            foreach ($products as $product){

                if ($product->item_id != ''){

                    $JCExists = DB::table('job_contracter')->where('item_id', $product->item_id)->get();

                    //echo count($JCExists).'<br>'; continue;

                    if (count($JCExists)>0){

                        echo '<br>'.$product->item_id.' ürününe ait olan Job Contracter ürünleri<br>';
                        echo '<pre>';
                        print_r($JCExists);
                        echo '</pre>';

                        $var++;

                    } else {

                        echo '<br>'.$product->item_id.' ürününe ait olan Job Contracter ürünü yok.<br>';
                        $yok++;

                    }
                }
            }
            echo '<br>Var olanlar : '.$var;
            echo '<br>Olmayanlar : '.$yok;
        }
    }

    public function getCustomersResetTheirPasswords(){

        $start = Input::get('start');

        if($start == 1){

            $username = 'optimum7';
            $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
            $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
            $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

            Bigcommerce::configure(array(
                'store_url' => $store_url,
                'username'	=> $username,
                'api_key'	=> $API_Token
            ));

            Bigcommerce::failOnError();

            $lastCustomer = 4231;
            $firstCustomer = 1;

            for ($i=$firstCustomer; $i<$lastCustomer+1; $i++){

                $currentCustomer = BigCommerce::getCustomer($i);

                if ($currentCustomer != false){

                    $customerUpdateObject = array(
                        "force_reset"=> true
                    );

                    $updateStatus = BigCommerce::updateCustomer($currentCustomer->id, (object)$customerUpdateObject);

                    if($updateStatus == true){

                        echo $i.' için customer için güncelleme tamamlandı<br>';

                    } else {

                        echo $i.' için customer için güncelleme yapılamadı<br>';
                    }

                } else {

                    echo $i.' için customer data bulunamadı<br>';

                }
            }
        }
    }

    public function createJobContracterUsers(){

        // 1- Get unique job contractors from job_contracter table.
        // 2- For each job contractor, get products from job_contacter table.
        // 3- For each job contracter, get category id which is already created.
        // 4- For each job_contracter, create customer and store the customer_id data.
        // 5- For each job_contracter, create customer_group and store the group_id data.
        // 6- Relate categories with related customer_groups
        // 7- Relate the created customer (4) and customer_group (5)
        // 8- Relate products of job_contracter to related category

        $start = Input::get('start');

        if($start == 1){

            $username = 'optimum7';
            $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
            $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
            $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

            Bigcommerce::configure(array(
                'store_url' => $store_url,
                'username'	=> $username,
                'api_key'	=> $API_Token
            ));

            Bigcommerce::failOnError();

            $currentCustomerId = 4232;
            $currentCustomerGroupId = 6;

            $jobContracters = DB::table('job_contracter')->select(DB::raw('DISTINCT job_no'),'BCCategoryId','job_description')->get();

            //dd($jobContracters);

            foreach ($jobContracters as $jobContracter){

                $customerGroupObjectData = array(

                    "name"=>$jobContracter->job_no.'-'.(!empty($jobContracter->job_description) ? $jobContracter->job_description : $jobContracter->job_no.'NA'),
                    "is_default"=>false,
                    "category_access"=>[
                        "type"=>"specific",
                        "categories"=>[$jobContracter->BCCategoryId]
                    ]
                );

                //dump($customerGroupObjectData);

                //continue;

                try {

                    $createCustomerGroupForEveryContracter = BigCommerce::createCustomerGroup((object)$customerGroupObjectData);

                    //echo $createCustomerGroupForEveryContracter; die();

                    if ($createCustomerGroupForEveryContracter){

                        $updateLocalDbForContracterCustomerGroup = DB::table('job_contracter')->where('job_no',$jobContracter->job_no)->update(['BCCustomerGroupId'=>$currentCustomerGroupId]);

                        if ($updateLocalDbForContracterCustomerGroup){

                            echo 'Local user group id has been updated for contracter.<br>';
                            $currentCustomerGroupId++;
                        
                        } else {

                            echo 'Local user group id update has been failed!.<br>';
                            continue;
                        } 
                    }

                } catch (Bigcommerce\Api\Error $error) {

                    echo 'Error : '.$category->job_description;
                    echo '<br>';
                    print_r($e);
                    
                }
            }
        }
    }

    public function productToCategoriesForJobContracts(){

        $start = Input::get('start');

        if($start == 1){

            $username = 'optimum7';
            $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
            $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
            $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

            Bigcommerce::configure(array(
                'store_url' => $store_url,
                'username'	=> $username,
                'api_key'	=> $API_Token
            ));

            Bigcommerce::failOnError();

            $currentCustomerId = 4232;
            $currentCustomerGroupId = 6;

            $jobContracters = DB::table('job_contracter')->select(DB::raw('DISTINCT job_no'),'BCCategoryId','job_description')->get();

            foreach ($jobContracters as $jobContracter){

                $customerGroupObjectData = array(

                    "name"=>$jobContracter->job_no.'-'.(!empty($jobContracter->job_description) ? $jobContracter->job_description : $jobContracter->job_no.'NA'),
                    "is_default"=>false,
                    "category_access"=>[
                        "type"=>"specific",
                        "categories"=>[$jobContracter->BCCategoryId]
                    ]
                );

                //dump($customerGroupObjectData);

                //continue;

                try {

                    $createCustomerGroupForEveryContracter = BigCommerce::createCustomerGroup((object)$customerGroupObjectData);

                    //echo $createCustomerGroupForEveryContracter; die();

                    if ($createCustomerGroupForEveryContracter){

                        $updateLocalDbForContracterCustomerGroup = DB::table('job_contracter')->where('job_no',$jobContracter->job_no)->update(['BCCustomerGroupId'=>$currentCustomerGroupId]);

                        if ($updateLocalDbForContracterCustomerGroup){

                            echo 'Local user group id has been updated for contracter.<br>';
                            $currentCustomerGroupId++;
                        
                        } else {

                            echo 'Local user group id update has been failed!.<br>';
                            continue;
                        } 
                    }

                } catch (Bigcommerce\Api\Error $error) {

                    echo 'Error : '.$category->job_description;
                    echo '<br>';
                    print_r($e);
                    
                }
            }
        }
    }

    public function getCustomerGroups (){

        $start = Input::get('start');

        if($start == 1){

            $username = 'optimum7';
            $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
            $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
            $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

            Bigcommerce::configure(array(
                'store_url' => $store_url,
                'username'	=> $username,
                'api_key'	=> $API_Token
            ));

            Bigcommerce::failOnError();

            try {

                $getCustomerGroup = BigCommerce::getCustomerGroups(array('page'=>2));

                dd($getCustomerGroup);

            } catch (Bigcommerce\Api\Error $error) {

                echo 'Error : '.$e;
                echo '<br>';
                
            }
        }
    }

    public function getCustomerGroup (){

        $start = Input::get('start');
        $id = Input::get('id');

        if($start == 1 && isset($id)){

            $username = 'optimum7';
            $store_url = 'https://store-8zcngt4nvy.mybigcommerce.com';
            $API_PATH = 'https://store-8zcngt4nvy.mybigcommerce.com/api/v2/';
            $API_Token = 'dc97ea13051f313e78750eb2acf3beb9a324fd91';

            Bigcommerce::configure(array(
                'store_url' => $store_url,
                'username'	=> $username,
                'api_key'	=> $API_Token
            ));

            Bigcommerce::failOnError();

            try {

                $getCustomerGroup = BigCommerce::getCustomerGroup($id);

                dd($getCustomerGroup);

            } catch (Bigcommerce\Api\Error $error) {

                echo 'Error : '.$e;
                echo '<br>';
                
            }
        }
    }

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

    public function setPriceList($data){
        
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.bigcommerce.com/stores/8zcngt4nvy/v3/pricelists",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
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

    public function deneme(){

        $customerGroupList = Bigcommerce::getCustomerGroups();

    }
}
