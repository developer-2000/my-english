<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnWord extends Model
{
    use HasFactory;

    protected $table = 'en_words';

    protected $fillable = [
        'word',
        'translation',
        'url_image',
        'description',
        'type_id',
        'time_forms',
        'is_known',
        'unknown_order',
    ];

    protected $casts = [
        'time_forms' => 'json',
        'is_known' => 'integer',
        'unknown_order' => 'integer',
    ];

    public function type()
    {
        return $this->belongsTo(EnWordType::class, 'type_id', 'id')->withDefault(function ($type, $word) {
            $type->type = '';
            $type->color = 'black';
            $type->id = 0;
        });
    }

    public static function processWords(array $words): void
    {
        foreach ($words as $word) {
            // Удаляем пробелы и точки из слова
            $cleanedWord = str_replace([' ', '.', '?', '!', ',', ':'], '', $word);

            self::firstOrCreate(
                ['word' => $cleanedWord],
                ['translation' => '', 'description' => '""']
            );
        }
    }
}
