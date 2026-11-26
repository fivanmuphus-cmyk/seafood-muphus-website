<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->string('subcategory', 40)->nullable()->index()->after('category');
            $table->unsignedInteger('price_max')->nullable()->after('price');
            $table->string('price_note', 30)->nullable()->after('price_max');
            $table->json('variants')->nullable()->after('description');
            $table->boolean('show_as_card')->default(true)->after('show_on_home');
            $table->boolean('show_in_list')->default(false)->after('show_as_card');
        });
    }

    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropColumn(['subcategory', 'price_max', 'price_note', 'variants', 'show_as_card', 'show_in_list']);
        });
    }
};