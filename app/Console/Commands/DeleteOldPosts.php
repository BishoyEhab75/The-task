<?php

namespace App\Console\Commands;

use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteOldPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-old-posts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Force delete posts that are soft-deleted 30 days ago';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = Carbon::now()->subDays(30);

        // Query for posts that are soft-deleted and older than 30 days
        $posts = Post::onlyTrashed()->where('deleted_at', '<', $date)->get();

        // Force delete each post
        if ($posts->isEmpty()) {
            $this->info('No posts to delete.');
            return;
        }

        // Delete the posts
        $deletedCount = $posts->each->forceDelete();

        // Output the result
        $this->info("Successfully deleted {$deletedCount->count()} posts.");
    }
}
