<?php
/**
 * Enquiries plugin for Craft CMS 3.x
 *
 * Plugin to log enquiries and manage notifications
 *
 * @link      http://ournameismud.co.uk/
 * @copyright Copyright (c) 2018 @cole007
 */

namespace ournameismud\enquiries\services;

use ournameismud\enquiries\Enquiries;

use Craft;
use craft\base\Component;

/**
 * @author    @cole007
 * @package   Enquiries
 * @since     1.0.0
 */
class Forms extends Component
{
    // Public Methods
    // =========================================================================

    /*
     * @return mixed
     */
    public function exampleService()
    {
        $result = 'something';
        // Check our Plugin's settings for `someAttribute`
        if (Enquiries::$plugin->getSettings()->someAttribute) {
        }

        return $result;
    }
}
