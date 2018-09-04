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
use ournameismud\enquiries\records\Notifications as NotificationRecord;
use Craft;
use craft\web\Controller;

/**
 * @author    @cole007
 * @package   Enquiries
 * @since     1.0.0
 */
class NotificationController extends Controller
{

    protected $allowAnonymous = ['index'];

    public function actionIndex()
    {
        $result = 'Welcome to the FormController actionIndex() method';

        return $result;
    }

    protected function validateEmail($email) {
        $validator = new \yii\validators\EmailValidator();
        if ($validator->validate($email, $error)) {
            return true;
        } else {
            return false;
        }
    }
    public function actionTemplate($notificationId)
    {
        $variables = [];
        $notificationRecord = NotificationRecord::find()
            ->where(['id'=>$notificationId])->one();
        $variables['subject'] = $notificationRecord->subject;
        $variables['recipients'] = $notificationRecord->recipients;
        $variables['form'] = $notificationRecord->form;
        $variables['notificationId'] = $notificationRecord->id;
        $variables['message'] = $notificationRecord->message;
        $variables['copyFields'] = $notificationRecord->copyFields;
        // Craft::dd($formRecords);        
        return $this->renderTemplate('enquiries/notifications/new', $variables);
        // 
    }
    public function actionUpdate()
    {
        $this->requirePostRequest();
        $request = Craft::$app->getRequest();
        $site = Craft::$app->getSites()->getCurrentSite();   

        $notificationId = $request->getBodyParam('notificationId');
        if (isset($notificationId) && $notificationId != '') {
            $notificationRecord = NotificationRecord::find()
            ->where(['id'=>$notificationId])->one();
            // Craft::dd($notificationId);
            // if count == null return false
        } else {
            $notificationRecord = new NotificationRecord;
        }
        $success = true;

        $formId = $request->getBodyParam('form');
        if (!isset($formId)) $success = false;
        // Craft::dd(strlen(trim($formId)));
        
        $recipients = $request->getBodyParam('recipients');
        if (strpos($recipients,',') !== false) {
            $recipientString = [];
            $recipients = explode(',',$recipients);
            foreach ($recipients AS $recipient) {
                $recipient = trim($recipient);

                $match = preg_match('/^{.+}$/i', $recipient);
                if($match) {
                    $recipientString[] = $recipient;
                    continue;
                }
                $email = $this->validateEmail($recipient);
                if ($email == false) $success = false;
                else $recipientString[] = $recipient;
            }
            $recipients = implode(',',$recipientString);
        } else {
            $email = $this->validateEmail($recipients);
            if ($email == false) $success = false;
        }
        
        $subject = $request->getBodyParam('subject');
        $copyFields = $request->getBodyParam('copyFields');

        if (strlen(trim($subject)) == 0) $success = false;
        $message = $request->getBodyParam('message');
        if (strlen(trim($message)) == 0) $success = false;
        
        // Craft::dd($notificationRecord);

        $notificationRecord->form = $formId;
        // Craft::dd($formId);
        
        $notificationRecord->recipients = $recipients;
        $notificationRecord->subject = $subject;
        $notificationRecord->message = $message;
        $notificationRecord->copyFields = $copyFields;
        $notificationRecord->siteId = $site->id;

        if ($success) {
            
            $notificationRecord->save();

            // return $this->redirectToPostedUrl();
            // Craft::$app->getSession()->setNotice($settings->successFlashMessage);
            
            Craft::$app->getSession()->setNotice(Craft::t('enquiries', 'Notification Saved'));
            return $this->redirectToPostedUrl($notificationRecord,'enquiries/notifications');


        } else {
            // echo 'ERRORS';

            $submission = '';
            Craft::$app->getSession()->setError(Craft::t('enquiries', 'There was a problem with your submission, please check the form and try again!'));
            Craft::$app->getUrlManager()->setRouteParams([
                'variables' => ['notification' => $notificationRecord]
            ]);
            return null;
        }
        

        // // get ID (if exists)
        
        // if ($formId) {
        //     $formRecord = FormRecord::find()
        //     ->where(['id'=>$formId])->one();
        //     // if count == null return false
        // } else {
        //     $formRecord = new FormRecord;
        // }
        
        // // validate model here
        
        // $formRecord->formName = $formName;
        // $formRecord->formFields = $formFields;
        // $formRecord->siteId = $site->id;
        // if ($formRecord->validate()) {
        //     $formRecord->save();            
        // } else {
        //     // echo 'Failed:';
        //     // Craft::dd($formRecord->errors);
        // }
        
        
        // if name is null return error

        
    }
}
