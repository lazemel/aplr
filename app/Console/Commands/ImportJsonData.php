<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportJsonData extends Command
{
    protected $signature = 'import:json';
    protected $description = 'Import JSON files into the database';

    public function handle()
    {
        // Path to your JSON files
        $path = storage_path('/Users/ahmetaydogan/Desktop/untitled folder 4/cleaned_April_2014.json'); // Adjust this path as necessary

        // Loop through each JSON file in the directory
        foreach (glob($path . '/*.json') as $filename) {
            $json = file_get_contents($filename);
            $data = json_decode($json, true);

            // Insert each item into the documents table
            foreach ($data as $item) {
                // Insert document
                $documentId = DB::table('documents')->insertGetId([
                    'title' => $item['title'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Insert page numbers
                foreach ($item['page_numbers'] as $pageNumber) {
                    DB::table('page_numbers')->insert([
                        'document_id' => $documentId,
                        'page_number' => $pageNumber,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        $this->info('JSON data imported successfully!');
    }
}

