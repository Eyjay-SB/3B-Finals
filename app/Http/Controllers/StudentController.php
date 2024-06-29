<?php
namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    
    public function index(Request $request)
    {
        $query = Student::query();

        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('firstname', 'like', '%' . $request->search . '%')
                  ->orWhere('lastname', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->has('year')) {
            $query->where('year', $request->year);
        }
        if ($request->has('course')) {
            $query->where('course', $request->course);
        }
        if ($request->has('section')) {
            $query->where('section', $request->section);
        }
        if ($request->has('sort')) {
            $sortParams = explode(',', $request->sort);
            foreach ($sortParams as $param) {
                $direction = 'asc';
                if (strpos($param, '-') === 0) {
                    $direction = 'desc';
                    $param = ltrim($param, '-');
                }
                $query->orderBy($param, $direction);
            }
        }

        $limit = $request->has('limit') ? $request->limit : 10;
        $offset = $request->has('offset') ? $request->offset : 0;

        $total = $query->count();
        $students = $query->limit($limit)->offset($offset)->get();

        return response()->json([
            'metadata' => [
                'count' => $students->count(),
                'total' => $total,
                'search' => $request->search,
                'limit' => $limit,
                'offset' => $offset,
                'fields' => $request->fields,
            ],
            'students' => $students
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'birthdate' => 'required|date',
            'sex' => 'required|in:MALE,FEMALE',
            'address' => 'required|string|max:255',
            'year' => 'required|integer',
            'course' => 'required|string|max:255',
            'section' => 'required|string|max:255',
        ]);

        $student = Student::create($validated);
        return response()->json($student, 201);
    }

    public function show($id)
    {
        $student = Student::findOrFail($id);
        return response()->json($student);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'firstname' => 'sometimes|required|string|max:255',
            'lastname' => 'sometimes|required|string|max:255',
            'birthdate' => 'sometimes|required|date',
            'sex' => 'sometimes|required|in:MALE,FEMALE',
            'address' => 'sometimes|required|string|max:255',
            'year' => 'sometimes|required|integer',
            'course' => 'sometimes|required|string|max:255',
            'section' => 'sometimes|required|string|max:255',
        ]);

        $student = Student::findOrFail($id);
        $student->update($validated);
        return response()->json($student);
    }

    public function getSubjects($id)
    {
        $student = Student::findOrFail($id);

        $subjects = $student->subjects; // Assuming you have a subjects relationship defined on the Student model

        return response()->json([
            'metadata' => [
                'count' => $subjects->count(),
                'search' => null,
                'limit' => 0,
                'offset' => 0,
                'fields' => []
            ],
            'subjects' => $subjects
        ]);
    }
    
}
