<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('surat_kuasa', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nik_pemberi', 16);
            $table->date('tanggal_pengajuan');
            $table->string('kota_pengajuan');
            $table->string('nama_pemberi');
            $table->string('ttl_pemberi');
            $table->integer('usia_pemberi');
            $table->string('pekerjaan_pemberi');
            $table->text('alamat_pemberi');
            $table->string('nama_penerima');
            $table->string('nik_penerima', 16);
            $table->text('alasan');
            $table->string('foto_pemberi_ktp');
            $table->string('no_hp_pemberi', 20);
            $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('pending');
            $table->text('alasan_penolakan')->nullable();
            $table->string('pdf_file')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index('nik_pemberi');
        });
    }

    public function down()
    {
        Schema::dropIfExists('surat_kuasa');
    }
};
