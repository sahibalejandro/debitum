<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property $due_date
 */
class Payment extends Model
{
    protected $fillable = [
        'name',
        'amount',
        'due_date',
        'repeat_period',
        'repeat_designator',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($payment) {
            $payment->sanitize();
        });
    }

    public function sanitize()
    {
        if (! $this->repeat_period) {
            $this->repeat_period = null;
            $this->repeat_designator = null;
        }
    }

    public function pay()
    {
        $this->paid_at = date('Y-m-d');
        $this->save();
    }

    public function isRepeatable()
    {
        return !is_null($this->repeat_period);
    }
}
