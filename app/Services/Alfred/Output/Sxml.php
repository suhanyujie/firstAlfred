<?php
/**
 * Created by PhpStorm.
 * User: suhanyu
 * Date: 18/3/24
 * Time: 下午7:31
 */

namespace App\Services\Alfred\Output;


class Sxml
{
    public static function renderXml($paramArr=[])
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
        $string = '<item valid="yes" arg="' . $options['nextArg'] . '" uid="' . $options['index'] . '">
					<title><![CDATA[' . $options['title'] . ']]></title>
					<subtitle><![CDATA[' . $options['data'] . ']]> </subtitle>
					<icon>icon.png</icon>
			    </item>';
    
        return $string;
    }
}