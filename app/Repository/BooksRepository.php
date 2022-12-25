<?php

namespace App\Repository;

use App\Models\Book;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class BooksRepository implements IBookRepository{

    public function getBooksList(Request $request, $publisherId): array
    {

        $strSql = "SELECT bp.book_id, b.title, GROUP_CONCAT(a.fullname SEPARATOR ', ') as authors_fullname, GROUP_CONCAT(a.email SEPARATOR ', ') as authors_email FROM book_publishers as bp
                        join books as b on b.book_id=bp.book_id
                        join book_authors as ba on ba.book_id = b.book_id
                        join authors as a on a.author_id=ba.author_id
                        WHERE bp.publisher_id=?
                        group by  ba.book_id";


        return DB::select($strSql, [$publisherId]);
    }

    public function getBookInfoById(Request $request, $id)
    {
        return $this->getPublisherInfoByRequestHeader($request);
    }
}
