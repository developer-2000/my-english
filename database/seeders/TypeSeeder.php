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

        $object->text = 'Имеют единственное и множественное число с приставкой “s” в конце. <br/>
Они используются с (the) или (a, an) артиклями - "a book" или "the car". <br/>
"the" когда речь идет о объекте, который известен или упоминается ранее - "She bought the book". <br/>
Артикль не ставится перед: <br/>
Множественным числом и после числительного "have boys and one girl" <br/>
После притяжательных местоимений "my, your, our, their, his, her" <br/>
"a" ставится перед согласной буквой "I need a new computer." <br/>
"an" ставится перед гласной "an interesting movie"
';
        WordType::create(
            ['type'=>'существительное','color'=>'#cc9a03','description'=>$object],
        );

        $object->text = null;
        $object->object = [
            "past"=>["word"=>null, "translation"=>null, "accent"=>null],
            "present"=>["word"=>null, "translation"=>null, "accent"=>null],
            "future"=>["word"=>null, "translation"=>null, "accent"=>null]
        ];

        WordType::create(
            ['type'=>'формы времени','color'=>'#dc3545','description'=>$object]
        );
    }
}
