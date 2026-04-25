<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Page extends Model
{
    protected $fillable = ['title', 'slug', 'content', 'meta_title', 'meta_description', 'status'];

    public static function generateSlug(string $title, ?int $ignoreId = null): string
    {
        // Keep Persian/Arabic words separated by hyphens, remove other special chars
        $slug = trim($title);
        // Replace spaces and common separators with hyphen
        $slug = preg_replace('/[\s\-_\/\\\\]+/', '-', $slug);
        // Remove characters that are not Persian/Arabic letters, digits, or hyphens
        $slug = preg_replace('/[^\p{Arabic}\p{N}\-]/u', '', $slug);
        // Remove leading/trailing hyphens
        $slug = trim($slug, '-');
        // Collapse multiple hyphens
        $slug = preg_replace('/-+/', '-', $slug);

        if (empty($slug)) {
            $slug = 'page-' . time();
        }

        $original = $slug;
        $i = 1;
        while (
            static::where('slug', $slug)
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $original . '-' . $i++;
        }
        return $slug;
    }
}
