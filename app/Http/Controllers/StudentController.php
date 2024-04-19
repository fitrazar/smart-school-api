<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Imports\StudentImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Resources\StudentResource;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class StudentController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $student = Student::all();

        return $this->sendResponse(StudentResource::collection($student), 'Data Student');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $input = $request->all();

        if ($input['phone']) {
            $input['phone'] = preg_replace('/[^\d]/', '', $input['phone']);
            $input['phone'] = preg_replace('/^0/', '628', $input['phone']);
        }


        $validator = Validator::make($input, [
            'name' => 'required|string',
            'rombel' => 'required',
            'nisn' => 'required|numeric|unique:students,nisn',
            'photo' => 'nullable|image|max:4098',
            'gender' => 'required|in:Laki - Laki,Perempuan',
            'phone' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        try {
            list($gradeId, $majorId, $groupId) = explode(' ', $input['rombel']);
            if ($request->hasFile('photo')) {
                $fileFilename = time() . '.' . $request->file('photo')->getClientOriginalExtension();
                $photoPath = $request->file('photo')->storeAs('student/photo', $fileFilename);
            }

            $user = new User([
                'name' => 'student' . mt_rand(100, 999999),
                'email' => $input['nisn'] . '@student.com',
                'password' => bcrypt($input['nisn']),
            ]);
            $user->assignRole('student');
            $user->save();

            $student = Student::create([
                'name' => $input['name'],
                'nisn' => $input['nisn'],
                'gender' => $input['gender'],
                'phone' => $input['phone'] ?? NULL,
                'address' => $input['address'] ?? NULL,
                'description' => $input['description'] ?? NULL,
                'birthplace' => $input['birthplace'] ?? NULL,
                'birthdate' => $input['birthdate'] ?? NULL,
                'photo' => $fileFilename ?? NULL,
                'user_id' => $user->id,
                'grade_id' => $gradeId,
                'major_id' => $majorId,
                'group_id' => $groupId,
                'point' => 100,
            ]);


            return $this->sendResponse(new StudentResource($student), 'Student Created');
        } catch (QueryException $e) {
            return $this->sendError('Failed', $e->errorInfo);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student)
    {
        return $this->sendResponse(new StudentResource($student), 'Detail Student');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        $input = $request->only(['name', 'gender', 'phone', 'address', 'rombel', 'photo']);

        if ($request->phone) {
            $input['phone'] = preg_replace('/[^\d]/', '', $input['phone']);
            $input['phone'] = preg_replace('/^0/', '628', $input['phone']);
        }

        $validator = Validator::make($input, [
            'name' => 'string',
            'gender' => 'in:Laki - Laki,Perempuan',
            'phone' => 'nullable|numeric',
            'address' => 'nullable|string',
            'rombel' => 'nullable',
            'photo' => 'nullable|image|max:4098',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        try {
            if ($request->hasFile('photo')) {
                if ($student->photo && Storage::exists('student/photo/' . $student->photo)) {
                    Storage::delete('student/photo/' . $student->photo);
                }

                $fileFilename = time() . '.' . $request->file('photo')->getClientOriginalExtension();
                $photoPath = $request->file('photo')->storeAs('student/photo', $fileFilename);
                $input['photo'] = $fileFilename;
            }

            if ($request->rombel) {
                list($gradeId, $majorId, $groupId) = explode(' ', $input['rombel']);
                $input['grade_id'] = $gradeId;
                $input['major_id'] = $majorId;
                $input['group_id'] = $groupId;
            }

            $student->update($input);

            return $this->sendResponse(new StudentResource($student), 'Student Updated');
        } catch (QueryException $e) {
            return $this->sendError('Failed', $e->errorInfo);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        try {
            if ($student->photo && Storage::exists('student/photo/' . $student->photo)) {
                Storage::delete('student/photo/' . $student->photo);
            }

            $student->delete();

            return $this->sendResponse('-', 'Student Deleted');
        } catch (QueryException $e) {
            return $this->sendError('Failed', $e->errorInfo);
        }
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'file|mimes:csv,xls,xlsx'
        ]);
        $file = $request->file('file');

        $file->store('files');
        Excel::import(new StudentImport, $request->file('file'));

        $response['message'] = 'Student Imported';

        return response()->json($response, 200);
    }
}
