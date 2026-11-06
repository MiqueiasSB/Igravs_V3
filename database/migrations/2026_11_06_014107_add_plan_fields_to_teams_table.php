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
        Schema::table('teams', function (Blueprint $table) {
            $table->string('city')->nullable()->after('slug');
            $table->string('plan')->default('trial')->after('city');
            $table->timestamp('trial_ends_at')->nullable()->after('plan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn(['city', 'plan', 'trial_ends_at']);
        });
    }
};
