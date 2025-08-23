<?php

namespace App\Http\Controllers;

use App\Http\Requests\Language\GetTranslation;
use App\Http\Requests\Language\SetLearnLanguageUser;
use App\Http\Responses\ApiResponse;
use App\Models\Language;
use App\Models\LanguageUser;
use App\Services\Translations\ConcreteTranslationCache;
use Illuminate\Support\Facades\DB;

class LanguageController extends Controller
{
    /**
     * Выбрать языки изучения
     */
    public function getLanguages(): ApiResponse
    {
        $languages = config('program.languages');

        return new ApiResponse($languages);
    }

    /**
     * Пользователь выбрал язык изучения
     */
    public function setLearnLanguageUser(SetLearnLanguageUser $request): ApiResponse
    {
        $user = auth()->user();
        $language = Language::where('language', $request->language)->first();

        if ($language) {
            DB::transaction(function () use ($user, $language) {
                LanguageUser::where('user_id', $user->id)
                    ->update(['learn_id' => $language->id]);
            });
        }

        // Обновляем объект $user после транзакции
        $user->refresh();

        return new ApiResponse(compact('user'));
    }

    /**
     * Возвращает все переводы интерфейса юзера
     */
    public function getTranslations(GetTranslation $request, ConcreteTranslationCache $translationCache): ApiResponse
    {
        try {
            // Получить переводы по ключу языка
            $translations = $translationCache->getTranslations($request->lang);

            // Возвращаем переводы
            return new ApiResponse(compact('translations'));
        } catch (\Throwable $th) {
            // Обработка ошибок, если файлы переводов не найдены или возникают другие проблемы
            return new ApiResponse([], 'Translations not found', 404);
        }
    }
}
