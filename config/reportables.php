<?php
// config/reportables.php (trecho relevante)

return [
    'tables' => [
        'students' => [
            'label' => 'Alunos',
            'columns' => ['id', 'person_id', 'registration', 'entry_date', 'status', 'created_at'],
            'options' => [
                'status' => ['active', 'locked', 'completed', 'dropped']
            ]
        ],
        'people' => [
            'label' => 'Dados Pessoais',
            'columns' => ['id', 'name', 'document', 'birth_date', 'gender', 'email', 'phone', 'address'],
        ],
        'students_deficiencies' => [
            'label' => 'Aluno x Deficiência (pivot)',
            'columns' => ['id', 'student_id', 'deficiency_id', 'severity', 'uses_support_resources', 'notes', 'created_at'],
            'pivot' => true,
        ],
        'deficiencies' => [
            'label' => 'Deficiências (catálogo)',
            'columns' => ['id', 'name', 'cid_code', 'description', 'is_active'],
        ],
    ],

    'relations' => [
        'students.people' => ['on' => ['students.person_id', 'people.id'], 'type' => 'inner'],
        'students.students_deficiencies' => ['on' => ['students.id', 'students_deficiencies.student_id'], 'type' => 'left'],
        'students_deficiencies.deficiencies' => ['on' => ['students_deficiencies.deficiency_id', 'deficiencies.id'], 'type' => 'inner'],
    ],

    'default_limit' => 200,
];