<?php

namespace App\Http\Resources;

use App\Models\Procedure;
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
        $procedure = Procedure::find($this->id);

        return [
            'id' => $procedure->id,
            'code' => $procedure->code,
            'origin' => $procedure->origin,
            'detail' => $procedure->detail,
            'archived' => $procedure->archived,
            'area_id' => $procedure->area_id,
            'procedure_type_id' => $procedure->procedure_type_id,
            'owner' => filter_var($this->owner, FILTER_VALIDATE_BOOLEAN),
            'has_flowed' => filter_var($this->has_flowed, FILTER_VALIDATE_BOOLEAN),
            'validated' => $procedure->validated,
            'created_at' => $procedure->created_at,
            'updated_at' => $procedure->updated_at,
            'deleted_at' => $procedure->updated_at,
            'from_area' => intval($this->from_area),
            'incoming_at' => $this->incoming_at,
            'incoming_user' => intval($this->incoming_user),
            'to_area' => intval($this->to_area),
            'outgoing_at' => $this->outgoing_at,
            'outgoing_user' => intval($this->outgoing_user),
        ];
    }
}
