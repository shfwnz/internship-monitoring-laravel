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
            'role' => $this->role, 
        ];

        // Profile (teacher or student)
        if ($this->profile) {
            $data['profile'] = match($this->role) {
                'student' => new StudentResource($this->profile),
                'teacher' => new TeacherResource($this->profile),
                default => null,
            };
        }

        return $data;
    }
}
