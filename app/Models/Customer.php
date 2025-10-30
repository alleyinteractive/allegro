<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class Customer extends Authenticatable
{
    protected $fillable = [
        'name',
        'email',
        'email_verified',
    ];
}
