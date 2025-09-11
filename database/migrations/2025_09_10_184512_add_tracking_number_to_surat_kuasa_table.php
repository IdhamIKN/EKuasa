<?php

// 1. MIGRATION - Tambah kolom tracking_number
// File: database/migrations/YYYY_MM_DD_add_tracking_number_to_surat_kuasa_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('surat_kuasa', function (Blueprint $table) {
            $table->string('tracking_number', 15)->unique()->after('id');
            $table->index('tracking_number');
        });
    }

    public function down()
    {
        Schema::table('surat_kuasa', function (Blueprint $table) {
            $table->dropIndex(['tracking_number']);
            $table->dropColumn('tracking_number');
        });
    }
};
