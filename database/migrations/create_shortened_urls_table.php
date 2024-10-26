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
        Schema::create('shortify_urls', function (Blueprint $table) {
            $table->id();

            $table->string('code')->collation(ShortifyConfig::getDatabaseCodeFieldCollation())->unique();
            $table->mediumText('original_url');

            $table->integer('visit_count')->unsigned()->default(0);

            $table->boolean('expired')->default(false)->index();
            $table->timestamp('expires_at', 3)->nullable();

            $table->timestamps(3);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shortify_urls');
    }
};
