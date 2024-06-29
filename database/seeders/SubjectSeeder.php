<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $students = Student::all();

        foreach ($students as $student) {
            $grades = [
                'prelims' => 2.75,
                'midterms' => 2.0,
                'pre_finals' => 1.75,
                'finals' => 1.0,
            ];

            $averageGrade = Subject::calculateAverageGrade($grades);
            $remarks = Subject::determineRemarks($averageGrade);

            Subject::create([
                'student_id' => $student->id,
                'subject_code' => 'T3B-123',
                'name' => 'Application Lifecycle Management',
                'description' => 'A comprehensive study of the application lifecycle management.',
                'instructor' => 'Mr. Cy',
                'schedule' => 'MW 7AM-12PM',
                'grades' => $grades,
                'average_grade' => $averageGrade,
                'remarks' => $remarks,
                'date_taken' => '2024-01-01',
            ]);

            Subject::create([
                'student_id' => $student->id,
                'subject_code' => 'T3B-124',
                'name' => 'Software Engineering',
                'description' => 'An in-depth look at the principles of software engineering.',
                'instructor' => 'Ms. Jane Smith',
                'schedule' => 'TTh 1PM-5PM',
                'grades' => $grades,
                'average_grade' => $averageGrade,
                'remarks' => $remarks,
                'date_taken' => '2024-02-01',
            ]);
        }
    }
}
