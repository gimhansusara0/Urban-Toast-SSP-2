<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str; // ⬅️ add this

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'status'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function scopeActive($q)
    {
        return $q->where('status', 'active');
    }

    //  Auto generate a unique slug if it's missing
    protected static function booted(): void
    {
        static::creating(function (Category $cat) {
            if (empty($cat->slug)) {
                $cat->slug = static::generateUniqueSlug($cat->name);
            }
        });

        static::updating(function (Category $cat) {
            // If name changed and slug left empty, regenerate
            if ($cat->isDirty('name') && empty($cat->slug)) {
                $cat->slug = static::generateUniqueSlug($cat->name, $cat->id);
            }
        });
    }

    protected static function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        $exists = fn($s) => static::where('slug', $s)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists();

        while ($exists($slug)) {
            $slug = $base.'-'.$i++;
        }
        return $slug;
    }
}
