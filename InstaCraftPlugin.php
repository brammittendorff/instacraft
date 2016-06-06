<?php
namespace craft;

/**
 * InstaCraft
 *
 * @author    Bram Mittendorff <bram@nerds.company>
 * @copyright Copyright (c) 2016, Bram Mittendorff
 * @license   MIT
 *
 * @link      https://github.com/brammittendorff/
 */
class InstaCraftPlugin extends BasePlugin
{
    /**
     * Get plugin name.
     *
     * @return string
     */
    public function getName()
    {
        return Craft::t('InstaCraft');
    }
    /**
     * Get plugin description.
     *
     * @return string
     */
    public function getDescription()
    {
        return Craft::t('An instagram image puller for Craft CMS with and without OAuth');
    }
    /**
     * Get plugin version.
     *
     * @return string
     */
    public function getVersion()
    {
        return '0.1.0';
    }
    /**
     * Get plugin developer.
     *
     * @return string
     */
    public function getDeveloper()
    {
        return 'Bram Mittendorff';
    }
    /**
     * Get plugin developer url.
     *
     * @return string
     */
    public function getDeveloperUrl()
    {
        return 'https://www.nerds.company';
    }
    /**
     * Get plugin documentation url.
     *
     * @return string
     */
    public function getDocumentationUrl()
    {
        return 'https://github.com/brammittendorff/instacraft';
    }
    /**
     * Has Control Panel section.
     *
     * @return bool
     */
    public function hasCpSection()
    {
        return true;
    }
}
