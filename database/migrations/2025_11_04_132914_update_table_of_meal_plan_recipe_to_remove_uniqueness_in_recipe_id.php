<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->dropForeignKeyIfExists('meal_plan_recipe', 'meal_plan_id');
        $this->dropForeignKeyIfExists('meal_plan_recipe', 'recipe_id');

        $this->dropPrimaryKeyIfExists('meal_plan_recipe');

        Schema::table('meal_plan_recipe', function (Blueprint $table) {
            $table->foreign('meal_plan_id')->references('id')->on('meal_plans')->onDelete('cascade');
            $table->foreign('recipe_id')->references('id')->on('recipes')->onDelete('cascade');

            $table->primary(['meal_plan_id', 'day_of_week', 'meal_type'], 'meal_plan_day_meal_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meal_plan_recipe', function (Blueprint $table) {
            //
        });
    }

    private function dropForeignKeyIfExists(string $table, string $column): void
    {
        $dbName = DB::getDatabaseName();
        $constraint = DB::table('information_schema.key_column_usage')
            ->where('table_schema', $dbName)
            ->where('table_name', $table)
            ->where('column_name', $column)
            ->whereNotNull('constraint_name')
            ->where('constraint_name', '!=', 'PRIMARY')
            ->value('constraint_name');

        if ($constraint) {
            DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$constraint}`");
        }
    }

    private function dropPrimaryKeyIfExists(string $table): void
    {
        $dbName = DB::getDatabaseName();
        $primaryExists = DB::table('information_schema.table_constraints')
            ->where('table_schema', $dbName)
            ->where('table_name', $table)
            ->where('constraint_type', 'PRIMARY KEY')
            ->exists();

        if ($primaryExists) {
            DB::statement("ALTER TABLE `{$table}` DROP PRIMARY KEY");
        }
    }
};
