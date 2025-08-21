# Миграция с Webpack на Vite

## Что было изменено

### 1. Конфигурационные файлы
- Удален `webpack.mix.js`
- Удален `webpack.config.js`
- Создан `vite.config.js` с настройками для Vue 2 и Laravel

### 2. Package.json
- Добавлен `"type": "module"` для поддержки ES модулей
- Заменены зависимости:
  - Удален `laravel-mix`
  - Добавлен `vite`
  - Добавлен `laravel-vite-plugin`
  - Добавлен `@vitejs/plugin-vue2`
- Обновлены скрипты для использования Vite

### 3. Blade шаблоны
- Заменены `{{ mix() }}` на `@vite()` в:
  - `resources/views/layouts/app.blade.php`
  - `resources/views/layouts/app-auth.blade.php`
  - `resources/views/errors.blade.php`

### 4. JavaScript файлы
- Обновлен `resources/js/app.js` для использования ES модулей
- Обновлен `resources/js/bootstrap.js` для использования ES модулей
- Добавлены расширения `.vue` и `.js` к импортам в Vue компонентах

### 5. SCSS
- Исправлен импорт Bootstrap в `resources/sass/app.scss`

## Команды для разработки

```bash
# Установка зависимостей
npm install

# Запуск dev сервера
npm run dev

# Сборка для продакшена
npm run build

# Просмотр изменений
npm run watch
```

## Преимущества Vite

1. **Быстрая сборка** - Vite использует esbuild для быстрой компиляции
2. **Hot Module Replacement (HMR)** - мгновенное обновление без перезагрузки страницы
3. **Оптимизация** - лучшая оптимизация для продакшена
4. **Современный подход** - использование ES модулей и современных стандартов

## Структура сборки

- **Dev режим**: Файлы обрабатываются по требованию
- **Prod режим**: Файлы собираются в `public/build/` с хешированием имен
- **Manifest**: Создается `public/build/.vite/manifest.json` для отслеживания файлов

## Совместимость

Проект полностью совместим с:
- Laravel 8+
- Vue 2.6+
- Bootstrap 5
- Все существующие компоненты и миксины
