<?php
require(__DIR__ . '/vendor/autoload.php');
ini_set("display_errors", "on");
require(__DIR__ . '/XLSXReader.php');
/*
$csv = new parseCSV();
$csv->encoding('UTF-16', 'UTF-8');
$csv->auto('storage/volusion/products.csv');
*/

$item = \App\Item::get()->take(1);
dd($item);
$header = [];
$header[] = [
    'Item Type',
    'Product Name',
    'Product Type',
    'Product Code/SKU',
    'Brand Name',
    'Product Description',
    'Price',
    'Sale Price',
    'Fixed Shipping Cost',
    'Free Shipping',
    'Product Weight',
    'Product Width',
    'Product Height',
    'Product Depth',
    'Product Visible?',
    'Product Availability',
    'Current Stock Level',
    'Low Stock Level',
    'Category',
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
    if ($search_quickbook) {
        foreach ($search_quickbook as $quickbook) {
            $options = [];
            foreach( process_values($quickbook[15]) as $k=>$opts){
                $options[] = str_replace(","," -","[S]".$k."=".$opts);
            }
            $header[] = [
                "SKU",
                implode(",",$options),
                "",
                $quickbook[1],
                "",
                "",
                $quickbook[2],
                $quickbook[2],
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                0,
                0,
                "",
                "",
                $quickbook[10],
                "Y",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                0,
                $quickbook[13],
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                ""
            ];
        }
    }
}
function nameControl($header, $productName, $sku)
{
    foreach ($header as $head) {
        if ($head[1] == $productName) {
            return $productName . " - " . $sku;
        }
    }
    return $productName;
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
?>