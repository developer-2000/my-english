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
    import good_table_mixin from "../../mixins/good_table_mixin";
    // sweetalerts
    import response_methods_mixin from "../../mixins/response_methods_mixin";
    // help_search_word
    import help_search_word_mixin from "../../mixins/help_search_word_mixin";
    import helpSearchWord from "../details/HelpSearchWord";
    // sound_word
    import sound_word_mixin from "../../mixins/sound_word_mixin";

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
                                return `<input data-id="${val.check_sound}" class="check" type="checkbox" id="check_${val.check_sound}">`;
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

            };
        },
        mixins: [
            response_methods_mixin,
            good_table_mixin,
            help_search_word_mixin,
            sound_word_mixin
        ],
        components: {VueGoodTable, helpSearchWord},
        methods: {
            // --- validate
            touchNewSentence() {
                this.$v.new_sentence.$touch();
            },
            touchTranslationSentence() {
                this.$v.translation_sentence.$touch();
            },
            // set all
            initialData() {
                this.loadSenteces();
                this.initialClickButSentenceUpdate();
                this.makeCheckboxTH();
                this.makeButtonClearSearch();
            },
            // --- checkbox
            makeCheckboxTH() {
                let a = setTimeout(() => {
                    $('#vgt-table th:first span').html('<input type="checkbox" id="checkbox">');
                    this.initialCheckbox();
                }, 1000);
            },
            initialCheckbox() {
                $('#checkbox').bind('click', (e) => {
                    if ($(e.target).prop('checked')) {
                        $('.check').prop('checked', true);
                    } else {
                        $('.check').prop('checked', false);
                    }
                })
            },
            // --- предложения
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
            openModalCreateSentence() {
                this.setVariableDefault();
                $('#create_sentence').modal('show');
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
