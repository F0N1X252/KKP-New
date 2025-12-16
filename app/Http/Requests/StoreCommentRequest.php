<?php

namespace App\Http\Requests;

use App\Comment;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class StoreCommentRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('comment_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ticket_id' => [
                'required',
                'integer',
                'exists:tickets,id',
            ],
            'comment_text' => [
                'required',
                'string',
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
