<?php
/**
 * LiveStreet CMS
 * Copyright © 2013 OOO "ЛС-СОФТ"
 *
 * ------------------------------------------------------
 *
 * Official site: www.livestreetcms.com
 * Contact e-mail: office@livestreetcms.com
 *
 * GNU General Public License, version 2:
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * ------------------------------------------------------
 *
 * @link http://www.livestreetcms.com
 * @copyright 2013 OOO "ЛС-СОФТ"
 * @author Serge Pustovit (PSNet) <light.feel@gmail.com>
 *
 */
$bans = require __DIR__ . '/bans.php';

$utils = require __DIR__ . '/encoding_checking_dirs.php';

$config_scheme = require __DIR__ . '/config_scheme.php';

$config_sections = require __DIR__ . '/config_sections.php';

$settings = require __DIR__ . '/settings.php';

$users = require __DIR__ . '/users.php';

$config = array();
/*
 * количество событий по-умолчанию для ленты последней активности главной страницы админки
 */
$config['dashboard']['stream']['count_default'] = 5;

/*
 * список значений количества элементов на страницу в выпадающем списке
 */
$config['pagination']['values_for_select_elements_on_page'] = array(10, 30, 100);        // range(5, 100, 5)

/*
 * макс. количество точек на графике (фильтрует подписи по оси х)
 */
$config['stats']['max_points_on_graph'] = 10;

/*
 * роутер
 */
$config['$root$']['router']['page']['admin'] = 'PluginAdmin_ActionAdmin';

/*
 * таблицы
 */
$config['$root$']['db']['table']['users_ban'] = '___db.table.prefix___admin_users_ban';

/**
 * Список базовых компонентов для админки
 */
$config['$root$']['components'] = array(
    // Базовые компоненты
//    'ls-vendor', 
//    'ls-core', 
//    'ls-component', 
//    'notification', 
//    'performance', 
////    'confirm', 
//    'lightbox', 
//    'bootstrap',
//    'tinymce',
//    'dropdown', 
//    'form', 
//    'pagination', 
//    'nav', 
//    'ajax',
//    'icon', 
//    'autocomplete',
//    'popover',
//    'text', 
//    'button'
    
);

$config['bans'] = $bans;

$config['utils'] = $utils;

$config['$config_scheme$'] = $config_scheme;

$config['$config_sections$'] = $config_sections;

$config['settings'] = $settings;

$config['users'] = $users;

return $config;