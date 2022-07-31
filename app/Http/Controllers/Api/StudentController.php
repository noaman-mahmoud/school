<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\StoreStudentRequest;
use App\Http\Requests\API\UpdateStudentRequest;
use App\Http\Resources\SchoolsResource;
use App\Http\Resources\StudentsResource;
use App\Traits\PaginationTrait;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use App\Models\Student;
use Validator;
use Auth;

class StudentController extends Controller {

    use ResponseTrait , PaginationTrait;

    /**  public function get All Students . */
    public function getStudents()
    {
        $students = Student::with(['school'])->paginate(10);
        $data     = StudentsResource::collection($students);

        $pagination = $this->paginationModel($students);

        return $this->successData(['pagination'=>$pagination,'students' => $data]);
    }

    /**  public function get All schools . */
    public function getSchools()
    {
        $schools = Student::select('id','name')->paginate(10);
        $data    = SchoolsResource::collection($schools);

        $pagination = $this->paginationModel($schools);

        return $this->successData(['pagination'=>$pagination,'schools' => $data]);
    }

    /**  public function store Student . */
    public function storeStudent(StoreStudentRequest $request)
    {
        Student::create($request->validated());

        return $this->successMsg('created successfully');
    }

    /**  public function update Student . */
    public function updateStudent(UpdateStudentRequest $request)
    {
        $student = Student::find($request->student_id);
        $student->update($request->validated());

        return $this->successMsg('updated successfully');
    }

    /**  public function delete Student . */
    public function deleteStudent(Request $request)
    {
        $student = Student::find($request->student_id);

        if (!isset($student)){
            return $this->failMsg("The selected school id is invalid.");
        }

        $student->delete();

        return $this->successMsg('deleted successfully');
    }

    /**  public function show Student . */
    public function showStudent($id)
    {
        $student = Student::find($id);

        if (!isset($student)){
            return $this->failMsg("The selected school id is invalid.");
        }

        $data = new StudentsResource($student);

        return $this->successData(['student' => $data]);
    }
}

