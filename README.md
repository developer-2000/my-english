# My English - Система изучения английского языка

Веб-приложение для изучения английского языка на Laravel 8 + Vue.js 2 с ИИ-генерацией предложений.

## 🚀 Быстрый запуск

### Требования
- PHP 7.3+ или 8.0+
- MySQL 5.7+
- Node.js 14+
- Composer

### Установка
```bash
# 1. Клонирование и зависимости
git clone <repository>
cd my-english
composer install
npm install

# 2. Настройка окружения
cp .env.example .env
php artisan key:generate

# 3. Настройка .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=my_english
DB_USERNAME=root
DB_PASSWORD=

# Email (Mailtrap для разработки)
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
APP_URL=http://your-domain

# ИИ API (опционально, Google Gemini)
GOOGLE_AI_API_KEY=your_google_gemini_api_key

# 4. Миграции и сидеры
php artisan migrate --seed

# 5. Сборка фронтенда (Vite)
npm run dev

# 6. Запуск очередей (для email)
php artisan queue:work

# 7. Запуск сервера
php artisan serve
```

## 👤 Учетные данные по умолчанию
- **Email:** admin@admin.admin
- **Пароль:** admin

## 🎯 Основные функции

### Система изучения
- **Слова**: добавление, перевод, временные формы глаголов
- **Предложения**: создание, автоматический перевод, озвучка
- **ИИ генерация**: создание предложений через Google Gemini API
- **Прогресс**: отслеживание изучения слов и предложений

### Многоязычность
- **Интерфейс**: русский/английский
- **Изучение**: английский/румынский
- **Автоперевод**: Google Translate PHP

### Безопасность
- Аутентификация через Laravel Sanctum
- Система ролей (admin/user)
- Верификация email через очереди
- Автоматические бэкапы БД

## 🏗️ Технический стек

### Backend
- Laravel 8.54, MySQL, Laravel Sanctum
- Spatie Laravel Backup, Google Translate PHP
- AI21 Studio API для генерации предложений

### Frontend
- Vue.js 2.6.12, Vue Router, Vuex
- Bootstrap 5.3.3, Vue Good Table
- Vue Tippy, SweetAlert2, Web Speech API
- **Vite** (сборщик) - быстрая разработка и HMR

### Инструменты разработки
- **Prettier** - форматирование кода
- **ESLint** - линтинг JavaScript/Vue с правилами для Vue 2
- **eslint-config-prettier** - интеграция Prettier с ESLint
- **Laravel Pint** - автоматическое форматирование PHP кода по стандартам Laravel

## 📁 Ключевые файлы

### Backend
- `app/Http/Controllers/` - контроллеры (Auth, Word, Sentence, AI)
- `app/Models/` - модели (User, EnWord, EnSentence, Language)
- `app/Http/Middleware/` - middleware (роли, бэкапы)

### Frontend
- `resources/js/components/Hallway.vue` - главный layout
- `resources/js/services/http_client.js` - API клиент
- `resources/js/mixins/` - общая логика компонентов
- `vite.config.js` - конфигурация Vite


### Как получить API-ключ Google Gemini

[Перейдите на Google Cloud Console](https://aistudio.google.com/apikey)
Создайте новый проект или используйте существующий.
Включите API Generative Language (Google Gemini).
Создайте API Key в разделе Credentials.
Скопируйте ключ и добавьте его в .env:
В .env - GOOGLE_AI_API_KEY=ваш_ключ

## 🤖 ИИ-генерация предложений

### Архитектура генерации

Система использует **Google Gemini API** для создания предложений с автоматическим переводом на русский язык.

#### Контроллер
- **Файл**: `app/Http/Controllers/GeneratingSentencesAiController.php`
- **Метод**: `generateSentence()`

#### Основной поток обработки

```php
// 1. Получение слов из запроса
$words = $request->input('arr_words', []);

// 2. Для каждого слова - генерация через Gemini
foreach ($words as $word) {
    $responseArray = $this->getSentencesFromGemini($word);
    
    // 3. Обработка и очистка результатов
    foreach ($responseArray as $item) {
        $enClean = $this->sanitizeText($item['en']);
        $ruClean = $this->sanitizeText($item['ru']);
        
        $sentences[] = [
            'original' => $enClean,
            'translated' => $ruClean,
        ];
    }
}
```

#### Ключевые методы

**`getSentencesFromGemini(string $word): array`**
- Отправляет промпт в Google Gemini API
- Формирует структурированный запрос с JSON-ответом
- Обрабатывает ошибки API и валидирует ответ

**Промпт для Gemini:**
```
Generate 5 simple literary sentences using the word: {$word}.
For each sentence, also provide the Russian translation.
Return result strictly as JSON array of objects with fields 'en' and 'ru'.
```

**`sanitizeText(string $text): string`**
- Удаляет Markdown-артефакты (`**`, `*`, `__`, `_`, `` ` ``)
- Очищает управляющие символы
- Нормализует пробелы и пунктуацию
- Убирает лишние пробелы перед знаками препинания

#### Преимущества подхода

✅ **Единый источник**: генерация и перевод в одном API-вызове  
✅ **Качество**: Google Gemini обеспечивает высокое качество перевода  
✅ **Производительность**: нет дополнительных запросов к Google Translate  
✅ **Надежность**: встроенная обработка ошибок и валидация  
✅ **Структурированность**: строгий JSON-формат ответа  

#### Конфигурация

В `.env` файле:
```env
GOOGLE_AI_API_KEY=your_google_gemini_api_key
```

#### API Endpoint

```
POST /ai/generate-sentences
Content-Type: application/json

{
    "arr_words": ["hello", "world"]
}
```

**Ответ:**
```json
{
    "sentences": [
        {
            "original": "Hello, how are you today?",
            "translated": "Привет, как дела сегодня?"
        },
        {
            "original": "The world is beautiful.",
            "translated": "Мир прекрасен."
        }
    ]
}
```


## 🔧 Полезные команды

```bash
# Очистка кэша
php artisan route:clear && php artisan view:clear && php artisan config:clear

# Технический роут для очистки
GET /technical/artisan/clear_all

# Frontend команды (Vite)
npm run dev          # Разработка с HMR
npm run build        # Сборка для продакшена
npm run watch        # Отслеживание изменений

# Форматирование и линтинг кода
npm run format       # Форматировать весь код (Prettier)
npm run format:check # Проверить формат без изменений
npm run lint         # Проверить качество кода (ESLint)
npm run lint:fix     # Автоматически исправить ошибки

# PHP форматирование
./vendor/bin/pint --test  # Проверить форматирование PHP
./vendor/bin/pint         # Исправить форматирование PHP

# Запуск тестов
php artisan test
```

## 💾 Резервное копирование

Проект создает бекапы базы данных MySQL с пакетом `spatie/laravel-backup`. Пакет работает с такими базами - MySQL, PostgreSQL, SQLite, MongoDB.

**Файл middleware:** `app/Http/Middleware/BackupDatabase.php`

## 🧩 Составные компоненты

### Настройка и инструменты
- **Vite**: современный сборщик с HMR (заменил webpack)
- **AdminLTE**: [админ панель](https://adminlte.io/themes/v3/)

### UI компоненты
- **Bootstrap 5.3.3**: [документация](https://bootstrap-4.ru/docs/4.0/components/tooltips/)
- **Bootstrap Toggle**: [документация](http://www.bootstraptoggle.com/)
- **Font Awesome**: [иконки](https://fontawesome.com/v5/icons/trash-alt?f=classic&s=solid)
- **Vue Bootstrap Toggle**: [npm пакет](https://www.npmjs.com/package/vue-bootstrap-toggle)

### Vue.js компоненты
- **Vue Tippy**: [тултипы для подсказок](https://kabbouchi.github.io/vue-tippy/4.0/features/default.html)
- **Tippy.js helper**: [документация](https://atomiks.github.io/tippyjs/v6/html-content/)
- **SweetAlert2**: [modal alert](https://sweetalert2.github.io/#handling-buttons)
- **Vue Good Table**: [таблицы](https://xaksis.github.io/vue-good-table/guide/)

### Дополнительные инструменты
- **Flex Simulator**: [CSS Flexbox](http://cssworld.ru/flex/)
- **Web Speech API**: [озвучка текста](https://xhtml.ru/2021/javascript/javascript-text-to-speech-and-its-many-quirks/)

## 📚 Документация

Подробная техническая документация находится в файле `.cursor/rules/aboute-project.mdc`

### Форматирование кода
- **Prettier**: автоматическое форматирование JS/Vue/SCSS файлов
- **ESLint**: проверка качества кода с правилами для Vue 2
- **Laravel Pint**: автоматическое форматирование PHP кода по стандартам Laravel
- **Конфигурация**: `.prettierrc`, `.eslintrc.cjs`, `.prettierignore`, `.eslintignore`
- **Руководство**: `FORMATTING_GUIDE.md` - подробные инструкции по использованию

## ⚠️ Важно

1. **Email**: настройте Mailtrap для верификации пользователей
2. **Очереди**: обязательно запускайте `php artisan queue:work`
3. **ИИ API**: опционально, для генерации предложений
4. **Бэкапы**: автоматически создаются при посещении страниц
5. **Vite**: используйте `npm run dev` для разработки с HMR
6. **Форматирование**: перед коммитом запускайте `npm run format && npm run lint:fix && ./vendor/bin/pint`

## 🔄 Миграция на Vite

Проект успешно мигрирован с webpack на Vite для улучшения производительности разработки:

- **Быстрая сборка**: esbuild для мгновенной компиляции
- **HMR**: Hot Module Replacement без перезагрузки страницы
- **ES модули**: современный подход к импортам
- **Оптимизация**: лучшая оптимизация для продакшена

Подробности миграции в файле `VITE_MIGRATION.md`

## Лицензия

MIT License

