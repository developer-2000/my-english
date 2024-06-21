<?php

namespace App\Http\Controllers;

use App\Http\Requests\Language\GetTranslation;
use App\Http\Requests\Language\SetLearnLanguageUser;
use App\Http\Responses\ApiResponse;
use App\Models\Language;
use App\Services\Translations\ConcreteTranslationCache;


class LanguageController extends Controller
{
    /**
     * Выбрать языки изучения
     * @return ApiResponse
     */
    public function getLanguages(): ApiResponse
    {
        $languages = config('program.languages');
        return new ApiResponse($languages);
    }

    /**
     * Пользователь выбрал язык изучения
     * @param SetLearnLanguageUser $request
     * @return ApiResponse
     */
    public function setLearnLanguageUser(SetLearnLanguageUser $request): ApiResponse
    {
        $language = Language::where('language', $request->language)->first();
        $user = auth()->user();

        // Связываем пользователя с языком через промежуточную таблицу
        $user->languages()->sync([$language->id]);

        return new ApiResponse(["Language set successfully"]);
    }

    /**
     * Возвращает все переводы интерфейса юзера
     * @param GetTranslation $request
     * @return ApiResponse
     */
    public function getTranslations(GetTranslation $request, ConcreteTranslationCache $translationCache): ApiResponse
    {
        try {
            // Получить переводы по ключу языка
            $translations = $translationCache->getTranslations($request->lang);

            // Возвращаем переводы
            return new ApiResponse(compact("translations"));
        } catch (\Throwable $th) {
            // Обработка ошибок, если файлы переводов не найдены или возникают другие проблемы
            return new ApiResponse([], 'Translations not found', 404);
        }
    }
}
