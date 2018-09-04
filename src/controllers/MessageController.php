<?php
/**
 * Enquiries plugin for Craft CMS 3.x
 *
 * Plugin to log enquiries and manage notifications
 *
 * @link      http://ournameismud.co.uk/
 * @copyright Copyright (c) 2018 @cole007
 */

namespace ournameismud\enquiries\controllers;

use ournameismud\enquiries\Enquiries;
use ournameismud\enquiries\records\MessageLogs as MessageRecord;
use Craft;
use craft\web\Controller;

/**
 * @author    @cole007
 * @package   Enquiries
 * @since     1.0.0
 */
class MessageController extends Controller
{

    protected $allowAnonymous = ['index'];

    public function actionIndex()
    {
        $result = 'Welcome to the FormController actionIndex() method';

        return $result;
    }

    public function actionTemplate($messageId)
    {
        $variables = [];
        $messageRecord = MessageRecord::find()
            ->where(['id'=>$messageId])->one();
        // $variables['subject'] = $notificationRecord->subject;
        // $variables['recipients'] = $notificationRecord->recipients;
        // $variables['form'] = $notificationRecord->form;
        // $variables['notificationId'] = $notificationRecord->id;
        $variables['id'] = $messageId;
        $variables['subject'] = $messageRecord->subject;
        $variables['recipient'] = $messageRecord->recipient;
        $variables['message'] = $messageRecord->message;
        $variables['dateUpdated'] = $messageRecord->dateUpdated;
        $variables['form'] = $messageRecord->form;
        // Craft::dd($formRecords);        
        return $this->renderTemplate('enquiries/message-logs/new', $variables);
        // 
    }
}
