<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Major extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Get all of the students for the Major
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'major_id');
    }

    /**
     * Get all of the homerooms for the Major
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function homeroom(): HasOne
    {
        return $this->hasOne(Homeroom::class, 'major_id');
    }

    /**
     * Get all of the schedules for the Major
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(LessonSchedule::class, 'major_id');
    }
}
