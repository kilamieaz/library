<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMemberRequest extends StoreMemberRequest
{
    public function rules()
    {
        $rules = parent::rules();
        $rules['email'] = 'required|unique:users,email,'. $this->route('member');
        return $rules;
    }
}
