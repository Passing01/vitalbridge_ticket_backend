<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OTPService
{
    /**
     * Send OTP via SMS using Infobip
     *
     * @param string $phoneNumber
     * @param string $otp
     * @return bool
     */
    protected function formatPhoneNumber(string $phoneNumber): string
    {
        // Supprimer tous les caractères non numériques
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // Si le numéro commence par l'indicatif par défaut, on le laisse tel quel
        if (str_starts_with($phoneNumber, config('infobip.default_country_code'))) {
            return $phoneNumber;
        }
        
        // Si le numéro commence par un 0 suivi de 8-15 chiffres, on le formate avec l'indicatif par défaut
        if (preg_match('/^0([0-9]{8,15})$/', $phoneNumber, $matches)) {
            return config('infobip.default_country_code') . $matches[1];
        }
        
        // Si le numéro a entre 8 et 15 chiffres, on ajoute l'indicatif par défaut
        if (preg_match('/^[0-9]{8,15}$/', $phoneNumber)) {
            return config('infobip.default_country_code') . $phoneNumber;
        }
        
        // Pour les numéros avec indicatif international (commençant par + ou 00), on les laisse tels quels
        return $phoneNumber;
    }

    protected function sendSMSViaInfobip(string $phoneNumber, string $otp): bool
    {
        $formattedNumber = $this->formatPhoneNumber($phoneNumber);
        
        try {
            $response = Http::withHeaders([
                'Authorization' => 'App ' . config('infobip.api_key'),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post(config('infobip.base_url') . '/sms/2/text/advanced', [
                'messages' => [
                    [
                        'from' => config('infobip.sender_id'),
                        'destinations' => [
                            ['to' => $formattedNumber]
                        ],
                        'text' => "Votre code de vérification VitalBridge est: $otp. Valable 10 minutes.",
                    ]
                ]
            ]);

            if ($response->successful()) {
                Log::info("SMS sent to $phoneNumber");
                return true;
            }

            Log::error('Failed to send SMS via Infobip', [
                'status' => $response->status(),
                'response' => $response->json()
            ]);
            return false;

        } catch (\Exception $e) {
            Log::error('Error sending SMS via Infobip: ' . $e->getMessage());
            return false;
        }
    }
    
    protected function sendSMSViaIkoddi(string $phoneNumber, string $otp): bool
    {
        try {
            $groupId = config('ikoddi.group_id');
            $otpAppId = config('ikoddi.otp_app_id');
            $type = 'phone';
            $identity = $this->formatPhoneNumber($phoneNumber);

            $baseUrl = rtrim(config('ikoddi.api_base_url'), '/');
            $url = $baseUrl . "/api/v1/groups/{$groupId}/otp/{$otpAppId}/{$type}/{$identity}";

            $response = Http::withHeaders([
                'x-api-key' => config('ikoddi.api_key'),
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post($url, []);

            if ($response->successful()) {
                Log::info("OTP sent via IKODDI to $phoneNumber");
                return true;
            }

            Log::error('Failed to send SMS via IKODDI', [
                'status' => $response->status(),
                'response' => $response->json(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('Error sending SMS via IKODDI: ' . $e->getMessage());
            return false;
        }
    }
    /**
     * Generate and send OTP to user
     *
     * @param User $user
     * @return string
     */
    public function generateAndSendOTP(User $user): string
    {
        // Generate a 6-digit OTP
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Set expiration time (10 minutes from now)
        $expiresAt = now()->addMinutes(10);
        
        // Save OTP to user
        $user->update([
            'otp' => $otp,
            'otp_expires_at' => $expiresAt,
            'otp_verified_at' => null,
        ]);

        // Send OTP via SMS: try IKODDI first, then Infobip as fallback
        if (! $this->sendSMSViaIkoddi($user->phone, $otp)) {
            $this->sendSMSViaInfobip($user->phone, $otp);
        }
        
        return $otp;
    }

    /**
     * Verify OTP for user
     *
     * @param User $user
     * @param string $otp
     * @return bool
     */
    public function verifyOTP(User $user, string $otp): bool
    {
        // Check if OTP matches and is not expired
        if ($user->otp === $otp && 
            $user->otp_expires_at && 
            $user->otp_expires_at->isFuture()) {
            
            $user->update([
                'otp_verified_at' => now(),
                'otp' => null, // Clear OTP after successful verification
                'otp_expires_at' => null,
            ]);
            
            return true;
        }
        
        return false;
    }

    /**
     * Check if user has a valid OTP
     *
     * @param User $user
     * @return bool
     */
    public function hasValidOTP(User $user): bool
    {
        return $user->otp && 
               $user->otp_expires_at && 
               $user->otp_expires_at->isFuture();
    }
}
