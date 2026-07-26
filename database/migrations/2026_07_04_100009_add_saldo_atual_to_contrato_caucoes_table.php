<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contrato_caucoes', function (Blueprint $table) {
            $table->decimal('saldo_atual', 12, 2)->default(0)->after('valor_caucao');
        });

        DB::statement('ALTER TABLE contrato_caucoes ALTER COLUMN status_caucao DROP NOT NULL');
        DB::statement('ALTER TABLE contrato_caucoes ALTER COLUMN status_caucao DROP DEFAULT');

        DB::table('contrato_caucoes')
            ->where('possui_caucao', true)
            ->update([
                'saldo_atual' => DB::raw('valor_caucao - COALESCE(valor_devolvido, 0)'),
            ]);
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE contrato_caucoes ALTER COLUMN status_caucao SET DEFAULT 'recebida'");
        DB::table('contrato_caucoes')->whereNull('status_caucao')->update(['status_caucao' => 'recebida']);
        DB::statement('ALTER TABLE contrato_caucoes ALTER COLUMN status_caucao SET NOT NULL');

        Schema::table('contrato_caucoes', function (Blueprint $table) {
            $table->dropColumn('saldo_atual');
        });
    }
};
