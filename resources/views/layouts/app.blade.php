<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('adminlte/plugins/fontawesome-free/css/all.min.css')}}">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="{{asset('adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css')}}">
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <style>
        #languagesList{
            display: flex;
            justify-content: space-between;
        }
        #languagesList .language-option{
            background: #f1f1f1;
            width: 50%;
            padding: 10px 10px;
            text-align: center;
            cursor: pointer;
            outline: 1px solid #dbdbdb;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        #languagesList .language-option:hover {
            background-color: #2000ff;
            color: white;
        }
    </style>
</head>
<body>
<div id="app">

    {{-- Header меню --}}
    <header class="d-flex justify-content-between align-items-center p-3">
        <a href="/" class="header-element header-main-link">English</a>
        <!-- Выпадающее меню справа -->
        <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle"
                    type="button"
                    id="dropdownMenuButton"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
            >
                {{ Auth::user()->name ?? 'Menu' }}
            </button>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#languageLearn">Язык изучения</a>
                <a class="dropdown-item" href="{{ route('auth.logout') }}">{{ __('Logout') }}</a>
            </div>
        </div>
    </header>

    {{-- Левое меню index страницы --}}
    <div class="main-page">
        <ul id="left_menu">
            <li>
                <router-link to="/page-list-words" class="left_menu" exact>
                    Список слов
                </router-link>
            </li>
            <li>
                <router-link to="/page-word-sentences" class="left_menu" exact>
                    Предложения слов
                </router-link>
            </li>
        </ul>
        @yield('content')
    </div>

    <!-- Модалка выбора языка изучения -->
    <div class="modal fade" id="languageLearn" tabindex="-1" aria-labelledby="languageLearnLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="languageLearnLabel">Изучить язык</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="languagesList"></div>
                </div>
            </div>
        </div>
    </div>

</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="{{asset('adminlte/plugins/jquery-ui/jquery-ui.min.js')}}"></script>
<script src="{{ mix('js/app.js') }}"></script>
<script>
    $(document).ready(function() {
        // открытие модалки выбора языка изучения
        $('#languageLearn').on('show.bs.modal', function (e) {
            $.ajax({
                type: 'POST',
                url: '{{ route("get.languages") }}',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function (data) {
                    let languagesList = $('#languagesList');
                    languagesList.empty();

                    if (data && data.data.learn_languages) {
                        // Перебираем языки и создаем для каждого кнопку
                        $.each(data.data.learn_languages, function(language, code) {
                            let languageOption = $('<div class="language-option"></div>');
                            languageOption.attr('data-code', code);
                            languageOption.text(language);
                            languagesList.append(languageOption);

                            // Язык изучения выбран
                            languageOption.on('click', function() {
                                let selectedLanguage = $(this).data('code');
                                $.ajax({
                                    type: 'POST',
                                    url: '{{ route("set.language.learn.user") }}',
                                    data: {
                                        _token: '{{ csrf_token() }}',
                                        language: selectedLanguage
                                    },
                                    success: function (response) {
                                        location.reload();
                                    },
                                    error: function (xhr, status, error) {
                                        console.error('Error saving language:', error);
                                    }
                                });
                            });
                        });
                    } else {
                        console.error('No learn_languages found in the response');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching languages:', error);
                }
            });
        });
    });
</script>
@yield('js')
</body>
</html>
