<?php

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
return [
    //别名配置,别名只能是映射到控制器且访问时必须加上请求的方法
    '__alias__'   => [
        'chickenleg'   => 'admin/index/login',
//        'news'  => 'index/news/index',
        //'new/:id' => 'index/news/read?id=:id',
    ],
    '[new]'     => [
        ':id'   => ['index/news/read', ['method' => 'get'], ['id' => '\d+']],
        ':title' => ['index/news/read', ['method' => 'post'],['title' => '\w+']],
    ],
    //'new/:id' => '/index.php/index/news/read?id=:id',
    //变量规则
    '__pattern__' => [
        //'/^new\/(\d+)$/' => '/index.php/index/news/read?id=$1',
    ],
//        域名绑定到模块
//        '__domain__'  => [
//            'admin' => 'admin',
//            'api'   => 'api',
//        ],
];