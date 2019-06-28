<?php

use App\Models\Communications\Memo;
use App\Models\User;

class MemosTableSeeder extends AbstractSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->truncateTables(['memos', 'memo_user']);

        /** @var \Illuminate\Database\Eloquent\Collection $users */
        $users = User::all();

        /** @var \Illuminate\Database\Eloquent\Collection $memos */
        $memos = factory(Memo::class, 10)->create();

        foreach ($memos as $memo) {
            /* @var Memo $memo */
            $memo->setRecipients($users->pluck('id')->toArray());
        }
    }
}
