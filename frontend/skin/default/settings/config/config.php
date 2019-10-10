<?php

$config = array();

// Подключение ресурсов шаблона
$config['assets']['template'] = [
    'js' => array(
        
    ),

    // Подключение стилей шаблона
    'css' => array(
        // Plugin style
        '___path.plugin.admin.template___/assets/css/base.css',
        '___path.plugin.admin.template___/assets/css/layout.css',
        // Vendor style
//        '___path.plugin.admin.template___/assets/css/vendor/jquery.notifier.css',
//        '___path.plugin.admin.template___/assets/css/vendor/icheck/skins/livestreet/minimal.css',
        ),
    'img' => [
        "logo" => "___path.plugin.admin.template___/assets/images/logo.png",
        "favicon" => "___path.plugin.admin.template___/assets/images/favicon.ico",
        'default_avatar' => "___path.plugin.admin.template___/assets/images/avatars/avatar_male_100x100crop.png",
    ]
];  

$config['components'] = [
// Базовые компоненты
    'admin:css-reset', 'admin:css-helpers', 'admin:typography', 'admin:forms', 'admin:grid', 'admin:ls-vendor', 'admin:ls-core', 'admin:ls-component', 'admin:lightbox',
    'admin:slider', 'admin:details', 'admin:alert', 'admin:dropdown', 'admin:button', 'admin:block', 'admin:confirm',
    'admin:nav', 'admin:tooltip', 'admin:tabs', 'admin:modal', 'admin:table', 'admin:text', 'admin:uploader', 'admin:email', 'admin:field', 'admin:pagination',
    'admin:editor', 'admin:more', 'admin:crop', 'admin:performance', 'admin:toolbar', 'admin:actionbar', 'admin:badge',
    'admin:autocomplete', 'admin:icon', 'admin:item', 'admin:highlighter', 'admin:jumbotron', 'admin:notification', 'admin:blankslate', 'admin:info-list',

    // Компоненты админки
    'admin:p-plugin', 'admin:p-skin', 'admin:p-settings', 'admin:p-actionbar', 'admin:p-cron', 'admin:p-property', 'admin:p-topic', 'admin:p-category', 'admin:p-optimization',
    'admin:p-form', 'admin:p-rbac', 'admin:p-user', 'admin:p-menu', 'admin:p-dashboard', 'admin:p-graph', 'admin:p-userbar',

    // Компоненты LS CMS
    'admin:note', 'admin:icons-contact', 'admin:toolbar-scrollup', 'admin:toolbar-scrollnav',
    'admin:media', 'admin:property', 'admin:content', 'admin:activity',
];



return $config;
