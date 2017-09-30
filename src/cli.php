<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager as Capsule;

require_once(__DIR__ . '/bootstrap.php');

function runMigrations() {
    if (Capsule::schema()->hasTable('migrations') === false) {
        Capsule::schema()
            ->create('migrations', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name')->unique();
                $table->timestamp('createdAt')->nullable();
            });
    }

    $appliedMigrations = Capsule::table('migrations')->get();
    $migrations = array_map(
        function ($n) {
            return substr($n, 0, -4);
        },
        array_filter(
            scandir(__DIR__ . '/migrations'),
            function ($n) { return !in_array($n, ['.', '..']); }
        )
    );
    $migrations = array_values(array_filter(
        array_values($migrations),
        function ($n) use ($appliedMigrations) {
            return $appliedMigrations->search(
                function ($i) use ($n) { return $i->name === $n; }
            ) === false;
        }
    ));

    foreach ($migrations as $migration) {
        $m = require_once(__DIR__ . '/migrations/' . $migration . '.php');
        $m();
        Capsule::table('migrations')->insert([
            'name' => $migration,
            'createdAt' => \Carbon\Carbon::now()
        ]);
    }
}

$cmd = $argv[1] ?? null;

switch ($cmd) {
    case 'migrate':
        runMigrations();
        break;
}