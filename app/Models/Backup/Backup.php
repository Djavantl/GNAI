<?php

namespace App\Models\Backup;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Backup extends Model {
    protected $fillable = ['file_name', 'file_path', 'size', 'status', 'user_id'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    /**
     * Escopo para filtrar por nome de exibição
     */
    public function scopeFilterName($query, $name)
    {
        if ($name) {
            return $query->where('file_name', 'like', "%{$name}%");
        }
    }

    /**
     * Escopo para filtrar por status
     */
    public function scopeByType($query, $status)
    {
        if ($status) {
            return $query->where('status', $status);
        }
    }

    /**
     * Escopo para filtrar por usuário (Responsável)
     */
    public function scopeByUser($query, $userId)
    {
        if ($userId) {
            return $query->where('user_id', $userId);
        }
    }
}
