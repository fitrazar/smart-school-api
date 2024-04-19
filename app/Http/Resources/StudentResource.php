<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
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
            'user_id' => $this->user_id,
            'class' => $this->grade->name . ' ' . $this->major->acronym . ' ' . $this->group->number,
            'nisn' => $this->nisn,
            'photo' => $this->photo && !(str_starts_with($this->photo, 'http')) ?
                Storage::url('student/photo/' . $this->photo) : $this->photo,
            'name' => $this->name,
            'gender' => $this->gender,
            'phone' => $this->phone,
            'address' => $this->address,
            'description' => $this->description,
            'birthplace' => $this->birthplace,
            'birthdate' => $this->birthdate,
            'point' => $this->point,
        ];
    }
}
