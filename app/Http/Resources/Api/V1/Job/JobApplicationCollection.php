<?php

namespace App\Http\Resources\Api\V1\Job;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class JobApplicationCollection extends ResourceCollection
{
    public $collects = JobApplicationResource::class;

    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data'  => $this->collection,
        ];
    }
}
