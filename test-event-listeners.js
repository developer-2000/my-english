// Код для тестирования накопления обработчиков событий в ModalLearnWord.vue
// Скопируй этот код в консоль браузера (F12 -> Console)

console.log('🔍 [TEST] Event listeners test started');

// Счетчики для отслеживания обработчиков
let escapeCount = 0;
let mouseoverCount = 0;

// Перехватываем добавление обработчиков Escape
const originalAddEventListener = document.addEventListener;
document.addEventListener = function(type, listener, options) {
    if (type === 'keydown') {
        escapeCount++;
        console.log('🔍 [TEST] Escape listener added, total count:', escapeCount);
    }
    return originalAddEventListener.call(this, type, listener, options);
};

// Перехватываем удаление обработчиков Escape
const originalRemoveEventListener = document.removeEventListener;
document.removeEventListener = function(type, listener, options) {
    if (type === 'keydown') {
        escapeCount--;
        console.log('🔍 [TEST] Escape listener removed, total count:', escapeCount);
    }
    return originalRemoveEventListener.call(this, type, listener, options);
};

// Перехватываем добавление jQuery обработчиков
const originalOn = $.fn.on;
$.fn.on = function(event, selector, data, handler) {
    if (event === 'mouseover' && selector === '.learn-word-trigger') {
        mouseoverCount++;
        console.log('🔍 [TEST] Mouseover listener added, total count:', mouseoverCount);
    }
    return originalOn.call(this, event, selector, data, handler);
};

// Перехватываем удаление jQuery обработчиков
const originalOff = $.fn.off;
$.fn.off = function(event, selector, handler) {
    if (event === 'mouseover' && selector === '.learn-word-trigger') {
        mouseoverCount--;
        console.log('🔍 [TEST] Mouseover listener removed, total count:', mouseoverCount);
    }
    return originalOff.call(this, event, selector, handler);
};

// Функция для проверки текущего состояния
window.checkEventListeners = function() {
    console.log('🔍 [TEST] Current event listeners count:');
    console.log('🔍 [TEST] Escape listeners:', escapeCount);
    console.log('🔍 [TEST] Mouseover listeners:', mouseoverCount);
    
    if (escapeCount > 0) {
        console.warn('⚠️ [TEST] WARNING: Escape listeners are accumulating!');
    }
    if (mouseoverCount > 0) {
        console.warn('⚠️ [TEST] WARNING: Mouseover listeners are accumulating!');
    }
    if (escapeCount === 0 && mouseoverCount === 0) {
        console.log('✅ [TEST] All event listeners are properly cleaned up');
    }
};

// Функция для сброса счетчиков
window.resetEventListenersCount = function() {
    escapeCount = 0;
    mouseoverCount = 0;
    console.log('🔍 [TEST] Event listeners count reset to 0');
};

console.log('🔍 [TEST] Event listeners monitoring activated');
console.log('🔍 [TEST] Use checkEventListeners() to check current state');
console.log('🔍 [TEST] Use resetEventListenersCount() to reset counters');
