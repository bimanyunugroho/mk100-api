<?php

namespace App\Http\Resources\Api\V1\Job;

use App\Http\Resources\Api\V1\Auth\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'title'              => $this->title,
            'description'        => $this->description,
            'requirements'       => $this->requirements,
            'salary_range'       => $this->salary_range,
            'location'           => $this->location,
            'type'               => $this->type->value,
            'status'             => $this->status->value,
            'published_at'       => $this->published_at?->toISOString(),
            'applications_count' => $this->whenCounted('applications'),
            'employer'           => new UserResource($this->whenLoaded('employer')),
            'created_at'         => $this->created_at?->toISOString(),
            'updated_at'         => $this->updated_at?->toISOString(),
        ];
    }
}
