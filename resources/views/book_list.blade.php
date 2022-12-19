@extends('layout.app')
@section('title', 'Library')
@section('content')

    <div class="container">

        <div class="row m-5">
            <div id="table_data">@include('books_pagination')</div>

        </div>
    </div>

    <script>
        $(document).ready(function () {
            $(document).on('click', '.pagination li', function (event) {
                event.preventDefault();

                var page = $(this).find('a').attr('href').split('pagination/page/')[1];
                fetch_data(page);
            });

            function fetch_data(page) {
                $.ajax({
                    type: "GET",
                    url: "pagination/page/" + page,
                    success: function (data) {
                        $('#table_data').html(data);
                    }
                });
            }

        });
    </script>
@endsection