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
        $year = date('Y');

        return <<<HTML
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>Booking Approved - HarvyMance Films</title>
        </head>
        <body style="margin: 0; padding: 0; background: #fafafa; font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif; color: #111111; -webkit-font-smoothing: antialiased;">
            <div style="max-width: 600px; margin: 0 auto; padding: 32px 16px;">

                <div style="background: #ffffff; border: 1px solid #e9ecef; border-radius: 18px; overflow: hidden; box-shadow: 0 4px 18px rgba(17,17,17,0.06);">

                    <div style="background: #111111; padding: 28px 32px; text-align: center;">
                        <h1 style="margin: 0; font-size: 20px; font-weight: 700; color: #ffffff; letter-spacing: -0.01em;">HarvyMance Films</h1>
                        <span style="display: inline-block; margin-top: 10px; background: #333333; color: #ffffff; font-size: 12px; font-weight: 500; padding: 4px 14px; border-radius: 999px;">Booking Approved</span>
                    </div>

                    <div style="padding: 32px;">
                        <p style="margin: 0 0 12px; font-size: 16px; color: #111111;">Hello <strong>{$this->clientName}</strong>,</p>
                        <p style="margin: 0; font-size: 14px; line-height: 1.6; color: #6c757d;">Great news! Your booking <strong style="color: #111111;">{$this->bookingRef}</strong> has been approved. You can now monitor your booking using the credentials below:</p>

                        <div style="margin: 24px 0; background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 16px; padding: 20px 24px;">
                            <div style="margin-bottom: 18px;">
                                <div style="font-size: 12px; font-weight: 500; color: #6c757d; margin-bottom: 4px;">Control Number</div>
                                <div style="font-size: 20px; font-weight: 700; color: #111111;">{$this->controlNumber}</div>
                            </div>
                            <div>
                                <div style="font-size: 12px; font-weight: 500; color: #6c757d; margin-bottom: 4px;">Temporary Password</div>
                                <div style="font-size: 20px; font-weight: 700; color: #dc3545;">{$this->tempPassword}</div>
                            </div>
                        </div>

                        <p style="margin: 0 0 24px; font-size: 14px; line-height: 1.6; color: #6c757d;">Please log in at the booking monitoring page and change your password after your first login.</p>

                        <div style="text-align: center; margin: 28px 0;">
                            <a href="{$this->getLoginUrl()}" style="display: inline-block; background: #111111; color: #ffffff; padding: 13px 32px; text-decoration: none; border-radius: 999px; font-weight: 600; font-size: 14px;">Log In to Monitor Booking</a>
                        </div>

                        <p style="margin: 0; font-size: 12px; color: #6c757d;">For security, please change your password after your first login.</p>
                    </div>

                    <div style="background: #f8f9fa; border-top: 1px solid #e9ecef; padding: 16px 32px; text-align: center; color: #adb5bd; font-size: 12px;">
                        &copy; {$year} HarvyMance Films. All rights reserved.
                    </div>
                </div>

                <p style="text-align: center; margin: 20px 0 0; font-size: 11px; color: #adb5bd;">This email was sent by HarvyMance Films regarding your booking.</p>
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
