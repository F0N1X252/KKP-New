<?php

namespace App\Http\Requests;

use App\Ticket;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('ticket_create');
    }

    /**
     * Prepare data SEBELUM validasi
     */
    protected function prepareForValidation()
    {
        // Auto-fill author_name & author_email SEBELUM validasi
        $this->merge([
            'author_name'  => $this->author_name ?: auth()->user()->name,
            'author_email' => $this->author_email ?: auth()->user()->email,
        ]);
        
        // Log untuk debug
        Log::info('PrepareForValidation:', [
            'author_name'  => $this->author_name,
            'author_email' => $this->author_email,
        ]);
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
                'max:10240', // 10MB
            ],

            'attachments'   => ['nullable', 'array', 'max:3'], // Max 3 files
            'attachments.*' => ['file', 'max:102400'], // Max 100MB per file

            'assigned_to_user_id' => [
                'nullable',
                'integer',
            ],
            'author_name' => [
                'required', // <-- UBAH JADI REQUIRED karena sudah auto-fill di prepareForValidation
                'string',
                'max:255',
            ],
            'author_email' => [
                'required', // <-- UBAH JADI REQUIRED karena sudah auto-fill di prepareForValidation
                'email',
                'max:255',
            ],
        ];
    }
}
