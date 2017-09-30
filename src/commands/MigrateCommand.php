<?php

namespace app\commands;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager as Capsule;

class MigrateCommand
{
    private $migrationsTable = 'migrations';
    private $migrationsDir = __DIR__ . '/../migrations';

    public function run() : bool
    {
        $this->createMigrationsTable();
        $all = $this->getAllMigrations();
        $applied = $this->getAppliedMigrations();
        $needed = $this->filterNewMigrations($all, $applied);

        foreach ($needed as $migration) {
            $m = require_once("{$this->migrationsDir}/{$migration}.php");
            $m();

            Capsule::table('migrations')->insert([
                'name' => $migration,
                'createdAt' => Carbon::now()
            ]);
        }

        return true;
    }

    protected function createMigrationsTable() : self
    {
        if (Capsule::schema()->hasTable($this->migrationsTable) === false) {
            Capsule::schema()
                ->create($this->migrationsTable, function (Blueprint $table) {
                    $table->increments('id');
                    $table->string('name')->unique();
                    $table->timestamp('createdAt')->nullable();
                });
        }

        return $this;
    }

    protected function getAppliedMigrations() : Collection
    {
        return Capsule::table('migrations')->get();
    }

    protected function getAllMigrations() : array
    {
        return array_values(array_map(
            function ($n) {
                return substr($n, 0, -4);
            },
            array_filter(
                scandir($this->migrationsDir),
                function ($n) { return !in_array($n, ['.', '..']); }
            )
        ));
    }

    protected function filterNewMigrations($migrations, $appliedMigrations) : array
    {
        return array_values(array_filter(
            array_values($migrations),
            function ($n) use ($appliedMigrations) {
                return $appliedMigrations->search(
                        function ($i) use ($n) { return $i->name === $n; }
                    ) === false;
            }
        ));
    }
}
