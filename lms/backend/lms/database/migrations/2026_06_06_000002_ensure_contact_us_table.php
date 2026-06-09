<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('contact_us')) {
            Schema::create('contact_us', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email');
                $table->text('message');
                $table->boolean('is_read')->default(false);
                $table->timestamps();
            });

            return;
        }

        Schema::table('contact_us', function (Blueprint $table) {
            if (! Schema::hasColumn('contact_us', 'is_read')) {
                $table->boolean('is_read')->default(false)->after('message');
            }
            if (! Schema::hasColumn('contact_us', 'created_at')) {
                $table->timestamps();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('contact_us')) {
            return;
        }

        Schema::table('contact_us', function (Blueprint $table) {
            if (Schema::hasColumn('contact_us', 'is_read')) {
                $table->dropColumn('is_read');
            }
        });
    }
};
