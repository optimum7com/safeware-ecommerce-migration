<?php

namespace App\Http\Controllers;

use App\Category;
use App\CategoryHierarchy;
use App\Item;
use App\ProductCategories;


use Illuminate\Support\Facades\Input;

use League\Csv\Reader;
use League\Csv\Writer;

use Bigcommerce\Api\Client as Bigcommerce;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\InvMast;

require_once base_path('XLSXReader.php' );

use Illuminate\Support\Facades\Storage;

class ExportController extends Controller
{
    
    function diger(){
        $header = [];
        $header[] = [
            'Item Type',
            'Product Name',
            'Product Type',
            'Product Code/SKU',
            'Brand Name',
            'Product Description',
            'Price',
            /*'Sale Price',
            'Fixed Shipping Cost',
            'Free Shipping',*/
            'Product Weight',
            'Product Width',
            /*'Product Height',
            'Product Depth',
            'Product Visible?',*/
            'Product Availability',
            /*'Current Stock Level',
            'Low Stock Level',*/
            'Category',
            /*
            'Option Set',
            'Option Set Align',

            'Product Image ID - 1',
            'Product Image File - 1',
            'Product Image Is Thumbnail - 1',
            'Product Image Sort - 1',
            'Product Image ID - 2',
            'Product Image File - 2',
            'Product Image Is Thumbnail - 2',
            'Product Image Sort - 2',
            'Product Image ID - 3',
            'Product Image File - 3',
            'Product Image Is Thumbnail - 3',
            'Product Image Sort - 3',
            'Product Image ID - 4',
            'Product Image File - 4',
            'Product Image Is Thumbnail - 4',
            'Product Image Sort - 4',
            'Product Image ID - 5',
            'Product Image File - 5',
            'Product Image Is Thumbnail - 5',
            'Product Image Sort - 5',
            'Product Image ID - 6',
            'Product Image File - 6',
            'Product Image Is Thumbnail - 6',
            'Product Image Sort - 6',

            'Search Keywords',
            'Page Title',
            'Meta Keywords',
            'Meta Description',
            'Product Condition',
            'Show Product Condition?',
            'Event Date Required?',
            'Event Date Is Limited?',
            'Sort Order',
            'Product UPC/EAN',
            'GPS Gender',
            'GPS Age Group',
            'GPS Color',
            'GPS Size',
            'GPS Material',
            'GPS Pattern',
            'GPS Category',
            'GPS Enabled',
            */
            'Product Custom Fields'];
        $xlsx = new XLSXReader("storage/volusion/quickbooks.xlsx");
        $sheet = $xlsx->getSheet("Worksheet");
        $products = array_group($sheet->getData(), 14);
        unset($products[0]);
        foreach ($csv->data as $key => $item) {
            if ($item["quickbooks_item_accnt"] == "") {
                if ($item['categorytree']) {
                    $categorytree = str_replace(' > ', '/', substr($item['categorytree'], 0, 50));
                } else {
                    $categorytree = "Uncategorized";
                }
                /*
                if ($item['productnameshort']) {
                    $productName = strip_tags(trim($item['productnameshort']));
                } else {
                    $productName = strip_tags(trim($item['productname']));
                }
                if ($productName == "") {
                    $productName = $item['productcode'];
                }
                if ($item['google_product_category']) {
                    $google_product_category = str_replace(' > ', '/', substr($item['google_product_category'], 0, 50));
                } else {
                    $google_product_category = "";
                }
                $descriptionHtml = '<ul class="tabs" data-tab>';
                if ($item["productdescription"]) {
                    $descriptionHtml .= '<li class="tab is-active"><a class="tab-title" href="#tab-description">Description</a></li>';
                }
                if ($item["productfeatures"]) {
                    $descriptionHtml .= '<li class="tab"><a class="tab-title" href="#tab-features">Features</a></li>';
                }
                if ($item["techspecs"]) {
                    $descriptionHtml .= '<li class="tab"><a class="tab-title" href="#tab-shippingandreturn">Shipping & Returns</a></li>';
                }
                if ($item["extinfo"]) {
                    $descriptionHtml .= '<li class="tab"><a class="tab-title" href="#tab-specifications">Specifications & FAQ</a></li>';
                }
                $descriptionHtml .= ' </ul><div class="tabs-contents">';
                if ($item["productdescription"]) {
                    $descriptionHtml .= '<div class="tab-content is-active" id="tab-description">' . $item["productdescription"] . '</div>';
                }
                if ($item["productfeatures"]) {
                    $descriptionHtml .= '<div class="tab-content" id="tab-features">' . $item["productfeatures"] . '</div>';
                }
                if ($item["techspecs"]) {
                    $descriptionHtml .= '<div class="tab-content" id="tab-shippingandreturn">' . $item["techspecs"] . '</div>';
                }
                if ($item["extinfo"]) {
                    $descriptionHtml .= '<div class="tab-content" id="tab-specifications">' . $item["extinfo"] . '</div>';
                }
                $descriptionHtml .= '</div>';
                $customfields = "";
                $re = '/rel="prettyPhoto"/mi';
                $str = $item["productdescription_abovepricing"];
                @preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);
                if ($item['customfield1']) {
                    $customfields .= "CustomField1=" . strip_tags($item['customfield1']) . ";";
                }
                if ($item['customfield2']) {
                    $customfields .= "CustomField2=" . strip_tags($item['customfield2']) . ";";
                }
                if ($item['customfield3']) {
                    $customfields .= "CustomField3=" . strip_tags($item['customfield3']) . ";";
                }
                if ($item['customfield4']) {
                    $customfields .= "CustomField4=" . strip_tags($item['customfield4']) . ";";
                }
                if ($item['vendor_price']) {
                    $customfields .= "Vendor_Price=" . strip_tags($item['vendor_price']) . ";";
                }
                if ($item['vendor_price']) {
                    $customfields .= "Vendor_Price=" . strip_tags($item['vendor_price']) . ";";
                }
                if ($item['vendor_partno']) {
                    $customfields .= "Vendor_PartNo=" . strip_tags($item['vendor_partno']) . ";";
                }
                if ($item['photo_alttext']) {
                    $customfields .= "Photo_AltText=" . strip_tags($item['photo_alttext']) . ";";
                }
                if ($matches) {
                    $customfields .= "badges=1;";
                }
                if ($item['photoseed']) {
                    $customfields .= "PhotoSeed=" . strip_tags($item['photoseed']);
                }
                */

                $photo1 = str_replace("-1.jpg", "-2.jpg", $item['photourl']);
                $photo2 = str_replace("-1.jpg", "-3.jpg", $item['photourl']);
                $photo3 = str_replace("-1.jpg", "-4.jpg", $item['photourl']);
                $photo4 = str_replace("-1.jpg", "-5.jpg", $item['photourl']);
                $photo5 = str_replace("-1.jpg", "-6.jpg", $item['photourl']);
                $photo6 = str_replace("-1.jpg", "-7.jpg", $item['photourl']);
                $search_quickbook = array_find($item["productcode"]);
                if( $search_quickbook ){
                    $optionset = $item['productcode'];
                }else{
                    $optionset = "";
                }
                $header[] = [
                    "Product",
                    nameControl($header, $productName, $item['productcode']),
                    "P",
                    $item['productcode'],
                    $item['productmanufacturer'],
                    str_replace("http://www.superiorlighting.com/v", "/content", $descriptionHtml),
                    $item['productprice'],
                    $item['saleprice'],
                    $item['fixed_shippingcost'],
                    $item['freeshippingitem'],
                    $item['productweight'],
                    $item['width'],
                    $item['height'],
                    $item['length'],
                    $item['hideproduct'],
                    $item['availability'],
                    0,
                    0,
                    $categorytree,
                    $optionset,
                    "right",
                    1, $photo1, "Y", 1,
                    2, $photo2, "N", 2,
                    3, $photo3, "N", 3,
                    4, $photo4, "N", 4,
                    5, $photo5, "N", 5,
                    6, $photo6, "N", 6,
                    $item['productkeywords'],
                    $item['metatag_title'],
                    $item['metatag_keywords'],
                    $item['metatag_description'],
                    "New",
                    "N",
                    "N",
                    "N",
                    0,
                    $item['upc_code'],
                    $item['google_gender'],
                    $item['google_age_group'],
                    $item['google_color'],
                    $item['google_size'],
                    $item['google_material'],
                    $item['google_pattern'],
                    $google_product_category,
                    "N",
                    $customfields
                ];
            }

        }

        $doc = new PHPExcel();
        $doc->setActiveSheetIndex(0);
        $doc->getActiveSheet()->fromArray($header);
        $filename = count($header) . '-products-import.xls';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($doc, 'Excel5');
        $objWriter->save('php://output');
        function array_find($productcode)
        {
            global $products;
            foreach ($products as $sku => $product) {
                if ($sku == $productcode) {
                    return $product;
                }
            }
            return false;
        }
        function array_group(array $data, $by_column)
        {
            $result = [];
            foreach ($data as $item) {
                $column = trim($item[$by_column]);
                unset($item[$by_column]);
                if (isset($result[$column])) {
                    $result[$column][] = $item;
                } else {
                    $result[$column] = array($item);
                }
            }
            unset($result["Quickbooks"]);
            return $result;
        }


        function process_values($values){
            $values = explode("|", $values);
            $vals = [];
            foreach ($values as $key => $val) {
                $val = explode(":", $val);
                @$vals[trim($val[0])] = trim($val[1]);
            }
            return $vals;
        }
    }

    public function export(){
        $category = [];
        /*
        electRaw("WHERE `parent_item_category_uid` = '2' LIMIT 0, 1000");

        dd( $db->get()->toArray() );
        */

        $cats = Category::get()->take(10)->toArray();
        foreach ($cats as $cat){

            $categoryTree = ( new CategoryHierarchy );
            dd($categoryTree);
        }
        dd($cats);

        $data = [];
        return view('page',$data);
    }

    public function xlsx_read(){
        $file = storage_path('safeware.xlsx');

        $xlsx = new \XLSXReader( $file );

        $sheet = $xlsx->getSheet( 'Sheet1' );
        $sheet = $sheet->getData();

        $header = $sheet[0];
        d($header);
        $b = 0;

        $first = Item::Where('item_id','SAF 92006 MD ')->first();
        $brand = ' 1 - ';
        $product_name = $brand .$first->item_id .' - '.$first->item_desc;
        d($product_name);
        $desc = $first->item_desc . ' ' . $first->extended_desc;
        d($desc);
        dd( $first->toArray() );
        foreach ($sheet as $i) {
            if( $b != 0 ){
                $item_id = $i[0];
                $category = '';
                $_category = $i[1];
                $_sub_1_category = $i[2];
                $_sub_2_category = $i[3];

                $_cat_push = [$_category,$_sub_1_category,$_sub_2_category];
                d($item_id);
                d($_cat_push);

                if( empty($_category)){
                    d($i);
                }
                $item = Item::where('item_id', $item_id )->first();

                $desc = $item->item_desc . ' ' . $item->extended_desc;
                d($desc);

                //d($item->toArray());

            }
            //d($i);
            echo $b;
            $b +=1 ;
            if($b == 10){
                return;
            }
        }

        dd($sheet);
    }
    
    public function products_export(){
        $export_file_name = 'products';
        $header = [
            'Item Type',
            'Product Name',
            'Product Type',
            'Product Code/SKU',
            'Brand Name',
            'Product Description',
            'Price',
            /*'Sale Price',
            'Fixed Shipping Cost',
            'Free Shipping',*/
            'Product Weight',
            /*'Product Width',
            'Product Height',
            'Product Depth',
            'Product Visible?',*/
            'Product Availability',
            /* 'Current Stock Level',
            'Low Stock Level',*/
            'Product Image ID - 1',
            'Product Image File - 1',
            'Product Image Is Thumbnail - 1',
            'Product Image Sort - 1',
            'Product Image ID - 2',
            'Product Image File - 2',
            'Product Image Is Thumbnail - 2',
            'Product Image Sort - 2',
            'Category',
            'Product Custom Fields'];
        $tum_categories = [];
        $items = Item::whereNotNull('class_id5')->take(1000)->get();
        $csv_body = [];
        foreach ($items as $i) {
            $cats = DB::table('item_category_x_inv_mast')->where('inv_mast_uid', '=', $i->inv_mast_uid)->get()->pluck('item_category_uid')->toArray();
            //d($cats);

            if (!empty($cats)) {
                $cats2 = DB::table('item_category')->whereIn('item_category_uid', $cats)->get()->pluck('item_category_desc')->toArray();
                foreach ($cats2 as $cate1) {
                    if (!in_array($cate1, $tum_categories)) {
                        $tum_categories[] = $cate1;
                    }
                }
                $categories = implode('/', $cats2);
            }

            $url = 'https://www.safewareinc.com/ecomm_images/items/large/'.rawurlencode($i->item_id).'.jpg';
            $photo1 = $url;
            $_data = [
                'Product',
                $i->item_desc,
                'P',
                $i->item_id,
                $i->brand_name,

                $i->extended_desc,
                $i->price1,
                $i->weight,

                ($i->inactive != 'N') ? 'available' : 'disabled',

                1, $photo1, "Y", 1,
                2, $photo1, "N", 2,


                //'Sale Price'          => $i->,
                //''                    => $i->shippable_unit_flag,
                $categories
            ];

            $ayir = ['item_desc', 'item_id', 'brand_name', 'extended_desc', 'price1', 'weight', 'inactive'];

            $customfields = [];
            foreach ($i->toArray() as $key => $value) {
                if (!in_array($key, $ayir)) {
                    $customfields[$key] = $value;
                }
            }
            $customfields_string = '';
            foreach ($customfields as $key => $value) {
                $customfields_string .= $key . '=' . strip_tags($value) . ';';
            }
            $_data[] = $customfields_string;

            $csv_body[] = $_data;
            $product_custom_field = 'Product Custom Fields';
        }
        //$csv = array_merge($header,$csv_body);

        $csv = Writer::createFromString('');

        $csv->insertOne($header);

        $csv->insertAll($csv_body);
        $csv->output($export_file_name.'.csv');

    }

    public function customer_export(){

        $export_file_name = 'products.csv';

        $headers = [
            'Customer ID',
            "First Name",
            "Last Name",
            'Company',
            'Email',
            'Phone',
            'Notes',
            "Store Credit",
            "Customer Group",
            "Date Joined",
            'Addresses',
            "Receive Review/Abandoned Cart Emails?",
            "Tax Exempt Category"
        ];

        $tum_categories = [];
        $items = Item::whereNotNull('class_id5')->take(1000)->get();
        $csv_body = [];
        foreach ($items as $i) {
            $cats = DB::table('item_category_x_inv_mast')->where('inv_mast_uid', '=', $i->inv_mast_uid)->get()->pluck('item_category_uid')->toArray();
            //d($cats);

            if (!empty($cats)) {
                $cats2 = DB::table('item_category')->whereIn('item_category_uid', $cats)->get()->pluck('item_category_desc')->toArray();
                foreach ($cats2 as $cate1) {
                    if (!in_array($cate1, $tum_categories)) {
                        $tum_categories[] = $cate1;
                    }
                }
                $categories = implode('/', $cats2);
            }

            $url = 'https://www.safewareinc.com/ecomm_images/items/large/'.rawurlencode($i->item_id).'.jpg';
            $photo1 = $url;
            $_data = [
                'Product',
                $i->item_desc,
                'P',
                $i->item_id,
                $i->brand_name,

                $i->extended_desc,
                $i->price1,
                $i->weight,

                ($i->inactive != 'N') ? 'available' : 'disabled',

                1, $photo1, "Y", 1,
                2, $photo1, "N", 2,


                //'Sale Price'          => $i->,
                //''                    => $i->shippable_unit_flag,
                $categories
            ];

            $ayir = ['item_desc', 'item_id', 'brand_name', 'extended_desc', 'price1', 'weight', 'inactive'];

            $customfields = [];
            foreach ($i->toArray() as $key => $value) {
                if (!in_array($key, $ayir)) {
                    $customfields[$key] = $value;
                }
            }
            $customfields_string = '';
            foreach ($customfields as $key => $value) {
                $customfields_string .= $key . '=' . strip_tags($value) . ';';
            }
            $_data[] = $customfields_string;

            $csv_body[] = $_data;
            $product_custom_field = 'Product Custom Fields';
        }
        //$csv = array_merge($header,$csv_body);

        $csv = Writer::createFromString('');

        $csv->insertOne($header);

        $csv->insertAll($csv_body);
        $csv->output($export_file_name.'.csv');

    }
}
