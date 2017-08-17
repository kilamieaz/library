<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreBookRequest extends FormRequest
{
    public function authorize()    // yang menentukan akses user
    {
        return Auth::check();
        // return false;
    }

    public function rules()    //menentukan aturan validasi
    {
        return [
            'title'     => 'required|unique:books,title',
            'author_id' => 'required|exists:authors,id',
            'amount'    => 'numeric',
            'cover'     => 'image|max:2048'
        ];
    }
}
