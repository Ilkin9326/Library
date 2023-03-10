<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePublisherPostRequest;
use App\Models\BookAuthors;
use App\Repository\IBookRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

        // Get publishers api key from request header
        $publisherInfo = $this->getPublisherInfoByRequestHeader($request);
        return $this->bookOperation->storeBookInfo($request, $publisherInfo->publisher_id);

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

        $bookListByPublisher = $this->bookOperation->getBooksList($publisherInfo->publisher_id);
        return response()->json(
            [
                'operation_message' => "Success response ",
                'book_list' => $bookListByPublisher
            ], 200);


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

        $bookInfoById = $this->bookOperation->getBookInfoById($publisherInfo->publisher_id, $bookId);

        //Log::channel('book')->warning($bookInfoById);
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
        // Get publishers api key from request header
        $publisherInfo = $this->getPublisherInfoByRequestHeader($request);
        return $this->bookOperation->deleteBookInfoById($publisherInfo->publisher_id, $bookId);

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
