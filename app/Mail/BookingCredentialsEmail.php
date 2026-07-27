<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingCredentialsEmail extends Mailable
{
    use Queueable, SerializesModels;

    public string $clientName;
    public string $controlNumber;
    public string $tempPassword;
    public string $bookingRef;

    public function __construct(string $clientName, string $controlNumber, string $tempPassword, string $bookingRef)
    {
        $this->clientName = $clientName;
        $this->controlNumber = $controlNumber;
        $this->tempPassword = $tempPassword;
        $this->bookingRef = $bookingRef;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Booking Approved - Your Monitoring Credentials - HarvyMance Films',
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
                    <p style="margin: 5px 0 0; opacity: 0.8;">Booking Approved</p>
                </div>
                <div style="padding: 30px;">
                    <p style="color: #333; font-size: 16px;">Hello <strong>{$this->clientName}</strong>,</p>
                    <p style="color: #666;">Great news! Your booking <strong>{$this->bookingRef}</strong> has been approved. You can now monitor your booking using the credentials below:</p>

                    <div style="background: #f8f9fa; border-radius: 8px; padding: 20px; margin: 20px 0;">
                        <p style="margin: 0 0 10px; color: #666;">Control Number:</p>
                        <p style="margin: 0 0 15px; font-size: 22px; font-weight: bold; color: #1a1a2e;">{$this->controlNumber}</p>
                        <p style="margin: 0 0 10px; color: #666;">Temporary Password:</p>
                        <p style="margin: 0; font-size: 22px; font-weight: bold; color: #dc3545;">{$this->tempPassword}</p>
                    </div>

                    <p style="color: #666;">Please log in at the booking monitoring page and change your password after your first login.</p>

                    <div style="text-align: center; margin: 25px 0;">
                        <a href="{$this->getLoginUrl()}" style="background: #1a1a2e; color: #fff; padding: 12px 30px; text-decoration: none; border-radius: 6px; font-weight: bold;">Log In to Monitor Booking</a>
                    </div>

                    <p style="color: #999; font-size: 13px;">For security, please change your password after your first login.</p>
                </div>
                <div style="background: #f8f9fa; padding: 15px; text-align: center; color: #999; font-size: 12px;">
                    &copy; {{ date('Y') }} HarvyMance Films. All rights reserved.
                </div>
            </div>
        </body>
        </html>
        HTML;
    }

    private function getLoginUrl(): string
    {
        return url('/login');
    }
}
