<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePublisherPostRequest;
use App\Models\Authors;
use App\Models\Book;
use App\Models\BookAuthors;
use App\Models\BookPublisher;
use App\Repository\IBookRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class PublisherController extends Controller
{
    private $bookOperation;

    public function __construct(IBookRepository $bookRepository)
    {
        $this->bookOperation = $bookRepository;
    }

    private function getPublisherInfoByRequestHeader($request)
    {
        $api_key = $request->header('x-api-key');

        // Select publisher from database by its API-KEY
        return DB::table('publisher as p')->where('api_key', $api_key)
            ->select('p.publisher_id')
            ->first();
    }

    /**
     * @lrd:start
     * #Store book info
     * ## This api is post request
     *
     * @lrd:end
     */
    public function storeBookInfo(StorePublisherPostRequest $request)
    {
        // Validate request
        $request->validated();

        //Begin Transaction
        DB::beginTransaction();
        try {
            // Get publishers api key from request header
            $publisherInfo = $this->getPublisherInfoByRequestHeader($request);

            // Handle Authors
            $authors = $request->authors;

            for ($i = 0; $i < count($authors); $i++) {
                $authorInfo = DB::table('authors')->where(
                    [
                        ['fullname', '=', $authors[$i]['fullname']],
                        ['email', '=', $authors[$i]['email']
                        ]
                    ])->first();

                $authorId = 0;

                if ($authorInfo != null) {
                    $authorId = $authorInfo->author_id;
                } else {
                    //Insert new author record and get Id
                    $authorId = DB::table('authors')->insertGetId(
                        [
                            'fullname' => $authors[$i]['fullname'],
                            'email' => $authors[$i]['email']
                        ]
                    );

                }

                // Set author id
                $authors[$i]['author_id'] = $authorId;
            }


            // Check if a book already exists in database with same title and authors
            $authorsInCondition = implode(",", array_column($authors, 'author_id'));
            $strSQL = "select b.book_id from books as b
                    where b.title=?
                    and (select count(*) from book_authors ba where ba.book_id=b.book_id)=?
                    and (select count(*) from book_authors ba where ba.book_id=b.book_id and ba.author_id not in (" . $authorsInCondition . "))=0";


            $bookInfo = DB::select($strSQL, [$request->book_title, count($authors)]);

            $book_id = 0;
            if ($bookInfo != null) {
                $book_id = $bookInfo[0]->book_id;
            } else {
                // Insert new book
                $book_id = DB::table('books')->insertGetId(['title' => $request->book_title]);

                // Insert book authors
                foreach ($authors as $author) {
                    BookAuthors::create(['book_id' => $book_id, 'author_id' => $author['author_id']]);
                }
            }

            // If this book is not exists already in given publisher
            $bookPublisherInfo = DB::table('book_publishers')->where([['book_id', '=', $book_id], ['publisher_id', '=', $publisherInfo->publisher_id]])
                ->select('publisher_id')
                ->first();

            // Insert book publisher if not already exists
            if ($bookPublisherInfo == null) {
                DB::table('book_publishers')->insert(['book_id' => $book_id, 'publisher_id' => $publisherInfo->publisher_id]);
            }

            DB::commit();
            return response()->json(
                [
                    'operation_message' => 'Your record was successfully created'
                ], 200);

        }catch (\Exception $exception){
            DB::rollBack();
            return response()->json(
                [
                    'operation_message' =>  $exception->getMessage()
                ], 500);
        }
    }


    /**
     * @lrd:start
     * #List Of Books by x-api-key
     * ## This api gets all the book list by Publisher
     * @lrd:end
     */
    public function getBooksList(Request $request):JsonResponse
    {
        // Get publishers api key from request header
        $publisherInfo = $this->getPublisherInfoByRequestHeader($request);

        $bookListByPublisher = $this->bookOperation->getBooksList($request, $publisherInfo->publisher_id);
        return response()->json(
            [
                'operation_message' => "Success response ",
                'book_list' => $bookListByPublisher
            ], 200);


    }


    private function checkBookExistByBookIdPublisherId($publisherID, $bookId)
    {
        if ($publisherID > 0 && $bookId > 0) {
            return DB::table('book_publishers as bp')
                ->whereRaw('bp.publisher_id = :bp_id and bp.book_id = :bid', ['bp_id' => $publisherID, 'bid' => $bookId])
                ->count();
        }
    }

    /**
     * @lrd:start
     * #Get Book Info By bookId
     * @bookId book's auto_increment id
     * @lrd:end
     */
    public function getBookById(Request $request, $bookId):JsonResponse
    {
        // Get publishers api key from request header
        $publisherInfo = $this->getPublisherInfoByRequestHeader($request);

        //Does book info exist in DB by given parameters(book_id and publisher_id), if not return corresponding response
        $getBookCountByBookId = $this->checkBookExistByBookIdPublisherId($publisherInfo->publisher_id, $bookId);

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


        $bookInfoById = DB::select($strSql, [$publisherInfo->publisher_id, $bookId]);

        return response()->json(
            [
                'operation_message' => "Success response",
                'book' => $bookInfoById
            ], 200);

    }

    /**
     * @lrd:start
     * #Delete book By bookId
     * @bookId book's auto_increment id
     * @lrd:end
     */
    public function deleteBookById(Request $request, $bookId)
    {

        //Begin Transaction
        DB::beginTransaction();

        try {

            if($bookId <= 0 || is_null($bookId)){
                return response()->json(
                    [
                        'operation_message' => 'book_id can\'t be null or less than zero '
                    ], 400);
            }

            // Get publishers api key from request header
            $publisherInfo = $this->getPublisherInfoByRequestHeader($request);

            //Does book info exist in DB by given parameters(book_id and publisher_id), if not return corresponding response
            $getBookCountByBookId = $this->checkBookExistByBookIdPublisherId($publisherInfo->publisher_id, $bookId);
            if ($getBookCountByBookId <= 0) {
                return response()->json(
                    [
                        'operation_message' => "No data found to delete"
                    ], 400);
            }

            //delete book_id and publisher from book_publishers table
            DB::table('book_publishers as bp')
                ->whereRaw('bp.publisher_id = :bp_id and bp.book_id = :bid', ['bp_id' => $publisherInfo->publisher_id, 'bid' => $bookId])
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

    /**
     * @lrd:start
     * #Update Book info and Book authorName By bookId
     * @bookId book's auto_increment id
     * @lrd:end
     */
    public function updateBookInfoById(Request $request, $bookId)
    {
        //Begin Transaction
        DB::beginTransaction();

        if($bookId < 0 || is_null($bookId)){
            return response()->json(
                [
                    'operation_message' => 'book_id can\'t be null or less than zero '
                ], 400);
        }
        try {
            // Get publishers api key from request header
            $publisherInfo = $this->getPublisherInfoByRequestHeader($request);

            //Does book info exist in DB by given parameters(book_id and publisher_id), if not return corresponding response
            $getBookCountByBookId = $this->checkBookExistByBookIdPublisherId($publisherInfo->publisher_id, $bookId);

            if ($getBookCountByBookId <= 0) {
                return response()->json(
                    [
                        'operation_message' => 'No data found'
                    ], 404);
            }

            //Update book title
            DB::table('books as b')
                ->whereRaw('b.book_id = ?', $bookId)
                ->update(['b.title' => $request->book_title]);

            //Update Author's name
            $authors = $request->authors;
            for ($i = 0; $i < count($authors); $i++) {

                //Update Author fullname by Email
                DB::table('authors as a')
                    ->whereRaw('a.email = ?', $authors[$i]['email'])
                    ->update(['a.fullname' => $authors[$i]['fullname']]);

            }


            DB::commit();
            return response()->json(
                [
                    'operation_message' => 'Your record was successfully updated'
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
