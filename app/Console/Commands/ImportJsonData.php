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
        // URL to your JSON file on GitHub
        $url = 'https://raw.githubusercontent.com/lazemel/aplr/refs/heads/main/Coin_Search_Engine%202/Coin_Search_Engine%202/RCNA%20Text%20Source%20Files/Coin-Text/Index2014/September_2014.json';

        // Fetch the JSON data from GitHub
        $json = file_get_contents($url);

        // Check if the data was retrieved successfully
        if ($json === false) {
            $this->error('Error fetching JSON data from GitHub');
            return;
        }

        // Decode the JSON data
        $data = json_decode($json, true);

        // Check if JSON was decoded successfully
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Error decoding JSON data: ' . json_last_error_msg());
            return;
        }

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

        $this->info('JSON data imported successfully!');
    }
}
