<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Tygh\Registry;
use Tygh\Http;
use Tygh\Tools\SecurityHelper;

DEFINE('AREA', 'A');
DEFINE('AREA_NAME', 'admin');
define('ACCOUNT_TYPE', 'admin');

require(dirname(__FILE__) . '/init.php');

ini_set('memory_limit', '1024M');
@set_time_limit(3600);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);




function prepareField($field) {
    if (!is_array($field)){
        $field = htmlspecialchars_decode($field);
        $field = strip_tags($field);
        $from = array('&', '"', '>', '<', '\'');
        $to = array('&amp;', '&quot;', '&gt;', '&lt;', '&apos;');
        $field = str_replace($from, $to, $field);
        $field = preg_replace('#[\x00-\x08\x0B-\x0C\x0E-\x1F]+#is', ' ', $field);
        return trim($field);
    } else {
        return $field;
    }
}



function getYmlHeader()
{
    $yml = '';
    $header = [
        '<?xml version="1.0" encoding="UTF-8"?>',
        //'<!DOCTYPE yml2_catalog SYSTEM "shops.dtd">',
        '<yml_catalog date="' . date('Y-m-d G:i') . '">',
        '<shop>'
    ];
    $yml.= implode(PHP_EOL, $header);
    //header

    //currencies
    buildCurrencies($currencies);
    $yml.= fn_yml_array_to_yml($currencies);


    $yml .= '<categories>' . PHP_EOL;

    $cats = db_get_array("SELECT d.category,c.category_id,c.parent_id FROM ?:categories as c inner join ?:category_descriptions as d on d.category_id = c.category_id WHERE d.category IS NOT NULL AND d.lang_code = 'ru' AND d.category_id NOT IN(539) and d.category <> '' ORDER BY c.category_id ASC");


    foreach ($cats as $category) {
        $category_name = prepareField($category['category']);
        $parentString = '';
        if (@$category['parent_id'] != null and $category['parent_id'] != 539){
            //$parentString = '" parentId="' . ((array_search($ac['parent'], (array_column($allCats, 'category')))) + 1);
            $parentString = ' parentId="' . $category['parent_id']. '"';
        }
        $yml .= '<category id="' . $category['category_id'].'"'.$parentString.'>' . $category_name . '</category>'.PHP_EOL;

    }
    $yml .= '</categories>' . PHP_EOL;
    $yml .= '<offers>' . PHP_EOL;

    return $yml;

}


function getYmlChunk($page)
{

    $skip = $page * 100;
    $limit = 100;

    $is_out = true;
    $sql_products = [];
    $sqlPre_products = db_get_array("SELECT product_id, amount, product_code FROM ?:products where amount > 0 LIMIT ".(int)$skip.", ".(int)$limit);
    foreach ($sqlPre_products as $s){
        $sql_products[] = $s['product_id'];
        $sql_productsA[$s['product_id']] = $s['amount'];
        $is_out = false;
    }
    //products

    //Categories

    $yml = [];


    if (count($sql_products) > 0) {
        $offerData = [];
        $sql = "SELECT pc.category_id, pc.product_id FROM ?:products_categories as pc WHERE pc.product_id IN(" . implode(',', $sql_products) . ") and link_type = 'A'";
        $sql_categories = db_get_array($sql);
        foreach ($sql_categories as $sc) {
            $offerData[$sc['product_id']] = '<categoryId>' . $sc['category_id'] . '</categoryId>';
        }

        $sql = 'SELECT DISTINCT fv.feature_id, fv.product_id, fvd.variant, fd.description FROM ?:product_features_values as fv 
        INNER JOIN ?:product_features_descriptions as fd ON fd.feature_id = fv.feature_id
        INNER JOIN ?:product_feature_variant_descriptions as fvd ON fv.variant_id = fvd.variant_id
        WHERE product_id IN(' . implode(',', $sql_products) . ') AND fv.lang_code = "' . CART_LANGUAGE . '"
        ORDER BY fv.product_id, fvd.variant ASC';
        $products_features_data = db_get_array($sql);

        $sql_products_prices = db_get_array(
            'SELECT product_id, price FROM ?:product_prices
        WHERE product_id IN(' . implode(',', $sql_products) . ')'
        );
        $products_prices = [];
        foreach ($sql_products_prices as $k => $pp) {
            $products_prices[$pp['product_id']] = $pp['price'];
        }

        //Offers
        $tMark = '';
        foreach ($products_features_data as $k => $pfd) {
            if ($pfd['description'] == 'Марка') {
                $tMark = $pfd['variant'];
            } elseif ($pfd['description'] == 'Модель' && $tMark != '') {
                $products_features_data[$k]['variant'] = trim(preg_replace('/' . $tMark . '/ui', '', $pfd['variant']));
                $tMark = '';
            }
        }

        foreach ($products_features_data as $pfd) {
            if ($pfd['description'] == 'Подраздел') {
                continue;
            } elseif ($pfd['description'] == 'Деталь') {
                $params[$pfd['product_id']][$pfd['feature_id']] = str_repeat('    ', 2) . '<name>' . prepareField($pfd['variant']) . '</name>';
                $descName[$pfd['product_id']] = prepareField($pfd['variant']);
                continue;
            } elseif ($pfd['description'] == 'VIN номер') {
                $pfd['description'] = 'Каталожный номер';
                //$descVin[$pfd['product_id']] = prepareField($pfd['variant']);
            } elseif ($pfd['description'] == 'Раздел')
                continue;
            $params[$pfd['product_id']][$pfd['feature_id']] = str_repeat('    ', 2) . '<param name="' . $pfd['description'] . '">' . prepareField($pfd['variant']) . '</param>';
        }
        $i = 1;
        foreach ($sql_products as $p) {
            //{if (!$product.hide_stock_info && !(($product_amount <= 0 || $product_amount < $product.min_qty) && ($product.avail_since > $smarty.const.TIME)))}
            $t = 'true';
            if (!$sql_productsA[$p]) {
                $t = 'false';
                continue;
            }
            $yml[] = str_repeat('    ', 1) . '<offer available="' . $t . '" id="' . abs($p) . '">';
            if ($t == 'true') {
                $yml[] = str_repeat('    ', 2) . '<pickup>true</pickup>';
                $yml[] = str_repeat('    ', 2) . '<delivery>true</delivery>';
            }
            //$priceString = $products_prices[array_search($p, $products_prices)]['price'];
            $priceString = $products_prices[$p];
            $priceString = substr($priceString, 0, strpos($priceString, '.'));
            if ($priceString == '0')
                $priceString = '';
            $yml[] = str_repeat('    ', 2) . '<price>' . $priceString . '</price>';
            $yml[] = str_repeat('    ', 2) . '<currencyId>' . CART_PRIMARY_CURRENCY . '</currencyId>';
            $yml[] = str_repeat('    ', 2) . $offerData[$p];

            $image = db_get_array('SELECT i.image_id, i.image_path FROM ?:images as i ' .
                'LEFT JOIN ?:images_links ON ?:images_links.detailed_id = i.image_id ' .
                'WHERE ?:images_links.object_id = '.abs($p).' limit 20'
            );
            if (!empty($image)) {
                foreach ($image as $img) {
                    $image_path = fn_attach_absolute_image_paths($img, 'detailed');
                    $yml[] = str_repeat('    ', 2) . '<picture>' . $image_path['https_image_path'] . '</picture>';
                }
            }

            foreach ($params[$p] as $param)
                $yml[] = $param;

            $yml[] = str_repeat('    ', 2) .'<description>'.PHP_EOL.'<![CDATA[<p>'.$descName[$p].' '.
                'Разборка XXXX</p>            
                <p>Вас приветствует разборка XXXX.</p>
                <p>Описание</p>'
                .']]>'.PHP_EOL.str_repeat('    ', 2) .'</description>';
            $yml[] = str_repeat('    ', 1) . '</offer>';
            $i++;
        }
    }


    if ($is_out) {
        return 'OUT';
    }

    return  implode(PHP_EOL, $yml);


}

if (php_sapi_name() === 'cli') {
    //header


    unlink(dirname(__FILE__) . '/ria_cron_logs/ria_cron_test.yml');
    $fp = fopen(dirname(__FILE__) . '/ria_cron_logs/ria_cron_test.yml', 'w');
    fwrite($fp, getYmlHeader());
    $page = 0;
    $products_yml = getYmlChunk($page);
    while ($products_yml !== 'OUT') {
        fwrite($fp, $products_yml);
        unset($products_yml);
        usleep(1000);
        $page++;
        $products_yml = getYmlChunk($page);
    }

    fwrite($fp, '</offers>' . PHP_EOL);
    fwrite($fp, '</shop>' . '</yml_catalog>');

}

function buildCurrencies(&$yml2_data)
{
    //
}

function currencyIsValid($currency)
{
    $currencies = array(
        'RUR',
        'RUB',
        'UAH',
        'BYN',
        'KZT',
        'USD',
        'EUR'
    );

    return in_array($currency, $currencies);
}
