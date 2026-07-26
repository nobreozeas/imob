<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const SLUGS_TRANSITO = ['aluguel', 'caucao', 'devolucao_caucao', 'repasse_proprietario'];

    public function up(): void
    {
        DB::table('categorias_financeiras')
            ->whereIn('slug', self::SLUGS_TRANSITO)
            ->update(['impacta_resultado' => false]);
    }

    public function down(): void
    {
        DB::table('categorias_financeiras')
            ->whereIn('slug', self::SLUGS_TRANSITO)
            ->update(['impacta_resultado' => true]);
    }
};
