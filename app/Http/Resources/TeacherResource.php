<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeacherResource extends JsonResource
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
            'nip' => $this->nip,
            'user' => $this->whenLoaded('user', function () {
                return $this->user ? [
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                    'gender' => $this->user->gender,
                    'phone' => $this->user->phone,
                    'address' => $this->user->address,
                    'image' => $this->image,
                    'role' => 'teacher'
                ] : null;
            })
        ];
    }
}
