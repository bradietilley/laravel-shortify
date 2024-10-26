<?php

use BradieTilley\Shortify\ShortifyConfig;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shortify_visits', function (Blueprint $table) {
            $table->id();
            $table->ulid()->unique();

            $table->foreignIdFor(ShortifyConfig::getShortUrlModel(), 'shortify_url_id')->nullable();
            $table->foreignIdFor(ShortifyConfig::getUserModel(), 'user_id')->nullable();

            $table->string('ip')->index()->nullable();
            $table->string('user_agent')->nullable();

            $table->timestamp('created_at', 3);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shortify_visits');
    }
};
