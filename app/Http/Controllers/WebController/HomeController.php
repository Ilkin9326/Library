<?php

namespace App\Http\Controllers\WebController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Illuminate\Support\Facades\DB;

class HomeController extends Controller
{

    public function index()
    {
        $authors = DB::table('book_publishers as bp')
            ->join('publisher as p', 'bp.publisher_id', '=', 'p.publisher_id')
            ->join('books as b', 'bp.book_id', '=', 'b.book_id')
            ->join('book_authors as ba', 'ba.book_id', '=', 'b.book_id')
            ->join('authors as a', 'ba.author_id', '=', 'a.author_id')
            ->select('b.title as book_title', DB::raw("GROUP_CONCAT(a.fullname SEPARATOR ', ') as author_name"), 'p.name as publisher_name')
            ->groupBy('bp.book_id')
            ->orderBy('bp.publisher_id')
            ->get();
//            ->simplePaginate(5);

        return view('library', ['data' => $authors]);
    }


    public function libDataPagination(Request $request){
        if($request->ajax()){
            $info = DB::table('book_publishers as bp')
                ->join('publisher as p', 'bp.publisher_id', '=', 'p.publisher_id')
                ->join('books as b', 'bp.book_id', '=', 'b.book_id')
                ->join('book_authors as ba', 'ba.book_id', '=', 'b.book_id')
                ->join('authors as a', 'ba.author_id', '=', 'a.author_id')
                ->select('b.title as book_title', DB::raw("GROUP_CONCAT(a.fullname SEPARATOR ', ') as author_name"), 'p.name as publisher_name')
                ->groupBy('bp.book_id')
                ->orderBy('bp.publisher_id')
                ->simplePaginate(5);
            return view('library_pagination_data', ['data' => $info])->render();
        }

    }
}
