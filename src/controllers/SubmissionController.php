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
use ournameismud\enquiries\records\Submissions as SubmissionRecord;
use Craft;
use craft\web\Controller;

/**
 * @author    @cole007
 * @package   Enquiries
 * @since     1.0.0
 */
class SubmissionController extends Controller
{

    protected $allowAnonymous = ['index'];

    public function actionIndex()
    {
        $result = 'Welcome to the FormController actionIndex() method';

        return $result;
    }

    public function actionTemplate($submissionId)
    {
        $variables = [];
        $submissionRecord = SubmissionRecord::find()
            ->where(['id'=>$submissionId])->one();
        // $variables['subject'] = $notificationRecord->subject;
        // $variables['recipients'] = $notificationRecord->recipients;
        // $variables['form'] = $notificationRecord->form;
        // $variables['notificationId'] = $notificationRecord->id;
        $variables['id'] = $submissionId;
        $variables['message'] = $submissionRecord->message;
        $variables['dateUpdated'] = $submissionRecord->dateUpdated;
        $variables['form'] = $submissionRecord->form;
        // Craft::dd($formRecords);        
        return $this->renderTemplate('enquiries/submissions/new', $variables);
        // 
    }
}
