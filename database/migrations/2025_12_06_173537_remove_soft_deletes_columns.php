<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Hapus kolom deleted_at dari tabel yang diinginkan
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('comments', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }

    public function down()
    {
        // Jika rollback, kembalikan kolomnya
        Schema::table('tickets', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('comments', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
        });
    }
};