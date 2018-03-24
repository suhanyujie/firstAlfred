<?php
/**
 * Created by PhpStorm.
 * User: suhanyu
 * Date: 18/3/24
 * Time: 下午7:30
 */

namespace App\Services\Alfred\Tool;

use App\Services\Alfred\Output\Sxml;
use Illuminate\Validation\Validator;
use GuzzleHttp\Client;
use QL\QueryList;

class Syoudao
{
    const API_URL = 'http://openapi.youdao.com/api';
    
    
    
    public function youdao($paramArr = [])
    {
        $options = [
            'keyword' => '',
        ];
        is_array($paramArr) && $options = array_merge($options, $paramArr);
        extract($options);
        $param = $this->initParam([
            'q' => $keyword,
        ]);
        $result = $this->post([
            'param' => $param,
        ]);
        $result = $this->setArray($result);
        $xmlString = '<?xml version="1.0" encoding="utf-8"?>
<items>';
        foreach ($result as $k=>$item) {
            $xmlString .= Sxml::renderXml($item);
        }
        $xmlString .= '</items>';
        
        return $xmlString;
    }
    
    /**
     * @desc 将api返回的数组拼装成alfred需要的单元
     * @param array $arr
     * @return array
     */
    public function setArray($arr = [])
    {
        $resultArr = [
            0 => [
                'id'      => 'init',
                'index'   => 'init',
                'title'   => implode(',', $arr['translation']),
                'data'    => $arr['query'],
                'nextArg' => $arr['query'],
            ],
        ];
        if (isset($arr['web']) && $arr['web']) {
            foreach ($arr['web'] as $k=>$item) {
                $tmp = [
                    'id'      => $k,
                    'index'   => $k,
                    'title'   => implode(',', $item['value']),
                    'data'    => $item['key'],
                    'nextArg' => $arr['query'],
                ];
                $resultArr[] = $tmp;
            }
        }
        
        return $resultArr;
    }
    
    /**
     * @desc 向有道api发送post请求
     * @param array $paramArr
     * @return array
     */
    public function post($paramArr = [])
    {
        $options = [
            'param' => '',
        ];
        is_array($paramArr) && $options = array_merge($options, $paramArr);
        extract($options);
        $client = new Client();
        $result = $client->request('post', self::API_URL, [
            'form_params' => $param,
        ]);
        $result = (string)$result->getBody();
        $result = \GuzzleHttp\json_decode($result, true);
        
        return $result;
    }
    
    /**
     * @desc 参数请参考有道翻译api的文档 http://ai.youdao.com/docs/doc-trans-api.s#p02
     * @param array $paramArr
     * @return array
     */
    public function initParam($paramArr = [])
    {
        $options = [
            'q'         => '',
        ];
        is_array($paramArr) && $options = array_merge($options, $paramArr);
        extract($options);
        $param = [
            'q'         => $q,
            'from'      => 'auto',
            'to'        => 'auto',
            'appKey'    => env('YOUDAO_APP_ID', ''),
            'secretKey' => env('YOUDAO_APP_SECRET_KEY', ''),
            'salt'      => mt_rand(10000, 99999),
            'ext'       => 'wav',
            'voice'     => 1,
        ];
        $sign = $this->getSign([
            'q'         => $q,
            'appKey'    => $param['appKey'],
            'salt'      => $param['salt'],
            'secretKey' => $param['secretKey'],
        ]);
        $param['sign'] = $sign;
        
        return $param;
    }
    
    /**
     * @desc 按照有道的文档规则，获取秘钥
     *  http://ai.youdao.com/docs/doc-trans-api.s#p08
     * @param array $paramArr
     * @return string
     */
    public function getSign($paramArr = [])
    {
        $options = [
            'q'         => '',
            'appKey'    => '',
            'salt'      => '',
            'secretKey' => '',
        ];
        is_array($paramArr) && $options = array_merge($options, $paramArr);
        extract($options);
        // todo 参数验证
        
        $str = $appKey.$q.$salt.$secretKey;
        return md5($str);
    }
}