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
/*
 *
 * Наследуемая сущность пользователя из движка
 *
 */

class PluginAdmin_ModuleUser_EntityUser extends PluginAdmin_Inherits_ModuleUser_EntityUser
{

    /*
     * кешированная сущность бана на время сессии
     */
    private $oBan = null;
    /*
     * флаг проверки бана (для кеширования на момент сессии)
     */
    private $bBanChecked = false;

    public function Init() {
        $this->aValidateRules = array_merge($this->aValidateRules, [
            array(
                'mail', 
                'email', 
                'on' => array('import')
            ),
            [   
                'mail', 
                'mail_exists', 
                'on' => array('import')
            ],
            [
                'login', 
                'login_exists', 
                'on' => array('import')
            ],
            [
                'login', 
                'login', 
                'on' => array('import')
            ],
            [   'name', 
                'string', 
                'min' => 3, 
                'max' => 200, 
                'on' => array( 'import')
            ],
            [   
                'role', 
                'string', 
                'allowEmpty' => false,
                'on' => array('import')
            ],
            [   
                'role', 
                'role_exists', 
                'on' => array('import')
            ],
            [   
                'phone', 
                'string',
                'max' => 20,
                'on' => array( 'import')
            ],
            [   
                'site', 
                'url',
                'max' => 500,
                'on' => array( 'import')
            ],
            [   
                'address', 
                'string',
                'max' => 500,
                'on' => array( 'import')
            ],
            [   
                'about', 
                'string',
                'max' => 1000,
                'on' => array( 'import')
            ],
            [
                'password', 
                'string', 
                'min' => 10,
                'max' => 50,
                'on' => array('import'),
            ],
            [   
                'photo', 
                'url',
                'on' => array( 'import')
            ],
        ]);
    }
    

    /**
     * Проверить забанен пользователь или нет (возвращает объект бана в случае успеха)
     *
     * @return Entity|null
     */
    public function getBanned()
    {
        return $this->PluginAdmin_Users_GetUserBannedByUser($this);
    }


    /**
     * Проверить забанен пользователь или нет (возвращает объект бана в случае успеха)
     * tip: с использованием сессионного кеша
     *
     * @return Entity|null
     */
    public function getBannedCached()
    {
        if (!$this->bBanChecked) {
            $this->oBan = $this->getBanned();
            $this->bBanChecked = true;
        }
        return $this->oBan;
    }


    /**
     * Забанен ли пользователь полностью (без доступа к сайту), возвращает объект бана в случае успеха
     *
     * @return Entity|null
     */
    public function getBannedCachedFully()
    {
        if ($oBan = $this->getBannedCached() and $oBan->getIsFull()) {
            return $oBan;
        }
        return null;
    }


    /**
     * Переведен ли пользователь в режим "только чтение", без возможности что либо публиковать, возвращает объект бана в случае успеха
     *
     * @return Entity|null
     */
    public function getBannedCachedForReadOnly()
    {
        if ($oBan = $this->getBannedCached() and $oBan->getIsReadOnly()) {
            return $oBan;
        }
        return null;
    }

}

?>