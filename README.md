
# Library API system
- There are several book publishing sites that would like to see their books in the Library. At the same time, different publishers can print the same books. Books can have multiple authors, and authors can have multiple books.
-  Write a public (without checking the secret token), public RESTful API (hereinafter referred to as the API) for publishing sites, which will allow you to add, modify and delete books from the Library list.
-  Display the list of books on the main page of the Library. Each line of the list should contain the title of the book, the names of the authors, and the name of the publisher. The list should have ajax pagination made from scratch - without using the features of Laravel Eloquent, Bootstrap or any other ready-made solutions.




## Authors

- [@ilkin isgenderli](https://www.linkedin.com/in/ilkin-isgenderli-aa720313b/)


## Run Locally

Clone the project

```bash
  https://github.com/Ilkin9326/Library.git
```

Go to the project directory

```bash
  cd Library
```

Install all the dependencies using composer

```bash
  composer install
```

Copy the example env file and make the required configuration changes in the .env file
```bash
  cp .env.example .env
```

Generate a new application key
```bash
  php artisan key:generate
```

Run the database migrations (Set the database connection in .env before migrating)
```bash
  php artisan migrate
```

Start the local development server

```bash
  php artisan serve
```
You can now access the server at http://localhost:8000

## Documentation

- API authorization has been configurated to manually set a key for every publisher in a publisher table.
- For API Documentation [laravel-request-docs](https://github.com/rakutentech/laravel-request-docs)
- For LRD view at http://localhost/request-docs

