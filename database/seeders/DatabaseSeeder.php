<?php

namespace Database\Seeders;

use Database\Seeders\Inclusion\DemoLoanWaitlistSeeder;
use Database\Seeders\Inclusion\PublicationSeeder;
use Database\Seeders\Inclusion\InstitutionSeeder;
use Database\Seeders\Inclusion\LocationSeeder;
use Database\Seeders\Inclusion\BarrierSeeder;
use Illuminate\Database\Seeder;
use Database\Seeders\Inclusion\SemesterSeeder;
use Database\Seeders\Inclusion\StudentContextSeeder;
use Database\Seeders\Inclusion\StudentDeficiencySeeder;
use Database\Seeders\Inclusion\DeficiencySeeder;
use Database\Seeders\Inclusion\PositionSeeder;
use Database\Seeders\Inclusion\PSPUSeeder;
use Database\Seeders\Inclusion\StudentSeeder;
use Database\Seeders\Inclusion\CoursesSeeder;
use Database\Seeders\Inclusion\DisciplinesSeeder;
use Database\Seeders\Inclusion\CourseDisciplineSeeder;
use Database\Seeders\Inclusion\StudentCourseSeeder;
use Database\Seeders\Inclusion\AeeRecordSeeder;
use Database\Seeders\Inclusion\PedagogicalRecordSeeder;
use Database\Seeders\Inclusion\PendencySeeder;
use Database\Seeders\Inclusion\GuardianSeeder;
use Database\Seeders\Inclusion\DisciplineSeeder;
use Database\Seeders\Inclusion\PeiSeeder;
use Database\Seeders\Inclusion\ProfessionalSeeder;
use Database\Seeders\Inclusion\AttendanceSessionSeeder;
use Database\Seeders\Inclusion\TeacherSeeder;
use Database\Seeders\Inclusion\BarrierCategorySeeder;
use Database\Seeders\Inclusion\AccessibilityFeatureSeeder;
use Database\Seeders\Inclusion\AssistiveTechnologySeeder;
use Database\Seeders\Inclusion\AccessibleEducationalMaterialSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SemesterSeeder::class,
            DeficiencySeeder::class,
            PositionSeeder::class,
            BarrierCategorySeeder::class,
            AccessibilityFeatureSeeder::class,
            AccessibleEducationalMaterialSeeder::class,
            PSPUSeeder::class,
            // StudentSeeder::class,
            StudentContextSeeder::class,
            StudentDeficiencySeeder::class,
            CoursesSeeder::class,
            DisciplinesSeeder::class,
            CourseDisciplineSeeder::class,
            StudentCourseSeeder::class,
            // ProfessionalSeeder::class,
            PendencySeeder::class,
            GuardianSeeder::class,
            DisciplineSeeder::class,
            AttendanceSessionSeeder::class,
            TeacherSeeder::class,
            PeiSeeder::class,
            InstitutionSeeder::class,
            LocationSeeder::class,
            AssistiveTechnologySeeder::class,
            BarrierSeeder::class,
            PublicationSeeder::class,
            DemoLoanWaitlistSeeder::class,
            AeeRecordSeeder::class,
            PedagogicalRecordSeeder::class,
        ]);
    }
}
