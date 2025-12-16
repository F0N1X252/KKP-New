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
        Schema::table('tickets', function (Blueprint $table) {
            // Check if attachment column doesn't exist and add it
            if (!Schema::hasColumn('tickets', 'attachment')) {
                $table->text('attachment')->nullable()->after('author_email');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (Schema::hasColumn('tickets', 'attachment')) {
                $table->dropColumn('attachment');
            }
        });
    }
};