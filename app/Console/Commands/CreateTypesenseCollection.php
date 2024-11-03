<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Typesense\Client;

class CreateTypesenseCollection extends Command
{
    protected $signature = 'typesense:create-collection';
    protected $description = 'Create a Typesense collection for documents';

    public function handle()
    {
        // Initialize the Typesense client
        $client = new Client([
            'api_key' => config('scout.typesense.client-settings.api_key'),
            'nodes' => [
                [
                    'host' => 'bugw8ds14xa2o69vp-1.a1.typesense.net',
                    'port' => '443',
                    'protocol' => 'https',
                ],
            ],
            'connection_timeout_seconds' => 2,
        ]);

        // Define the collection schema
        $collectionSchema = [
            'name' => 'documents',
            'fields' => [
                ['name' => 'id', 'type' => 'string'],
                ['name' => 'title', 'type' => 'string'],
                ['name' => 'created_at', 'type' => 'int64'], // Include created_at for sorting
            ],
            'default_sorting_field' => 'created_at', // Change this to a sortable field
        ];

        try {
            // Attempt to create the collection
            $client->collections->create($collectionSchema);
            $this->info('Typesense collection "documents" created successfully.');
        } catch (\Exception $e) {
            // Catch any errors during collection creation
            $this->error('Error creating Typesense collection: ' . $e->getMessage());
        }
    }
}
