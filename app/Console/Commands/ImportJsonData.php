<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Typesense\Client as TypesenseClient;
use Illuminate\Support\Facades\File;

class ImportJsonData extends Command
{
    protected $signature = 'import:json';
    protected $description = 'Import JSON files into the database';
    
    // Add log file paths as class properties
    protected $errorLogFile = 'storage/logs/import_errors.txt';
    protected $skippedLogFile = 'storage/logs/import_skipped.txt';

    // Add method to log issues
    protected function logIssue($file, $reason, $data, $isError = true)
    {
        $logFile = $isError ? $this->errorLogFile : $this->skippedLogFile;
        $timestamp = now()->format('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] File: {$file}\nReason: {$reason}\nData: " . json_encode($data, JSON_PRETTY_PRINT) . "\n\n";
        
        File::append($logFile, $logMessage);
    }

    public function handle()
    {
        // Clear previous log files
        File::put($this->errorLogFile, "");
        File::put($this->skippedLogFile, "");

        $client = new TypesenseClient([
            'api_key' => env('TYPESENSE_API_KEY'),
            'nodes' => [
                [
                    'host' => env('TYPESENSE_HOST', 'localhost'),
                    'port' => env('TYPESENSE_PORT', '8108'),
                    'protocol' => env('TYPESENSE_PROTOCOL', 'http'),
                ]
            ],
            'connection_timeout_seconds' => 2
        ]);

        // GitHub details
        $repoOwner = 'lazemel';
        $repoName = 'aplr';
        $basePath = 'Coin_Search_Engine%202/Coin_Search_Engine%202/RCNA%20Text%20Source%20Files/Coin-Text';

        // First, get all folders in the base path
        $baseUrl = "https://api.github.com/repos/$repoOwner/$repoName/contents/$basePath";
        $response = Http::get($baseUrl);

        if ($response->failed()) {
            $this->error('Error fetching folder list from GitHub');
            return;
        }

        $folders = $response->json();

        foreach ($folders as $folder) {
            // Skip if it's not a directory or if it's not what we want
            if ($folder['type'] !== 'dir') {
                continue;
            }

            $this->info("Processing folder: " . $folder['name']);

            // Get files in this folder
            $folderUrl = "https://api.github.com/repos/$repoOwner/$repoName/contents/" . $folder['path'];
            $filesResponse = Http::get($folderUrl);

            if ($filesResponse->failed()) {
                $this->error('Error fetching file list from folder: ' . $folder['name']);
                continue;
            }

            $files = $filesResponse->json();

            // Process each file in the folder
            foreach ($files as $file) {
                // Check if the file is a JSON file
                if (isset($file['name']) && pathinfo($file['name'], PATHINFO_EXTENSION) === 'json') {
                    $this->info("Processing file: " . $file['name']);
                    
                    $fileUrl = $file['download_url'];
                    
                    // Check if file fetch failed
                    if (($json = file_get_contents($fileUrl)) === false) {
                        $this->error('Error fetching JSON data from: ' . $fileUrl);
                        $this->logIssue(
                            $file['name'],
                            'Failed to fetch JSON data',
                            ['url' => $fileUrl],
                            true
                        );
                        continue;
                    }

                    // Check JSON decode
                    $data = json_decode($json, true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        $this->error('Error decoding JSON data: ' . json_last_error_msg());
                        $this->logIssue(
                            $file['name'],
                            'JSON decode error: ' . json_last_error_msg(),
                            ['raw_data' => substr($json, 0, 1000) . '...'], // First 1000 chars
                            true
                        );
                        continue;
                    }

                    // Insert the documents into the database
                    foreach ($data as $item) {
                        // Initialize variables
                        $title = '';
                        $pageNumbers = [];
                        $paragraphs = null;
                        $sourceType = '';

                        // Debug the raw item
                        $this->info("Processing raw item: " . json_encode($item));

                        // Find title (check all possible title fields)
                        $titleFields = ['title', 'Title', 'file', 'File'];
                        foreach ($titleFields as $field) {
                            if (!empty($item[$field])) {
                                $title = $item[$field];
                                $sourceType = $field . '_based';
                                break;
                            }
                        }

                        // Find page numbers (check all possible page number fields)
                        $pageFields = [
                            'page_numbers', 'pageNumbers', 'PageNumbers',
                            'page_no', 'pageNo', 'PageNo', 'pageno',
                            'page', 'Page', 'pages', 'Pages',
                            'PageNos', 'pageNos', 'page_nos'
                        ];
                        foreach ($pageFields as $field) {
                            if (isset($item[$field])) {
                                $pageNumbers = is_array($item[$field]) ? $item[$field] : [$item[$field]];
                                break;
                            }
                        }

                        // Find paragraphs (check all possible paragraph fields)
                        $paragraphFields = ['paragraphs', 'Paragraphs', 'paragraph', 'Paragraph', 'text', 'Text', 'content', 'Content'];
                        foreach ($paragraphFields as $field) {
                            if (isset($item[$field])) {
                                $paragraphs = $item[$field];
                                break;
                            }
                        }

                        // Debug what we found
                        $this->info("Found data:");
                        $this->info("- Title: " . $title);
                        $this->info("- Page Numbers: " . json_encode($pageNumbers));
                        $this->info("- Has Paragraphs: " . ($paragraphs ? 'Yes' : 'No'));
                        $this->info("- Source Type: " . $sourceType);

                        // Validate what we found
                        if (empty($title)) {
                            $this->warn("⚠️ Skipping item - no title found in any field");
                            $this->logIssue(
                                $file['name'],
                                'Missing title',
                                $item,
                                false
                            );
                            continue;
                        }

                        // Clean up page numbers
                        if (!empty($pageNumbers)) {
                            // Convert all page numbers to integers and remove any invalid ones
                            $pageNumbers = array_map(function($page) {
                                return filter_var($page, FILTER_SANITIZE_NUMBER_INT);
                            }, $pageNumbers);
                            $pageNumbers = array_filter($pageNumbers); // Remove empty/zero values
                            $pageNumbers = array_unique($pageNumbers);
                            sort($pageNumbers);
                        }

                        // Even if no page numbers found, we'll still process the document
                        if (empty($pageNumbers)) {
                            $this->warn("⚠️ No page numbers found for title: " . $title);
                            $this->logIssue(
                                $file['name'],
                                'Missing page numbers for title: ' . $title,
                                $item,
                                false
                            );
                        }

                        // Prepare document for database
                        $documentData = [
                            'title' => $title,
                            'paragraphs' => $paragraphs ? json_encode($paragraphs) : null,
                            'page_numbers' => json_encode($pageNumbers),
                            'source_type' => $sourceType,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];

                        // Insert into database
                        try {
                            $documentId = DB::table('documents')->insertGetId($documentData);
                            
                            // Prepare for Typesense
                            $typesenseDocument = [
                                'id' => (string)$documentId,
                                'title' => $title,
                                'paragraphs' => $paragraphs ?? [],
                                'page_numbers' => array_map('intval', $pageNumbers),
                                'source_type' => $sourceType,
                                'created_at' => now()->timestamp,
                                'updated_at' => now()->timestamp,
                            ];
                            
                            // Insert into Typesense
                            $client->collections['documents2']->documents->create($typesenseDocument);
                            
                            // Insert page numbers
                            foreach ($pageNumbers as $pageNumber) {
                                DB::table('page_numbers')->insert([
                                    'document_id' => $documentId,
                                    'page_number' => $pageNumber,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                            }

                            $this->info("✅ Successfully imported: $title");
                            $this->info("   - Document ID: $documentId");
                            $this->info("   - Pages: " . count($pageNumbers));
                            
                        } catch (\Exception $e) {
                            $this->error("❌ Error importing document: " . $e->getMessage());
                            $this->logIssue(
                                $file['name'],
                                'Database/Typesense insertion error: ' . $e->getMessage(),
                                $item,
                                true
                            );
                            continue;
                        }
                    }

                    $this->info('Imported data from: ' . $file['name']);
                }
            }

            $this->info("Completed processing folder: " . $folder['name']);
        }

        $this->info('All folders processed successfully!');
    }
}
