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
            'name' => 'documents2',
            'fields' => [
                ['name' => 'id', 'type' => 'string'],
                ['name' => 'title', 'type' => 'string'],
                ['name' => 'paragraphs', 'type' => 'string[]', 'optional' => true],
                ['name' => 'page_numbers', 'type' => 'int32[]'],
                ['name' => 'source_type', 'type' => 'string', 'facet' => true],
                ['name' => 'created_at', 'type' => 'int64'],
                ['name' => 'updated_at', 'type' => 'int64'],
            ],
            'default_sorting_field' => 'created_at',
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
