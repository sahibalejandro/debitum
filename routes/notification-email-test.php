<?php

use Illuminate\Support\Facades\DB;

if (app()->isLocal()) {
    Route::get('notification-test', function () {
        DB::connection()->beginTransaction();

        $user = factory(App\User::class)->create();

        // Incoming payments
        factory(App\Payment::class)->create([
            'user_id' => $user->id,
            'due_date' => date('Y-m-d', strtotime('today')),
            'paid_at' => null,
        ]);

        factory(App\Payment::class)->create([
            'user_id' => $user->id,
            'due_date' => date('Y-m-d', strtotime('tomorrow')),
            'paid_at' => null,
        ]);

        factory(App\Payment::class)->create([
            'user_id' => $user->id,
            'due_date' => date('Y-m-d', strtotime('+2 days')),
            'paid_at' => null,
        ]);

        // Overdue payments
        factory(App\Payment::class, 3)->create([
            'user_id' => $user->id,
            'due_date' => date('Y-m-d', strtotime('-5 days')),
            'paid_at' => null,
        ]);

        return new App\Mail\PaymentsReport($user);

        DB::connection()->rollBack();
    });
}
