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
use ournameismud\enquiries\records\Forms as FormRecord;
use ournameismud\enquiries\records\Submissions as SubmissionRecord;
use Craft;
use craft\helpers\StringHelper;
use craft\validators\StringValidator;
use craft\validators\DateTimeValidator;
use craft\web\Controller;

/**
 * @author    @cole007
 * @package   Enquiries
 * @since     1.0.0
 */
class FormController extends Controller
{

    protected $allowAnonymous = ['submission'];
    protected $fieldKeys = [
        'label',
        'instructions',
        'type',
        'required',
        'submissionLabel',
        'options',
    ];
    // protected function sanitizeRows($subKey) {

    // }
    protected function validateDate($date) {
        $validator = new \yii\validators\DateValidator();
        if ($validator->validate($date, $error)) {
            return true;
        } else {
            return false;
        }
    }
    protected function validateEmail($email) {
        $validator = new \yii\validators\EmailValidator();
        if ($validator->validate($email, $error)) {
            return true;
        } else {
            return false;
        }
    }
    public function actionSubmission()
    {
        $result = 'Welcome to the FormController actionIndex() method';
        $this->requirePostRequest();
        $request = Craft::$app->getRequest();
        $site = Craft::$app->getSites()->getCurrentSite();   

        $success = true;
        $errors = [];
        $formId = $request->getBodyParam('form');
        // if formId is null return error
        
        $formRecord = FormRecord::find()
            ->where(['id'=>$formId])->one();

        $submissionRecord = new SubmissionRecord;
        $submissionRecord->siteId = $site->id;

        // if $formRecord is null return error
            
        // $date = '10 September 2018';
        // Craft::dd($this->validateDate($date));
        $enquiry = $request->getBodyParam('enquiry');
        $submissionRecord->form = $formId;
        // $submissionRecord->submission = $form->subject;
        $submissionRecord->message = $enquiry;

        // print_r($enquiry);
        $formFields = json_decode($formRecord->formFields);
        $formFieldClean = [];
        foreach ($formFields AS $row) {
            $fieldTmp = [];
            // sanitize method here?
            // sanitizeRows()
            foreach ($row AS $subKey => $subValue) {
                $key = $this->fieldKeys[$subKey];
                $fieldTmp[$key] = $subValue;
            }
            // $slug = StringHelper::toKebabCase($fieldTmp['label']);
            $slug = StringHelper::toLowerCase($fieldTmp['label']);
            $slug = StringHelper::replace($slug,' ','_');
            $fieldTmp['slug'] = $slug;
            if (array_key_exists($slug,$enquiry)) {
                $fieldTmp['value'] = is_string($enquiry[$slug]) ? trim($enquiry[$slug]) : $enquiry[$slug];
            } else {
                $fieldTmp['value'] = '';
            }
            $errors[$slug] = [];
            // $value = $enquiry
            // echo '::' . StringValidator::isEmpty(' ') . '::';
            if ($fieldTmp['required'] && is_string($fieldTmp['value']) && $fieldTmp['value'] == '') {
                $errors[$slug][] = $fieldTmp['label'] . ' is required';
            } 
            if ($fieldTmp['type'] == 'email' && is_string($fieldTmp['value']) && $this->validateEmail($fieldTmp['value']) == false) {
                $errors[$slug][] = $fieldTmp['label'] . ' is invalid';
            }
            if ($fieldTmp['type'] == 'date' && is_string($fieldTmp['value']) && $this->validateDate($fieldTmp['value']) == false) {
                $errors[$slug][] = $fieldTmp['label'] . ' is invalid';
            }
            // else echo 'not required'; 
            $formFieldClean[] = $fieldTmp;
            // if required == true check value
            // if type = email validate
            // if type = date validate
        }
        $errors = array_filter($errors);
        
        if (count($errors) > 0) {
            Craft::dd($errors);
            // detect JSON here
            Craft::$app->getSession()->setError(Craft::t('enquiries', 'There was a problem with your submission, please check the form and try again!'));
            Craft::$app->getUrlManager()->setRouteParams([
                'variables' => ['errors' => $errors, 'enquiry' => $submissionRecord->message]
            ]);
            return null;

            // Craft::$app->getSession()->setError(Craft::t('enquiries', 'There was a problem with your submission, please check the form and try again!'));
            // return $this->redirectToPostedUrl([
            //     'errors' => $errors,
            //     'submission' => $submissionRecord]);
        }
        // create new submissionrecord
        // check notifications > service
        $notifications = Enquiries::getInstance()->notifications->sendNotifications($formId,$submissionRecord);
        // loop through recipients and send to each
        // save record
        // save log
        $submissionRecord->save();

        if ($request->getAcceptsJson()) {
            return $this->asJson(['success' => true]);
        }
        Craft::$app->getSession()->setNotice(Craft::t('enquiries', 'Your message has been sent.'));
        return $this->redirectToPostedUrl($submissionRecord);
        // Craft::dd($_POST);
        Craft::dd($formFieldClean);
        return $result;
    }

    public function actionTemplate($formId)
    {
        $variables = [];
        $formRecord = FormRecord::find()
            ->where(['id'=>$formId])->one();
        if (!$formRecord) $formRecord = new FormRecord;
        // Craft::dd($formRecords);
        $variables['formName'] = $formRecord->formName;
        $variables['formIntro'] = $formRecord->formIntro;
        $variables['formId'] = $formId;
        $variables['formFields'] = (array)json_decode($formRecord->formFields);
        return $this->renderTemplate('enquiries/forms/new', $variables);
        // 
    }
    public function actionUpdate()
    {
        $this->requirePostRequest();
        $request = Craft::$app->getRequest();
        $site = Craft::$app->getSites()->getCurrentSite();

        $formName = $request->getBodyParam('formName');
        $formIntro = $request->getBodyParam('formIntro');
        if (strlen(trim($formName)) == 0) {

            return false;
        }
        $formFields = $request->getBodyParam('formFields');
        $labelIndex = array_search('submissionLabel',$this->fieldKeys);
        $labelName = array_search('label',$this->fieldKeys);
        $submissionLabels = [];
        $index = 0;
        foreach ($formFields AS $row) {
            if ($row[$labelIndex] == 1) {
                $submissionLabels[] = $index;
            }
            $index++;
        }

        $formId = $request->getBodyParam('formId');
                
        // get ID (if exists)
        // Craft::dd($formId);

        if (isset($formId) && $formId !== 'new') {
            $formRecord = FormRecord::find()
            ->where(['id'=>$formId])->one();
            // if count == null return false
        } else {
            $formRecord = new FormRecord;
        }
        
        // validate model here
        
        $formRecord->formName = $formName;
        $formRecord->formFields = $formFields;
        $formRecord->formIntro = trim($formIntro);
        $formRecord->formLabel = json_encode($submissionLabels);
        $formRecord->siteId = $site->id;
        
        if ($formRecord->validate()) {
            $formRecord->save();            
            Craft::$app->getSession()->setNotice(Craft::t('enquiries', 'Form Saved'));
            return $this->redirectToPostedUrl($formRecord,'enquiries/forms');
        } else {
            // echo 'Failed:';
            // Craft::dd($formRecord->errors);
            $submission = '';
            Craft::$app->getSession()->setError(Craft::t('enquiries', 'There was a problem with your submission, please check the form and try again!'));
            Craft::$app->getUrlManager()->setRouteParams([
                'variables' => ['form' => $formRecord]
            ]);
            return null;
        }
        
        
        // if name is null return error

        
    }
}
