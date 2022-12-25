<?php

namespace App\Repository;

use App\Models\Book;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class BooksRepository implements IBookRepository{

    public function getBooksList($publisherId): array
    {

        $strSql = "SELECT bp.book_id, b.title, GROUP_CONCAT(a.fullname SEPARATOR ', ') as authors_fullname, GROUP_CONCAT(a.email SEPARATOR ', ') as authors_email FROM book_publishers as bp
                        join books as b on b.book_id=bp.book_id
                        join book_authors as ba on ba.book_id = b.book_id
                        join authors as a on a.author_id=ba.author_id
                        WHERE bp.publisher_id=?
                        group by  ba.book_id";


        return DB::select($strSql, [$publisherId]);
    }


    private function checkBookExistByBookIdPublisherId($publisherID, $bookId)
    {
        if ($publisherID > 0 && $bookId > 0) {
            return DB::table('book_publishers as bp')
                ->whereRaw('bp.publisher_id = :bp_id and bp.book_id = :bid', ['bp_id' => $publisherID, 'bid' => $bookId])
                ->count();
        }
    }

    public function getBookInfoById($publisher_id, $bookId) : array
    {
        //Does book info exist in DB by given parameters(book_id and publisher_id), if not return corresponding response
        $getBookCountByBookId = $this->checkBookExistByBookIdPublisherId($publisher_id,  $bookId);

        if ($getBookCountByBookId <= 0) {
            return response()->json(
                [
                    'operation_message' => "No data found"
                ], 400);
        }

        $strSql = "SELECT bp.book_id, b.title, GROUP_CONCAT(a.fullname SEPARATOR ', ') as authors_fullname, GROUP_CONCAT(a.email SEPARATOR ', ') as authors_email FROM book_publishers as bp
                        join books as b on b.book_id=bp.book_id
                        join book_authors as ba on ba.book_id = b.book_id
                        join authors as a on a.author_id=ba.author_id
                        WHERE bp.publisher_id=? and bp.book_id=?
                        group by  ba.book_id";


        return DB::select($strSql, [$publisher_id, $bookId]);
    }


    public function deleteBookInfoById($publisher_id, $bookId)
    {
        //Begin Transaction
        DB::beginTransaction();

        try {

            //Does book info exist in DB by given parameters(book_id and publisher_id), if not return corresponding response
            $getBookCountByBookId = $this->checkBookExistByBookIdPublisherId($publisher_id, $bookId);
            if ($getBookCountByBookId <= 0) {
                return response()->json(
                    [
                        'operation_message' => "No data found to delete"
                    ], 400);
            }

            //delete book_id and publisher from book_publishers table
            DB::table('book_publishers as bp')
                ->whereRaw('bp.publisher_id = :bp_id and bp.book_id = :bid', ['bp_id' => $publisher_id, 'bid' => $bookId])
                ->delete();

            //Also delete book's author
            DB::table('book_authors as ba')
                ->whereRaw('ba.book_id = :bid', ['bid' => $bookId])
                ->delete();

            //Also delete Book
            DB::table('books as b')
                ->whereRaw('b.book_id = :bid', ['bid' => $bookId])
                ->delete();

            DB::commit();
            return response()->json(
                [
                    'operation_message' => "The book number ".$bookId." was successfully deleted"
                ], 200);

        }catch (\Exception $exception){
            DB::rollBack();
            return response()->json(
                [
                    'operation_message' =>  $exception->getMessage()
                ], 500);
        }
    }
}
