<?php

namespace App\Http\Requests\Backup;

use Illuminate\Foundation\Http\FormRequest;

class BackupRequest extends FormRequest {
    public function authorize() {
        return true;
    }

    public function rules() {
        return [];
    }
}
