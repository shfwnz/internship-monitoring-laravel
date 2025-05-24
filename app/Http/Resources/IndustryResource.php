<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IndustryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'business_field' => $this->whenLoaded('business_field', function () {
                return $this->business_field ? [
                    'id' => $this->business_field->id,
                    'name' => $this->business_field->name
                ] : null;
            }),
            'address' => $this->address,
            'phone' => $this->phone,
            'email' => $this->email
        ];
    }
}
