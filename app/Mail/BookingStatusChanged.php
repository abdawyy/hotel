<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $messageText;

    /**
     * Create a new message instance.
     */
    public function __construct(Booking $booking, $messageText = null)
    {
        $this->booking = $booking;
        $this->messageText = $messageText;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Your Booking Status Has Changed')
            ->view('emails.booking-status-changed');
    }
}
