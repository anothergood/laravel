<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreChatRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        if ($this->type == 'chat') {
            $condition = 'array';
        }   elseif ($this->type == 'dialog') {
            $condition = 'required|numeric|exists:users,id';
        }
        return [
            'title' => 'required|max:50',
            'users' => $condition,
            'type' => 'required|in:chat,dialog',
        ];
    }
}
