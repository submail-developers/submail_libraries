<?php
function get_option()
{
    $arr['sign'] =
        array(
            'title' => '短信签名',
            'type' => 'text',
            'value' => '',
            'tip' => '（必须配置）'
        );
    return $arr;
}

return array_merge(array(
    'switch' => array(//配置在表单中的键名 ,这个会是config[title]
        'title' => '是否开启赛邮云短信：',//表单的文字
        'type' => 'radio',         //表单的类型：text、textarea、checkbox、radio、select等
        'options' => array(
            '1' => '启用',
            '0' => '禁用',
        ),
        'value' => '1',
        'tip' => '默认开启'
    ),
),
    get_option()
);

