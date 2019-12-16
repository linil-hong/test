<?php
return array (
  'db' => 
  array (
    'type' => 'pdo_mysql',
    'mysql' => 
    array (
      'master' => 
      array (
        'host' => '127.0.0.1',
        'user' => 'iMuto',
        'password' => 'Dbk_imuto#9102',
        'name' => 'bbs',
        'tablepre' => 'bbs_',
        'charset' => 'utf8',
        'engine' => 'myisam',
      ),
      'slaves' => 
      array (
      ),
    ),
    'pdo_mysql' => 
    array (
      'master' => 
      array (
        'host' => '127.0.0.1',
        'user' => 'iMuto',
        'password' => 'Dbk_imuto#9102',
        'name' => 'bbs',
        'tablepre' => 'bbs_',
        'charset' => 'utf8',
        'engine' => 'myisam',
      ),
      'slaves' => 
      array (
      ),
    ),
  ),
  'cache' => 
  array (
    'enable' => true,
    'type' => 'mysql',
    'memcached' => 
    array (
      'host' => 'localhost',
      'port' => '11211',
      'cachepre' => 'bbs_',
    ),
    'redis' => 
    array (
      'host' => 'localhost',
      'port' => '6379',
      'cachepre' => 'bbs_',
    ),
    'xcache' => 
    array (
      'cachepre' => 'bbs_',
    ),
    'yac' => 
    array (
      'cachepre' => 'bbs_',
    ),
    'apc' => 
    array (
      'cachepre' => 'bbs_',
    ),
    'mysql' => 
    array (
      'cachepre' => 'bbs_',
    ),
  ),
  'tmp_path' => './tmp/',
  'log_path' => './log/',
  'view_url' => 'view/',
  'upload_url' => 'upload/',
  'upload_path' => './upload/',
  'logo_mobile_url' => 'view/img/logo.png',
  'logo_pc_url' => 'view/img/logo.png',
  'logo_water_url' => 'view/img/water-small.png',
  'sitename' => 'iMuto_bbs',
  'sitebrief' => 'Site Brief',
  'timezone' => 'Asia/Shanghai',
  'lang' => 'en-us',
  'runlevel' => 5,
  'runlevel_reason' => 'The site is under maintenance, please visit later.',
  'cookie_domain' => '',
  'cookie_path' => '',
  'auth_key' => 'D42XTX5JB7FK5ETV8YQ3FGZ9N7FMWWHDEK997Y596USH3HT6BUR4USDRFYXCK3TP',
  'pagesize' => 20,
  'postlist_pagesize' => 100,
  'cache_thread_list_pages' => 10,
  'online_update_span' => 120,
  'online_hold_time' => 3600,
  'session_delay_update' => 0,
  'upload_image_width' => 927,
  'order_default' => 'lastpid',
  'attach_dir_save_rule' => 'Ym',
  'update_views_on' => 1,
  'user_create_email_on' => 0,
  'user_create_on' => 1,
  'user_resetpw_on' => 0,
  'admin_bind_ip' => 0,
  'cdn_on' => 0,
  'url_rewrite_on' => 0,
  'disabled_plugin' => 0,
  'version' => '4.0.4',
  'static_version' => '?1.0',
  'installed' => 1,
);
?>