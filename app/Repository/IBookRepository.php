<?php

namespace App\Repository;

use Illuminate\Http\Request;

 interface IBookRepository{
     public function getBooksList(Request $request, $publisherId);
     public function getBookInfoById(Request $request, $id);
 }
