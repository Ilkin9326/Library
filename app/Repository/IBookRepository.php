<?php

namespace App\Repository;

use Illuminate\Http\Request;

 interface IBookRepository{
     public function getBooksList($publisherId);
     public function getBookInfoById($publisher_id,  $bookId);
     public function deleteBookInfoById($publisher_id, $bookId);
 }
