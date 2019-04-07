<?php

namespace App\Models;

use Illuminate\Support\Str;

class PasswordReset extends BaseEntity
{
    const TOKEN_EXPIRATION_PERIOD = 60;

    protected $table = 'password_resets';

    protected $dates = [
        'created_at',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'email',
        'token',
    ];

    /**
     * Generates a password recovery token.
     *
     * @return string
     */
    public function generateToken()
    {
        return hash_hmac('sha256', Str::random(40), config('app.key'));
    }
}
