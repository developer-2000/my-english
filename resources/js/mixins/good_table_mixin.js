export default {
    data() {
        return {
            isRequestInProgress: false
        }
    },
    methods: {
        // заполнить обьект данных для таблицы
        makeObjectDataForTable(list) {
            console.log('🔍 [GOOD_TABLE] makeObjectDataForTable called, list length:', list.length);
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
            console.log('🔍 [GOOD_TABLE] Table rows created:', this.table.rows.length);
        },
        // изменение input search
        onSearch(search) {
            console.log('🔍 [GOOD_TABLE] onSearch called, isRequestInProgress:', this.isRequestInProgress);
            console.log('🔍 [GOOD_TABLE] Search term:', search.searchTerm);
            
            if (this.isRequestInProgress) {
                console.log('🔍 [GOOD_TABLE] Request in progress, skipping...');
                return;
            }
            this.isRequestInProgress = true;
            
            this.updateParams({search: search.searchTerm, page: 1});
            this.initialData();
            $('.vgt-global-search__input.vgt-pull-left span.sr-only').css('display', 'flex');
            $('input.vgt-input.vgt-pull-left').css('margin-left', '34px');
            
            // Сброс флага через 1 секунду
            const timerId = setTimeout(() => {
                console.log('🔍 [GOOD_TABLE] Resetting isRequestInProgress flag, timer ID:', timerId);
                this.isRequestInProgress = false;
            }, 1000);
            console.log('🔍 [GOOD_TABLE] Timer created for onSearch:', timerId);
        },
        // шаги в пагинации
        onPageChange(page) {
            console.log('🔍 [GOOD_TABLE] onPageChange called, page:', page, 'isRequestInProgress:', this.isRequestInProgress);
            
            if (this.isRequestInProgress) {
                console.log('🔍 [GOOD_TABLE] Request in progress, skipping...');
                return;
            }
            this.isRequestInProgress = true;
            
            this.updateParams({page: page});
            this.initialData();
            
            // Сброс флага через 1 секунду
            const timerId = setTimeout(() => {
                console.log('🔍 [GOOD_TABLE] Resetting isRequestInProgress flag, timer ID:', timerId);
                this.isRequestInProgress = false;
            }, 1000);
            console.log('🔍 [GOOD_TABLE] Timer created for onPageChange:', timerId);
        },
        // по сколько показывать на странице
        onPerPageChange(perPage) {
            console.log('🔍 [GOOD_TABLE] onPerPageChange called, perPage:', perPage, 'isRequestInProgress:', this.isRequestInProgress);
            
            if (this.isRequestInProgress) {
                console.log('🔍 [GOOD_TABLE] Request in progress, skipping...');
                return;
            }
            this.isRequestInProgress = true;
            
            this.updateParams({page: 1, perPage: perPage});
            this.initialData();
            
            // Сброс флага через 1 секунду
            const timerId = setTimeout(() => {
                console.log('🔍 [GOOD_TABLE] Resetting isRequestInProgress flag, timer ID:', timerId);
                this.isRequestInProgress = false;
            }, 1000);
            console.log('🔍 [GOOD_TABLE] Timer created for onPerPageChange:', timerId);
        },
        onSortChange(params) {
            console.log('🔍 [GOOD_TABLE] onSortChange called, params:', JSON.stringify(params), 'isRequestInProgress:', this.isRequestInProgress);
            
            if (this.isRequestInProgress) {
                console.log('🔍 [GOOD_TABLE] Request in progress, skipping...');
                return;
            }
            this.isRequestInProgress = true;
            
            this.updateParams({page: 1, sort: params});
            this.initialData();
            
            // Сброс флага через 1 секунду
            const timerId = setTimeout(() => {
                console.log('🔍 [GOOD_TABLE] Resetting isRequestInProgress flag, timer ID:', timerId);
                this.isRequestInProgress = false;
            }, 1000);
            console.log('🔍 [GOOD_TABLE] Timer created for onSortChange:', timerId);
        },
        updateParams(newProps) {
            console.log('🔍 [GOOD_TABLE] updateParams called with:', JSON.stringify(newProps));
            console.log('🔍 [GOOD_TABLE] Current serverParams:', JSON.stringify(this.serverParams));
            this.serverParams = Object.assign({}, this.serverParams, newProps);
            console.log('🔍 [GOOD_TABLE] Updated serverParams:', JSON.stringify(this.serverParams));
        },
        // клик по кнопки зачистки поля поиска
        makeButtonClearSearch() {
           console.log('🔍 [GOOD_TABLE] makeButtonClearSearch called');
           const timerId = setTimeout(() => {
                console.log('🔍 [GOOD_TABLE] makeButtonClearSearch timer executed, ID:', timerId);
                $('.vgt-global-search__input span.sr-only').html('<svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="w-4 h-4"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"></path></svg>');

                $('.vgt-global-search__input span.sr-only').bind('click', (e) => {
                    console.log('🔍 [GOOD_TABLE] Clear search button clicked');
                    this.serverParams.selection_type_id = null

                    // Очистить поле ввода
                    this.resetButtonClearSearch()

                    const innerTimerId = setTimeout(()=>{
                        console.log('🔍 [GOOD_TABLE] Inner timer executed, ID:', innerTimerId);
                        // сместить search
                        $('input.vgt-input.vgt-pull-left').css('margin-left', '0');
                        // спрятать кнопку
                        $('.vgt-global-search__input.vgt-pull-left span.sr-only').css('display', 'none');
                    },50)
                    console.log('🔍 [GOOD_TABLE] Inner timer created, ID:', innerTimerId);

                    // На странице page-list-words
                    if (typeof this.handleSelectChange === 'function') {
                        console.log('🔍 [GOOD_TABLE] handleSelectChange function exists, calling it');
                        this.handleSelectChange();
                        this.table.selectedOption = 'null'
                    }
                });
            }, 500);
            console.log('🔍 [GOOD_TABLE] makeButtonClearSearch timer created, ID:', timerId);
        },
        // очистка поля поиска слов
        resetButtonClearSearch() {
            console.log('🔍 [GOOD_TABLE] resetButtonClearSearch called');
            // обратиться к кнопке зачистки поля поиска
            const clearSearchButton = document.querySelector('.vgt-global-search__input span.sr-only');
            if (clearSearchButton) {
                console.log('🔍 [GOOD_TABLE] Clear search button found');

                this.serverParams.search = '';
                this.serverParams.page = 1;
                console.log('🔍 [GOOD_TABLE] Server params reset');

                // Очищаем поле ввода поиска
                const searchInput = document.querySelector('.vgt-global-search__input input');
                if (searchInput) {
                    console.log('🔍 [GOOD_TABLE] Search input found, clearing it');
                    // Создаем новое событие ввода
                    const event = new Event('input', { bubbles: true });
                    // Устанавливаем значение поля ввода в пустую строку
                    searchInput.value = '';
                    // Диспатчим событие ввода
                    searchInput.dispatchEvent(event);
                }

                console.log('🔍 [GOOD_TABLE] Calling initialData');
                this.initialData();
            } else {
                console.log('🔍 [GOOD_TABLE] Clear search button not found');
            }
        },
        // Безопасное использование bootstrapToggle
        safeBootstrapToggle(element, action) {
            console.log('🔍 [GOOD_TABLE] safeBootstrapToggle called, action:', action);
            if (typeof $.fn.bootstrapToggle !== 'undefined' && element) {
                console.log('🔍 [GOOD_TABLE] bootstrapToggle available, executing action');
                $(element).bootstrapToggle(action);
            } else {
                console.log('🔍 [GOOD_TABLE] bootstrapToggle not available or element not found');
            }
        },
    },
}


