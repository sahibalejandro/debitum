<?php

namespace App\Mail;

use App\User;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentsReport extends Mailable
{
    use SerializesModels;

    /**
     * @var \App\User
     */
    public $user;

    /**
     * Create a new message instance.
     *
     * @param  \App\User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Payments Report')
            ->markdown('email.payments-report');
    }
}
