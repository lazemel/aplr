<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Document extends Model
{
    use HasFactory, Searchable;

    protected $casts = [
        'id' => 'string', // Cast the id to a string
    ];

    // Define the fields you want to be searchable
    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'created_at' => $this->created_at->timestamp, // Convert to timestamp for Typesense
        ];
    }
}
