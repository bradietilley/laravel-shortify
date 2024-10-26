<?php

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
            $table->ulid()->unique();

            $table->string('code')->index();
            $table->mediumText('original_url');

            $table->integer('visit_count')->unsigned()->default(0);

            $table->boolean('expired')->default(false)->index();
            $table->timestamp('expires_at', 3)->nullable();

            $table->softDeletes('deleted_at', 3);
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
