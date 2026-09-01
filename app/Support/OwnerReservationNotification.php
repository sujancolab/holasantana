<?php

namespace App\Support;

use App\Models\PropertyReservation;
use Throwable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OwnerReservationNotification
{
    public function send(PropertyReservation $reservation): ?string
    {
        $reservation->loadMissing('property.owner');

        $message = $this->message($reservation);

        $this->sendEmail($message);

        try {
            return $this->sendMetaWhatsapp($message);
        } catch (Throwable $exception) {
            Log::warning('Owner reservation WhatsApp notification could not be sent.', [
                'message' => $exception->getMessage(),
            ]);

            return $this->whatsappClickToChatUrl($message);
        }
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

    private function sendEmail(string $message): void
    {
        try {
            Mail::raw($message, function ($mail): void {
                $mail->to(config('services.owner_reservations.email'))
                    ->subject('New Owner Reservation');
            });
        } catch (Throwable $exception) {
            Log::warning('Owner reservation email notification could not be sent.', [
                'message' => $exception->getMessage(),
            ]);
        }
    }

    private function sendMetaWhatsapp(string $message): ?string
    {
        $to = $this->whatsappPhoneNumber();
        $phoneNumberId = config('services.owner_reservations.meta_phone_number_id');
        $accessToken = config('services.owner_reservations.meta_access_token');

        if (blank($to) || blank($phoneNumberId) || blank($accessToken)) {
            Log::info('Owner reservation Meta WhatsApp notification skipped because Meta Cloud API is not configured.');

            return $this->whatsappClickToChatUrl($message);
        }

        $response = Http::withToken($accessToken)
            ->acceptJson()
            ->timeout(10)
            ->post(sprintf(
                'https://graph.facebook.com/%s/%s/messages',
                config('services.owner_reservations.meta_api_version'),
                $phoneNumberId
            ), [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $to,
                'type' => 'text',
                'text' => [
                    'preview_url' => false,
                    'body' => $message,
                ],
            ]);

        if ($response->failed() || str_contains(strtolower($response->body()), 'error')) {
            Log::warning('Owner reservation Meta WhatsApp notification failed.', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return $this->whatsappClickToChatUrl($message);
        }

        return null;
    }

    private function whatsappPhoneNumber(): string
    {
        return preg_replace('/\D/', '', (string) config('services.owner_reservations.whatsapp_to'));
    }
}
