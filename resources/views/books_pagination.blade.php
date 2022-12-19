<div class="card col shadow p-3 mb-5 bg-body rounded">
    <div class="card-body col">
        <h3 class="card-subtitle mb-2 text-primary">Books</h3>
        <hr>
        <table id="example" class="table table-hover col">
            <thead>
            <tr>
                <th scope="col">Book Title</th>
                <th scope="col">Author</th>
                <th scope="col">Publisher</th>
            </tr>
            </thead>
            <tbody>
            @foreach($data as $value)
                <tr>
                    <td>{{$value->book_title}}</td>
                    <td>{{$value->author_name}}</td>
                    <td>{{$value->publisher_name}}</td>

                </tr>
            @endforeach

            </tbody>
        </table>
        <nav aria-label="Page navigation example">

            <ul class='pagination text-center mr-1' id="pagination">
                @for($i=1; $i<=$total_pages; $i++)

                    <li class='page-item' id="{{$i}}"><a class="page-link"
                                                         href="pagination/page/{{$i}}">{{$i}}</a>
                    </li>

                @endfor
            </ul>
        </nav>
    </div>

</div>