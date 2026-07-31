<?php

namespace App\Http\Controllers;

use App\Models\Otp;
use App\Mail\OtpEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class OtpController extends Controller
{
    public function generate(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'client_name' => 'required|string|max:255',
        ]);

        $email = $request->input('email');
        $clientName = $request->input('client_name');

        $otp = Otp::where('email', $email)
            ->where('purpose', 'booking_verification')
            ->where('verified', false)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if ($otp) {
            $secondsSince = now()->diffInSeconds($otp->created_at);
            if ($secondsSince < 60) {
                $waitSeconds = (int) ceil(60 - $secondsSince);
                $waitMinutes = floor($waitSeconds / 60);
                $waitSecs = $waitSeconds % 60;

                if ($waitMinutes > 0 && $waitSecs > 0) {
                    $waitText = $waitMinutes . ' minute' . ($waitMinutes > 1 ? 's' : '') . ' and ' . $waitSecs . ' second' . ($waitSecs > 1 ? 's' : '');
                } elseif ($waitMinutes > 0) {
                    $waitText = $waitMinutes . ' minute' . ($waitMinutes > 1 ? 's' : '');
                } else {
                    $waitText = $waitSecs . ' second' . ($waitSecs > 1 ? 's' : '');
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Please wait ' . $waitText . ' before requesting a new code.',
                ]);
            }
        }

        $code = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        Otp::where('email', $email)
            ->where('purpose', 'booking_verification')
            ->where('verified', false)
            ->update(['verified' => true]);

        $otpRecord = Otp::create([
            'email' => $email,
            'code' => $code,
            'purpose' => 'booking_verification',
            'expires_at' => now()->addMinutes(5),
        ]);

        try {
            Mail::to($email)->send(new OtpEmail($code, $clientName));
            $mailSent = true;
        } catch (\Exception $e) {
            $mailSent = false;
        }

        return response()->json([
            'success' => true,
            'message' => 'Verification code has been sent to your email.',
            'expires_at' => $otpRecord->expires_at->toISOString(),
            'otp_code' => $code,
            'mail_sent' => $mailSent,
        ]);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6',
        ]);

        $email = $request->input('email');
        $code = $request->input('code');

        $otp = Otp::where('email', $email)
            ->where('code', $code)
            ->where('purpose', 'booking_verification')
            ->where('verified', false)
            ->latest()
            ->first();

        if (!$otp) {
            $attempts = Otp::where('email', $email)
                ->where('purpose', 'booking_verification')
                ->where('created_at', '>=', now()->subMinutes(5))
                ->count();

            if ($attempts >= 5) {
                return response()->json([
                    'success' => false,
                    'message' => 'Maximum verification attempts reached. Please request a new code.',
                    'max_attempts' => true,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Invalid verification code. Please try again.',
            ]);
        }

        if ($otp->isExpired()) {
            return response()->json([
                'success' => false,
                'message' => 'Verification code has expired. Please request a new code.',
                'expired' => true,
            ]);
        }

        $otp->update(['verified' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Email verified successfully!',
        ]);
    }

    public function resend(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'client_name' => 'required|string|max:255',
        ]);

        $email = $request->input('email');
        $clientName = $request->input('client_name');

        Otp::where('email', $email)
            ->where('purpose', 'booking_verification')
            ->where('verified', false)
            ->update(['verified' => true]);

        $code = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        $otpRecord = Otp::create([
            'email' => $email,
            'code' => $code,
            'purpose' => 'booking_verification',
            'expires_at' => now()->addMinutes(5),
        ]);

        try {
            Mail::to($email)->send(new OtpEmail($code, $clientName));
            $mailSent = true;
        } catch (\Exception $e) {
            $mailSent = false;
        }

        return response()->json([
            'success' => true,
            'message' => 'A new verification code has been sent to your email.',
            'expires_at' => $otpRecord->expires_at->toISOString(),
            'otp_code' => $code,
            'mail_sent' => $mailSent,
        ]);
    }
}
