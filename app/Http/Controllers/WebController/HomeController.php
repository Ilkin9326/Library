<?php

namespace App\Http\Controllers\WebController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Illuminate\Support\Facades\DB;


class HomeController extends Controller
{

    public function showBooksList()
    {
        $bookCount = DB::select('select count(*) as count from books');
        $limit=5;
        $total_records = $bookCount[0]->count;
        $total_pages = ceil($total_records / $limit);



        $strSQL = "select b.book_id, b.title as book_title, GROUP_CONCAT(a.fullname SEPARATOR ', ') as author_name, p.name as publisher_name from book_publishers as bp
            join publisher as p on bp.publisher_id = p.publisher_id
            join books as b on bp.book_id = b.book_id
            join book_authors as ba on ba.book_id = b.book_id
            join authors as a on ba.author_id = a.author_id
            GROUP by bp.book_id
            ORDER by book_title asc LIMIT ?, ?";


        $authors = DB::select($strSQL, [0, $limit-1]);
        return view('book_list', ['data' => $authors, 'total_pages'=>$total_pages]);

    }


    function fetch_data(Request $request, $id)
    {
        $limit = 4;
        $strSQL = "select b.book_id, b.title as book_title, GROUP_CONCAT(a.fullname SEPARATOR ', ') as author_name, p.name as publisher_name from book_publishers as bp
            join publisher as p on bp.publisher_id = p.publisher_id
            join books as b on bp.book_id = b.book_id
            join book_authors as ba on ba.book_id = b.book_id
            join authors as a on ba.author_id = a.author_id
            GROUP by bp.book_id
            ORDER by book_title asc LIMIT ?, ?";

        $startFrom = ($id-1) * $limit;

        $bookInfo = DB::select($strSQL, [$startFrom, $limit]);

        $bookCount = DB::select('select count(*) as count from books');
        $limit=5;
        $total_records = $bookCount[0]->count;
        $total_pages = ceil($total_records / $limit);

        return view('books_pagination', ['data' => $bookInfo, 'total_pages'=>$total_pages])->render();

    }

}