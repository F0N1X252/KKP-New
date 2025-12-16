<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'author_name' => $this->author_name,
            'author_email' => $this->author_email,
            'attachment' => $this->attachment ? asset('storage/' . $this->attachment) : null,
            'attachment_name' => $this->attachment ? basename($this->attachment) : null,
            'status' => new StatusResource($this->whenLoaded('status')),
            'priority' => new PriorityResource($this->whenLoaded('priority')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'assigned_to_user' => new UserResource($this->whenLoaded('assigned_to_user')),
            'comments' => CommentResource::collection($this->whenLoaded('comments')),
            'comments_count' => $this->comments_count ?? $this->comments->count(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_at_formatted' => $this->created_at ? $this->created_at->format('d M Y, H:i') : null,
        ];
    }
}
