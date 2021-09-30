<?php
namespace Database\Seeders;

use App\Models\WordType;
use Illuminate\Database\Seeder;

class TypeSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        WordType::create([
            'type'=>'',
            'color'=>'#000000',
        ]);
    }
}
