<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\SpecializedEducationalSupport\SemesterSeeder;
use Database\Seeders\SpecializedEducationalSupport\StudentContextSeeder;
use Database\Seeders\SpecializedEducationalSupport\StudentDeficiencySeeder;
use Database\Seeders\SpecializedEducationalSupport\DeficiencySeeder;
use Database\Seeders\SpecializedEducationalSupport\PositionSeeder;
use Database\Seeders\SpecializedEducationalSupport\PSPUSeeder;
use Database\Seeders\SpecializedEducationalSupport\CoursesSeeder;
use Database\Seeders\SpecializedEducationalSupport\DisciplinesSeeder;
use Database\Seeders\SpecializedEducationalSupport\CourseDisciplineSeeder;
use Database\Seeders\SpecializedEducationalSupport\StudentCourseSeeder;
use Database\Seeders\InclusiveRadar\BarrierCategorySeeder;
use Database\Seeders\InclusiveRadar\ResourceTypeSeeder;
use Database\Seeders\InclusiveRadar\TypeAttributeSeeder;
use Database\Seeders\InclusiveRadar\TypeAttributeAssignmentSeeder;
use Database\Seeders\InclusiveRadar\AccessibilityFeatureSeeder;
use Database\Seeders\InclusiveRadar\AssistiveTechnologySeeder;
use Database\Seeders\InclusiveRadar\AccessibleEducationalMaterialSeeder;
use Database\Seeders\InclusiveRadar\ResourceStatusSeeder;


class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SemesterSeeder::class,
            DeficiencySeeder::class,
            PositionSeeder::class,
            BarrierCategorySeeder::class,
            ResourceTypeSeeder::class,
            TypeAttributeSeeder::class,
            TypeAttributeAssignmentSeeder::class,
            AccessibilityFeatureSeeder::class,
            ResourceStatusSeeder::class,
            AssistiveTechnologySeeder::class,
            AccessibleEducationalMaterialSeeder::class,
            PSPUSeeder::class,
            StudentContextSeeder::class,
            StudentDeficiencySeeder::class,
            CoursesSeeder::class,
            DisciplinesSeeder::class,
            CourseDisciplineSeeder::class,
            StudentCourseSeeder::class,
        ]);
    }
}
