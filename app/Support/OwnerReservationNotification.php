<?php

namespace App\Support;

use App\Models\PropertyReservation;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OwnerReservationNotification
{
    public function send(PropertyReservation $reservation): ?string
    {
        $reservation->loadMissing('property.owner');

        $message = $this->message($reservation);

        Mail::raw($message, function ($mail): void {
            $mail->to(config('services.owner_reservations.email'))
                ->subject('New Owner Reservation');
        });

        return $this->sendCallMeBotWhatsapp($message);
    }

    public function whatsappUrl(PropertyReservation $reservation): string
    {
        $reservation->loadMissing('property.owner');

        return sprintf(
            'https://api.whatsapp.com/send?phone=%s&text=%s',
            rawurlencode((string) config('services.owner_reservations.whatsapp_to')),
            rawurlencode($this->message($reservation))
        );
    }

    private function message(PropertyReservation $reservation): string
    {
        return implode(PHP_EOL, [
            'New owner reservation',
            '',
            'Owner Name: ' . ($reservation->property?->owner?->name ?? ''),
            'Check-in: ' . $reservation->check_in_date?->format('Y-m-d'),
            'Check-out: ' . $reservation->check_out_date?->format('Y-m-d'),
            'No. Of persons: ' . $reservation->number_of_guests,
        ]);
    }

    private function whatsappClickToChatUrl(string $message): string
    {
        return sprintf(
            'https://api.whatsapp.com/send?phone=%s&text=%s',
            rawurlencode((string) config('services.owner_reservations.whatsapp_to')),
            rawurlencode($message)
        );
    }

    private function sendCallMeBotWhatsapp(string $message): ?string
    {
        $to = $this->whatsappPhoneNumber();
        $apiKey = config('services.owner_reservations.callmebot_api_key');

        if (blank($to) || blank($apiKey)) {
            Log::info('Owner reservation CallMeBot WhatsApp notification skipped because CallMeBot is not configured.');

            return $this->whatsappClickToChatUrl($message);
        }

        $response = Http::timeout(10)->get('https://api.callmebot.com/whatsapp.php', [
            'phone' => $to,
            'apikey' => $apiKey,
            'text' => $message,
        ]);

        if ($response->failed() || str_contains(strtolower($response->body()), 'error')) {
            Log::warning('Owner reservation CallMeBot WhatsApp notification failed.', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return $this->whatsappClickToChatUrl($message);
        }

        return null;
    }

    private function whatsappPhoneNumber(): string
    {
        $phone = preg_replace('/[^\d+]/', '', (string) config('services.owner_reservations.whatsapp_to'));

        if ($phone !== '' && ! str_starts_with($phone, '+')) {
            return '+' . $phone;
        }

        return $phone;
    }
}
