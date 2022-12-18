<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookPublisher extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'book_publishers';
    protected $fillable = ['book_id','publisher_id'];
}
