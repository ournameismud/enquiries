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
use ournameismud\enquiries\records\Notifications as NotificationRecord;
use ournameismud\enquiries\records\MessageLogs as MessageLogRecord;
// use ournameismud\enquiries\events\SendEvent AS SendEvent;

use Craft;
use craft\base\Component;
use craft\helpers\StringHelper;
use craft\mail\Message;

/**
 * @author    @cole007
 * @package   Enquiries
 * @since     1.0.0
 */
class Notifications extends Component
{
    // Public Methods
    // =========================================================================

    /*
     * @return mixed
     */
    public function sendNotifications( $formId, $submissionRecord )
    {
        $notifications = 0;
        $site = Craft::$app->getSites()->getCurrentSite();
        $notificationRecords = NotificationRecord::find()
            ->where([
                'form'=>$formId,
                'siteId'=>$site->id
            ])->all();
        
        $mailer = Craft::$app->getMailer();            
        // Craft::dd($mailer);        
        foreach($notificationRecords AS $notificationRecord) {
            $recipients = $notificationRecord['recipients'];
            // if (strpos($recipients,',') !== false) {
            //     $recipientArray = explode(',',$recipients);
            //     $recipients = [];                
            //     foreach ($recipientArray AS $recipient) {
            //         $recipients[] = $recipient;
            //     }
            //     // $recipients = implode(',',$recipientString);
            // } else {
            //     $recipients[] = $recipients;
            // }

            $messageText = $notificationRecord->message;
            if( $notificationRecord->copyFields ) {
                $messageText .=  "\r\n";
                $messageText .=  "========\r\n";

                $now = new \DateTime();
                $messageText .=  "Posted: " . $now->format('H:i l j M Y') . "\r\n";
                foreach ($submissionRecord->message AS $label => $value) {
                    $messageText .= ucwords(str_replace('-',' ',$label)) . ': ';
                    if (is_string($value)) $messageText .= trim($value) . "\r\n";
                    else {
                        $messageText .= implode(', ',$value) . "\r\n";
                    }
                }
                // Craft::dd($messageText);
            }
            $message = (new Message())
                // ->setFrom([$fromEmail => $fromName])
                // ->setReplyTo([$submission->fromEmail => $submission->fromName])
                ->setSubject($notificationRecord->subject)
                ->setTextBody($messageText)
                ->setHtmlBody(nl2br($messageText));
            $toEmails = is_string($recipients) ? StringHelper::split($recipients) : $recipients;
            foreach($toEmails AS $recipient) {
                $tags = preg_match('/^{(.+)}$/i', $recipient, $match);
                if ($tags) {
                    $recipient = $submissionRecord->message[$match[1]];
                }
                $message->setTo( $recipient );
                $sent = $mailer->send( $message );
                if ($sent) {
                    $messageLog = new MessageLogRecord;
                    $messageLog->form = $formId;
                    $messageLog->recipient = $recipient;
                    $messageLog->subject = $message->subject;
                    $messageLog->message = $messageText;
                    $messageLog->siteId = $site->id;
                    $messageLog->save();
                    $notifications++;
                }
                // Craft::dd( $message );
            }
            // $event = new SendEvent([
            //     'submission' => $submissionRecord,
            //     'message' => $message,
            //     'toEmails' => $toEmails,
            // ]);
            // $this->trigger(self::EVENT_BEFORE_SEND, $event);

            // if ($event->isSpam) {
            //     Craft::info('Contact form submission suspected to be spam.', __METHOD__);
            //     return true;
            // }
            // foreach ($event->toEmails as $toEmail) {
            //     $message->setTo($toEmail);
            //     $sent = $mailer->send( $message );
            //     if ($sent) {
            //         $messageLog = new MesssageLogRecord;
            //         $messageLog->form = $formId;
            //         $messageLog->recipient = $toEmail;
            //         $messageLog->subject = $message->subject;
            //         $messageLog->message = $toEmail;
            //     }
            // }

            // Fire an 'afterSend' event
            // if ($this->hasEventHandlers(self::EVENT_AFTER_SEND)) {
            //     $this->trigger(self::EVENT_AFTER_SEND, new SendEvent([
            //         'submission' => $submissionRecord,
            //         'message' => $message,
            //         'toEmails' => $event->toEmails,
            //     ]));
            // }
            return $notifications;
            // Craft::dd($recipients);
            // Craft::dd($notificationRecord);
        }
        // Craft::dd($notificationRecords);
        // 
    }
    public function exampleService()
    {
        $result = 'something';
        // Check our Plugin's settings for `someAttribute`
        if (Enquiries::$plugin->getSettings()->someAttribute) {
        }

        return $result;
    }
}
