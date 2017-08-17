<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateBookRequest extends StoreBookRequest
{
    public function authorize()
    {
        return Auth::check();
        // return false;
    }

    public function rules()
    {
        $rules = Parent::rules();
        $rules['title'] = 'required|unique:books,title,' . $this->route('book');
        return $rules;
    }
}
