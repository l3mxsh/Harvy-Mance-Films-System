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
        return <<<HTML
        <!DOCTYPE html>
        <html>
        <head><meta charset="utf-8"></head>
        <body style="font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px;">
            <div style="max-width: 500px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <div style="background: #1a1a2e; color: #fff; padding: 20px; text-align: center;">
                    <h2 style="margin: 0;">HarvyMance Films</h2>
                    <p style="margin: 5px 0 0; opacity: 0.8;">Email Verification</p>
                </div>
                <div style="padding: 30px; text-align: center;">
                    <p style="color: #333; font-size: 16px;">Hello <strong>{$this->clientName}</strong>,</p>
                    <p style="color: #666;">Your verification code is:</p>
                    <div style="background: #f8f9fa; border: 2px dashed #1a1a2e; border-radius: 8px; padding: 15px; margin: 20px 0;">
                        <span style="font-size: 32px; font-weight: bold; color: #1a1a2e; letter-spacing: 8px;">{$this->otpCode}</span>
                    </div>
                    <p style="color: #999; font-size: 13px;">This code expires in 5 minutes. Do not share this code with anyone.</p>
                </div>
                <div style="background: #f8f9fa; padding: 15px; text-align: center; color: #999; font-size: 12px;">
                    &copy; {{ date('Y') }} HarvyMance Films. All rights reserved.
                </div>
            </div>
        </body>
        </html>
        HTML;
    }
}
