<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('admin')->after('email')->index();
        });

        // Akun pertama (pembuat situs) menjadi superadmin agar tidak terkunci dari menu pengaturan.
        $first = DB::table('users')->min('id');

        if ($first !== null) {
            DB::table('users')->where('id', $first)->update(['role' => 'superadmin']);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
