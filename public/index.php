<?php
/**
 * Created by PhpStorm.
 * User: suhanyu
 * Date: 18/3/21
 * Time: 上午9:59
 */
define('_ROOT_', dirname(__DIR__));

include('vendor/autoload.php');

use GuzzleHttp\Client;
use QL\QueryList;

$param1 = $argv[1];
$client = new Client([
    'base_uri' => 'http://php.net',// http://php.net/manual/zh/function.json-encode.php
    'timeout'  => 5,
]);
// 查询的url拼接，因为PHP官网的搜索会将下划线_替换成-
$tmpName = str_replace('_','-',$param1);
$response = $client->request('get', 'manual/zh/function.'.$tmpName.'.php');
$body = $response->getBody();

$rules = [
    'functionName'=>['#layout-content h1.refname', 'text'],
    'description'=>['#layout-content div.description p.rdfs-comment', 'text'],
];
$ql = QueryList::html($body)->rules($rules)->query();
$data = $ql->getData()->all();
// 渲染成xml输出
$xmlString = '<?xml version="1.0" encoding="utf-8"?>
<items>';
foreach ($data as $k=>$row) {
    $xmlString .= renderXml([
        'title'   => $row['functionName'],
        'data'    => $row['description'],
        'id'      => $k,
        'index'   => $k,
        'nextArg' => $tmpName,
    ]);
}
$xmlString .= '</items>';
echo $xmlString;die;

/**
 * @desc 将数据渲染成Alfred需要的xml
 * @param array $paramArr
 * @return string
 */
function renderXml($paramArr = [])
{
    $options = [
        'title'   => '',
        'data'    => '',
        'id'      => '',
        'index'   => '',
        'nextArg' => '',
    ];
    is_array($paramArr) && $options = array_merge($options, $paramArr);
    extract($options);
    $string = '<item valid="yes" arg="' . $nextArg . '" uid="' . $index . '">
					<title><![CDATA[' . $title . ']]></title>
					<subtitle><![CDATA[' . $data . ']]> </subtitle>
					<icon>icon.png</icon>
			    </item>';
    
    return $string;
}

