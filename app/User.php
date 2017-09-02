<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'provider',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token',
    ];

    /**
     * Checks if the given entity belongs to this user.
     *
     * @param  \Illuminate\Database\Eloquent\Model $entity
     * @return bool
     */
    public function owns($entity)
    {
        return $entity->user_id == $this->id;
    }

    /**
     * User payments.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * User incoming payments
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function incomingPayments()
    {
        return $this->payments()
            ->whereNull('paid_at')
            ->whereBetween('due_date', [
                date('Y-m-d'),
                date('Y-m-d', strtotime('+2 days'))
            ]);
    }

    /**
     * User overdue payments
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function overduePayments()
    {
        return $this->payments()
            ->whereNull('paid_at')
            ->where('due_date', '<', date('Y-m-d'));
    }
}
