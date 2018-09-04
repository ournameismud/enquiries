<?php
/**
 * Enquiries plugin for Craft CMS 3.x
 *
 * Plugin to log enquiries and manage notifications
 *
 * @link      http://ournameismud.co.uk/
 * @copyright Copyright (c) 2018 @cole007
 */

namespace ournameismud\enquiries\assetbundles\submissionscpsection;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    @cole007
 * @package   Enquiries
 * @since     1.0.0
 */
class SubmissionsCPSectionAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@ournameismud/enquiries/assetbundles/submissionscpsection/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/Submissions.js',
        ];

        $this->css = [
            'css/Submissions.css',
        ];

        parent::init();
    }
}
