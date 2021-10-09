<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'last_name' => $this->last_name,
            'identity_card' => $this->identity_card,
            'username' => $this->username,
            'email' => $this->email,
            'address' => $this->address,
            'phone' => $this->phone,
            'area_id' => $this->area_id,
            'role' => $this->roles()->first()->name,
            'is_active' => $this->deleted_at === NULL ? true : false,
            'created_at' => $this->created_at,
        ];
    }
}
