<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(Request $request, $studentId)
    {
        $student = Student::findOrFail($studentId);
        $query = $student->subjects();


        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('subject_code', 'like', '%' . $request->search . '%')
                  ->orWhere('name', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->has('remarks')) {
            $query->where('remarks', $request->remarks);
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
        $subjects = $query->limit($limit)->offset($offset)->get();

        return response()->json([
            'metadata' => [
                'count' => $subjects->count(),
                'total' => $total,
                'search' => $request->search,
                'limit' => $limit,
                'offset' => $offset,
                'fields' => $request->fields,
            ],
            'subjects' => $subjects
        ]);
    }

    public function store(Request $request, $studentId)
    {
        $student = Student::findOrFail($studentId);

        $validated = $request->validate([
            'subject_code' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'instructor' => 'required|string|max:255',
            'schedule' => 'required|string|max:255',
            'grades' => 'required|array',
            'grades.prelims' => 'required|numeric',
            'grades.midterms' => 'required|numeric',
            'grades.pre_finals' => 'required|numeric',
            'grades.finals' => 'required|numeric',
            'date_taken' => 'required|date',
        ]);

        $grades = $validated['grades'];
        $averageGrade = Subject::calculateAverageGrade($grades);
        $remarks = Subject::determineRemarks($averageGrade);

        $subject = $student->subjects()->create([
            'subject_code' => $validated['subject_code'],
            'name' => $validated['name'],
            'description' => $validated['description'],
            'instructor' => $validated['instructor'],
            'schedule' => $validated['schedule'],
            'grades' => $grades,
            'average_grade' => $averageGrade,
            'remarks' => $remarks,
            'date_taken' => $validated['date_taken'],
        ]);

        return response()->json($subject, 201);
    }

    public function show($studentId, $subjectId)
    {
        $subject = Subject::where('student_id', $studentId)->findOrFail($subjectId);
        return response()->json($subject);
    }

    public function update(Request $request, $studentId, $subjectId)
    {
        $subject = Subject::where('student_id', $studentId)->findOrFail($subjectId);

        $validated = $request->validate([
            'subject_code' => 'sometimes|required|string|max:255',
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'instructor' => 'sometimes|required|string|max:255',
            'schedule' => 'sometimes|required|string|max:255',
            'grades' => 'sometimes|required|array',
            'grades.prelims' => 'sometimes|required|numeric',
            'grades.midterms' => 'sometimes|required|numeric',
            'grades.pre_finals' => 'sometimes|required|numeric',
            'grades.finals' => 'sometimes|required|numeric',
            'date_taken' => 'sometimes|required|date',
        ]);

        if (isset($validated['grades'])) {
            $grades = $validated['grades'];
            $averageGrade = Subject::calculateAverageGrade($grades);
            $remarks = Subject::determineRemarks($averageGrade);
            $validated['average_grade'] = $averageGrade;
            $validated['remarks'] = $remarks;
        }

        $subject->update($validated);
        return response()->json($subject);
    }
}
