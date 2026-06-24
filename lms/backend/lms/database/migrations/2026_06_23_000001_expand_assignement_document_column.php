<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('assignement') || ! Schema::hasColumn('assignement', 'document')) {
            return;
        }

        DB::statement('ALTER TABLE `assignement` MODIFY `document` TEXT NOT NULL');
    }

    public function down(): void
    {
        if (! Schema::hasTable('assignement') || ! Schema::hasColumn('assignement', 'document')) {
            return;
        }

        DB::statement('ALTER TABLE `assignement` MODIFY `document` VARCHAR(255) NOT NULL');
    }
};
