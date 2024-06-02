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
                    <button class="btn btn-outline-success" id="coll1" @click="toggleCollapse(1)">
                        Create Type
                    </button>
                    <button class="btn btn-outline-success" id="coll2" @click="toggleCollapse(2)">
                        Update Type
                    </button>
                    <button class="btn bg-gradient-primary" @click="openModalCreateWord">
                        Add word
                    </button>
                </div>
            </div>

            <!-- collapse функционал и таблица слов -->
            <div class="content-wrapper">
                <!-- collapse create -->
                <div class="collapse" id="collapse1" >
                    <div class="card card-body">
                        <div class="collapse_heder">Create Type</div>

                        <div class="group_type">
                            <!-- new type -->
                            <div class="form-group">
                                <label for="collapse_type" class="col-form-label">New type</label>
                                <input type="text" class="form-control" placeholder="Insert new type" id="collapse_type"
                                       v-model="collapse_type"
                                       @keyup="touchCollapse()"
                                       required
                                >
                            </div>
                            <!-- select type word -->
                            <div class="form-group">
                                <label for="collapse_select" class="col-form-label">Type color</label>
                                <select @change="touchCollapse()" id="collapse_select" v-model="collapse_select" class="custom-select" size="3">
                                    <option disabled>Insert color for type</option>
                                    <option v-for="(color, key) in allColor" :key="key"
                                            :style="`background-color:${color}`"
                                            :value="`${color}`">
                                        {{color}}
                                    </option>
                                </select>
                            </div>
                            <!-- Word description -->
                            <div class="form-group">
                                <label for="collapse_description" class="col-form-label">Description type</label>
                                <textarea @keyup="touchCollapse()" v-model="collapse_description" class="form-control" id="collapse_description" placeholder="Insert description type"></textarea>
                            </div>

                            <button type="button" class="btn btn-primary"
                                    :disabled="!collapse_but"
                                    @click="createType"
                            >Save</button>
                        </div>

                    </div>
                </div>
                <!-- collapse update -->
                <div class="collapse" id="collapse2">
                    <div class="card card-body">
                        <div class="collapse_heder">Update Type</div>

                        <div class="group_type">
                            <!-- select old -->
                            <div class="form-group">
                                <label for="collapse_select_old" class="col-form-label">Old type</label>
                                <select @change="touchCollapseOld()" id="collapse_select_old" v-model="collapse_select_old" class="custom-select" size="3">
                                    <option disabled>Select old type</option>
                                    <option v-for="(color, key) in allTypes" :key="key"
                                            :style="`background-color:${color.color}`"
                                            :value="`${color.color}`"
                                            @click="touchOldType(color.id)"
                                    >
                                        {{color.type}}
                                    </option>
                                </select>
                            </div>
                            <!-- name type -->
                            <div class="form-group">
                                <label for="collapse_type_new" class="col-form-label">Name type</label>
                                <input type="text" class="form-control" placeholder="Insert name type" id="collapse_type_new"
                                       v-model="collapse_type"
                                       @keyup="touchCollapseOld()"
                                       required
                                >
                            </div>
                            <!-- select type word -->
                            <div class="form-group">
                                <label for="collapse_select_new" class="col-form-label">Type color</label>
                                <select @change="touchCollapseOld()" id="collapse_select_new" v-model="collapse_select" class="custom-select" size="3">
                                    <option disabled>Insert color for type</option>
                                    <option v-for="(color, key) in allColor" :key="key"
                                            :style="`background-color:${color}`"
                                            :value="`${color}`">
                                        {{color}}
                                    </option>
                                </select>
                            </div>
                            <!-- Word description -->
                            <div class="form-group">
                                <label for="collapse_description_old" class="col-form-label">Description type</label>
                                <textarea @keyup="touchCollapseOld()" v-model="collapse_description" class="form-control" id="collapse_description_old" placeholder="Insert description type"></textarea>
                            </div>

                            <button type="button" class="btn btn-primary"
                                    :disabled="!collapse_but_old"
                                    @click="updateType"
                            >Update</button>
                        </div>

                    </div>
                </div>

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
                word_id: 0,
                type_id: 0,
                new_word: '',
                translation_word: '',
                description: '',
                select_type_id: 0,
                collapse_type: '',
                collapse_select: '',
                collapse_select_old: '',
                collapse_description: '',
                collapse_but: false,
                collapse_but_old: false,
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
                                    '<a class="btn btn-danger btn_word" role="button"><span class="far fa-trash-alt"></span></a>' +
                                    '<a class="btn btn-warning btn_word" role="button"><span class="fa fa-edit"></span></a>' +
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
                                    '<a class="btn btn-danger btn_word" role="button"><span class="far fa-trash-alt"></span></a>' +
                                    '<a class="btn btn-warning btn_word" role="button"><span class="fa fa-edit"></span></a>' +
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
                                    '<a class="btn btn-danger btn_word" role="button"><span class="far fa-trash-alt"></span></a>' +
                                    '<a class="btn btn-warning btn_word" role="button"><span class="fa fa-edit"></span></a>' +
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
                                    '<a class="btn btn-danger btn_word" role="button"><span class="far fa-trash-alt"></span></a>' +
                                    '<a class="btn btn-warning btn_word" role="button"><span class="fa fa-edit"></span></a>' +
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
                                    '<a class="btn btn-danger btn_word" role="button"><span class="far fa-trash-alt"></span></a>' +
                                    '<a class="btn btn-warning btn_word" role="button"><span class="fa fa-edit"></span></a>' +
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
            touchNewWord() {
                this.$v.new_word.$touch();
            },
            touchTranslationWord() {
                this.$v.translation_word.$touch();
            },
            touchCollapse() {
                this.collapse_but = ( this.collapse_type.length >= 3 && this.collapse_select != '' ) ? true : false;
            },
            touchCollapseOld() {
                if(
                    this.collapse_type.length >= 3 &&
                    this.collapse_select_old != ''
                ){
                    this.collapse_but_old = true;
                }
                else{
                    this.collapse_but_old = false;
                }
            },
            touchOldType(id) {
                for(let i=0; i < this.allTypes.length; i++){
                    if(this.allTypes[i].id == id){
                        this.collapse_type = this.allTypes[i].type;
                        this.collapse_description = this.allTypes[i].description;
                        this.type_id = this.allTypes[i].id;
                        this.collapse_but_old = true;
                        break;
                    }
                }
            },
            initialData() {
                this.loadWordsAndTypes();
                this.hoverWordShowTitle();
                this.showStyleDataOnSelectType();
                this.updateColumnTable();
                this.initialClickButWordUpdate();
                this.makeButtonClearSearch();
            },
            async createWord() {
                let data = {
                    word: this.new_word,
                    translation: this.translation_word,
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
            async createType() {
                try {
                    let data = {
                        type: this.collapse_type,
                        color: this.collapse_select
                    };
                    if(this.collapse_description != ''){
                        data.description = this.collapse_description;
                    }
                    const response = await this.$http.post(`${this.$http.apiUrl()}type`, data);
                    if(this.checkSuccess(response)){
                        $('#collapse1').collapse('hide');
                        this.toggleCollapse(1);
                        this.initialData();
                    }
                } catch (e) {
                    console.log(e);
                }
            },
            async updateType() {
                try {
                    let data = {
                        type: this.collapse_type,
                        color: this.collapse_select == '' ? this.getType(this.type_id).color : this.collapse_select,
                    };
                    if(this.collapse_description != '' && this.collapse_description != null){
                        data.description = this.collapse_description;
                    }
                    const response = await this.$http.patch(`${this.$http.apiUrl()}type/${this.type_id}`, data);
                    if(this.checkSuccess(response)){
                        $('#collapse2').collapse('hide');
                        this.toggleCollapse(2);
                        this.initialData();
                    }
                } catch (e) {
                    console.log(e);
                }
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
                // навести на слово
                $('body').on('mouseover', '.trigger', (event) => {
                    // выбрать колекцию слова
                    let row = this.getRowForWord($(event.target).text());
                    if (row == null) { return false; }
                    let text_type = ""
                    let text_description = ""
                    let span_style = row.type == null ? '' : 'color:'+row.type.color
                    if(row.type !== null){
                        text_type = row.type.type
                    }
                    if(row.time_forms === null && row.type.description !== undefined){
                        text_description = ' - '+ row.type.description.text
                    }
                    if(row.time_forms !== null){
                        text_description = ' - Прошлое: '+ row.time_forms.past.word + ', ' + row.time_forms.past.translation + ', ' + row.time_forms.past.accent + '.'
                        text_description += ' Настоящее: '+ row.time_forms.present.word + ', ' + row.time_forms.present.translation + ', ' + row.time_forms.present.accent + '.'
                        text_description += ' Будущее: '+ row.time_forms.future.word + ', ' + row.time_forms.future.translation + ', ' + row.time_forms.future.accent + '.'
                    }

                    // 1 создать строку html
                    let html = `<div style="text-align: left;">
<div style="font-weight: 700;">${row.translation == null ? '' : row.translation.toLowerCase()}
<span style="${span_style};">${text_type} ${text_description}</span>
</div>
${row.description == null ? '' : row.description.toLowerCase()}
</div>`;

                    // 2 показ подсказки
                    tippy(event.target, {
                        content: html,
                        theme: 'light-border',
                        allowHTML: true,
                    });
                });
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
            // Возвращает по слову обьект слова из базы
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
            setVariableDefault(word_id=0, word='', translation='', type_id=0, description='""', time_forms=null){
                this.word_id = word_id;
                this.new_word = word;
                this.translation_word = translation;
                this.select_type_id = type_id;
                this.description = description;
                this.objWordTimeForms = time_forms
            },
            // события клика по кнопкам - удалить или редактировать слово
            initialClickButWordUpdate(){
                let a = setTimeout(() => {
                    // удалить
                    $('.btn-danger').bind('click', (e) => {
                        let queryObj = ($(e.target).prop("tagName") !== "A") ? $(e.target).parent() : $(e.target);
                        let word = queryObj.parent().prev(".trigger").text();
                        let row = this.getRowForWord(word);
                        // confirm delete
                        this.confirmMessage('Really delete word ?', 'success', row.id)
                    });
                    // редактировать
                    $('.btn-warning').bind('click', (e) => {
                        let queryObj = ($(e.target).prop("tagName") !== "A") ? $(e.target).parent() : $(e.target);
                        let word = queryObj.parent().prev(".trigger").text();
                        let row = this.getRowForWord(word);
                        this.objUpdateWord = row
                        this.setStyleDataModal(row.type);
                        this.setVariableDefault(row.id, row.word, row.translation, row.type.id, row.description, row.time_forms);
                        $('#update_word').modal('show');
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
            toggleCollapse(num) {
                let arr = ['collapse1', 'collapse2'];

                // закрыть другой и обратное действие выбранному
                arr.forEach((element) => {
                    if ('collapse' + num != element) {
                        $('#' + element).collapse('hide');
                        arr.splice(arr.indexOf(element), 1);
                        return;
                    }
                });
                $('#collapse' + num).collapse('toggle');

                // изменить заливку кнопок
                let num2 = num == 1 ? 2 : 1;
                $('#coll' + num2).attr('class', 'btn btn-outline-success');

                if ($('#coll' + num).hasClass("btn-outline-success")) {
                    $('#coll' + num).attr('class', 'btn bg-gradient-success');
                } else {
                    $('#coll' + num).attr('class', 'btn btn-outline-success');
                }

                this.collapse_type = '';
                this.collapse_select = '';
                this.collapse_select_old = '';
                this.collapse_description = '';
                this.collapse_but = false;
                this.collapse_but_old = false;
            }
        },
        mounted() {
            this.initialData();

            $(".modal").on("hidden.bs.modal", () => {
                this.help_dynamic = "";
                this.objWordFromTable.bool_click_button_word_from_table = false
                this.objUpdateWord = null
            })
            // $('#collapseExample1').collapse('hide');
            // $('#collapseExample2').collapse('hide');
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
        padding: 10px 7px;
        .box-button{
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 340px;
        }
    }
    #collapse1,
    #collapse2{
        .card{
            box-shadow: none;
            margin: 0;
            border: none;
            padding: 10px;
            .collapse_heder{
                margin: 0 0 15px;
            }
        }
        .group_type{
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
            justify-content: flex-start;
            align-content: stretch;
            align-items: flex-start;
            .form-group{
                width: 20%;
                margin-right: 20px;
            }
            button{
                margin-top: 40px;
            }
            textarea{
                height: 86px;
            }
        }
    }
    #collapse_select,
    #collapse_select_old,
    #collapse_select_new {
        padding: 0;
    }
}

</style>
