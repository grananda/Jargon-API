<?php

use App\Models\Memo;
use App\Models\User;
use App\Notifications\MemoSentNotification;
use Illuminate\Support\Facades\Notification;

class MemosTableSeeder extends AbstractSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->truncateTables(['notifications']);

        $users = User::all();

        /** @var \App\Models\User $user */
        foreach ($users as $user) {
            factory(Memo::class, 5)->create(['user_id' => $user->id]);
        }

        $memos = Memo::all();

        /** @var Memo $memo */
        foreach ($memos as $memo) {
            Notification::send($user, new MemoSentNotification($memo));
        }
    }
}
