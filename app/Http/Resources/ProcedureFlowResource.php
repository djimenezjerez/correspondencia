<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProcedureFlowResource extends JsonResource
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
            'id' => intval($this->id),
            'code' => strval($this->code),
            'origin' => strval($this->origin),
            'detail' => strval($this->detail),
            'archived' => filter_var($this->archived, FILTER_VALIDATE_BOOLEAN),
            'area_id' => intval($this->area_id),
            'procedure_type_id' => intval($this->procedure_type_id),
            'owner' => filter_var($this->owner, FILTER_VALIDATE_BOOLEAN),
            'has_flowed' => filter_var($this->has_flowed, FILTER_VALIDATE_BOOLEAN),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->updated_at,
            'from_area' => intval($this->from_area),
            'incoming_at' => $this->incoming_at,
            'incoming_user' => intval($this->incoming_user),
            'to_area' => intval($this->to_area),
            'outgoing_at' => $this->outgoing_at,
            'outgoing_user' => intval($this->outgoing_user),
        ];
    }
}
