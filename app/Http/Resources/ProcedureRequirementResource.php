<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProcedureRequirementResource extends JsonResource
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
            'id' => $this->id,
            'code' => $this->code,
            'verified' => $this->verified,
            'requirements' => $this->requirements()->orderBy('name')->get()->map(function($item, $key) {
                $item->validated = $item->pivot->validated;
                return $item->only(['id', 'name', 'validated', 'updated_at']);
            }),
        ];
    }
}
