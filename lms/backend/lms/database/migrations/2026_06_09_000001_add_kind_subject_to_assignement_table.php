<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('assignement')) {
            return;
        }

        Schema::table('assignement', function (Blueprint $table) {
            if (! Schema::hasColumn('assignement', 'content_kind')) {
                $table->string('content_kind', 20)->default('assignment');
            }
            if (! Schema::hasColumn('assignement', 'subject_id')) {
                $table->unsignedBigInteger('subject_id')->nullable();
            }
            if (! Schema::hasColumn('assignement', 'subject_name')) {
                $table->string('subject_name', 255)->nullable();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('assignement')) {
            return;
        }

        Schema::table('assignement', function (Blueprint $table) {
            foreach (['subject_name', 'subject_id', 'content_kind'] as $column) {
                if (Schema::hasColumn('assignement', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
