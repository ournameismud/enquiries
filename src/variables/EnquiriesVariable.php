<?php
/**
 * Enquiries plugin for Craft CMS 3.x
 *
 * Plugin to log enquiries and manage notifications
 *
 * @link      http://ournameismud.co.uk/
 * @copyright Copyright (c) 2018 @cole007
 */

namespace ournameismud\enquiries\variables;

use ournameismud\enquiries\Enquiries;
use ournameismud\enquiries\records\Forms as FormRecord;
use ournameismud\enquiries\records\Notifications as NotificationRecord;
use ournameismud\enquiries\records\Submissions as SubmissionRecord;
use ournameismud\enquiries\records\MessageLogs as MessageLogRecord;

use Craft;

/**
 * @author    @cole007
 * @package   Enquiries
 * @since     1.0.0
 */
class EnquiriesVariable
{
    // Public Methods
    // =========================================================================

    /**
     * @param null $optional
     * @return string
     */
    protected $fieldKeys = [
        'label',
        'instructions',
        'type',
        'required',
        'submissionLabel',
        'options',
    ];

    public function exampleVariable($optional = null)
    {
        $result = "And away we go to the Twig template...";
        if ($optional) {
            $result = "I'm feeling optional today...";
        }
        return $result;
    }


    public function getMessageLogs($messageId = null) 
    {
        if ($messageId) {
            $records = MessageLogRecord::find()
                ->where(['id'=>$messageId])->one();
        } else {
            $records = MessageLogRecord::find()
                ->orderBy('dateUpdated DESC')
                ->all();
        }
        return $records;    
    }
    public function getSubmissionsByForm($formId)
    {
        $records = SubmissionRecord::find()
            ->where(['form'=>$formId])->all();
        return $records;
    }
    public function getSubmissions($submissionId = null)
    {
        if ($submissionId) {
            $records = SubmissionRecord::find()
                ->where(['id'=>$submissionId])->one();
        } else {
            $records = SubmissionRecord::find()
                ->orderBy('dateUpdated DESC')
                ->all();
        }
        return $records;
    }

    public function getNotifications($notificationId = null)
    {
        if ($notificationId) {
            $records = NotificationRecord::find()
                ->where(['id'=>$notificationId])->one();
        } else {
            $records = NotificationRecord::find()
                ->orderBy('dateUpdated DESC')
                ->all();
        }
        return $records;
    }

    public function getForms()
    {        
        $formRecords = FormRecord::find()->all();
        return $formRecords;
    }

    public function form($formId)
    {
        $formRecord = FormRecord::find()
            ->where(['id'=>$formId])->one();
        // if count == null return false
        
        $formRecord->formFields = json_decode($formRecord->formFields);
        $fields = [];
        foreach ($formRecord->formFields AS $key => $value) {
            $fieldTmp = [];
            foreach ($value AS $subKey => $subValue) {
                $tmpKey = $this->fieldKeys[$subKey];                
                if ($tmpKey == 'options') $fieldTmp[$tmpKey] = preg_split("/\\r\\n|\\r|\\n/", $subValue);
                    else $fieldTmp[$tmpKey] = $subValue;
            }
            $fields[] = $fieldTmp;
        }
        $formRecord->formFields = $fields;
        return $formRecord;
    }
    
}
