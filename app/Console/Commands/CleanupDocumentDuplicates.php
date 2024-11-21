<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Typesense\Client;
use Typesense\Exceptions\ObjectUnprocessable;

class CleanupDocumentDuplicates extends Command
{
    protected $signature = 'cleanup:documents-duplicates';
    protected $description = 'Cleanup duplicate documents in the Typesense collection';

    public function handle()
    {
        $client = new Client([
            'api_key' => env('TYPESENSE_API_KEY'),
            'nodes' => [
                [
                    'host' => env('TYPESENSE_HOST'),
                    'port' => '443',
                    'protocol' => 'https',
                ],
            ],
            'connection_timeout_seconds' => 2,
        ]);

        $collectionName = 'documents';
        $page = 1;
        $perPage = 250; // Max per page
        $duplicates = [];

        // Loop through pages
        while (true) {
            $searchResults = $client->collections[$collectionName]->documents->search([
                'q' => '*',
                'query_by' => 'title', // Searching based on title
                'page' => $page,
                'per_page' => $perPage,
            ]);

            if (empty($searchResults['hits'])) {
                break; // No more results
            }

            // Process the results and find duplicates
            foreach ($searchResults['hits'] as $hit) {
                $title = $hit['document']['title'];

                if (isset($duplicates[$title])) {
                    // This title already exists, so we need to delete the duplicate document
                    try {
                        $client->collections[$collectionName]->documents[$hit['document']['id']]->delete();
                        $this->info("Deleted duplicate document with ID: " . $hit['document']['id']);
                    } catch (ObjectUnprocessable $e) {
                        $this->error("Error deleting document: " . $hit['document']['id']);
                    }
                } else {
                    // Mark this title as seen
                    $duplicates[$title] = $hit['document']['id'];
                }
            }

            // Move to the next page
            $page++;
        }

        $this->info("Finished cleaning up duplicates.");
    }
}
