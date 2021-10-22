
export default {
    data() {
        return { }
    },
    methods: {
        // заполнить обьект данных для таблица
        makeObjectDataForTable(list) {
            let row = '';
            this.table.rows = [];

            for (let i = 0; i < list.length; i++) {
                row = {
                    sound_all: list[i].id,
                    id: list[i].id,
                    sentence: list[i].sentence.charAt(0).toUpperCase() + list[i].sentence.slice(1),
                    translation: list[i].translation.charAt(0).toUpperCase() + list[i].translation.slice(1),
                    but: list[i].id
                };
                this.table.rows.push(row);
            }
        },
        onSearch(search) {
            this.updateParams({search: search.searchTerm});
            this.initialData();
            $('.vgt-global-search__input.vgt-pull-left span.sr-only').css('display','block');
            $('input.vgt-input.vgt-pull-left').css('margin-left','34px');
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
        makeButtonClearSearch(){
            let a = setTimeout(() => {
                $('.vgt-global-search__input span.sr-only').html('<span id="clear_search" aria-hidden="true">&times;</span>');

                $('#clear_search').bind('click' , (e) => {
                    // спрятать кнопку
                    $('.vgt-global-search__input.vgt-pull-left span.sr-only').css('display','none');
                    this.serverParams.search = '';
                    this.initialData();
                    a = setTimeout(() => {
                        // сместить search и зачистить
                        $('input.vgt-input.vgt-pull-left').css('margin-left','0px').val("");
                    }, 500);
                });
            }, 500);
        },
    },
    props: [ ],
    mounted() { }
}


