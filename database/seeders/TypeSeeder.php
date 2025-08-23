<?php

namespace Database\Seeders;

use App\Models\EnWordType;
use Illuminate\Database\Seeder;

class TypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $object = new \stdClass;
        $object->text = 'отвечает на вопросы что делать? что сделать?';
        $object->object = null;
        $arrData = [];

        // 1
        $arrData[] = [
            'type' => 'глагол',
            'color' => '#007bff',
            'description' => json_encode($object),
        ];

        // 2
        $object->text = 'действие, состояние, свойство, качество';
        $arrData[] = [
            'type' => 'подлежащее',
            'color' => '#28a745',
            'description' => json_encode($object),
        ];

        // 3
        $object->text = 'Имеют единственное и множественное число с приставкой “s” в конце. <br/>
Они используются с (the) или (a, an) артиклями - "a book" или "the car". <br/>
"the" когда речь идет о объекте, который известен или упоминается ранее - "She bought the book". <br/>
Артикль не ставится перед: <br/>
Множественным числом и после числительного "have boys and one girl" <br/>
После притяжательных местоимений "my, your, our, their, his, her" <br/>
"a" ставится перед согласной буквой "I need a new computer." <br/>
"an" ставится перед гласной "an interesting movie"
';
        $arrData[] = [
            'type' => 'существительное',
            'color' => '#cc9a03',
            'description' => json_encode($object),
        ];

        // 4
        $object->text = null;
        $object->object = [
            'past' => ['word' => null, 'translation' => null, 'accent' => null],
            'present' => ['word' => null, 'translation' => null, 'accent' => null],
            'future' => ['word' => null, 'translation' => null, 'accent' => null],
        ];
        $arrData[] = [
            'type' => 'формы времени',
            'color' => '#dc3545',
            'description' => json_encode($object),
        ];

        // 5
        $object->text = 'Целые и дробные цифры';
        $object->object = ['number' => null];
        $arrData[] = [
            'type' => 'числительные',
            'color' => 'rgb(118, 0, 151)',
            'description' => json_encode($object),
        ];

        // 6
        $object->text = null;
        $object->object = [
            'coordinating' => [
                'select' => false,
                'about' => 'Эти союзы соединяют слова, фразы или предложения, которые имеют равное значение',
                'name' => 'Сочинительные',
            ],
            'subordinating' => [
                'select' => false,
                'about' => 'Эти союзы соединяют главное предложение с придаточным, показывая зависимость одного от другого',
                'name' => 'Подчинительные',
            ],
            'correlative' => [
                'select' => false,
                'about' => 'Эти союзы работают парами для соединения слов, фраз или предложений',
                'name' => 'Коррелативные',
            ],
        ];
        $arrData[] = [
            'type' => 'союзы',
            'color' => 'rgb(0, 170, 188)',
            'description' => json_encode($object),
        ];

        EnWordType::insert($arrData);
    }
}
