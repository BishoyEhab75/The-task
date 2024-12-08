<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RequestUserData implements ShouldQueue
{
    use Dispatchable, Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $response = Http::get('https://randomuser.me/api/');
    
        // Log the entire response to check its structure
        // Log::info('Full Response: ' . $response->body());
    
        // Check if the response was successful
        if ($response->successful()) {
            // Get the 'results' object from the response
            $results = $response->json('results');
            
            // Log the 'results' object
            Log::info('Random User Results:', ['results' => json_encode($results, JSON_PRETTY_PRINT)]);
        } else {
            // Log an error message if the request fails
            Log::error('Request failed.');
        }
    }
}
