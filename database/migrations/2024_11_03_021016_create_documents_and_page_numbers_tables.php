<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentsAndPageNumbersTables extends Migration
{
    public function up()
    {
        // // Create documents table
        // Schema::create('documents', function (Blueprint $table) {
        //     $table->id(); // Auto-incrementing ID
        //     $table->string('title'); // Title of the document
        //     $table->timestamps(); // Created and updated timestamps
        // });

        // Create page_numbers table
        Schema::create('page_numbers', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade'); // Foreign key referencing documents table
            $table->string('page_number'); // Page number associated with the document
            $table->timestamps(); // Created and updated timestamps
        });
    }

    public function down()
    {
        // Drop page_numbers table first because it has a foreign key
        Schema::dropIfExists('page_numbers');
        Schema::dropIfExists('documents');
    }
}

