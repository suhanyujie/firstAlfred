<?php
/**
 * Created by PhpStorm.
 * User: suhanyu
 * Date: 18/3/24
 * Time: 下午8:56
 */

namespace App\Services\Alfred\Tool;

use GuzzleHttp\Client;
use QL\QueryList;
use App\Services\Alfred\Output\Sxml;

class SphpManual
{
    /**
     * @desc 获取php函数手册
     * @return string
     */
    public function phpFunctionManual($paramArr=[])
    {
        $options = [
            'functionName' => '',
        ];
        is_array($paramArr) && $options = array_merge($options, $paramArr);
        extract($options);
        $client = new Client([
            'base_uri' => 'http://php.net',// http://php.net/manual/zh/function.json-encode.php
            'timeout'  => 5,
        ]);
        // 查询的url拼接，因为PHP官网的搜索会将下划线_替换成-
        $tmpName = str_replace('_','-',$functionName);
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
            $xmlString .= Sxml::renderXml([
                'title'   => $row['functionName'],
                'data'    => $row['description'],
                'id'      => $k,
                'index'   => $k,
                'nextArg' => $tmpName,
            ]);
        }
        $xmlString .= '</items>';
        return $xmlString;
    }
}