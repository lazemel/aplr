<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageNumber extends Model
{
    protected $fillable = ['page_number'];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }
} 