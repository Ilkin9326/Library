<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Publisher extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'publisher';
    protected $fillable = ['name', 'api_key'];
}
