<?php

namespace App;

use Carbon\Carbon;
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

    protected $hidden = [
        'user_id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'amount' => 'int',
        'repeat_period' => 'int',
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

    /**
     * Get the payment proximity date, such as "Today", "Tomorro"
     * or "January 23, 2017".
     *
     * @return string
     */
    public function getDueDateProximityAttribute()
    {
        $dueDate = Carbon::parse($this->due_date);

        if ($dueDate->isToday()) {
            return 'Today';
        }

        if ($dueDate->isTomorrow()) {
            return 'Tomorrow';
        }

        return $dueDate->format('F d, Y');
    }

    /**
     * Get the amount in currency format.
     *
     * @return string
     */
    public function getAmountAsCurrencyAttribute()
    {
        return '$' . number_format($this->amount / 100, 2);
    }
}
