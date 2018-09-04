<?php
/**
 * Enquiries plugin for Craft CMS 3.x
 *
 * Plugin to log enquiries and manage notifications
 *
 * @link      http://ournameismud.co.uk/
 * @copyright Copyright (c) 2018 @cole007
 */

namespace ournameismud\enquiries\assetbundles\Enquiries;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;
// use craft\redactor\assets\field;

/**
 * @author    @cole007
 * @package   Enquiries
 * @since     1.0.0
 */
class EnquiriesAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@ournameismud/enquiries/assetbundles/enquiries/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/Enquiries.js',
        ];

        $this->css = [
            'css/Enquiries.css',
        ];

        parent::init();
    }
}
