<?php

namespace App\Http\Requests;

use App\Ticket;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateTicketRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('ticket_edit');
    }

    public function rules()
    {
        return [
            'title'       => [
                'required',
                'string',
                'max:255',
            ],
            'content'     => [
                'required',
            ],
            'status_id'   => [
                'required',
                'integer',
            ],
            'priority_id' => [
                'required',
                'integer',
            ],
            'category_id' => [
                'required',
                'integer',
            ],
            'attachment'  => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf,doc,docx',
                'max:10240',
            ],
            'assigned_to_user_id' => [
                'nullable',
                'integer',
            ],
            'author_name' => [
                'nullable',
                'string',
                'max:255',
            ],
            'author_email' => [
                'nullable',
                'email',
                'max:255',
            ],
        ];
    }
}