<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePublisherPostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'book_title' => 'required|max:255|min:3',
            'authors' => 'required|array|min:1',
            'authors.*.fullname' => 'required|max:255|min:3',
            'authors.*.email' => 'required|max:255|email:rfc,dns'
        ];
    }

    public function messages()
    {
        return [
            'book_title.required' => 'Book title is required',
            'authors.*.fullname' => 'Author name is required',
            'authors.*.email.required' => 'The author\'s email is required'
        ];
    }
}
