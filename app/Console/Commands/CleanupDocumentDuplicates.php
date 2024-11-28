<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Typesense\Client;
use Typesense\Exceptions\ObjectUnprocessable;
use Illuminate\Support\Facades\File;

/**
 * Class CleanupDocumentDuplicates
 * 
 * This command identifies and removes duplicate documents from the Typesense collection.
 * A document is considered a duplicate if it has:
 * 1. The same normalized title and file name (case-insensitive, special characters removed)
 * 2. Exactly matching paragraphs (order-independent, case-insensitive)
 *
 * @package App\Console\Commands
 */
class CleanupDocumentDuplicates extends Command
{
    /**
     * The console command name and signature.
     * @var string
     */
    protected $signature = 'cleanup:documents-duplicates';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Cleanup duplicate documents in the Typesense collection based on title, file, and exact paragraph matches';

    /**
     * Path to the log file where duplicate removals are recorded
     * @var string
     */
    protected $logFile = 'storage/logs/duplicate_cleanup.txt';

    /**
     * Log information about a duplicate document that was removed
     *
     * @param array $original The original document that was kept
     * @param array $duplicate The duplicate document that was removed
     * @param string $reason The reason for considering it a duplicate
     * @return void
     */
    protected function logDuplicate($original, $duplicate, $reason)
    {
        $message = sprintf(
            "[%s]\nOriginal ID: %s\nDuplicate ID: %s\nReason: %s\n" .
            "Title: %s\nFile: %s\n\n",
            now()->format('Y-m-d H:i:s'),
            $original['id'],
            $duplicate['id'],
            $reason,
            $duplicate['title'],
            $duplicate['file'] ?? 'N/A'
        );
        
        File::append($this->logFile, $message);
    }

    /**
     * Execute the console command.
     *
     * @return void
     * @throws \Exception If there's an error connecting to Typesense
     */
    public function handle()
    {
        // Clear log file at the start of each run
        File::put($this->logFile, "");

        // Initialize Typesense client
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

        $collectionName = 'documents2';
        $page = 1;
        $perPage = 250; // Maximum documents per page
        $processedDocs = []; // Track processed documents
        $duplicatesFound = 0;

        $this->info("Starting duplicate cleanup process...");
        $this->info("Checking collection: {$collectionName}");

        // Process documents page by page
        while (true) {
            $this->info("Processing page {$page}...");
            
            // Search for all documents
            $searchResults = $client->collections[$collectionName]->documents->search([
                'q' => '*',         // Match all documents
                'query_by' => 'title', // Search by title field
                'page' => $page,
                'per_page' => $perPage,
            ]);

            // Break if no more documents
            if (empty($searchResults['hits'])) {
                break;
            }

            // Process each document in the current page
            foreach ($searchResults['hits'] as $hit) {
                $doc = $hit['document'];
                $normalizedKey = $this->getNormalizedKey($doc);
                
                // Check if we've seen this document before
                if (isset($processedDocs[$normalizedKey])) {
                    $original = $processedDocs[$normalizedKey];
                    
                    // Check for exact paragraph match
                    if ($this->hasExactParagraphMatch($doc, $original)) {
                        try {
                            // Delete the duplicate document
                            $client->collections[$collectionName]->documents[$doc['id']]->delete();
                            $this->logDuplicate($original, $doc, 
                                "Exact match (title/file and paragraphs)");
                            $duplicatesFound++;
                            $this->info("Deleted duplicate document ID: {$doc['id']} (Exact match)");
                        } catch (ObjectUnprocessable $e) {
                            $this->error("Error deleting document: {$doc['id']}");
                            $this->error("Error message: " . $e->getMessage());
                        }
                    }
                } else {
                    // Store this document as processed
                    $processedDocs[$normalizedKey] = $doc;
                }
            }

            $page++;
        }

        $this->info("Cleanup process completed!");
        $this->info("Total duplicates removed: {$duplicatesFound}");
        $this->info("Check {$this->logFile} for detailed removal log");
    }

    /**
     * Generate a normalized key for document comparison
     * Combines title and file name, removes special characters and spaces
     *
     * @param array $doc The document to generate a key for
     * @return string The normalized key
     */
    protected function getNormalizedKey($doc)
    {
        $key = strtolower(trim($doc['title']));
        if (isset($doc['file'])) {
            $key .= '|' . strtolower(trim($doc['file']));
        }
        return preg_replace('/[^a-z0-9]+/', '', $key);
    }

    /**
     * Check if two documents have exactly matching paragraphs
     * Comparison is case-insensitive and order-independent
     *
     * @param array $doc1 First document to compare
     * @param array $doc2 Second document to compare
     * @return bool True if paragraphs match exactly
     */
    protected function hasExactParagraphMatch($doc1, $doc2)
    {
        // If either document doesn't have paragraphs, they're not exact matches
        if (!isset($doc1['paragraphs']) || !isset($doc2['paragraphs'])) {
            return false;
        }

        // Normalize and compare paragraphs
        $paragraphs1 = $this->normalizeParagraphs($doc1['paragraphs']);
        $paragraphs2 = $this->normalizeParagraphs($doc2['paragraphs']);

        // Compare the normalized paragraphs
        return $paragraphs1 === $paragraphs2;
    }

    /**
     * Normalize paragraphs for comparison
     * - Converts to array if string
     * - Normalizes whitespace
     * - Makes case-insensitive
     * - Sorts paragraphs for order-independent comparison
     *
     * @param array|string $paragraphs Paragraphs to normalize
     * @return array Normalized paragraphs
     */
    protected function normalizeParagraphs($paragraphs)
    {
        // Handle both array and string cases
        if (is_string($paragraphs)) {
            $paragraphs = [$paragraphs];
        }

        // Ensure we're working with an array
        if (!is_array($paragraphs)) {
            return [];
        }

        // Normalize each paragraph
        $normalized = array_map(function($p) {
            return strtolower(trim(preg_replace('/\s+/', ' ', $p)));
        }, $paragraphs);

        // Sort paragraphs to ensure consistent comparison
        sort($normalized);

        return $normalized;
    }
}
