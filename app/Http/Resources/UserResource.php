<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // User
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'gender' => $this->gender,
            'address' => $this->address,
            'phone' => $this->phone,
            'roles' => $this->roles->pluck('name'), 
            'user_type' => $this->user_type,
            'image' => $this->image
        ];

        // Profile (teacher or student)
        if ($this->userable) {
            $profileClass = class_basename($this->userable_type);
            $resourceClass = "App\\Http\\Resources\\{$profileClass}Resource";
            
            if (class_exists($resourceClass)) {
                $data['profile'] = new $resourceClass($this->userable);
            }
        }

        return $data;
    }
}
