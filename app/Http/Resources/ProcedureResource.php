<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProcedureResource extends JsonResource
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
            'origin' => $this->origin,
            'detail' => $this->detail,
            'archived' => $this->archived,
            'area_id' => $this->area_id,
            'procedure_type_id' => $this->procedure_type_id,
            'owner' => $this->owner,
            'verified' => $this->verified,
            'counter' => $this->counter,
            'user_id' => $this->user_id,
            'has_flowed' => $this->has_flowed,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->updated_at,
        ];
    }
}
