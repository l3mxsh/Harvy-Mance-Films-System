<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OutsourcedStaffCredentialsEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $staffName,
        public string $email,
        public string $plainPassword,
        public string $bookingRef,
        public string $loginUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your Temporary Staff Access - HarvyMance Films');
    }

    public function content(): Content
    {
        return new Content(htmlString: $this->buildHtml());
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
                    <p style="margin: 5px 0 0; opacity: 0.8;">Outsourced Staff Portal Access</p>
                </div>
                <div style="padding: 30px;">
                    <p style="color: #333; font-size: 16px;">Hello <strong>{$this->staffName}</strong>,</p>
                    <p style="color: #666;">You have been assigned to a post-production task for booking <strong>{$this->bookingRef}</strong>. Use the credentials below to access your assigned tasks:</p>

                    <div style="background: #f8f9fa; border-radius: 8px; padding: 20px; margin: 20px 0;">
                        <p style="margin: 0 0 10px; color: #666;">Login Email:</p>
                        <p style="margin: 0 0 15px; font-size: 18px; font-weight: bold; color: #1a1a2e;">{$this->email}</p>
                        <p style="margin: 0 0 10px; color: #666;">Temporary Password:</p>
                        <p style="margin: 0; font-size: 22px; font-weight: bold; color: #dc3545;">{$this->plainPassword}</p>
                    </div>

                    <p style="color: #666;">You will only see the tasks assigned to you. Your access will be automatically revoked once all your tasks are marked as done.</p>

                    <div style="text-align: center; margin: 25px 0;">
                        <a href="{$this->loginUrl}" style="background: #1a1a2e; color: #fff; padding: 12px 30px; text-decoration: none; border-radius: 6px; font-weight: bold;">Log In to Staff Portal</a>
                    </div>

                    <p style="color: #999; font-size: 13px;">Do not share these credentials. This access is temporary and will expire automatically.</p>
                </div>
                <div style="background: #f8f9fa; padding: 15px; text-align: center; color: #999; font-size: 12px;">
                    &copy; HarvyMance Films. All rights reserved.
                </div>
            </div>
        </body>
        </html>
        HTML;
    }
}
