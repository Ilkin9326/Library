@extends('layout.app')
@section('title', 'Library')
@section('content')


    <div class="container">
        <div class="row m-5">
            <div class="card col shadow p-3 mb-5 bg-body rounded">
                <div class="card-body col">
                    <table id="example" class="table table-hover col">
                        <thead>
                        <tr>
                            <th scope="col">Book Title</th>
                            <th scope="col">Author</th>
                            <th scope="col">Publisher</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $key =>  $value)
                            <tr>

                                <td>{{$value->book_title}}</td>
                                <td>{{$value->author_name}}</td>
                                <td>{{$value->publisher_name}}</td>

                            </tr>
                        @endforeach

                        </tbody>
                    </table>

                </div>

            </div>
        </div>
    </div>

@endsection
