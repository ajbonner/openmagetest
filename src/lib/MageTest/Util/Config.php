<?php

class MageTest_Util_Config
{
    private static array $_newConfigValues = [];
    private static array $_originalConfigValues = [];
    private static array $_removedConfigValues = [];

    /**
     * Set an internal config value for Magento
     *
     * Original values will be stores internally and then restored after
     * all tests have been run with resetConfig().
     *
     * @return void
     * @author Alistair Stead
     **/
    public static function set($path, $value, $scope = null): void
    {
        $configCollection = Mage::getModel('core/config_data')->getCollection();

        $configCollection->addFieldToFilter('path', ['eq' => $path]);
        if (is_string($scope)) {
            $configCollection->addFieldToFilter('scope', ['eq' => $scope]);
        }
        $configCollection->load();

        // If existing config does not exist create it
        if (count($configCollection) === 0) {
            $configData = Mage::getModel('core/config_data');
            $configData->setPath($path);
            $configData->setValue($value);
            // Calculate scope
            $scope = ($scope) ?: 'default';
            $configData->setScope($scope);
            $configData->save();
            self::$_newConfigValues[] = $configData;
        }
        foreach ($configCollection as $config) {
            self::$_originalConfigValues[] = $config;
            $config->setValue($value);
            $config->save();
        }
        unset(
            $configCollection,
            $configData
        );
    }

    /**
     * Remove an internal config value from Magento
     *
     * This mimics the functionality of the admin when you set a yes|no option
     * to no. Original values will be stores internally and then restored after
     * all tests have been run with resetConfig().
     *
     * @return void
     * @author Alistair Stead
     **/
    public static function remove($path, $scope = null): void
    {
        $configCollection = Mage::getModel('core/config_data')->getCollection();
        $configCollection->addFieldToFilter('path', ['eq' => $path]);
        if (is_string($scope)) {
            $configCollection->addFieldToFilter('scope', ['eq' => $scope]);
        }
        $configCollection->load();
        foreach ($configCollection as $config) {
            self::$_removedConfigValues[] = $config;
            $config->delete();
        }
        unset($configCollection);
    }

    /**
     * Reset the Magento config to its original values.
     *
     * @return void
     * @author Alistair Stead
     **/
    public static function reset()
    {
        $config = Mage::getModel('core/config_data');
        // Reset the original values
        foreach (self::$_originalConfigValues as $value) {
            // $config->reset();
            $config->load($value->getId());
            $config->setValue($value->getValue());
            $config->save();
        }
        // Remove the new config valuse
        foreach (self::$_newConfigValues as $value) {
            // $config->reset();
            $config->load($value->getId());
            $config->delete();
        }
        // Create the values that were removed
        foreach (self::$_removedConfigValues as $value) {
            // $config->reset();
            $config->setPath($value->getPath());
            $config->setValue($value->getValue());
            // Calculate scope
            $scope = $value->getScope() ?: 'default';
            $config->setScope($scope);
            $config->save();
        }
        unset($config);
        self::$_originalConfigValues = self::$_newConfigValues = self::$_removedConfigValues = [];
    }
}
