<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProcedureTypeResource extends JsonResource
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
            'name' => $this->name,
            'code' => $this->code,
            'counter' => $this->counter,
            'total_procedures' => $this->total_procedures,
            'requirements' => $this->requirements()->orderBy('name')->pluck('id'),
        ];
    }
}
