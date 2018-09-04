<?php
/**
 * Enquiries plugin for Craft CMS 3.x
 *
 * Plugin to log enquiries and manage notifications
 *
 * @link      http://ournameismud.co.uk/
 * @copyright Copyright (c) 2018 @cole007
 */

namespace ournameismud\enquiries\models;

use ournameismud\enquiries\Enquiries;

use Craft;
use craft\base\Model;

/**
 * @author    @cole007
 * @package   Enquiries
 * @since     1.0.0
 */
class Notifications extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $someAttribute = 'Some Default';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['form'], 'number'],
            [['recipients', 'subject'], 'string'],
            [['message'], 'mixed'],
            [['copyFields'], 'boolean']
        ];
    }
}
