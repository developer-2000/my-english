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
                    <!-- learn words -->
                    <button class="btn bg-gradient-success"
                            @click="openLearnModal()"
                            v-if="!bool_learn_words"
                    >
                        Learn words
                    </button>
                    <!-- stop learn -->
                    <button class="btn bg-gradient-warning"
                            v-if="bool_learn_words"
                            @click="bool_learn_words = false"
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
        <div class="modal fade" id="create_word" tabindex="-1" role="dialog"
             aria-labelledby="create_word" aria-hidden="true"
        >
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <!-- header -->
                    <div class="modal-header">
                        <h5 class="modal-title" v-if="!objGenerateSentences.boolAddSentences">Create new word</h5>
                        <h5 class="modal-title" v-else>Loading generate sentences</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <!-- body -->
                    <div class="modal-body">
                        <!-- Чекбоксы сгенерированных предложений -->
                        <div class="box-view-generate-sentences"
                             :class="{ 'visible-generate-sentences': objGenerateSentences.boolAddSentences }"
                        >
                            <!-- Индикатор загрузки предложений -->
                            <div class="dots-loader"
                                 v-if="!objGenerateSentences.boolLoadingIndicator"
                            >
                                <div class="dot"></div>
                                <div class="dot"></div>
                                <div class="dot"></div>
                            </div>
                            <!-- Сгенерированные предложения -->
                            <div class="box-new-sentence"
                                 v-for="(obj, key) in objGenerateSentences.arrGenerateSentences" :key="key"
                            >
                                <input type="checkbox" class="form-check-input" :value="obj" v-model="objGenerateSentences.selectedSentences">
                                <div class="box-sentence">
                                    <div class="original-sentence" v-text="obj.original"></div>
                                    <div class="translation-sentence" v-text="obj.translated"></div>
                                </div>
                            </div>
                        </div>
                        <!-- Поля создаваемого слова -->
                        <form action="#" v-show="!objGenerateSentences.boolAddSentences">
                            <!-- new word -->
                            <div class="form-group">
                                <label for="new_word" class="col-form-label">New word</label>
                                <input type="text"
                                       class="form-control entry-field-help"
                                       placeholder="Insert new word"
                                       id="new_word"
                                       ref="new_word"
                                       v-model="new_word"
                                       @blur="touchNewWord()"
                                       @keyup="searchHelpWord(new_word)"
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
                                <textarea class="form-control"
                                          id="word_description"
                                          placeholder="Insert description word"
                                          v-model="description"
                                ></textarea>
                            </div>

                            <!-- Предложения -->
                            <div class="box-content-sentences">
                                <!-- переключатель добавления предложений -->
                                <input
                                    type="checkbox"
                                    ref="toggle1"
                                    data-toggle="toggle"
                                    data-on="Add Sentences"
                                    data-off="No Sentences"
                                    class="toggle-input"
                                />
                            </div>
                        </form>
                    </div>
                    <!-- footer -->
                    <div class="modal-footer">
                        <!-- button save -->
                        <div class="button_footer">
                            <!-- save -->
                            <button type="button" class="btn btn-primary"
                                    :class="{'un_active': $v.$invalid, 'active2': !$v.$invalid}"
                                    :disabled="$v.$invalid"
                                    @click="createWord"
                                    v-if="!objGenerateSentences.status_toggle || objGenerateSentences.boolAddSentences"
                            >Create</button>
                            <!-- next to generate sentences-->
                            <button type="button" class="btn btn-success"
                                    :class="{'un_active': $v.$invalid, 'active2': !$v.$invalid}"
                                    :disabled="$v.$invalid"
                                    @click="loadGenerateSentences()"
                                    v-if="objGenerateSentences.status_toggle && !objGenerateSentences.boolAddSentences"
                            >Next</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modals обновить слово  -->
        <div class="modal fade" id="update_word" tabindex="-1" role="dialog"
             aria-labelledby="update_word" aria-hidden="true"
        >
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <!-- header -->
                    <div class="modal-header">
                        <h5 class="modal-title" v-if="!objGenerateSentences.boolAddSentences">Update word</h5>
                        <h5 class="modal-title" v-else>Loading generate sentences</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <!-- body -->
                    <div class="modal-body">
                        <!-- Чекбоксы сгенерированных предложений -->
                        <div class="box-view-generate-sentences"
                            :class="{ 'visible-generate-sentences': objGenerateSentences.boolAddSentences }"
                        >
                            <!-- Индикатор загрузки предложений -->
                            <div class="dots-loader"
                                 v-if="!objGenerateSentences.boolLoadingIndicator"
                            >
                                <div class="dot"></div>
                                <div class="dot"></div>
                                <div class="dot"></div>
                            </div>
                            <!-- Сгенерированные предложения -->
                            <div class="box-new-sentence"
                                 v-for="(obj, key) in objGenerateSentences.arrGenerateSentences" :key="key"
                            >
                                <input type="checkbox" class="form-check-input" :value="obj" v-model="objGenerateSentences.selectedSentences">
                                <div class="box-sentence">
                                    <div class="original-sentence" v-text="obj.original"></div>
                                    <div class="translation-sentence" v-text="obj.translated"></div>
                                </div>
                            </div>
                        </div>
                        <!-- Поля создаваемого слова -->
                        <form action="#" v-show="!objGenerateSentences.boolAddSentences">
                            <!-- word -->
                            <div class="form-group">
                                <label for="old_word" class="col-form-label">Update word</label>
                                <input type="text" class="form-control entry-field-help" placeholder="Insert word" id="old_word"
                                       v-model="new_word"
                                       @blur="touchNewWord()"
                                       @keyup="searchHelpWord(new_word)"
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

                            <!-- Предложения -->
                            <div class="box-content-sentences">
                                <!-- предложения этого слова -->
                                <div class="box-sentences">
                                    <div v-for="(sentence, key) in arrSentences" :key="key">
                                        {{sentence.sentence}}
                                    </div>
                                </div>

                                <!-- переключатель добавления предложений -->
                                <input
                                    type="checkbox"
                                    ref="toggle2"
                                    data-toggle="toggle"
                                    data-on="Add Sentences"
                                    data-off="No Sentences"
                                    class="toggle-input"
                                />
                            </div>
                        </form>
                    </div>
                    <!-- footer -->
                    <div class="modal-footer">
                        <!-- button save -->
                        <div class="button_footer">
                            <!-- save -->
                            <button type="button" class="btn btn-primary"
                                    :class="{'un_active': $v.$invalid, 'active2': !$v.$invalid}"
                                    :disabled="$v.$invalid"
                                    @click="updateWord"
                                    v-if="!objGenerateSentences.status_toggle || objGenerateSentences.boolAddSentences"
                            >Update</button>
                            <!-- next to generate sentences-->
                            <button type="button" class="btn btn-success"
                                    :class="{'un_active': $v.$invalid, 'active2': !$v.$invalid}"
                                    :disabled="$v.$invalid"
                                    @click="loadGenerateSentences()"
                                    v-if="objGenerateSentences.status_toggle && !objGenerateSentences.boolAddSentences"
                            >Next</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modals изучения слов  -->
        <ModalLearnWord
            ref="modalLearnWord"
            @callInitialData="initialData"
            @callOpenUpdateWordModal="openUpdateWordModal"
        ></ModalLearnWord>

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
    // components
    import ModalLearnWord from "../details/ModalLearnWord";

    import 'bootstrap-toggle/js/bootstrap-toggle.min.js';
    import 'bootstrap-toggle/css/bootstrap-toggle.min.css';
    import $ from 'jquery';

    export default {
        data() {
            return {
                objGenerateSentences: {
                    status_toggle: false,         // Статус on/off кнопки добавления предложений к слову
                    boolAddSentences: false,      // Статус нажатой кнопки Next для генерации предложений
                    boolLoadingIndicator: false,  // Статус индикатора загрузки предложений в клиент
                    arrGenerateSentences: [],     // Сгенерированные предложения
                    selectedSentences: []         // Выбранные чекбоксы предложений
                },
                bool_learn_words: false, // bool открытия модалки изучения слов
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
                arrSentences: [],
            };
        },
        mixins: [
            response_methods_mixin,
            good_table_mixin,
            help_search_word_mixin
        ],
        components: { VueGoodTable, helpSearchWord, ModalLearnWord },
        methods: {
            async createWord() {
                let data = {
                    word: this.new_word,
                    translation: this.translation_word,
                    url_image: this.url_image,
                    description: this.description,
                    type_id: this.select_type_id,
                    time_forms: this.objWordTimeForms,
                    arr_new_sentences: this.objGenerateSentences.selectedSentences,
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
                        arr_new_sentences: this.objGenerateSentences.selectedSentences,
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
            // выборка слов и типов слов с пагинацией
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
            // выбрать все предложения с этим словом
            async searchSentences(word) {
                let data = { word: word };

                try {
                    const response = await this.$http.post(`${this.$http.apiUrl()}sentence/search-sentences`, data);
                    if(this.checkSuccess(response)){
                        this.arrSentences = response.data.data.sentences
                    }
                } catch (e) {
                    console.log(e);
                }
            },
            async loadGenerateSentences(){
                this.objGenerateSentences.boolAddSentences = true
                this.objGenerateSentences.boolLoadingIndicator = false
                let data = {
                    arr_words: Array.isArray(this.new_word) ? this.new_word : [this.new_word],
                };

                try {
                    const response = await this.$http.post(`${this.$http.apiUrl()}ai/generate-sentences`, data);
                    if(this.checkSuccess(response)){
                        this.objGenerateSentences.arrGenerateSentences = response.data.data.sentences
                        this.objGenerateSentences.boolLoadingIndicator = true
                    }
                } catch (e) {
                    console.log(e);
                }
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
                this.closeAllModals()
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
                    this.outputHelperAlertInTable(event)
                });
            },
            // вывод подсказки при наведении на слово в таблице
            outputHelperAlertInTable(event){
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

                // строка html для таблицы
                let html = `<div style="text-align: left;">
<div style="font-weight: 700;">${row.translation == null ? '' : row.translation.toLowerCase()}
<span style="${span_style};">${text_type} ${text_description}</span>
</div>
${row.description == null ? '' : row.description.toLowerCase()}
</div>
${row.url_image != null ? `<img style="width: auto; height: 100px;" src="${row.url_image}" alt="Image">` : ''}
`;

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
                        this.openUpdateWordModal(word)
                    });
                }, 1000);
            },
            // Открыть модалку создания
            openModalCreateWord(){
                this.setVariableDefault();
                this.setStyleDataModal({description:null, type:'', color:'black'});
                $('#create_word').modal('show');
                $('#create_word').on('shown.bs.modal', () => {
                    this.$refs.new_word.focus();
                });
            },
            // Открыть модалку редактирования
            openUpdateWordModal(word){
                // Выбрать обьект слова по слову
                let row = this.getRowForWord(word);
                this.objUpdateWord = row
                this.setStyleDataModal(row.type);
                this.setVariableDefault(row);
                this.searchSentences(word)
                $('#update_word').modal('show');
            },
            // Закрыть любую модалку
            closeAllModals(){
                $(".modal").on("hidden.bs.modal", () => {
                    this.help_dynamic = "";
                    this.objWordFromTable.bool_click_button_word_from_table = false
                    this.objUpdateWord = null
                    this.clearGenerateSentences()
                })
            },
            // Открыть модалку изучения слова
            openLearnModal() {
                // Вызов openLearnModal у дочернего компонента через референцию
                this.$refs.modalLearnWord.openLearnModal();
                this.bool_learn_words = true;
                // событие закрытия модалки
                $('#learn_word').on('hidden.bs.modal', () => {
                    this.bool_learn_words = false;
                })
            },
            // заполнение переменных для модалок создания и редактирования слова
            setVariableDefault(obj = {id: 0, word: '', translation: '', url_image: '', type: {id: 0}, description: '""', time_forms: null}){
                this.word_id = obj.id || 0;
                this.new_word = obj.word || '';
                this.translation_word = obj.translation || '';
                this.url_image = obj.url_image || '';
                this.select_type_id = obj.type.id || 0;
                this.description = obj.description || '""';
                this.objWordTimeForms = obj.time_forms || null;
            },
            // инициализация toggle
            initialiseToggle() {
                // Инициализация toggle
                $(this.$refs.toggle1).bootstrapToggle();
                // Устанавливаем стили после инициализации
                this.setToggleStyles(this.$refs.toggle1);
                // Добавляем обработчик событий для вывода значения в консоль
                $(this.$refs.toggle1).change(this.logToggleState);
                // Инициализация toggle
                $(this.$refs.toggle2).bootstrapToggle();
                // Устанавливаем стили после инициализации
                this.setToggleStyles(this.$refs.toggle2);
                // Добавляем обработчик событий для вывода значения в консоль
                $(this.$refs.toggle2).change(this.logToggleState);
            },
            // задает CSS кнопке переключателю toggle
            setToggleStyles(toggleElement) {
                const parentDiv = toggleElement.closest('.toggle');
                if (parentDiv) {
                    parentDiv.style.minWidth = '101px';
                    parentDiv.style.minHeight = '50px';

                    let handle = parentDiv.querySelector('.toggle-handle');
                    if (handle) {
                        handle.style.minWidth = '19px';
                        handle.style.border = '1px solid #ccc';
                        handle.style.padding = '0';
                    }

                    handle = parentDiv.querySelector('.toggle-off');
                    if (handle) {
                        handle.style.paddingLeft = '5px';
                        handle.style.paddingRight = '0';
                        handle.style.lineHeight = '18px';
                    }

                    handle = parentDiv.querySelector('.toggle-on');
                    if (handle) {
                        handle.style.paddingRight = '21px';
                        handle.style.lineHeight = '18px';
                    }
                }
            },
            // при изменении toggle
            logToggleState(event) {
                this.objGenerateSentences.status_toggle = $(event.target).prop('checked');
            },
            clearGenerateSentences() {
                this.objGenerateSentences.status_toggle = false
                this.objGenerateSentences.boolAddSentences = false
                this.objGenerateSentences.boolLoadingIndicator = false
                this.objGenerateSentences.selectedSentences = []
                this.objGenerateSentences.arrGenerateSentences = []
                // Снятие checked состояния после инициализации
                $(this.$refs.toggle1).prop('checked', false).change();
                $(this.$refs.toggle2).prop('checked', false).change();
            },
        },
        mounted() {
            this.initialData();
            this.initialiseToggle()

            // todo убрать
            // this.objGenerateSentences.arrGenerateSentences = [
            //     { original: "The happy couple married on the beach.", translated: "Счастливая пара поженилась на пляже." },
            //     { original: "The prince married a beautiful princess.", translated: "Принц женился на прекрасной принцессе." },
            //     { original: "They married in secret, away from prying eyes.", translated: "Они поженились тайно, вдали от посторонних глаз." },
            //     { original: "The lonely man married his cat, but no one else knew.", translated: "Одинокий мужчина женился на своей кошке, но больше никто об этом не знал." },
            //     { original: "The wizard married his broomstick, and everyone thought it was strange.", translated: "Волшебник женился на своей метле, и всем показалось это странным." }
            // ];
        },
        beforeDestroy: function () {
            $('.btn-warning').unbind('click');
            $('.btn-danger').unbind('click');

            // Удаляем инициализацию перед уничтожением компонента
            $(this.$refs.toggle1).bootstrapToggle('destroy');
            $(this.$refs.toggle2).bootstrapToggle('destroy');
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

#page_list_worlds{
    max-height: calc(100vh - 60px);
    overflow-y: auto;
    .wrapper{
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
    .modal{
        .modal-body{
            overflow: hidden;
            padding: 1rem 0;
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
            .box-content-sentences{
                display: flex;
                justify-content: space-between;
                align-items: flex-end;
                margin-bottom: 1rem;
                .box-sentences{
                    div{
                        color: #747474;
                        font-weight: 700;
                        font-size: 13px;
                    }
                }
            }

            .box-view-generate-sentences{
                width: 100%;
                height: 100%;
                background-color: rgba(255, 255, 255, 0.8); /* Полупрозрачный белый фон */
                backdrop-filter: blur(10px); /* Размытие фона */
                position: absolute;
                left: 100%;
                top: 0;
                right: 0;
                bottom: 0;
                z-index: 1;
                transition: left 0.3s ease-in-out;
                .dots-loader {
                    display: flex;
                    justify-content: space-between;
                    width: 80px;
                    position: absolute;
                    left: 50%;
                    top: 50%;
                    transform: translate(-50%, -50%);
                    .dot {
                        width: 20px;
                        height: 20px;
                        background-color: #3498db;
                        border-radius: 50%;
                        animation: bounce 1.5s infinite ease-in-out;
                        &:nth-child(2) { animation-delay: -0.5s; }
                        &:nth-child(3) { animation-delay: -1s; }
                        @keyframes bounce {
                            0%, 100% { transform: scale(0); }
                            50% { transform: scale(1); }
                        }
                    }
                }
                .box-new-sentence{
                    display: flex;
                    align-items: center;
                    padding: 6px 15px;
                    border-bottom: 1px solid #e9ecef;
                    &:last-child{
                        border: none;
                    }
                    .form-check-input{
                        padding: 0;
                        position: static;
                        cursor: pointer;
                        margin: 0 15px 0 0;
                        min-width: 16px;
                        min-height: 16px;
                    }
                    .box-sentence{
                        div{
                            line-height: 23px;
                            &:first-child{
                                margin-bottom: 3px;
                            }
                        }
                        .original-sentence{
                            font-weight: 700;
                            font-size: 17px;
                        }
                        .translation-sentence{
                            color: #525252;
                        }
                    }
                }
            }
            .visible-generate-sentences{
                left: 0;
                position: relative;
            }
            form{
                padding: 0 1rem;
            }
        }
    }
    #create_word{
        .modal-body{
            .box-content-sentences{
                justify-content: flex-end;
            }
        }
    }
}

</style>
