<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('posts_count')->default(0);
        });

        
        DB::table('users')->get()->each(function ($user) {
            $postsCount = DB::table('posts')->where('user_id', $user->id)->count();
            DB::table('users')->where('id', $user->id)->update(['posts_count' => $postsCount]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
