<?php
/**
 * Enquiries plugin for Craft CMS 3.x
 *
 * Plugin to log enquiries and manage notifications
 *
 * @link      http://ournameismud.co.uk/
 * @copyright Copyright (c) 2018 @cole007
 */

namespace ournameismud\enquiries;

use ournameismud\enquiries\services\Forms as FormsService;
use ournameismud\enquiries\services\Notifications as NotificationsService;
use ournameismud\enquiries\services\Submissions as SubmissionsService;
use ournameismud\enquiries\variables\EnquiriesVariable;
use ournameismud\enquiries\models\Settings;
use ournameismud\enquiries\fields\Form as FormField;
use ournameismud\enquiries\twigextensions\EnquiriesTwigExtension;


use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\web\UrlManager;
use craft\services\Fields;
use craft\web\twig\variables\CraftVariable;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;

use yii\base\Event;

/**
 * Class Enquiries
 *
 * @author    @cole007
 * @package   Enquiries
 * @since     1.0.0
 *
 * @property  FormsService $forms
 * @property  NotificationsService $notifications
 * @property  SubmissionsService $submissions
 */
class Enquiries extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var Enquiries
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1.0.3.3';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['siteActionTrigger1'] = 'enquiries/form';
            }
        );

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['cpActionTrigger1'] = 'enquiries/form/do-something';
                $event->rules['enquiries/forms/<formId:\w+>'] = 'enquiries/form/template';
                $event->rules['enquiries/notifications/<notificationId:\w+>'] = 'enquiries/notification/template';
                $event->rules['enquiries/submissions/<submissionId:\w+>'] = 'enquiries/submission/template';
                $event->rules['enquiries/message-logs/<messageId:\w+>'] = 'enquiries/message/template';
                // $event->rules['enquiries/forms/<formId:\w+>'] = ['template' => 'enquiries/forms/new'];
            }
        );

        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = FormField::class;
            }
        );

        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('enquiries', EnquiriesVariable::class);
            }
        );

        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                }
            }
        );

        $extension = new EnquiriesTwigExtension();
        Craft::$app->view->registerTwigExtension($extension);
        
        Craft::info(
            Craft::t(
                'enquiries',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    public function getCpNavItem()
    {
        $item = parent::getCpNavItem();
        $item['subnav'] = [
            'forms' => ['label' => 'Tests', 'url' => 'enquiries/forms'],
            'notifications' => ['label' => 'Seals', 'url' => 'enquiries/notifications'],
            'submissions' => ['label' => 'Searches', 'url' => 'enquiries/submissions'],
            'message-logs' => ['label' => 'Favourites', 'url' => 'enquiries/message-logs'],
        ];
        return $item;

    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml(): string
    {
        return Craft::$app->view->renderTemplate(
            'enquiries/settings',
            [
                'settings' => $this->getSettings()
            ]
        );
    }
}
