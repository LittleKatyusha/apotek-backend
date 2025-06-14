<?php
// database/seeders/DatabaseSeeder.php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Panggil ObatSeeder di sini
        $this->call([
            ObatSeeder::class,
        ]);
    }
}