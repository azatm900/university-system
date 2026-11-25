<?php

namespace App\Models;

use App\Enums\DegreeLevel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Program extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'name_en',
        'degree_level',
        'duration_years',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'degree_level'   => DegreeLevel::class,
            'duration_years' => 'integer',
            'is_active'      => 'boolean',
        ];
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}