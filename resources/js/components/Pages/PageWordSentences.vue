<template>
    <div id="page_list_worlds">

        <!-- body -->
        <div class="wrapper">
            <!-- верхнее меню -->
            <div class="card card-primary card-outline top_menu">
                <div class="card-header">
                    <button @click="initialSpeak" class="btn bg-gradient-success">
                        <i class="fas fa-play"></i>
                        Speak text
                    </button>
                    <button @click="openModalCreateSentence" class="btn bg-gradient-primary">
                        Add sentence
                    </button>
                </div>
            </div>

            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <div class="content-header">
                    <div class="container-fluid">
                        <!-- заголовок окна-->
                        <h1 class="m-0 text-dark">List sentences</h1>
                        <!-- body окна-->
                        <div class="card card-primary card-outline block_table">

                            <!-- Table -->
                            <div class="table_wrapper">
                                <vue-good-table
                                    :columns="table.columns"
                                    :isLoading.sync="table.isLoading"
                                    :mode="table.mode"
                                    :pagination-options="table.optionsPaginate"
                                    :rows="table.rows"
                                    :search-options="{
                                        enabled: true,
                                        placeholder: 'Search word',
                                    }"
                                    :totalRows="table.totalRecords"
                                    @on-page-change="onPageChange"
                                    @on-per-page-change="onPerPageChange"
                                    @on-search="onSearch"
                                    @on-sort-change="onSortChange"
                                    styleClass="vgt-table bordered sentence"
                                >
                                    <template slot="loadingContent">
                                        <div></div>
                                    </template>
                                </vue-good-table>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- /.content-header -->

            </div>
        </div>
        <!-- / body -->

        <!-- Modals -->
        <div aria-hidden="true" aria-labelledby="create_sentence" class="modal fade" id="create_sentence" role="dialog"
             tabindex="-1">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Create new sentence</h5>
                        <button aria-label="Close" class="close" data-dismiss="modal" type="button">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="#">
                            <!-- new sentence -->
                            <div class="form-group">
                                <label class="col-form-label" for="new_sentence">New sentence</label>
                                <textarea :class="{'is-invalid': $v.new_sentence.$error}" @blur="touchNewSentence()"
                                          @keyup="searchHelpWord"
                                          class="form-control entry-field-help"
                                          id="new_sentence"
                                          placeholder="Insert new sentence"
                                          required
                                          v-model="new_sentence"
                                ></textarea>
                                <help-search-word :help-dynamic="help_dynamic"/>
                                <div class="invalid-feedback" v-if="!$v.new_sentence.required">The field is empty!</div>
                                <div class="invalid-feedback" v-if="(!$v.new_sentence.minLength)">Number of characters
                                    {{ this.new_sentence.length }} less needed
                                </div>
                            </div>

                            <!-- translation sentence -->
                            <div class="form-group">
                                <label class="col-form-label" for="translation_sentence">Translation</label>
                                <textarea :class="{'is-invalid': $v.translation_sentence.$error}" @blur="touchTranslationSentence()"
                                          class="form-control"
                                          id="translation_sentence"
                                          placeholder="Insert translation sentence"
                                          required
                                          v-model="translation_sentence"
                                ></textarea>
                                <div class="invalid-feedback" v-if="!$v.translation_sentence.required">The field is
                                    empty!
                                </div>
                                <div class="invalid-feedback" v-if="(!$v.translation_sentence.minLength)">Number of
                                    characters {{ this.translation_sentence.length }} less needed
                                </div>
                            </div>

                            <!-- button save -->
                            <div class="button_footer">
                                <button :class="{'un_active': $v.$invalid, 'active2': !$v.$invalid}" :disabled="$v.$invalid"
                                        @click="createSentence"
                                        class="btn btn-primary"
                                        type="button"
                                >Save
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- 2 -->
        <div aria-hidden="true" aria-labelledby="update_sentence" class="modal fade" id="update_sentence" role="dialog"
             tabindex="-1">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update sentence</h5>
                        <button aria-label="Close" class="close" data-dismiss="modal" type="button">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="#">
                            <!-- sentence -->
                            <div class="form-group">
                                <label class="col-form-label" for="old_sentence">Sentence</label>
                                <textarea :class="{'is-invalid': $v.new_sentence.$error}" @blur="touchNewSentence()"
                                          @keyup="searchHelpWord"
                                          class="form-control entry-field-help"
                                          id="old_sentence"
                                          placeholder="Insert new sentence"
                                          required
                                          v-model="new_sentence"
                                ></textarea>
                                <help-search-word :help-dynamic="help_dynamic"/>
                                <div class="invalid-feedback" v-if="!$v.new_sentence.required">The field is empty!</div>
                                <div class="invalid-feedback" v-if="(!$v.new_sentence.minLength)">Number of characters
                                    {{ this.new_sentence.length }} less needed
                                </div>
                            </div>

                            <!-- translation sentence -->
                            <div class="form-group">
                                <label class="col-form-label" for="old_translation">Translation</label>
                                <textarea :class="{'is-invalid': $v.translation_sentence.$error}" @blur="touchTranslationSentence()"
                                          class="form-control"
                                          id="old_translation"
                                          placeholder="Insert translation sentence"
                                          required
                                          v-model="translation_sentence"
                                ></textarea>
                                <div class="invalid-feedback" v-if="!$v.translation_sentence.required">The field is
                                    empty!
                                </div>
                                <div class="invalid-feedback" v-if="(!$v.translation_sentence.minLength)">Number of
                                    characters {{ this.translation_sentence.length }} less needed
                                </div>
                            </div>

                            <!-- button save -->
                            <div class="button_footer">
                                <button :class="{'un_active': $v.$invalid, 'active2': !$v.$invalid}" :disabled="$v.$invalid"
                                        @click="updateSentence"
                                        class="btn btn-primary"
                                        type="button"
                                >Update
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
    // validate
    import {required, minLength} from 'vuelidate/lib/validators'
    // table
    import 'vue-good-table/dist/vue-good-table.css'
    import {VueGoodTable} from 'vue-good-table';
    // mixins
    import good_table_mixin from "../../mixins/good_table_mixin";
    import response_methods_mixin from "../../mixins/response_methods_mixin";
    import help_search_word_mixin from "../../mixins/help_search_word_mixin";

    import helpSearchWord from "../details/HelpSearchWord";

    export default {
        data() {
            return {
                sentence_id: 0,
                new_sentence: '',
                translation_sentence: '',
                table: {
                    // max rows in database
                    totalRecords: 0,
                    translation: '',
                    description: '',
                    origin_rows: [],
                    // settings title
                    columns: [
                        {
                            tdClass: 'check_td',
                            width: '3%',
                            sortable: false,
                            html: true,
                            field: (val) => {
                                return `<input data-id="${val.checkbox}" class="check" type="checkbox" id="check_${val.checkbox}">`;
                            }
                        },
                        {
                            tdClass: 'id_td',
                            label: 'ID',
                            field: 'id',
                            width: '3%',
                            sortable: false,
                        },
                        {
                            tdClass: 'text_td',
                            label: 'Sentences',
                            field: 'sentence',
                            width: '47%',
                        },
                        {
                            tdClass: 'text_td',
                            label: 'Translation',
                            field: 'translation',
                            width: '47%',
                            sortable: false,
                        },
                        {
                            tdClass: 'but_td',
                            label: 'Edit',
                            width: '47%',
                            sortable: false,
                            html: true,
                            field: (val) => {
                                return '<a data-id=' + val.but + ' class="btn btn-warning btn_sentence" role="button"> <span class="fa fa-edit"></span> </a>';
                            }
                        },
                    ],
                    // array objects rows
                    rows: [],
                    // settings paginate
                    optionsPaginate: {
                        enabled: true,
                        perPageDropdown: [3, 50],
                        nextLabel: 'next',
                        prevLabel: 'prev',
                        perPage: 50,
                    },
                    // sorting server data on the client side
                    mode: "remote",
                    isLoading: true,
                },
                serverParams: {
                    search: '',
                    page: 0,
                    perPage: 50,
                    sort: [{
                        field: '',
                        type: '',
                    }],
                },
                speak: {
                    arrText: [],
                    nextCheckbox: [],
                    cycle: 0,
                    lang: [
                        {
                            lang: 'en-US',
                            alpha: ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z']
                        },
                        {
                            lang: 'ru-RU',
                            alpha: ['а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ь', 'ы', 'ъ', 'э', 'ю', 'я']
                        },
                    ]
                },
            };
        },
        mixins: [
            response_methods_mixin,
            good_table_mixin,
            help_search_word_mixin
        ],
        components: {VueGoodTable, helpSearchWord},
        methods: {
            touchNewSentence() {
                this.$v.new_sentence.$touch();
            },
            touchCheckbox() {
                $('#checkbox').bind('click', (e) => {
                    if ($(e.target).prop('checked')) {
                        $('.check').prop('checked', true);
                    } else {
                        $('.check').prop('checked', false);
                    }
                })
            },
            touchTranslationSentence() {
                this.$v.translation_sentence.$touch();
            },
            initialData() {
                this.loadSenteces();
                this.initialClickButSentenceUpdate();
                this.makeCheckboxTH();
                this.makeButtonClearSearch();
            },
            async loadSenteces() {
                try {
                    this.isLoading = true;
                    const response = await this.$http.get(
                        `${this.$http.apiUrl()}sentence?search=${this.serverParams.search}&page=${this.serverParams.page}&perPage=${this.serverParams.perPage}&sortField=sentence&sortType=${this.serverParams.sort[0].type}`
                    );
                    if (this.checkSuccess(response)) {
                        this.table.totalRecords = response.data.data.sentences.total_count;
                        this.makeObjectDataForTable(response.data.data.sentences.list);
                        this.table.origin_rows = response.data.data.sentences.list;
                    }
                } catch (e) {
                    console.log(e);
                }
                this.isLoading = false;
            },
            async createSentence() {
                try {
                    let data = {
                        sentence: this.new_sentence,
                        translation: this.translation_sentence,
                    };
                    $('#create_sentence').modal('hide');
                    $('.modal-backdrop.fade.show').remove();
                    const response = await this.$http.post(`${this.$http.apiUrl()}sentence`, data);
                    if (this.checkSuccess(response)) {
                        this.initialData();
                    }
                } catch (e) {
                    console.log(e);
                }
            },
            async updateSentence() {
                try {
                    let data = {
                        sentence: this.new_sentence,
                        translation: this.translation_sentence,
                    };
                    const response = await this.$http.patch(`${this.$http.apiUrl()}sentence/${this.sentence_id}`, data);
                    if (this.checkSuccess(response)) {
                        this.initialData();
                        $('#update_sentence').modal('hide');
                        $('.modal-backdrop.fade.show').remove();
                    }
                } catch (e) {
                    console.log(e);
                }
            },
            makeObjectDataForTable(list) {
                let row = '';
                this.table.rows = [];

                for (let i = 0; i < list.length; i++) {
                    row = {
                        checkbox: list[i].id,
                        id: list[i].id,
                        sentence: list[i].sentence.charAt(0).toUpperCase() + list[i].sentence.slice(1),
                        translation: list[i].translation.charAt(0).toUpperCase() + list[i].translation.slice(1),
                        but: list[i].id
                    };
                    this.table.rows.push(row);
                }
            },
            openModalCreateSentence() {
                this.setVariableDefault();
                $('#create_sentence').modal('show');
            },
            setVariableDefault(sentence_id = 0, sentence = '', translation = '') {
                this.sentence_id = sentence_id;
                this.new_sentence = sentence;
                this.translation_sentence = translation;
            },
            getSentenceCollection(id) {
                let row = null;
                for (let i = 0; i < this.table.origin_rows.length; i++) {
                    if (this.table.origin_rows[i].id == id) {
                        row = this.table.origin_rows[i];
                        break;
                    }
                }
                return row;
            },
            initialClickButSentenceUpdate() {
                let a = setTimeout(() => {
                    $('.btn_sentence').bind('click', (e) => {
                        let queryObj = ($(e.target).prop("tagName") !== "A") ? $(e.target).parent() : $(e.target);
                        let id = queryObj.attr("data-id");
                        let row = this.getSentenceCollection(id);
                        this.setVariableDefault(row.id, row.sentence, row.translation);
                        $('#update_sentence').modal('show');
                        this.help_dynamic = '';
                    })
                }, 1000);
            },
            makeCheckboxTH() {
                let a = setTimeout(() => {
                    $('#vgt-table th:first span').html('<input type="checkbox" id="checkbox">');
                    this.touchCheckbox();
                }, 1000);
            },
            initialSpeak() {
                this.speak.cycle = 0;
                this.setText();
                this.soundSpeak();
            },
            // сбор текста в выбранных checkbox eng и перевод
            // [ ['eng','ru'], [] ]
            setText() {
                let checkboxes = document.getElementsByClassName('check');
                this.speak.arrText = [];
                let id = 0;
                // все checkboxes
                for (let i = 0; i < checkboxes.length; i++) {
                    // он выбран
                    if (checkboxes[i].checked) {
                        id = checkboxes[i].getAttribute('data-id');

                        for (let r = 0; r < this.table.rows.length; r++) {
                            if (id == this.table.rows[r].id) {
                                this.speak.arrText.push([
                                    this.table.rows[r].sentence,
                                    this.table.rows[r].translation
                                ]);
                            }
                        }
                    }
                }
            },
            getIndexLanguage(text, synthesis) {
                // все языки
                for (let i = 0; i < this.speak.lang.length; i++) {
                    // все буквы языка
                    for (let s = 0; s < this.speak.lang[i].alpha.length; s++) {
                        if (text.indexOf(this.speak.lang[i].alpha[s]) !== -1) {
                            // доступные языки в обьекте озвучки
                            for (let l = 0; l < synthesis.length; l++) {
                                if (synthesis[l].lang === this.speak.lang[i].lang) {
                                    // вернуть индекс доступного языка
                                    return l;
                                }
                            }
                        }
                    }
                }
            },
            // вернуть следущий checkbox
            addNextCheckbox() {
                if (this.speak.nextCheckbox.length < this.speak.arrText.length) {
                    this.speak.nextCheckbox.push(this.speak.arrText[this.speak.nextCheckbox.length]);
                    // новый checkbox
                    return this.speak.nextCheckbox[this.speak.nextCheckbox.length - 1];
                }
                return false;
            },
            soundSpeak(last_checkbox = []) {
                let text = '';
                window.speechSynthesis.cancel();
                let synthesis = window.speechSynthesis;
                if(last_checkbox.length === 0){
                    last_checkbox = this.addNextCheckbox();
                }

                if(last_checkbox){
                    // существует елемент текста
                    if (this.speak.cycle < last_checkbox.length) {
                        text = last_checkbox[this.speak.cycle];
                        let utterance = new SpeechSynthesisUtterance(text);
                        // определить язык текста
                        let index_lang = this.getIndexLanguage(text, synthesis.getVoices());

                        setTimeout(() => {
                            utterance.voice = synthesis.getVoices()[index_lang];
                            // озвучить текст
                            synthesis.speak(utterance);
                        }, 1000);

                        // событие завершения озвучки
                        utterance.addEventListener('end', (event) => {
                            this.speak.cycle++;
                            this.soundSpeak(last_checkbox);
                        });
                    }
                    // выбрать следущий checkbox
                    else {
                        this.speak.cycle = 0;
                        this.soundSpeak();
                    }
                }
            },
        },
        mounted() {
            this.initialData();

            $(".modal").on("hidden.bs.modal", () => {
                this.help_dynamic = "";
            })
        },
        validations: {
            new_sentence: {
                required,
                minLength: minLength(3),
            },
            translation_sentence: {
                required,
                minLength: minLength(3),
            },
        },
        beforeDestroy: function () {
            $('.btn_sentence').unbind('click');
            $('#checkbox').unbind('click');
            $('#clear_search').unbind('click');
        },
        name: "PageWordSentences.vue"
    }
</script>

<style lang="scss" scoped>

</style>
