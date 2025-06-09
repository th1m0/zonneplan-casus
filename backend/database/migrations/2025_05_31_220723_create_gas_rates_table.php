<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('gas_rates', function (Blueprint $table): void {
            $table->id();
            $table->string('currency', 3)->default('EUR');
            $table->datetime('period_start');
            $table->datetime('period_end');
            $table->date('rate_date');
            $table->string('period');
            $table->bigInteger('market_price'); // in micro-units
            $table->bigInteger('total_price_tax_included');
            $table->bigInteger('price_incl_handling_vat');
            $table->bigInteger('price_tax_with_vat');
            $table->json('metadata')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['period_start', 'period_end']);
            $table->index(['rate_date']);

            $table->unique(['period_start', 'period_end']); // there can only be one rate per period

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gas_rates');
    }
};
