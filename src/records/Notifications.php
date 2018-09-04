<?php
/**
 * Enquiries plugin for Craft CMS 3.x
 *
 * Plugin to log enquiries and manage notifications
 *
 * @link      http://ournameismud.co.uk/
 * @copyright Copyright (c) 2018 @cole007
 */

namespace ournameismud\enquiries\records;

use ournameismud\enquiries\Enquiries;

use Craft;
use craft\db\ActiveRecord;

/**
 * @author    @cole007
 * @package   Enquiries
 * @since     1.0.0
 */
class Notifications extends ActiveRecord
{
    // Public Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%enquiries_notifications}}';
    }
}
