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
        $object = new \stdClass();
        $object->text = 'отвечает на вопросы что делать? что сделать?';
        $object->object = null;

        WordType::create(
            ['type'=>'глагол','color'=>'#007bff','description'=>$object]
        );

        $object->text = 'действие, состояние, свойство, качество';
        WordType::create(
            ['type'=>'подлежащее','color'=>'#28a745','description'=>$object],
        );

        $object->text = null;
        $object->object = [
            "past"=>["word"=>null, "translation"=>null, "accent"=>null],
            "present"=>["word"=>null, "translation"=>null, "accent"=>null],
            "future"=>["word"=>null, "translation"=>null, "accent"=>null]
        ];

        WordType::create(
            ['type'=>'формы времени','color'=>'#86751d','description'=>$object]
        );
    }
}
