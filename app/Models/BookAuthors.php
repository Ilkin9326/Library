<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookAuthors extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'book_authors';

    protected $fillable = ['book_id','author_id'];
}
