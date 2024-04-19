<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentImport implements ToModel, WithHeadingRow, WithChunkReading, ShouldQueue
{
    public function model(array $row)
    {
        $grade = 0;
        $major = 0;
        $parts = explode(' ', $row[4]);
        if ($parts[0] == "X") {
            $grade = 1;
        } elseif ($parts[0] == "XI") {
            $grade = 2;
        } elseif ($parts[0] == "XII") {
            $grade = 3;
        }

        if ($parts[1] == "TKJ" || $parts[1] == "TJKT") {
            $major = 1;
        } elseif ($parts[1] == "DPIB") {
            $major = 2;
        } elseif ($parts[1] == "TBSM") {
            $major = 3;
        }

        $user = new User([
            'name' => 'student' . mt_rand(100, 999999),
            'email' => $row[3] . '@student.com',
            'password' => bcrypt($row[3]),
        ]);
        $user->assignRole('student');
        $user->save();


        return new Student([
            'name' => Str::title($row[1]),
            'gender' => $row[2] == 'L' ? 'Laki - Laki' : 'Perempuan',
            'nisn' => $row[3],
            'phone' => '-',
            'point' => 100,
            'address' => $row[5] ?? NULL,
            'grade_id' => $grade,
            'major_id' => $major,
            'group_id' => $parts[2],
            'user_id' => $user->id,
        ]);
    }

    public function headingRow(): int
    {
        return 2;
    }

    public function chunkSize(): int
    {
        return 50;
    }
}
