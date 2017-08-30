<?php

namespace App;

use Carbon\Carbon;

class PayAction
{
    /**
     * @var \App\Payment
     */
    protected $nextPayment = null;

    public static function instance()
    {
        return new self;
    }

    /**
     * @param  \App\Payment $payment
     * @return $this
     */
    public function pay(Payment $payment)
    {
        $payment->pay();

        if ($payment->isRepeatable()) {
            $this->createNextPaymentFrom($payment);
        }

        return $this;
    }

    /**
     * @return \App\Payment|null
     */
    public function getNextPayment()
    {
        return $this->nextPayment;
    }

    /**
     * @param  \App\Payment $payment
     */
    protected function createNextPaymentFrom(Payment $payment)
    {
        $this->nextPayment = $payment->replicate();
        $this->nextPayment->paid_at = null;

        $this->nextPayment->due_date = Carbon::parse($payment->due_date)
            ->modify("{$payment->repeat_period} {$payment->repeat_designator}")
            ->format('Y-m-d');

        $this->nextPayment->save();
    }
}
