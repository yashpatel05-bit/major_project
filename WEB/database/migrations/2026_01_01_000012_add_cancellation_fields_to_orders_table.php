<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('cancellation_fee', 10, 2)->default(0.00)->after('notes');
            $table->text('cancellation_reason')->nullable()->after('cancellation_fee');
            $table->string('cancellation_payment_id')->nullable()->after('cancellation_reason');
            $table->timestamp('cancelled_at')->nullable()->after('cancellation_payment_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'cancellation_fee',
                'cancellation_reason',
                'cancellation_payment_id',
                'cancelled_at'
            ]);
        });
    }
};
