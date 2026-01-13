<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Backup extends Model {
    protected $fillable = ['file_name', 'file_path', 'size', 'status', 'user_id'];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
