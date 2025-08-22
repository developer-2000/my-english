export default {
    data() {
        return {
            isRequestInProgress: false
        }
    },
    methods: {
        // заполнить обьект данных для таблицы
        makeObjectDataForTable(list) {
            let row = '';
            this.table.rows = [];

            list.forEach((obj, index) => {
                row = {
                    memorable_checkbox_sound: (obj.sound == null) ? false : true,
                    id: obj.id,
                    sentence: obj.sentence.charAt(0).toUpperCase() + obj.sentence.slice(1),
                    translation: obj.translation.charAt(0).toUpperCase() + obj.translation.slice(1),
                    but: obj.id
                };
                this.table.rows.push(row);
            });
        },
        // изменение input search
        onSearch(search) {
            if (this.isRequestInProgress) return;
            this.isRequestInProgress = true;
            
            this.updateParams({search: search.searchTerm, page: 1});
            this.initialData();
            $('.vgt-global-search__input.vgt-pull-left span.sr-only').css('display', 'flex');
            $('input.vgt-input.vgt-pull-left').css('margin-left', '34px');
            
            // Сброс флага через 1 секунду
            setTimeout(() => {
                this.isRequestInProgress = false;
            }, 1000);
        },
        // шаги в пагинации
        onPageChange(page) {
            if (this.isRequestInProgress) return;
            this.isRequestInProgress = true;
            
            this.updateParams({page: page});
            this.initialData();
            
            // Сброс флага через 1 секунду
            setTimeout(() => {
                this.isRequestInProgress = false;
            }, 1000);
        },
        // по сколько показывать на странице
        onPerPageChange(perPage) {
            if (this.isRequestInProgress) return;
            this.isRequestInProgress = true;
            
            this.updateParams({page: 1, perPage: perPage});
            this.initialData();
            
            // Сброс флага через 1 секунду
            setTimeout(() => {
                this.isRequestInProgress = false;
            }, 1000);
        },
        onSortChange(params) {
            if (this.isRequestInProgress) return;
            this.isRequestInProgress = true;
            
            this.updateParams({page: 1, sort: params});
            this.initialData();
            
            // Сброс флага через 1 секунду
            setTimeout(() => {
                this.isRequestInProgress = false;
            }, 1000);
        },
        updateParams(newProps) {
            this.serverParams = Object.assign({}, this.serverParams, newProps);
        },
        // клик по кнопки зачистки поля поиска
        makeButtonClearSearch() {
           setTimeout(() => {
                $('.vgt-global-search__input span.sr-only').html('<svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="w-4 h-4"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"></path></svg>');

                $('.vgt-global-search__input span.sr-only').bind('click', (e) => {
                    this.serverParams.selection_type_id = null

                    // Очистить поле ввода
                    this.resetButtonClearSearch()

                    setTimeout(()=>{
                        // сместить search
                        $('input.vgt-input.vgt-pull-left').css('margin-left', '0');
                        // спрятать кнопку
                        $('.vgt-global-search__input.vgt-pull-left span.sr-only').css('display', 'none');
                    },50)

                    // На странице page-list-words
                    if (typeof this.handleSelectChange === 'function') {
                        this.handleSelectChange();
                        this.table.selectedOption = 'null'
                    }
                });
            }, 500);
        },
        // очистка поля поиска слов
        resetButtonClearSearch() {
            // обратиться к кнопке зачистки поля поиска
            const clearSearchButton = document.querySelector('.vgt-global-search__input span.sr-only');
            if (clearSearchButton) {

                this.serverParams.search = '';
                this.serverParams.page = 1;

                // Очищаем поле ввода поиска
                const searchInput = document.querySelector('.vgt-global-search__input input');
                if (searchInput) {
                    // Создаем новое событие ввода
                    const event = new Event('input', { bubbles: true });
                    // Устанавливаем значение поля ввода в пустую строку
                    searchInput.value = '';
                    // Диспатчим событие ввода
                    searchInput.dispatchEvent(event);
                }

                this.initialData();
            }
        },
        // Безопасное использование bootstrapToggle
        safeBootstrapToggle(element, action) {
            if (typeof $.fn.bootstrapToggle !== 'undefined' && element) {
                $(element).bootstrapToggle(action);
            }
        },
    },
}


