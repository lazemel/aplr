<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Document extends Model
{
    use HasFactory, Searchable;

    protected $casts = [
        'id' => 'string', // Ensure the `id` is cast to a string for Typesense compatibility
    ];

    // Define the fields you want to be searchable
    public function toSearchableArray()
    {
        return [
            'id' => (string) $this->id,
            'title' => $this->title,
            'created_at' => $this->created_at->timestamp, // Use timestamp format
        ];
    }
}
