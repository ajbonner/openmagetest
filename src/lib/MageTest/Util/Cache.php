<?php

/**
* 
*/
class MageTest_Util_Cache
{
    
    /**
     * Enable the Magento cache
     *
     * @return void
     * @author Alistair Stead
     **/
    public static function enable($types = null)
    {
        if (is_null($types)) {
            $ypes = array(
                'config',
                'layout',
                'block_html',
                'translate',
                'collections',
                'eav',
                'config_api'
            );
        }
        $allTypes = Mage::app()->useCache();
        $cacheTypes = array();
        foreach ($types as $type) {
            $cacheTypes[] = $type->getId();
        }

        $updatedTypes = 0;
        foreach ($cacheTypes as $code) {
            if (empty($allTypes[$code])) {
                $allTypes[$code] = 1;
                $updatedTypes++;
            }
        }
        if ($updatedTypes > 0) {
            Mage::app()->saveUseCache($allTypes);
        }
    }
    
    /**
     * Disable the Magento cache
     *
     * @return void
     * @author Alistair Stead
     **/
    public static function disable($types = null)
    {
        if (is_null($types)) {
            $ypes = array(
                'config',
                'layout',
                'block_html',
                'translate',
                'collections',
                'eav',
                'config_api'
            );
        }
        $allTypes = Mage::app()->useCache();
        $cacheTypes = array();
        foreach ($types as $type) {
            $cacheTypes[] = $type->getId();
        }

        $updatedTypes = 0;
        foreach ($cacheTypes as $type) {
            if (!empty($allTypes[$code])) {
                $allTypes[$code] = 0;
                $updatedTypes++;
            }
            $tags = Mage::app()->getCacheInstance()->cleanType($type);
        }
        if ($updatedTypes > 0) {
            Mage::app()->saveUseCache($allTypes);
        }
    }
    
    /**
     * Clear the Magento cache
     *
     * @return void
     * @author Alistair Stead
     **/
    public static function clean($types = null)
    {
        if (is_null($types)) {
            $types = array(
                'config',
                'layout',
                'block_html',
                'translate',
                'collections',
                'eav',
                'config_api'
            );
        }
        
        if (!empty($types)) {
            foreach ($types as $type) {
                $tags = Mage::app()->getCacheInstance()->cleanType($type);
            }
        }
    }
    
    /**
     * Entirely flush the cache within the system
     *
     * @return void
     * @author Alistair Stead
     **/
    public static function flush()
    {
        Mage::app()->getCacheInstance()->flush();
    }
}
