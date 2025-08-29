export default {
    data() {
        return {
            requestInProgressFlag: false, // Флаг запроса на сервер
            searchDebounceTimer: null,  // Таймер для debounce поиска
        };
    },
    methods: {
        // Проверка флага запроса и его установка
        checkAndSetRequestFlag() {
            if (this.requestInProgressFlag) {
                return false;
            }
            this.requestInProgressFlag = true;
            return true;
        },
        // Сброс флага запроса через 1 секунду
        resetRequestFlag() {
            setTimeout(() => {
                this.requestInProgressFlag = false;
            }, 1000);
        },
        // Обновление параметров и запрос данных
        updateParamsAndFetch(newProps) {
            this.updateParams(newProps);
            this.initialData();
            this.resetRequestFlag();
        },
        // Показать кнопку очистки поиска
        showClearSearchButton() {
            $('.vgt-global-search__input.vgt-pull-left span.sr-only').css('display', 'flex');
            $('input.vgt-input.vgt-pull-left').css('margin-left', '34px');
        },
        // Скрыть кнопку очистки поиска
        hideClearSearchButton() {
            $('.vgt-global-search__input.vgt-pull-left span.sr-only').css('display', 'none');
            $('input.vgt-input.vgt-pull-left').css('margin-left', '0');
        },
        // заполнить обьект данных для таблицы
        makeObjectDataForTable(list) {
            let row = '';
            this.table.rows = [];

            list.forEach((obj) => {
                row = {
                    memorable_checkbox_sound: obj.sound == null ? false : true,
                    id: obj.id,
                    sentence: obj.sentence.charAt(0).toUpperCase() + obj.sentence.slice(1),
                    translation: obj.translation.charAt(0).toUpperCase() + obj.translation.slice(1),
                    but: obj.id,
                };
                this.table.rows.push(row);
            });
        },
        // изменение input search с debounce
        onSearch(search) {

            // Очищаем предыдущий таймер если есть
            if (this.searchDebounceTimer) {
                clearTimeout(this.searchDebounceTimer);
            }

            // Создаем новый таймер с задержкой 0.5 секунды
            this.searchDebounceTimer = setTimeout(() => {

                if (this.requestInProgressFlag) {
                    return;
                }

                // Проверяем если поле поиска пустое после debounce
                if (!search.searchTerm || search.searchTerm.trim() === '') {
                    this.requestInProgressFlag = true;

                    this.updateParams({search: '', page: 1});
                    this.initialData();

                    // Скрываем кнопку очистки поиска
                    this.hideClearSearchButton();

                    this.resetRequestFlag();
                    return;
                }

                this.requestInProgressFlag = true;

                this.updateParams({search: search.searchTerm, page: 1});
                this.initialData();

                // Показываем кнопку очистки поиска только если есть текст поиска
                if (search.searchTerm && search.searchTerm.trim() !== '') {
                    this.showClearSearchButton();
                }

                this.resetRequestFlag();
            }, 500); // Задержка 0.5 секунды
        },
        // шаги в пагинации
        onPageChange(page) {
            if (!this.checkAndSetRequestFlag()) {
                return;
            }
            this.updateParamsAndFetch({page: page});
        },
        // по сколько показывать на странице
        onPerPageChange(perPage) {
            if (!this.checkAndSetRequestFlag()) {
                return;
            }
            this.updateParamsAndFetch({page: 1, perPage: perPage});
        },
        onSortChange(params) {
            if (!this.checkAndSetRequestFlag()) {
                return;
            }
            this.updateParamsAndFetch({page: 1, sort: params});
        },
        updateParams(newProps) {
            this.serverParams = Object.assign({}, this.serverParams, newProps);
        },
        // клик по кнопки зачистки поля поиска
        makeButtonClearSearch() {
            setTimeout(() => {
                $('.vgt-global-search__input span.sr-only').html(
                    '<svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="w-4 h-4"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"></path></svg>'
                );

                // Удаляем предыдущие обработчики чтобы избежать дублирования
                $('.vgt-global-search__input span.sr-only').unbind('click');

                $('.vgt-global-search__input span.sr-only').bind('click', () => {
                    this.serverParams.selection_type_id = null;

                    // Очистить поле ввода
                    this.resetButtonClearSearch();

                    setTimeout(() => {
                        this.hideClearSearchButton();
                    }, 50);

                    // Сбрасываем select типов слов без вызова handleSelectChange
                    if (this.table && this.table.selectedOption) {
                        this.table.selectedOption = 'null';
                    }
                });
            }, 500);
        },
        // очистка поля поиска слов
        resetButtonClearSearch() {
            // Очищаем таймер debounce поиска
            if (this.searchDebounceTimer) {
                clearTimeout(this.searchDebounceTimer);
                this.searchDebounceTimer = null;
            }

            // обратиться к кнопке зачистки поля поиска
            const clearSearchButton = document.querySelector(
                '.vgt-global-search__input span.sr-only'
            );
            if (clearSearchButton) {
                this.serverParams.search = '';
                this.serverParams.page = 1;

                // Очищаем поле ввода поиска
                const searchInput = document.querySelector('.vgt-global-search__input input');
                if (searchInput) {
                    // Создаем новое событие ввода
                    const event = new Event('input', {bubbles: true});
                    // Устанавливаем значение поля ввода в пустую строку
                    searchInput.value = '';
                    // Диспатчим событие ввода
                    searchInput.dispatchEvent(event);
                }
            } else {
                console.log('⚠️ Кнопка очистки поиска не найдена');
            }
        },
        // Безопасное использование bootstrapToggle
        safeBootstrapToggle(element, action) {
            if (typeof $.fn.bootstrapToggle !== 'undefined' && element) {
                $(element).bootstrapToggle(action);
            }
        },
    },
};
