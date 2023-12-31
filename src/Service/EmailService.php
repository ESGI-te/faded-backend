<?php

namespace App\Service;

class EmailService
{
    
    public function __construct(string $apiKey)
    {
        $this->resend = \Resend::client($apiKey);
    }

    public function sendEmail(string $from, array $emails, string $subject, string $content)
    {
        try {
            $result = $this->resend->emails->send([
                "from" => $from,
                "to" => $emails,
                "subject" => $subject,
                "html" => $content,
            ]);
            return $result->toJson();
        } catch (\Exception $e) {
            throw new \Exception('Error: ' . $e->getMessage());
        }
    }

}

