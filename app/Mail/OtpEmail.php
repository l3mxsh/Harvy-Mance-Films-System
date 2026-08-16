<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpEmail extends Mailable
{
    use Queueable, SerializesModels;

    public string $otpCode;
    public string $clientName;

    public function __construct(string $otpCode, string $clientName)
    {
        $this->otpCode = $otpCode;
        $this->clientName = $clientName;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Verification Code - HarvyMance Films',
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: $this->buildHtml(),
        );
    }

    private function buildHtml(): string
    {
        $year = date('Y');

        return <<<HTML
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>Your Verification Code - HarvyMance Films</title>
        </head>
        <body style="margin: 0; padding: 0; background: #fafafa; font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif; color: #111111; -webkit-font-smoothing: antialiased;">
            <div style="max-width: 600px; margin: 0 auto; padding: 32px 16px;">

                <div style="background: #ffffff; border: 1px solid #e9ecef; border-radius: 18px; overflow: hidden; box-shadow: 0 4px 18px rgba(17,17,17,0.06);">

                    <div style="background: #111111; padding: 28px 32px; text-align: center;">
                        <h1 style="margin: 0; font-size: 20px; font-weight: 700; color: #ffffff; letter-spacing: -0.01em;">HarvyMance Films</h1>
                        <span style="display: inline-block; margin-top: 10px; background: #333333; color: #ffffff; font-size: 12px; font-weight: 500; padding: 4px 14px; border-radius: 999px;">Email Verification</span>
                    </div>

                    <div style="padding: 32px;">
                        <p style="margin: 0 0 12px; font-size: 16px; color: #111111;">Hello <strong>{$this->clientName}</strong>,</p>
                        <p style="margin: 0; font-size: 14px; line-height: 1.6; color: #6c757d;">Your verification code is:</p>

                        <div style="margin: 24px auto; max-width: 360px; background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 16px; padding: 20px; text-align: center;">
                            <span style="font-size: 32px; font-weight: 700; color: #111111; letter-spacing: 8px;">{$this->otpCode}</span>
                        </div>

                        <p style="margin: 0; font-size: 12px; line-height: 1.6; color: #6c757d;">This code expires in 5 minutes. Do not share this code with anyone.</p>
                    </div>

                    <div style="background: #f8f9fa; border-top: 1px solid #e9ecef; padding: 16px 32px; text-align: center; color: #adb5bd; font-size: 12px;">
                        &copy; {$year} HarvyMance Films. All rights reserved.
                    </div>
                </div>

                <p style="text-align: center; margin: 20px 0 0; font-size: 11px; color: #adb5bd;">This email was sent by HarvyMance Films.</p>
            </div>
        </body>
        </html>
        HTML;
    }
}
