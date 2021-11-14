export default {
    methods: {
        // заполнить обьект данных для таблица
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
        onSearch(search) {
            this.updateParams({search: search.searchTerm});
            this.initialData();
            $('.vgt-global-search__input.vgt-pull-left span.sr-only').css('display', 'block');
            $('input.vgt-input.vgt-pull-left').css('margin-left', '34px');
        },
        onPageChange(params) {
            this.updateParams({page: params.currentPage});
            this.initialData();
        },
        onPerPageChange(params) {
            this.updateParams({page: 0, perPage: params.currentPerPage});
            this.initialData();
        },
        onSortChange(params) {
            this.updateParams({page: 0, sort: params});
            this.initialData();
        },
        updateParams(newProps) {
            this.serverParams = Object.assign({}, this.serverParams, newProps);
        },
        makeButtonClearSearch() {
            let a = setTimeout(() => {
                $('.vgt-global-search__input span.sr-only').html('<span id="clear_search" aria-hidden="true">&times;</span>');

                $('#clear_search').bind('click', (e) => {
                    // спрятать кнопку
                    $('.vgt-global-search__input.vgt-pull-left span.sr-only').css('display', 'none');
                    this.serverParams.search = '';
                    this.initialData();
                    a = setTimeout(() => {
                        // сместить search и зачистить
                        $('input.vgt-input.vgt-pull-left').css('margin-left', '0px').val("");
                    }, 500);
                });
            }, 500);
        },
    },
}


