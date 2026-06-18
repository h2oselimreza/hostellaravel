<?php

namespace App\Services;

use App\Mail\DynamicGeneralMail;
use App\Mail\ContactUsMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Throwable;

class MailSendService
{
    /**
     * General Mail Send
     *
     * @param array{
     * toMail?: string, 
     * toMailMultiple?: array<string>, 
     * customerMail?: string, 
     * mailHeading: string, 
     * mailBody: string
     * } $mailArr
     */
    public function mailSend(array $mailArr): bool
    {
        try {
            // Laravel's to() accepts a string array, collection, or individual string natively
            $recipients = $mailArr['toMailMultiple'] ?? $mailArr['toMail'] ?? null;

            if (empty($recipients)) {
                Log::warning('MailSendService: No recipient email provided.');
                return false;
            }

            $mailable = new DynamicGeneralMail(
                subjectText: $mailArr['mailHeading'],
                htmlBody: $mailArr['mailBody'],
                replyToEmail: $mailArr['customerMail'] ?? null
            );

            // Directly dispatches using the 'custom' mailer defined in config/mail.php
            Mail::mailer('custom')->to($recipients)->send($mailable);

            Log::channel('mail')->info('Mail sent successfully');

            return true;
        } catch (Throwable $e) {
            Log::channel('mail')->error('Mail Send Error: ' . $e->getMessage(), [
                'exception' => $e,
                'payload' => $mailArr
            ]);
            return false;
        }
    }

    /**
     * Contact Us Mail Send
     *
     * @param array{
     * customerMail?: string, 
     * mailHeading: string, 
     * mailBody: string
     * } $mailArr
     */
    public function contactUsMailSend(array $mailArr): bool
    {
        try {
            $toAddress = config('mail.mailers.contact.to_address');

            if (empty($toAddress)) {
                Log::error('MailSendService: Configuration missing for target contact recipient address.');
                return false;
            }

            $mailable = new ContactUsMail(
                subjectText: $mailArr['mailHeading'],
                htmlBody: $mailArr['mailBody'],
                replyToEmail: $mailArr['customerMail'] ?? null
            );

            // Directly dispatches using the 'contact' mailer defined in config/mail.php
            Mail::mailer('contact')->to($toAddress)->send($mailable);

            return true;
        } catch (Throwable $e) {
            Log::error('Contact Mail Error: ' . $e->getMessage(), [
                'exception' => $e,
                'payload' => $mailArr
            ]);
            return false;
        }
    }
}