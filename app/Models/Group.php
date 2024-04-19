<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Group extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Get all of the students for the Group
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'group_id');
    }

    /**
     * Get all of the homerooms for the Group
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function homeroom(): HasOne
    {
        return $this->hasOne(Homeroom::class, 'group_id');
    }

    /**
     * Get all of the schedules for the Group
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(LessonSchedule::class, 'group_id');
    }
}
