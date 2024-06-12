<template>
    <div id="page_list_worlds">
        <!-- Контент -->
        <div class="wrapper">
            <!-- верхнее меню -->
            <div class="top-menu">
                <!-- заголовок окна-->
                <h1>List Words</h1>
                <!-- кнопки -->
                <div class="box-button">
                    <!-- learn words  -->
                    <button class="btn bg-gradient-success"
                            @click="openLearnModal"
                            v-if="!learn_words"
                    >
                        Learn words
                    </button>
                    <!-- stop learn  -->
                    <button class="btn bg-gradient-warning"
                            @click="learn_words = false"
                            v-if="learn_words"
                    >
                        Stop learn
                    </button>
                    <button class="btn bg-gradient-primary" @click="openModalCreateWord">
                        Add word
                    </button>
                </div>
            </div>

            <!-- таблица слов -->
            <div class="content-wrapper">
                <div class="container-fluid">
                    <!-- body окна-->
                    <div class="card card-primary card-outline block_table">
                        <!-- Table -->
                        <div v-if="table.rows.length" class="table_wrapper">
                            <vue-good-table
                                :isLoading.sync="table.isLoading"
                                :mode="table.mode"
                                :totalRows="table.totalRecords"
                                :rows="table.rows"
                                :columns="table.columns"
                                :pagination-options="table.optionsPaginate"
                                :search-options="{
                                    enabled: true,
                                    placeholder: 'Search word',
                                }"
                                styleClass="vgt-table bordered"
                                @on-search="onSearch"
                                @on-page-change="onPageChange"
                                @on-per-page-change="onPerPageChange"
                                @on-sort-change="onSortChange"
                            >
                                <template slot="loadingContent">
                                    <div></div>
                                </template>
                            </vue-good-table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modals создать слово -->
        <div class="modal fade" id="create_word" tabindex="-1" role="dialog" aria-labelledby="create_word" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Create new word</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="#">
                            <!-- new word -->
                            <div class="form-group">
                                <label for="new_word" class="col-form-label">New word</label>
                                <input type="text" class="form-control entry-field-help" placeholder="Insert new word"
                                       id="new_word"
                                       ref="new_word"
                                       v-model="new_word"
                                       @blur="touchNewWord()"
                                       @keyup="searchHelpWord"
                                       :class="{'is-invalid': $v.new_word.$error}"
                                       required
                                >
                                <help-search-word :help-dynamic="help_dynamic"/>
                                <div class="invalid-feedback" v-if="!$v.new_word.required">The field is empty!</div>
                                <div class="invalid-feedback" v-if="(!$v.new_word.minLength)">Number of characters {{ this.new_word.length }} less needed</div>
                            </div>

                            <!-- translation word -->
                            <div class="form-group">
                                <label for="translation_word" class="col-form-label">Translation</label>
                                <input type="text" class="form-control" placeholder="Insert translation a word" id="translation_word"
                                       v-model="translation_word"
                                       @blur="touchTranslationWord()"
                                       :class="{'is-invalid': $v.translation_word.$error}"
                                       required
                                >
                                <div class="invalid-feedback" v-if="!$v.translation_word.required">The field is empty!</div>
                                <div class="invalid-feedback" v-if="(!$v.translation_word.minLength)">Number of characters {{ this.translation_word.length }} less needed</div>
                            </div>

                            <!-- url image from out source -->
                            <div class="form-group">
                                <label for="url_image" class="col-form-label">Url image</label>
                                <input type="text" class="form-control" placeholder="Input url" id="url_image"
                                       v-model="url_image"
                                >
                            </div>

                            <!-- типы значений слова -->
                            <div class="block_type">
                                <!-- select значений -->
                                <div class="box-left-site">
                                    <div class="form-group">
                                        <label for="select_type" class="col-form-label">Word type</label>
                                        <select id="select_type" v-model="select_type_id" class="custom-select" size="3">
                                            <option v-for="(type, key) in allTypes" :key="key"
                                                    :value="type.id"
                                            >
                                                {{type.type}}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <!-- правый блок свойств -->
                                <div class="desc_type">
                                    <div class="text"></div>
                                    <div class="box-time-forms" v-if="objWordTimeForms !== null">
                                        <!-- прошедшее -->
                                        <div class="box-past">
                                            <label>Past time</label>
                                            <input type="text" class="form-control" placeholder="Insert word"
                                                   v-model="objWordTimeForms.past.word"
                                            >
                                            <input type="text" class="form-control" placeholder="Insert translation"
                                                   v-model="objWordTimeForms.past.translation"
                                            >
                                            <input type="text" class="form-control" placeholder="Insert accent"
                                                   v-model="objWordTimeForms.past.accent"
                                            >
                                        </div>
                                        <!-- настоящее -->
                                        <div class="box-present">
                                            <label>Present time</label>
                                            <input type="text" class="form-control" placeholder="Insert word"
                                                   v-model="objWordTimeForms.present.word"
                                            >
                                            <input type="text" class="form-control" placeholder="Insert translation"
                                                   v-model="objWordTimeForms.present.translation"
                                            >
                                            <input type="text" class="form-control" placeholder="Insert accent"
                                                   v-model="objWordTimeForms.present.accent"
                                            >
                                        </div>
                                        <!-- будущее -->
                                        <div class="box-future">
                                            <label>Future time</label>
                                            <input type="text" class="form-control" placeholder="Insert word"
                                                   v-model="objWordTimeForms.future.word"
                                            >
                                            <input type="text" class="form-control" placeholder="Insert translation"
                                                   v-model="objWordTimeForms.future.translation"
                                            >
                                            <input type="text" class="form-control" placeholder="Insert accent"
                                                   v-model="objWordTimeForms.future.accent"
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Word description -->
                            <div class="form-group">
                                <label for="word_description" class="col-form-label">Word description</label>
                                <textarea v-model="description" class="form-control" id="word_description" placeholder="Insert description word"></textarea>
                            </div>

                            <!-- button save -->
                            <div class="button_footer">
                                <button type="button" class="btn btn-primary"
                                        :class="{'un_active': $v.$invalid, 'active2': !$v.$invalid}"
                                        :disabled="$v.$invalid"
                                        @click="createWord"
                                >Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modals обновить слово  -->
        <div class="modal fade" id="update_word" tabindex="-1" role="dialog" aria-labelledby="update_word" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update word</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="#">
                            <!-- word -->
                            <div class="form-group">
                                <label for="old_word" class="col-form-label">Update word</label>
                                <input type="text" class="form-control entry-field-help" placeholder="Insert word" id="old_word"
                                       v-model="new_word"
                                       @blur="touchNewWord()"
                                       @keyup="searchHelpWord"
                                       :class="{'is-invalid': $v.new_word.$error}"
                                       required
                                >
                                <help-search-word :help-dynamic="help_dynamic"/>
                                <div class="invalid-feedback" v-if="!$v.new_word.required">The field is empty!</div>
                                <div class="invalid-feedback" v-if="(!$v.new_word.minLength)">Number of characters {{ this.new_word.length }} less needed</div>
                            </div>

                            <!-- translation word -->
                            <div class="form-group">
                                <label for="update_translation" class="col-form-label">Translation</label>
                                <input type="text" class="form-control" placeholder="Insert translation" id="update_translation"
                                       v-model="translation_word"
                                       @blur="touchTranslationWord()"
                                       :class="{'is-invalid': $v.translation_word.$error}"
                                       required
                                >
                                <div class="invalid-feedback" v-if="!$v.translation_word.required">The field is empty!</div>
                                <div class="invalid-feedback" v-if="(!$v.translation_word.minLength)">Number of characters {{ this.translation_word.length }} less needed</div>
                            </div>

                            <!-- url image from out source -->
                            <div class="form-group">
                                <label for="update_url_image" class="col-form-label">Url image</label>
                                <input type="text" class="form-control" placeholder="Input url" id="update_url_image"
                                       v-model="url_image"
                                >
                            </div>

                            <!-- select type word -->
                            <div class="block_type">
                                <!-- select значений -->
                                <div class="box-left-site">
                                    <div class="form-group">
                                        <label for="update_select_type" class="col-form-label">Word type</label>
                                        <select id="update_select_type" v-model="select_type_id" class="custom-select" size="3">
                                            <option v-for="(type, key) in allTypes" :key="key"
                                                    :value="type.id"
                                            >
                                                {{type.type}}
                                            </option>
                                        </select>
                                    </div>
                                    <!-- добавление этого типа слову из таблицы -->
                                    <button type="button" class="btn btn-primary"
                                            v-if="objUpdateWord !== null && objUpdateWord.time_forms !== null && !objWordFromTable.bool_click_button_word_from_table"
                                            @click="objWordFromTable.bool_click_button_word_from_table = true"
                                    >Добавить этот тип слову из таблицы</button>
                                    <!-- блок ввода слова которому добавить этот тип -->
                                    <div class="box-input-add-type-word-from-table"
                                         v-if="objWordFromTable.bool_click_button_word_from_table"
                                    >
                                        <input type="text" class="form-control" placeholder="Insert word"
                                               v-model="objWordFromTable.word"
                                        >
                                        <button type="button" class="btn btn-primary"
                                                @click="addTypeWordFromTable"
                                        >Добавить</button>
                                    </div>
                                </div>

                                <!-- правый блок свойств -->
                                <div class="desc_type">
                                    <div class="text"></div>
                                    <div class="box-time-forms" v-if="objWordTimeForms !== null">
                                        <!-- прошедшее -->
                                        <div class="box-past">
                                            <label>Past time</label>
                                            <input type="text" class="form-control" placeholder="Insert word"
                                                   v-model="objWordTimeForms.past.word"
                                            >
                                            <input type="text" class="form-control" placeholder="Insert translation"
                                                   v-model="objWordTimeForms.past.translation"
                                            >
                                            <input type="text" class="form-control" placeholder="Insert accent"
                                                   v-model="objWordTimeForms.past.accent"
                                            >
                                        </div>
                                        <!-- настоящее -->
                                        <div class="box-present">
                                            <label>Present time</label>
                                            <input type="text" class="form-control" placeholder="Insert word"
                                                   v-model="objWordTimeForms.present.word"
                                            >
                                            <input type="text" class="form-control" placeholder="Insert translation"
                                                   v-model="objWordTimeForms.present.translation"
                                            >
                                            <input type="text" class="form-control" placeholder="Insert accent"
                                                   v-model="objWordTimeForms.present.accent"
                                            >
                                        </div>
                                        <!-- будущее -->
                                        <div class="box-future">
                                            <label>Future time</label>
                                            <input type="text" class="form-control" placeholder="Insert word"
                                                   v-model="objWordTimeForms.future.word"
                                            >
                                            <input type="text" class="form-control" placeholder="Insert translation"
                                                   v-model="objWordTimeForms.future.translation"
                                            >
                                            <input type="text" class="form-control" placeholder="Insert accent"
                                                   v-model="objWordTimeForms.future.accent"
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Word description -->
                            <div class="form-group">
                                <label for="update_word_description" class="col-form-label">Word description</label>
                                <textarea v-model="description" class="form-control" id="update_word_description" placeholder="Insert description word"></textarea>
                            </div>

                            <!-- button save -->
                            <div class="button_footer">
                                <button type="button" class="btn btn-primary"
                                        :class="{'un_active': $v.$invalid, 'active2': !$v.$invalid}"
                                        :disabled="$v.$invalid"
                                        @click="updateWord"
                                >Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modals учить слова  -->
        <div class="modal fade" id="learn_word" tabindex="-1" role="dialog" aria-labelledby="learn_word_label" aria-hidden="true">
            <div class="modal-dialog custom-modal" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Learn words</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <template v-if="obj_learn_word !== null">
                            <div class="box-word">
                                <!-- Изучаемое слово -->
                                <div class="learn-word-trigger" v-text="obj_learn_word.word"></div>
                                <!-- Не знаю -->
                                <a class="btn btn-success" role="button"
                                   @click="loadLearnWord('up')"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M214.6 41.4c-12.5-12.5-32.8-12.5-45.3 0l-160 160c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L160 141.2V448c0 17.7 14.3 32 32 32s32-14.3 32-32V141.2L329.4 246.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3l-160-160z"/></svg>
                                    не знаю
                                </a>
                                <!-- Знаю -->
                                <a class="btn btn-warning" role="button"
                                   @click="loadLearnWord('down')"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M169.4 470.6c12.5 12.5 32.8 12.5 45.3 0l160-160c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L224 370.8 224 64c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 306.7L54.6 265.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l160 160z"/></svg>
                                    знаю
                                </a>
                            </div>
                            <div class="box-helper" v-if="obj_learn_word.sentences.length > 0 || obj_learn_word.url_image !== null">
                                <!-- изображение слова -->
                                <img v-if="obj_learn_word.url_image !== null" :src="obj_learn_word.url_image" alt="Image">
                                <!-- предложения -->
                                <div class="box-sentences" v-if="obj_learn_word.sentences.length > 0">
                                    <div class="sentence"
                                         v-for="(sentence, key) in obj_learn_word.sentences" :key="key"
                                    >{{sentence.sentence}}</div>
                                </div>
                            </div>
                        </template>
                        <template v-else>
                            <div class="no-word">
                                There are no words in the database to study
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
    // hover alert text
    import "tippy.js/themes/light-border.css";
    import { tippy } from "vue-tippy";
    // validate
    import { required, minLength } from 'vuelidate/lib/validators'
    // table
    import 'vue-good-table/dist/vue-good-table.css'
    import { VueGoodTable } from 'vue-good-table';
    // mixins
    import good_table_mixin from "../../mixins/good_table_mixin";
    import response_methods_mixin from "../../mixins/response_methods_mixin";
    import help_search_word_mixin from "../../mixins/help_search_word_mixin";

    import helpSearchWord from "../details/HelpSearchWord";

    export default {
        data() {
            return {
                obj_learn_word: null, // изучаемое слово
                last_updated_at: null, // дата изменения изучаемого слова
                learn_words: false, // bool открытия модалки изучения слов
                word_id: 0,
                type_id: 0,
                new_word: '',
                translation_word: '',
                url_image: '',
                description: '',
                select_type_id: 0,
                table: {
                    // max rows in database
                    totalRecords: 0,
                    origin_rows: [],
                    translation: '',
                    description: '',
                    // settings title
                    columns: [
                        {
                            tdClass: 'id_td',
                            label: 'Letter',
                            field: 'letter',
                            width: '5%',
                        },
                        {
                            tdClass: 'text_td',
                            label: 'Word',
                            width: '19%',
                            sortable: false,
                            html: true,
                            field: (val) => {
                                return '' +
                                    '<div class="trigger">'+val.word1+'</div>' +
                                    '<div class="btn_block_column">' +
                                    '<a class="btn btn-danger btn_word delete" role="button"><span class="far fa-trash-alt"></span></a>' +
                                    '<a class="btn btn-warning btn_word edit" role="button"><span class="fa fa-edit"></span></a>' +
                                    '</div>';
                            }
                        },
                        {
                            tdClass: 'text_td',
                            label: 'Word',
                            width: '19%',
                            sortable: false,
                            html: true,
                            field: (val) => {
                                return '' +
                                    '<div class="trigger">'+val.word2+'</div>' +
                                    '<div class="btn_block_column">' +
                                    '<a class="btn btn-danger btn_word delete" role="button"><span class="far fa-trash-alt"></span></a>' +
                                    '<a class="btn btn-warning btn_word edit" role="button"><span class="fa fa-edit"></span></a>' +
                                    '</div>';
                            }
                        },
                        {
                            tdClass: 'text_td',
                            label: 'Word',
                            width: '19%',
                            sortable: false,
                            html: true,
                            field: (val) => {
                                return '' +
                                    '<div class="trigger">'+val.word3+'</div>' +
                                    '<div class="btn_block_column">' +
                                    '<a class="btn btn-danger btn_word delete" role="button"><span class="far fa-trash-alt"></span></a>' +
                                    '<a class="btn btn-warning btn_word edit" role="button"><span class="fa fa-edit"></span></a>' +
                                    '</div>';
                            }
                        },
                        {
                            tdClass: 'text_td',
                            label: 'Word',
                            width: '19%',
                            sortable: false,
                            html: true,
                            field: (val) => {
                                return '' +
                                    '<div class="trigger">'+val.word4+'</div>' +
                                    '<div class="btn_block_column">' +
                                    '<a class="btn btn-danger btn_word delete" role="button"><span class="far fa-trash-alt"></span></a>' +
                                    '<a class="btn btn-warning btn_word edit" role="button"><span class="fa fa-edit"></span></a>' +
                                    '</div>';
                            }
                        },
                        {
                            tdClass: 'text_td',
                            label: 'Word',
                            width: '19%',
                            sortable: false,
                            html: true,
                            field: (val) => {
                                return '' +
                                    '<div class="trigger">'+val.word5+'</div>' +
                                    '<div class="btn_block_column">' +
                                    '<a class="btn btn-danger btn_word delete" role="button"><span class="far fa-trash-alt"></span></a>' +
                                    '<a class="btn btn-warning btn_word edit" role="button"><span class="fa fa-edit"></span></a>' +
                                    '</div>';
                            }
                        },
                    ],
                    // array objects rows
                    rows: [],
                    // settings paginate
                    optionsPaginate: {
                        enabled: true,
                        perPageDropdown: [50, 100],
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
                allTypes: [],
                allColor: [],
                objWordTimeForms: null,
                objUpdateWord: null,
                objWordFromTable: {
                    bool_click_button_word_from_table: false,
                    word: '',
                },
            };
        },
        mixins: [
            response_methods_mixin,
            good_table_mixin,
            help_search_word_mixin
        ],
        components: { VueGoodTable, helpSearchWord },
        methods: {
            async createWord() {
                let data = {
                    word: this.new_word,
                    translation: this.translation_word,
                    url_image: this.url_image,
                    description: this.description,
                    type_id: this.select_type_id,
                    time_forms: this.objWordTimeForms,
                };

                try {
                    const response = await this.$http.post(`${this.$http.apiUrl()}word`, data);
                    if(this.checkSuccess(response)){
                        this.initialData();
                        $('#create_word').modal('hide');
                        $('.modal-backdrop.fade.show').remove();
                    }
                } catch (e) {
                    console.log(e);
                }
            },
            async updateWord() {
                try {
                    let data = {
                        word: this.new_word,
                        translation: this.translation_word,
                        url_image: this.url_image,
                        description: this.description,
                        time_forms: this.objWordTimeForms,
                    };

                    if(this.select_type_id !== 0){
                        data.type_id = this.select_type_id;
                    }

                    const response = await this.$http.patch(`${this.$http.apiUrl()}word/${this.word_id}`, data);
                    if(this.checkSuccess(response)){
                        this.initialData();
                        $('#update_word').modal('hide');
                        $('.modal-backdrop.fade.show').remove();
                    }
                } catch (e) {
                    console.log(e);
                }
            },
            async deleteWord(word_id) {
                let data = { id: word_id };
                try {
                    this.confirmMessage('message', 'success');
                    const response = await this.$http.post(`${this.$http.apiUrl()}word/delete-word`, data);
                    if(this.checkSuccess(response)){
                        this.$swal.close()
                        this.initialData();
                    }
                } catch (e) {
                    console.log(e);
                }
            },
            // выборка слов и типов слов
            async loadWordsAndTypes() {
                try {
                    this.isLoading = true;
                    const response = await this.$http.get(`${this.$http.apiUrl()}word?search=${this.serverParams.search}&page=${this.serverParams.page}&perPage=${this.serverParams.perPage}&sortField=${this.serverParams.sort[0].field}&sortType=${this.serverParams.sort[0].type}`);
                    if(this.checkSuccess(response)){
                        this.table.totalRecords = response.data.data.total_count;
                        this.makeObjectDataForTable(response.data.data.list);
                        this.table.origin_rows = response.data.data.list;
                        this.allTypes = response.data.data.types;
                        this.deleteColorFromArrColor(response.data.data.colors);
                    }
                } catch (e) {
                    console.log(e);
                }
                this.isLoading = false;
            },
            // добавить тип временная форма другому слову из таблицы
            async addTypeWordFromTable() {
                try {
                    let data = {
                        from_word_id: this.word_id,
                        to_word_text: this.objWordFromTable.word,
                    }
                    const response = await this.$http.post(`${this.$http.apiUrl()}word/add-type-another-word`, data);
                    if(this.checkSuccess(response)){
                        this.initialData();
                        $('#update_word').modal('hide');
                        $('.modal-backdrop.fade.show').remove();
                        this.objWordFromTable.word = ''
                    }
                } catch (e) {
                    console.log(e);
                }
            },
            // загрузка изучаемого слова
            async loadLearnWord(action = null) {
                try {
                    this.last_updated_at = this.obj_learn_word !== null ? this.obj_learn_word.updated_at : null
                    let data = {
                        last_updated_at: this.last_updated_at,
                        last_word_id: this.obj_learn_word !== null ? this.obj_learn_word.id : null,
                        action_with_word: action,
                    }
                    const response = await this.$http.post(`${this.$http.apiUrl()}learn/get-word`, data);
                    if(this.checkSuccess(response)){
                        this.obj_learn_word = response.data.data
                        this.initialData();
                    }
                } catch (e) {
                    console.log(e);
                }
            },
            // открываем модалку изучения слов
            openLearnModal() {
                this.learn_words = true;
                const element = $('#learn_word')
                element.modal('show');
                // инициализация hover на изучаемое слово
                $('body').on('mouseover', '.learn-word-trigger', (event) => {
                    this.outputHelperAlert(event, 1)
                });
                // загрузить из DB изучаемое слово
                this.loadLearnWord()
                // событие закрытия модалки
                element.on('hidden.bs.modal', () => {
                    this.learn_words = false;
                    this.obj_learn_word = null
                    this.last_updated_at = null
                });
            },
            touchNewWord() {
                this.$v.new_word.$touch();
            },
            touchTranslationWord() {
                this.$v.translation_word.$touch();
            },
            initialData() {
                this.loadWordsAndTypes();
                this.hoverWordShowTitle();
                this.showStyleDataOnSelectType();
                this.updateColumnTable();
                this.initialClickButWordUpdate();
                this.makeButtonClearSearch();
            },
            deleteColorFromArrColor(arrColor) {
                let index = 0;
                this.allColor = arrColor;
                for(let i=0; i < this.allTypes.length; i++){
                    index = this.allColor.indexOf(this.allTypes[i].color);
                    if(index !== -1){
                        this.allColor.splice(index,1)
                    }
                }
            },
            makeObjectDataForTable(list) {
                let row = {letter:"", word1:"", word2: "", word3: "", word4: "", word5: ""};
                this.table.rows = [];
                let tick = 1;
                let last_simbol = "";
                let simbol = "";

                for(let i=0; i < list.length; i++){
                    if(tick == 6){
                        row['letter'] = simbol.substring(0, simbol.length - 1);
                        simbol = '';
                        this.table.rows.push(row);
                        row = {letter:"", word1:"", word2: "", word3: "", word4: "", word5: ""};
                        tick = 1;
                    }
                    if(tick <= 5){
                        last_simbol = list[i]['word'].charAt(0).toLowerCase();
                        simbol += (simbol.indexOf(last_simbol) === -1) ? last_simbol+'-' : '';
                        row['word'+tick] = list[i]['word'].toLowerCase();
                        tick++;
                    }
                }
                row['letter'] = simbol.substring(0, simbol.length - 1);
                this.table.rows.push(row);
            },
            // methods table
            updateColumnTable(){
                let timerId = setTimeout(() => {
                    let row = '';
                    let prev = '';

                    document.querySelectorAll("#vgt-table .btn_block_column").forEach((tag) => {
                        prev = $(tag).prev();
                        // нет слова в столбце
                        if(prev.text() == ''){
                            $(tag).parent().css('display','none');
                        }
                        // преобразовать слово в столбце
                        else{
                            row = this.getRowForWord(prev.text());
                            // if (row == null || row.type == null) { return false; }
                            if(row.translation != '' && row.translation != null){
                                prev.css('color',row.type.color);
                            }
                            else{
                                prev.css({'color':'#959595','font-weight':'bold'});
                            }
                            $(tag).parent().css('display','flex');
                        }
                    });
                }, 500);
            },
            // навести на слово в таблице
            hoverWordShowTitle() {
                $('body').on('mouseover', '.trigger', (event) => {
                    this.outputHelperAlert(event, 0)
                });
            },
            // вывод подсказки при наведении
            outputHelperAlert(event, index){
                const arr = ["table","learn"]
                // выбрать колекцию слова
                let row = this.getRowForWord($(event.target).text());
                if (row == null) { return false; }

                let text_type = (row.type !== null) ? row.type.type : ""
                let text_description = (row.time_forms === null && row.type.description !== undefined) ?
                    ' - '+ row.type.description.text :
                    ""
                if(row.time_forms !== null){
                    text_description = ' - Прошлое: '+ row.time_forms.past.word + ', ' + row.time_forms.past.translation + ', ' + row.time_forms.past.accent + '.'
                    text_description += ' Настоящее: '+ row.time_forms.present.word + ', ' + row.time_forms.present.translation + ', ' + row.time_forms.present.accent + '.'
                    text_description += ' Будущее: '+ row.time_forms.future.word + ', ' + row.time_forms.future.translation + ', ' + row.time_forms.future.accent + '.'
                }
                let span_style = row.type == null ? '' : 'color:'+row.type.color

                let html = ""
                // строка html для таблицы
                if(arr[index] === "table"){
                    html = `<div style="text-align: left;">
<div style="font-weight: 700;">${row.translation == null ? '' : row.translation.toLowerCase()}
<span style="${span_style};">${text_type} ${text_description}</span>
</div>
${row.description == null ? '' : row.description.toLowerCase()}
</div>
${row.url_image != null ? `<img style="width: auto; height: 100px;" src="${row.url_image}" alt="Image">` : ''}
`;
                }
                // строка html изучения слов
                else{
                    html = `
<div style="text-align: left;">
<div style="font-weight: 700;">${row.translation == null ? '' : row.translation.toLowerCase()}</div>
${row.description == null ? '' : row.description.toLowerCase()}
</div>`;
                }

                // Получить ссылку на экземпляр tippy
                let instance = $(event.target)[0]._tippy;
                // Если экземпляр tippy существует, обновить его содержимое
                if (instance) {
                    instance.setContent(html);
                }
                else {
                    // 2 показ подсказки
                    tippy(event.target, {
                        content: html,
                        theme: 'light-border',
                        allowHTML: true,
                    });
                }
            },
            // события выборки значения в select типов слов
            showStyleDataOnSelectType(){
                // в модалке создания слова
                document.getElementById("select_type").addEventListener('change', () => {
                    for(let i=0; i < this.allTypes.length; i++){
                        if(this.allTypes[i].id === this.select_type_id){
                            this.setStyleDataModal(this.allTypes[i]);
                            break;
                        }
                    }
                });

                // в модалке обновления слова
                document.getElementById("update_select_type").addEventListener('change', () => {
                    for(let i=0; i < this.allTypes.length; i++){
                        if(this.allTypes[i].id == this.select_type_id){
                            this.setStyleDataModal(this.allTypes[i]);
                            break;
                        }
                    }
                });
            },
            // Возвращает по слову обьект слова
            getRowForWord(word){
                let row = null;
                for (let i = 0; i < this.table.origin_rows.length; i++) {
                    if (this.table.origin_rows[i].word.toLowerCase() == word) {
                        row = this.table.origin_rows[i];
                        break;
                    }
                }
                return row;
            },
            getType(id){
                for (let i = 0; i < this.allTypes.length; i++) {
                    if (this.allTypes[i].id == id) {
                        return this.allTypes[i];
                    }
                }
                return null;
            },
            // отобразить значение типа слова в правой части select выбора
            setStyleDataModal(type){
                let string = ''
                this.objWordTimeForms = null

                if(type.description == null){
                    string = ''
                }
                else{
                    if(type.description['text'] !== null){
                        string = type.description['text']
                    }
                    else{
                        this.objWordTimeForms = type.description['object']
                    }
                }
                $('.desc_type').css('border-color',type.color);
                $('.desc_type .text').html(string);
            },
            setVariableDefault(word_id=0, word='', translation='', url_image='', type_id=0, description='""', time_forms=null){
                this.word_id = word_id;
                this.new_word = word;
                this.translation_word = translation;
                this.url_image = url_image;
                this.select_type_id = type_id;
                this.description = description;
                this.objWordTimeForms = time_forms
            },
            // события клика по кнопкам - удалить или редактировать слово
            initialClickButWordUpdate(){
                let a = setTimeout(() => {
                    // удалить
                    $('.btn-danger.delete').bind('click', (e) => {
                        let queryObj = ($(e.target).prop("tagName") !== "A") ? $(e.target).parent() : $(e.target);
                        let word = queryObj.parent().prev(".trigger").text();
                        let row = this.getRowForWord(word);
                        // confirm delete
                        this.confirmMessage('Really delete word ?', 'success', row.id)
                    });
                    // редактировать
                    $('.btn-warning.edit').bind('click', (e) => {
                        let queryObj = ($(e.target).prop("tagName") !== "A") ? $(e.target).parent() : $(e.target);
                        let word = queryObj.parent().prev(".trigger").text();
                        // открытие модалки редактирования
                        this.preparingDataOpenUpdateWordModal(word)
                    });
                    // клик по изучаемому слову в модалке
                    $('.learn-word-trigger').bind('click', (e) => {
                        // Закрываем модальное окно изучения
                        $('#learn_word').modal('hide');
                        // открытие модалки редактирования
                        this.preparingDataOpenUpdateWordModal(e.target.textContent)
                    });
                }, 1000);
            },
            openModalCreateWord(){
                this.setVariableDefault();
                this.setStyleDataModal({description:null, type:'', color:'black'});
                $('#create_word').modal('show');
                $('#create_word').on('shown.bs.modal', () => {
                    this.$refs.new_word.focus();
                });
            },
            // подготовка данных для редактирования слова и открытие модалки редактирования
            preparingDataOpenUpdateWordModal(word){
                // Выбрать обьект слова по слову
                let row = this.getRowForWord(word);
                this.objUpdateWord = row
                this.setStyleDataModal(row.type);
                this.setVariableDefault(row.id, row.word, row.translation, row.url_image, row.type.id, row.description, row.time_forms);
                $('#update_word').modal('show');
            }
        },
        mounted() {
            this.initialData();
            $(".modal").on("hidden.bs.modal", () => {
                this.help_dynamic = "";
                this.objWordFromTable.bool_click_button_word_from_table = false
                this.objUpdateWord = null
            })
        },
        beforeDestroy: function () {
            $('.btn-warning').unbind('click');
            $('.btn-danger').unbind('click');
        },
        validations: {
            new_word: {
                required,
                minLength: minLength(1),
            },
            translation_word: {
                required,
                minLength: minLength(1),
            },
        },
        name: "PageListWords.vue"
    }
</script>

<style lang="scss" scoped>

.box-time-forms{
    label{
        padding: 3px 0;
        margin: 0;
    }
    .box-past, .box-present, .box-future{
        input{
            margin-bottom: 5px;
            &:last-child{
                margin: 0;
            }
        }
    }
}

#page_list_worlds{
    max-height: calc(100vh - 60px);
    overflow-y: auto;
    .top-menu{
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 15px 10px 7px;
        .box-button{
            display: flex;
            justify-content: space-between;
            align-items: center;
            button{
                margin-right: 15px;
                &:last-child{
                    margin-right: 0;
                }
            }
        }
    }
    .content-wrapper{
        .container-fluid{
            padding-right: 0;
        }
        padding-right: 15.5px;
    }
}

#learn_word{
    .custom-modal{
        width: 50%;
        max-width:3000px!important;
    }
    .box-word{
        display: flex;
        .learn-word-trigger{
            flex: 1 1 auto;
            display: flex;
            justify-content: space-between;
            padding: 2px 10px;
            line-height: 38px;
            &:hover{
                background: #f2f1f1;
                font-weight: 700;
            }
        }
        a{
            font-size: 16px;
            svg{
                height: 16px;
                margin-right: 5px;
            }
        }
        .btn-success{
            margin: 0 10px;
            svg{
                fill: white;
            }
        }
    }
    .box-helper{
        display: flex;
        margin-top: 15px;
        img{
            width: auto;
            height: 100px;
            margin-right: 15px;
        }
    }
}

</style>
