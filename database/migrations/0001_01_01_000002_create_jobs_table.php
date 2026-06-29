<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    // Tabel negara yang dipantau
    Schema::create('countries', function (Blueprint $table) {
        $table->id();
        $table->string('code', 3)->unique();
        $table->string('name');
        $table->string('region')->nullable();
        $table->timestamps();
    });

    // Tabel skor risiko
    Schema::create('risk_scores', function (Blueprint $table) {
        $table->id();
        $table->string('country_code', 3);
        $table->float('weather_risk')->default(0);
        $table->float('inflation_risk')->default(0);
        $table->float('currency_risk')->default(0);
        $table->float('news_risk')->default(0);
        $table->float('total_risk')->default(0);
        $table->string('risk_level')->default('low');
        $table->timestamps();
    });

    // Tabel pelabuhan
    Schema::create('ports', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('country');
        $table->string('region');
        $table->float('latitude');
        $table->float('longitude');
        $table->float('volume')->default(0);
        $table->string('status')->default('active');
        $table->timestamps();
    });

    // Tabel watchlist (negara favorit)
    Schema::create('watchlists', function (Blueprint $table) {
        $table->id();
        $table->string('session_id');
        $table->string('country_code', 3);
        $table->string('country_name');
        $table->timestamps();
    });

    // Tabel cache berita
    Schema::create('news_cache', function (Blueprint $table) {
        $table->id();
        $table->string('category');
        $table->string('title');
        $table->text('description')->nullable();
        $table->string('url');
        $table->string('source')->nullable();
        $table->string('image')->nullable();
        $table->string('sentiment')->default('neutral');
        $table->integer('sentiment_score')->default(0);
        $table->timestamp('published_at')->nullable();
        $table->timestamps();
    });

    // Tabel artikel admin
    Schema::create('articles', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->text('content');
        $table->string('category');
        $table->string('author')->default('Admin');
        $table->timestamps();
    });
}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('failed_jobs');
    }
};
