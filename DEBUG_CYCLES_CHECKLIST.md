# План проверок на зацикливание в проекте

## 1. МИКСИНЫ - КРИТИЧЕСКИЕ МЕСТА

### 1.1 good_table_mixin.js
**Потенциальные проблемы:**
- `isRequestInProgress` флаг может не сбрасываться
- `setTimeout` в методах может создавать множественные таймеры
- `initialData()` может вызываться рекурсивно

**Проверки:**
```javascript
// В onSearch, onPageChange, onPerPageChange, onSortChange
console.log('🔍 [GOOD_TABLE] Method called:', methodName, 'isRequestInProgress:', this.isRequestInProgress);
console.log('🔍 [GOOD_TABLE] Server params:', JSON.stringify(this.serverParams));

// В updateParams
console.log('🔍 [GOOD_TABLE] Updating params:', JSON.stringify(newProps));

// В initialData (если есть)
console.log('🔍 [GOOD_TABLE] InitialData called, rows count:', this.table.rows.length);
```

### 1.2 help_search_word_mixin.js
**Потенциальные проблемы:**
- `searchWord()` может вызываться при каждом вводе символа
- `searchHelpWord()` может зациклиться при быстром вводе

**Проверки:**
```javascript
// В searchWord
console.log('🔍 [HELP_SEARCH] searchWord called with:', word);
console.log('🔍 [HELP_SEARCH] isLoading:', this.isLoading);

// В searchHelpWord
console.log('🔍 [HELP_SEARCH] searchHelpWord called with:', word);
console.log('🔍 [HELP_SEARCH] help_dynamic length:', this.help_dynamic.length);
```

### 1.3 sound_word_mixin.js
**Потенциальные проблемы:**
- `forSpeak()` может создавать множественные Promise.all
- `setTimeout` в forSpeak может накапливаться

**Проверки:**
```javascript
// В forSpeak
console.log('🔍 [SOUND] forSpeak called, arrText length:', this.speak.arrText.length);
console.log('🔍 [SOUND] speak.start:', this.speak.start);

// В readSound
console.log('🔍 [SOUND] readSound called for:', text);
```

## 2. VUE КОМПОНЕНТЫ

### 2.1 PageListWords.vue
**Потенциальные проблемы:**
- `loadWordsAndTypes()` может вызываться рекурсивно
- `updateColumnTable()` с setTimeout может накапливаться
- `hoverWordShowTitle()` может создавать множественные обработчики

**Проверки:**
```javascript
// В loadWordsAndTypes
console.log('🔍 [PAGE_LIST_WORDS] loadWordsAndTypes called');
console.log('🔍 [PAGE_LIST_WORDS] URL:', url);
console.log('🔍 [PAGE_LIST_WORDS] Response data:', response.data);

// В updateColumnTable
console.log('🔍 [PAGE_LIST_WORDS] updateColumnTable called');
console.log('🔍 [PAGE_LIST_WORDS] Timer ID:', timerId);

// В hoverWordShowTitle
console.log('🔍 [PAGE_LIST_WORDS] hoverWordShowTitle called');
```

### 2.2 PageWordSentences.vue
**Потенциальные проблемы:**
- `loadSentences()` может вызываться рекурсивно
- `activationButtonSoundInMenu()` с setTimeout может накапливаться

**Проверки:**
```javascript
// В loadSentences
console.log('🔍 [PAGE_WORD_SENTENCES] loadSentences called');
console.log('🔍 [PAGE_WORD_SENTENCES] URL:', url);

// В activationButtonSoundInMenu
console.log('🔍 [PAGE_WORD_SENTENCES] activationButtonSoundInMenu called');
```

## 3. WATCHERS И COMPUTED

### 3.1 Проверка watchers
**Потенциальные проблемы:**
- Watchers могут вызывать методы, которые изменяют наблюдаемые свойства

**Проверки:**
```javascript
// В каждом watcher
console.log('🔍 [WATCHER] Watcher triggered for:', propertyName);
console.log('🔍 [WATCHER] Old value:', oldValue);
console.log('🔍 [WATCHER] New value:', newValue);
```

### 3.2 Проверка computed
**Потенциальные проблемы:**
- Computed свойства могут зависеть друг от друга

**Проверки:**
```javascript
// В каждом computed
console.log('🔍 [COMPUTED] Computed property calculated:', propertyName);
```

## 4. HTTP ЗАПРОСЫ

### 4.1 Проверка дублирующихся запросов
**Потенциальные проблемы:**
- Одинаковые запросы могут отправляться одновременно
- Запросы могут не отменяться при размонтировании компонента

**Проверки:**
```javascript
// Перед каждым HTTP запросом
console.log('🔍 [HTTP] Request started:', method, url);
console.log('🔍 [HTTP] Request data:', data);

// После каждого HTTP запроса
console.log('🔍 [HTTP] Request completed:', method, url);
console.log('🔍 [HTTP] Response status:', response.status);
```

## 5. TIMEOUT И INTERVAL

### 5.1 Проверка накопившихся таймеров
**Потенциальные проблемы:**
- setTimeout/setInterval могут накапливаться
- Таймеры могут не очищаться при размонтировании

**Проверки:**
```javascript
// При создании таймера
const timerId = setTimeout(() => {
    console.log('🔍 [TIMER] Timer executed:', timerId);
}, delay);
console.log('🔍 [TIMER] Timer created:', timerId);

// При очистке таймера
clearTimeout(timerId);
console.log('🔍 [TIMER] Timer cleared:', timerId);
```

## 6. EVENT LISTENERS

### 6.1 Проверка множественных обработчиков
**Потенциальные проблемы:**
- Обработчики событий могут добавляться многократно
- Обработчики могут не удаляться при размонтировании

**Проверки:**
```javascript
// При добавлении обработчика
console.log('🔍 [EVENT] Event listener added for:', eventType);

// При удалении обработчика
console.log('🔍 [EVENT] Event listener removed for:', eventType);
```

## 7. VUE LIFECYCLE

### 7.1 Проверка жизненного цикла
**Потенциальные проблемы:**
- Методы могут вызываться после размонтирования компонента
- Данные могут обновляться в несуществующих компонентах

**Проверки:**
```javascript
// В mounted
console.log('🔍 [LIFECYCLE] Component mounted:', this.$options.name);

// В beforeDestroy
console.log('🔍 [LIFECYCLE] Component destroying:', this.$options.name);

// В методах после проверки существования компонента
if (this.$el) {
    console.log('🔍 [LIFECYCLE] Method called on existing component');
} else {
    console.log('🔍 [LIFECYCLE] Method called on destroyed component');
}
```

## 8. ПЛАН ВНЕДРЕНИЯ

### 8.1 Этап 1: Критические миксины
1. Добавить логирование в `good_table_mixin.js`
2. Добавить логирование в `help_search_word_mixin.js`
3. Добавить логирование в `sound_word_mixin.js`

### 8.2 Этап 2: Основные компоненты
1. Добавить логирование в `PageListWords.vue`
2. Добавить логирование в `PageWordSentences.vue`

### 8.3 Этап 3: HTTP запросы
1. Добавить логирование всех HTTP запросов
2. Проверить на дублирующиеся запросы

### 8.4 Этап 4: Таймеры и события
1. Добавить логирование всех setTimeout/setInterval
2. Добавить логирование всех event listeners

## 9. КРИТЕРИИ ЗАЦИКЛИВАНИЯ

### 9.1 Признаки зацикливания:
- Одинаковые сообщения в консоли повторяются более 3 раз подряд
- HTTP запросы отправляются чаще чем раз в секунду
- Таймеры создаются быстрее чем очищаются
- Методы вызываются в бесконечном цикле

### 9.2 Действия при обнаружении:
1. Остановить выполнение
2. Проанализировать стек вызовов
3. Найти причину зацикливания
4. Добавить защитные механизмы
5. Протестировать исправление

## 10. ЗАЩИТНЫЕ МЕХАНИЗМЫ

### 10.1 Дебаунсинг для поиска
```javascript
// Добавить в help_search_word_mixin.js
debounceSearch: debounce(function(word) {
    this.searchWord(word);
}, 300)
```

### 10.2 Ограничение частоты запросов
```javascript
// Добавить в good_table_mixin.js
throttleRequest: throttle(function(method) {
    this[method]();
}, 1000)
```

### 10.3 Очистка таймеров
```javascript
// В beforeDestroy каждого компонента
beforeDestroy() {
    if (this.timers) {
        this.timers.forEach(timerId => {
            clearTimeout(timerId);
            clearInterval(timerId);
        });
    }
}
```
