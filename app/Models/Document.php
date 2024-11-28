<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Document extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'title',
        'paragraphs',
        'page_numbers',
        'source_type'
    ];

    protected $casts = [
        'id' => 'string',
        'page_numbers' => 'array',
        'paragraphs' => 'array'
    ];

    public function toSearchableArray()
    {
        $searchable = [
            'id' => (string) $this->id,
            'title' => $this->title,
            'page_numbers' => $this->page_numbers,
            'created_at' => $this->created_at->timestamp,
        ];

        if (!empty($this->paragraphs)) {
            $searchable['paragraphs'] = $this->paragraphs;
        }

        return $searchable;
    }

    public function pageNumbers()
    {
        return $this->hasMany(PageNumber::class);
    }

    public function searchableAs()
    {
        return 'documents2';
    }
}
