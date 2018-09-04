<?php
/**
 * Enquiries plugin for Craft CMS 3.x
 *
 * Plugin to log enquiries and manage notifications
 *
 * @link      http://ournameismud.co.uk/
 * @copyright Copyright (c) 2018 @cole007
 */

namespace ournameismud\enquiries\assetbundles\formfield;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    @cole007
 * @package   Enquiries
 * @since     1.0.0
 */
class FormFieldAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@ournameismud/enquiries/assetbundles/formfield/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/Form.js',
        ];

        $this->css = [
            'css/Form.css',
        ];

        parent::init();
    }
}
