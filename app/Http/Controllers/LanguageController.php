<?php

namespace App\Http\Controllers;

use App\Http\Requests\Language\SetLearnLanguageUser;
use App\Http\Responses\ApiResponse;
use App\Models\Language;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function getLanguages()
    {
        $languages = config('program.languages');
        return new ApiResponse($languages);
    }

    public function setLearnLanguageUser(SetLearnLanguageUser $request)
    {
        $language = Language::where('language', $request->language)->first();
        $user = auth()->user();

        // Связываем пользователя с языком через промежуточную таблицу
        $user->languages()->sync([$language->id]);

        return new ApiResponse(["Language set successfully"]);
    }
}
