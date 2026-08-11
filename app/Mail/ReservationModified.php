<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservationModified extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Reservation $reservation,
        public ?string $previousCheckIn = null,
        public ?string $previousCheckOut = null,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Réservation modifiée - SugnuHotel',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reservation-modified',
            with: [
                'previousCheckIn' => $this->previousCheckIn,
                'previousCheckOut' => $this->previousCheckOut,
            ],
        );
    }
}
