<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
Use Illuminate\Support\Facades\Auth;

class UpdateAuthorRequest extends StoreAuthorRequest
{
    public function authorize()
    {
        return Auth::check();
        // return false;
    }

    public function rules()
    {
        $rules = Parent::rules();
        $rules['name'] = 'required|unique:authors,name,'. $this->route('author');
        return $rules;
    }
}
