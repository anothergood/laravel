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
        $data = [
            'title' => 'required|max:50',
            'type' => 'required|in:chat,dialog',
        ];

        if ($this->type == 'chat') {
            $data['users'] = 'array';
            $data['users.*'] = 'exists:users,id';
        }   elseif ($this->type == 'dialog') {
            $data['users'] = 'required|exists:users,id';
        }

        return $data;
    }
}
