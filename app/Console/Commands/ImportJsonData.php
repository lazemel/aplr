<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ImportJsonData extends Command
{
    protected $signature = 'import:json';
    protected $description = 'Import JSON files into the database';

    public function handle()
    {
        // GitHub details
        $repoOwner = 'lazemel';  // GitHub owner
        $repoName = 'aplr';  // GitHub repo name
        $folderPath = 'Coin_Search_Engine%202/Coin_Search_Engine%202/RCNA%20Text%20Source%20Files/Coin-Text/Index2014';  // Folder path in the repo

        // Construct GitHub API URL to list files in the folder
        $url = "https://api.github.com/repos/$repoOwner/$repoName/contents/$folderPath";

        // Fetch the file list from GitHub API
        $response = Http::get($url);

        // Check if the request was successful
        if ($response->failed()) {
            $this->error('Error fetching file list from GitHub');
            return;
        }

        // Get the list of files
        $files = $response->json();

        // Iterate through each file in the folder
        foreach ($files as $file) {
            // Check if the file is a JSON file
            if (isset($file['name']) && pathinfo($file['name'], PATHINFO_EXTENSION) === 'json') {
                $fileUrl = $file['download_url']; // Get the file's download URL

                // Fetch the JSON content of the file
                $json = file_get_contents($fileUrl);

                // Check if the file data was retrieved successfully
                if ($json === false) {
                    $this->error('Error fetching JSON data from: ' . $fileUrl);
                    continue;
                }

                // Decode the JSON data
                $data = json_decode($json, true);

                // Check if JSON was decoded successfully
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $this->error('Error decoding JSON data: ' . json_last_error_msg());
                    continue;
                }

                // Insert the documents into the database
                foreach ($data as $item) {
                    // Insert document into the documents table
                    $documentId = DB::table('documents')->insertGetId([
                        'title' => $item['title'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Insert page numbers (if any)
                    if (isset($item['page_numbers'])) {
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

                $this->info('Imported data from: ' . $file['name']);
            }
        }

        $this->info('All JSON data imported successfully!');
    }
}
