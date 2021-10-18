<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TimelineResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'to_area' => $this['flowed_to_area']['name'],
            'action' => $this['action'],
            'date' => $this['created_at'],
        ];
    }
}
