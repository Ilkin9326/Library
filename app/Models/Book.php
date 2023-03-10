<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected  $table = 'books';
    protected  $primaryKey = 'book_id';
    protected $fillable = ['book_id','title'];

    protected $columns = [
        'book_title' => 'title'
    ];
}
