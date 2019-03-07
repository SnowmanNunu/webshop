<?php
return array(
	//'配置项'=>'配置值'

    //对模板中访问的静态资源文件进行"路径"配置变量定义
    //以下配置变量内容的大小写与真实情况要保持一致
    //Home前台：
    'CSS_URL'   => '/Public/Home/style/',
    'JS_URL'   => '/Public/Home/js/',
    'IMG_URL'   => '/Public/Home/images/',
    //Admin后台：
    'AD_CSS_URL'   => '/Public/Admin/css/',
    'AD_JS_URL'   => '/Public/Admin/js/',
    'AD_IMG_URL'   => '/Public/Admin/images/',

    //为引入Plugin插件静态文件设置访问目录
    'PLUGIN_URL'    => '/Application/Common/Plugin/',

    /* 数据库设置 */
    'DB_TYPE'               =>  'mysql',     // 数据库类型
    'DB_HOST'               =>  'localhost', // 服务器地址
    'DB_NAME'               =>  'tpshop',          // 数据库名
    'DB_USER'               =>  'root',      // 用户名
    'DB_PWD'                =>  '',          // 密码
    'DB_PORT'               =>  '3306',        // 端口
    'DB_PREFIX'             =>  'sp_',    // 数据库表前缀

);