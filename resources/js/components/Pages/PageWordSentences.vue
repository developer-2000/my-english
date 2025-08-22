<template>
    <div id="page_list_sentences" class="page-word-sentences">
        <div class="container-fluid">
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <div class="space-y-1">
                    <h1 class="text-3xl font-bold tracking-tight">
                        {{ $t('all.list_sentences') }}
                    </h1>
                    <p class="text-muted-foreground">
                        Управление предложениями и их переводами
                    </p>
                </div>

                <div class="flex items-end space-x-3">
                    <!-- кнопки озвучки предложений -->
                    <div class="box-sound" v-show="getCodeLearnLanguage2 == 'en'">
                        <div id="block_repeat" v-if="!speak.start" class="flex items-center space-x-3">
                            <div class="text-center">
                                <div class="title_repeat text-sm font-medium">
                                    {{ $t('all.repeat') }}
                                </div>
                                <div class="block_input_repeat flex items-center justify-center space-x-2">
                                    <input :checked="speak.repeat_bool"
                                           @change="handleRepeatCheckboxChange"
                                           class="checkbox_repeat"
                                           type="checkbox">
                                    <input class="form-control number_repeat"
                                           min="1"
                                           max="10"
                                           type="number"
                                           v-if="speak.repeat_bool"
                                           v-model="speak.count_repeat"
                                           @input="handleRepeatCountChange">
                                </div>
                            </div>
                        </div>

                        <!-- start -->
                        <button class="btn btn-success"
                                :disabled="disabled_play"
                                @click="initialSpeak"
                                v-if="!speak.start">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $t('all.sound_translation') }}
                        </button>

                        <!-- pause -->
                        <button @click="pauseReadSound"
                                class="btn btn-outline"
                                v-if="speak.start && !speak.pause">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $t('all.pause') }}
                        </button>

                        <!-- continue -->
                        <button @click="continueReadSound"
                                class="btn btn-success"
                                v-if="speak.pause">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $t('all.continue') }}
                        </button>

                        <!-- stop -->
                        <button @click="stopReadSound"
                                class="btn btn-destructive"
                                v-if="speak.start">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"></path>
                            </svg>
                            {{ $t('all.stop') }}
                        </button>
                    </div>

                    <!-- Создать предложение -->
                    <button @click="openModalCreateSentence"
                            class="btn btn-primary">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        {{ $t('all.add_sentence') }}
                    </button>

                    <!-- learn sentences -->
                    <button class="btn btn-secondary"
                            @click="openLearnModal()"
                            v-if="!bool_learn_sentences">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        Learn
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div v-if="table.rows.length" class="table_wrapper">
                <vue-good-table
                    :columns="filteredColumns"
                    :isLoading.sync="table.isLoading"
                    :mode="table.mode"
                    :pagination-options="{ enabled: false }"
                    :rows="table.rows"
                    :search-options="{
                                enabled: true,
                                placeholder: 'Search word',
                            }"
                    :totalRows="table.totalRecords"
                    @on-search="onSearch"
                    @on-sort-change="onSortChange"
                    styleClass="vgt-table sentence"
                >
                    <template slot="loadingContent">
                        <div></div>
                    </template>
                </vue-good-table>

                <!-- Компактная пагинация -->
                <CompactPagination
                    v-if="table.rows.length"
                    :current-page="currentPage"
                    :total="table.totalRecords"
                    :per-page="globalPerPage"
                    :per-page-options="[10, 25, 50, 100]"
                    @page-changed="onPageChange"
                    @per-page-changed="onPerPageChange"
                />
            </div>
        </div>

        <!-- Modals создать предложение -->
        <div aria-hidden="true" aria-labelledby="create_sentence" class="modal fade"
             id="create_sentence" role="dialog" tabindex="-1"
             @click.self="closeCreateSentenceModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <!-- header -->
                    <div class="modal-header">
                        <h5 class="modal-title text-lg font-semibold">
                            {{ $t('all.create_new_sentence') }}
                        </h5>
                        <button type="button" class="btn-close" @click="closeCreateSentenceModal" aria-label="Close"></button>
                    </div>
                    <!-- body -->
                    <div class="modal-body">
                        <form action="#" class="space-y-4">
                            <!-- new sentence -->
                            <div class="form-group">
                                <label class="form-label" for="new_sentence">
                                    {{ $t('all.new_sentence') }}
                                </label>
                                <textarea class="form-control entry-field-help"
                                          :class="{'is-invalid': $v.new_sentence.$error}"
                                          @blur="touchNewSentence()"
                                          @keyup="searchHelpWord(new_sentence)"
                                          @paste="handlePaste"
                                          id="new_sentence"
                                          placeholder="New sentence"
                                          required
                                          v-model="new_sentence"
                                          rows="3"></textarea>
                                <help-search-word :help-dynamic="help_dynamic"/>
                                <div class="invalid-feedback" v-if="!$v.new_sentence.required">
                                    {{ $t('all.field_is_empty') }}
                                </div>
                                <div class="invalid-feedback" v-if="(!$v.new_sentence.minLength)">
                                    {{ $t('all.number_of_characters') }}
                                    {{ this.new_sentence.length }}
                                    {{ $t('all.less_needed') }}
                                </div>
                            </div>

                            <!-- translation sentence -->
                            <div class="form-group">
                                <label class="form-label" for="translation_sentence">
                                    {{ $t('all.translation') }}
                                </label>
                                <textarea class="form-control"
                                          :class="{'is-invalid': $v.translation_sentence.$error}"
                                          @blur="touchTranslationSentence()"
                                          id="translation_sentence"
                                          placeholder="Translation sentence"
                                          required
                                          v-model="translation_sentence"
                                          rows="3"></textarea>
                                <div class="invalid-feedback" v-if="!$v.translation_sentence.required">
                                    {{ $t('all.field_is_empty') }}
                                </div>
                                <div class="invalid-feedback" v-if="(!$v.translation_sentence.minLength)">
                                    {{ $t('all.number_of_characters') }}
                                    {{ this.translation_sentence.length }}
                                    {{ $t('all.less_needed') }}
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- footer -->
                    <div class="modal-footer">
                        <button :class="{'un_active': $v.$invalid, 'active2': !$v.$invalid}"
                                :disabled="$v.$invalid"
                                @click="createSentence"
                                class="btn btn-primary"
                                type="button">
                            {{ $t('all.save') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modals обновить предложение -->
        <div aria-hidden="true" aria-labelledby="update_sentence" class="modal fade"
             id="update_sentence" role="dialog" tabindex="-1"
             @click.self="closeUpdateSentenceModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <!-- header -->
                    <div class="modal-header">
                        <h5 class="modal-title text-lg font-semibold">
                            {{ $t('all.update_sentence') }}
                        </h5>
                        <button type="button" class="btn-close" @click="closeUpdateSentenceModal" aria-label="Close"></button>
                    </div>
                    <!-- body -->
                    <div class="modal-body">
                        <form action="#" class="space-y-4">
                            <!-- sentence -->
                            <div class="form-group">
                                <label class="form-label" for="old_sentence">
                                    {{ $t('all.sentence') }}
                                </label>
                                <textarea :class="{'is-invalid': $v.new_sentence.$error}"
                                          @blur="touchNewSentence()"
                                          @keyup="searchHelpWord(new_sentence)"
                                          class="form-control entry-field-help"
                                          id="old_sentence"
                                          placeholder="Insert new sentence"
                                          required
                                          v-model="new_sentence"
                                          rows="3"></textarea>
                                <help-search-word :help-dynamic="help_dynamic"/>
                                <div class="invalid-feedback" v-if="!$v.new_sentence.required">
                                    {{ $t('all.field_is_empty') }}
                                </div>
                                <div class="invalid-feedback" v-if="(!$v.new_sentence.minLength)">
                                    {{ $t('all.number_of_characters') }}
                                    {{ this.new_sentence.length }}
                                    {{ $t('all.less_needed') }}
                                </div>
                            </div>

                            <!-- translation sentence -->
                            <div class="form-group">
                                <label class="form-label" for="old_translation">
                                    {{ $t('all.translation') }}
                                </label>
                                <textarea :class="{'is-invalid': $v.translation_sentence.$error}"
                                          @blur="touchTranslationSentence()"
                                          class="form-control"
                                          id="old_translation"
                                          placeholder="Insert translation sentence"
                                          required
                                          v-model="translation_sentence"
                                          rows="3"></textarea>
                                <div class="invalid-feedback" v-if="!$v.translation_sentence.required">
                                    {{ $t('all.field_is_empty') }}
                                </div>
                                <div class="invalid-feedback" v-if="(!$v.translation_sentence.minLength)">
                                    {{ $t('all.number_of_characters') }}
                                    {{ this.translation_sentence.length }}
                                    {{ $t('all.less_needed') }}
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- footer -->
                    <div class="modal-footer">
                        <button :class="{'un_active': $v.$invalid, 'active2': !$v.$invalid}"
                                :disabled="$v.$invalid"
                                @click="updateSentence"
                                class="btn btn-primary"
                                type="button">
                            {{ $t('all.update') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modals изучения слов  -->
        <ModalLearnSentence
            ref="modalLearnSentence"
        ></ModalLearnSentence>
    </div>
</template>

<script>

    // validate
    import {minLength, required} from 'vuelidate/lib/validators'
    // table
    import 'vue-good-table/dist/vue-good-table.css'
    import {VueGoodTable} from 'vue-good-table';
    import good_table_mixin from "../../mixins/good_table_mixin.js";
    // soft methods
    import soft_methods_mixin from "../../mixins/response_methods_mixin.js";
    // help_search_word
    import help_search_word_mixin from "../../mixins/help_search_word_mixin.js";
    import helpSearchWord from "../details/HelpSearchWord.vue";
    // sound_word
    import sound_word_mixin from "../../mixins/sound_word_mixin.js";
    // bootstrap toggle
    import BootstrapToggle from 'vue-bootstrap-toggle'
    import translation_i18n_mixin from "../../mixins/translation_i18n_mixin.js";
    import {mapGetters} from "vuex";
    import user_mixin from "../../mixins/user_mixin.js";
    import $ from "jquery";
    // components
    import ModalLearnSentence from "../details/ModalLearnSentence.vue";
    import CompactPagination from "../CompactPagination.vue";

    export default {
        data() {
            return {
                disabled_play: true,
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
                            tdClass: 'id_td',
                            label: 'ID',
                            field: 'id',
                            width: '3%',
                            sortable: false,
                        },
                        {
                            tdClass: 'checkbox_td',
                            label: 'Озвучка',
                            width: '3%',
                            html: true,
                            field: (val) => {
                                return `<input
                                            ${(val.memorable_checkbox_sound == true) ? 'checked' : ''}
                                            data-id="${val.id}" class="memorable_checkbox" type="checkbox" id="memorable_checkbox_${val.id}">`;
                            }
                        },
                        {
                            tdClass: 'text_td',
                            label: 'Предложение',
                            field: 'sentence',
                            width: '44%',
                        },
                        {
                            tdClass: 'text_td',
                            label: 'Перевод',
                            field: 'translation',
                            width: '47%',
                            sortable: false,
                        },
                        {
                            tdClass: 'but_td',
                            label: 'Правка',
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
                    page: 1,
                    perPage: this.$store ? this.$store.getters.getPerPage : 10,
                    sort: [{
                        field: '',
                        type: '',
                    }],
                },
                bool_learn_sentences: false,
            };
        },
        mixins: [
            soft_methods_mixin,
            good_table_mixin,
            help_search_word_mixin,
            sound_word_mixin,
            translation_i18n_mixin
        ],
        components: {
            VueGoodTable,
            helpSearchWord,
            BootstrapToggle,
            ModalLearnSentence,
            CompactPagination
        },
        computed: {
            ...mapGetters({
                // Геттер для получения текущего языка изучения
                getLearnLanguage: 'getLearnLanguage',
                globalPerPage: 'getPerPage'
            }),
            filteredColumns() {
                console.log('🔍 [COMPUTED] filteredColumns calculated');
                console.log('🔍 [COMPUTED] getCodeLearnLanguage2:', this.getCodeLearnLanguage2);
                const filtered = this.table.columns.filter(column => {
                    // Condition to hide the Озвучка column based on language code
                    if (column.label === 'Озвучка' && this.getCodeLearnLanguage2 !== 'en') {
                        return false;
                    }
                    return true;
                });
                console.log('🔍 [COMPUTED] filteredColumns result length:', filtered.length);
                return filtered;
            },
            currentPage() {
                console.log('🔍 [COMPUTED] currentPage calculated:', this.serverParams.page);
                return this.serverParams.page;
            }
        },
        watch: {
            getLearnLanguage: {
                handler(newVal, oldVal) {
                    console.log('🔍 [WATCHER] getLearnLanguage changed:', oldVal, '->', newVal);
                    this.learnAnotherLanguage();
                },
                immediate: false // Не Вызов loadData сразу после создания компонента
            },
            globalPerPage: {
                handler(newPerPage, oldPerPage) {
                    console.log('🔍 [WATCHER] globalPerPage changed:', oldPerPage, '->', newPerPage);
                    this.serverParams.perPage = newPerPage;
                },
                immediate: true
            }
        },
        methods: {
            async bindCheckboxSound(sentence_id, status) {
                console.log('🔍 [PAGE_WORD_SENTENCES] bindCheckboxSound called, sentence_id:', sentence_id, 'status:', status);
                try {
                    let data = {
                        sentence_id: sentence_id,
                        status: status,
                    };
                    console.log('🔍 [PAGE_WORD_SENTENCES] Making HTTP request to bind checkbox sound');
                    const response = await this.$http.post(`${this.$http.webUrl()}sentence/bind-checkbox-sound`, data);
                    console.log('🔍 [PAGE_WORD_SENTENCES] Response received:', response.status);
                    if (this.checkSuccess(response)) {
                        console.log('🔍 [PAGE_WORD_SENTENCES] Checkbox sound binding successful, calling initialData');
                        this.initialData();
                    }
                } catch (e) {
                    console.error('🔍 [PAGE_WORD_SENTENCES] Error in bindCheckboxSound:', e);
                }
            },
            // --- предложения
            async loadSentences() {
                console.log('🔍 [PAGE_WORD_SENTENCES] loadSentences called');
                console.log('🔍 [PAGE_WORD_SENTENCES] isLoading before:', this.isLoading);
                
                try {
                    this.isLoading = true;
                    let field = this.serverParams.sort[0].field;
                    // заменить название столбца для сортировки checkbox sound
                    if (typeof field !== "string" && field.name == "field") {
                        field = 'sound';
                    }

                    const url = `selection_type_id=&search=${this.serverParams.search}&page=${this.serverParams.page}&perPage=${this.serverParams.perPage}&sortField=${field}&sortType=${this.serverParams.sort[0].type}`
                    console.log('🔍 [PAGE_WORD_SENTENCES] Request URL:', url);
                    
                    console.log('🔍 [PAGE_WORD_SENTENCES] Making HTTP request');
                    const response = await this.$http.get(`${this.$http.webUrl()}sentence?${url}`
                    );
                    console.log('🔍 [PAGE_WORD_SENTENCES] Response received:', response.status);
                    
                    if (this.checkSuccess(response)) {
                        this.table.totalRecords = response.data.data.sentences.total_count;
                        console.log('🔍 [PAGE_WORD_SENTENCES] Total records:', this.table.totalRecords);
                        console.log('🔍 [PAGE_WORD_SENTENCES] Sentences list length:', response.data.data.sentences.list.length);
                        
                        this.makeObjectDataForTable(response.data.data.sentences.list);
                        this.table.origin_rows = response.data.data.sentences.list;
                        
                        console.log('🔍 [PAGE_WORD_SENTENCES] Table rows after processing:', this.table.rows.length);
                    }
                } catch (e) {
                    console.error('🔍 [PAGE_WORD_SENTENCES] Error in loadSentences:', e);
                }
                this.isLoading = false;
                console.log('🔍 [PAGE_WORD_SENTENCES] isLoading after:', this.isLoading);
            },
            async createSentence() {
                console.log('🔍 [PAGE_WORD_SENTENCES] createSentence called');
                try {
                    let data = {
                        sentence: this.new_sentence,
                        translation: this.translation_sentence,
                    };
                    console.log('🔍 [PAGE_WORD_SENTENCES] Data to save:', JSON.stringify(data));
                    
                    console.log('🔍 [PAGE_WORD_SENTENCES] Closing create modal');
                    $('#create_sentence').modal('hide');
                    $('.modal-backdrop.fade.show').remove();
                    
                    console.log('🔍 [PAGE_WORD_SENTENCES] Making HTTP request to create sentence');
                    const response = await this.$http.post(`${this.$http.webUrl()}sentence`, data);
                    console.log('🔍 [PAGE_WORD_SENTENCES] Response received:', response.status);
                    
                    if (this.checkSuccess(response)) {
                        console.log('🔍 [PAGE_WORD_SENTENCES] Sentence created successfully, calling initialData');
                        this.initialData();
                    }
                } catch (e) {
                    console.error('🔍 [PAGE_WORD_SENTENCES] Error in createSentence:', e);
                }
            },
            async updateSentence() {
                console.log('🔍 [PAGE_WORD_SENTENCES] updateSentence called');
                try {
                    let data = {
                        sentence_id: this.sentence_id,
                        sentence: this.new_sentence,
                        translation: this.translation_sentence,
                    };
                    console.log('🔍 [PAGE_WORD_SENTENCES] Data to update:', JSON.stringify(data));
                    
                    console.log('🔍 [PAGE_WORD_SENTENCES] Making HTTP request to update sentence');
                    const response = await this.$http.post(`${this.$http.webUrl()}sentence/update-sentence`, data);
                    console.log('🔍 [PAGE_WORD_SENTENCES] Response received:', response.status);
                    
                    if (this.checkSuccess(response)) {
                        console.log('🔍 [PAGE_WORD_SENTENCES] Sentence updated successfully, calling initialData');
                        this.initialData();
                        console.log('🔍 [PAGE_WORD_SENTENCES] Closing update modal');
                        $('#update_sentence').modal('hide');
                        $('.modal-backdrop.fade.show').remove();
                    }
                } catch (e) {
                    console.error('🔍 [PAGE_WORD_SENTENCES] Error in updateSentence:', e);
                }
            },
            // --- validate
            touchNewSentence() {
                this.$v.new_sentence.$touch();
            },
            touchTranslationSentence() {
                this.$v.translation_sentence.$touch();
            },
            // set all
            initialData() {
                console.log('🔍 [PAGE_WORD_SENTENCES] initialData called');
                this.loadSentences();
                this.initialClickButSentenceUpdate();
                this.initialCheckbox();
                this.makeButtonClearSearch();
            },
            // --- checkbox
            initialCheckbox() {
                console.log('🔍 [PAGE_WORD_SENTENCES] initialCheckbox called');
                this.activationButtonSoundInMenu(); // активация кнопки Sound в меню
            },
            activationButtonSoundInMenu() {
                console.log('🔍 [PAGE_WORD_SENTENCES] activationButtonSoundInMenu called');
                const timerId = setTimeout(() => {
                    console.log('🔍 [PAGE_WORD_SENTENCES] activationButtonSoundInMenu timer executed, ID:', timerId);
                    // состояние кнопки sound по умолчанию
                    this.disabled_play = $('.memorable_checkbox:checked').length ? false : true;
                    console.log('🔍 [PAGE_WORD_SENTENCES] disabled_play set to:', this.disabled_play);
                    
                    // Сначала отвязываем предыдущие обработчики
                    $(".memorable_checkbox").off('change');
                    console.log('🔍 [PAGE_WORD_SENTENCES] Previous event handlers removed');
                    
                    // изменнеие одного из sound checkbox
                    $(".memorable_checkbox").on('change', (e) => {
                        console.log('🔍 [PAGE_WORD_SENTENCES] Checkbox change event triggered');
                        this.disabled_play = $('.memorable_checkbox:checked').length ? false : true;
                        this.bindCheckboxSound($(e.target).attr('data-id'), e.target.checked);
                    });
                }, 1000);
                console.log('🔍 [PAGE_WORD_SENTENCES] activationButtonSoundInMenu timer created, ID:', timerId);
            },
            setVariableDefault(sentence_id = 0, sentence = '', translation = '') {
                console.log('🔍 [PAGE_WORD_SENTENCES] setVariableDefault called, sentence_id:', sentence_id, 'sentence:', sentence, 'translation:', translation);
                this.sentence_id = sentence_id;
                this.new_sentence = sentence;
                this.translation_sentence = translation;
            },
            getSentenceCollection(id) {
                console.log('🔍 [PAGE_WORD_SENTENCES] getSentenceCollection called, id:', id);
                let row = null;
                for (let i = 0; i < this.table.origin_rows.length; i++) {
                    if (this.table.origin_rows[i].id == id) {
                        row = this.table.origin_rows[i];
                        console.log('🔍 [PAGE_WORD_SENTENCES] Sentence found:', row);
                        break;
                    }
                }
                if (!row) {
                    console.log('🔍 [PAGE_WORD_SENTENCES] Sentence not found for id:', id);
                }
                return row;
            },
            initialClickButSentenceUpdate() {
                console.log('🔍 [PAGE_WORD_SENTENCES] initialClickButSentenceUpdate called');
                // открываем редактирование предложения
                const timerId = setTimeout(() => {
                    console.log('🔍 [PAGE_WORD_SENTENCES] initialClickButSentenceUpdate timer executed, ID:', timerId);
                    $('.btn_sentence').off('click');
                    console.log('🔍 [PAGE_WORD_SENTENCES] Previous btn_sentence handlers removed');
                    
                    $('.btn_sentence').on('click', (e) => {
                        console.log('🔍 [PAGE_WORD_SENTENCES] btn_sentence clicked');
                        let queryObj = ($(e.target).prop("tagName") !== "A") ? $(e.target).parent() : $(e.target);
                        let id = queryObj.attr("data-id");
                        console.log('🔍 [PAGE_WORD_SENTENCES] Sentence ID:', id);
                        let row = this.getSentenceCollection(id);
                        this.setVariableDefault(row.id, row.sentence, row.translation);
                        // Открываем модалку обновления предложения
                        const modalElement = document.getElementById('update_sentence');
                        if (modalElement) {
                            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                                const modal = new bootstrap.Modal(modalElement);
                                modal.show();
                            } else {
                                modalElement.style.display = 'block';
                                modalElement.classList.add('show');
                                document.body.classList.add('modal-open');
                                // Блокируем скролл body
                                document.body.style.overflow = 'hidden';
                                const backdrop = document.createElement('div');
                                backdrop.className = 'modal-backdrop fade show';
                                document.body.appendChild(backdrop);
                            }
                        }
                        this.help_dynamic = '';
                    })
                }, 1000);
                console.log('🔍 [PAGE_WORD_SENTENCES] initialClickButSentenceUpdate timer created, ID:', timerId);
            },
            openModalCreateSentence() {
                console.log('🔍 [PAGE_WORD_SENTENCES] openModalCreateSentence called');
                this.setVariableDefault();
                // Открываем модалку создания предложения
                const modalElement = document.getElementById('create_sentence');
                if (modalElement) {
                    console.log('🔍 [PAGE_WORD_SENTENCES] Create modal element found');
                    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                        const modal = new bootstrap.Modal(modalElement);
                        modal.show();
                        console.log('🔍 [PAGE_WORD_SENTENCES] Bootstrap create modal shown');
                    } else {
                        modalElement.style.display = 'block';
                        modalElement.classList.add('show');
                        document.body.classList.add('modal-open');
                        // Блокируем скролл body
                        document.body.style.overflow = 'hidden';
                        const backdrop = document.createElement('div');
                        backdrop.className = 'modal-backdrop fade show';
                        document.body.appendChild(backdrop);
                        console.log('🔍 [PAGE_WORD_SENTENCES] Create modal shown manually');
                    }
                } else {
                    console.log('🔍 [PAGE_WORD_SENTENCES] Create modal element not found');
                }
            },
            // очистка параметров пагинации
            clearServerParams(){
                console.log('🔍 [PAGE_WORD_SENTENCES] clearServerParams called');
                console.log('🔍 [PAGE_WORD_SENTENCES] Server params before clear:', JSON.stringify(this.serverParams));
                this.serverParams.search = ''
                this.serverParams.page = 0
                this.serverParams.sort[0].field = ''
                this.serverParams.sort[0].type = ''
                console.log('🔍 [PAGE_WORD_SENTENCES] Server params after clear:', JSON.stringify(this.serverParams));
            },
            // операции после смены языка изучения
            learnAnotherLanguage(){
                console.log('🔍 [PAGE_WORD_SENTENCES] learnAnotherLanguage called');
                this.clearServerParams()
                this.initialData()
            },
            handlePaste(event) {
                console.log('🔍 [PAGE_WORD_SENTENCES] handlePaste called');
                event.preventDefault();
                const pastedText = event.clipboardData.getData('text');
                console.log('🔍 [PAGE_WORD_SENTENCES] Pasted text:', pastedText);

                // Ищем пары предложений, разделенные тире с пробелами по бокам
                const parts = pastedText.split(/\s+[-–—]\s+/);
                console.log('🔍 [PAGE_WORD_SENTENCES] Parts after split:', parts);

                if (parts.length >= 2) {
                    // Очищаем и получаем первое предложение (английское)
                    const englishSentence = parts[0].trim();

                    // Очищаем и получаем второе предложение (русское)
                    const russianSentence = parts[1].trim();

                    this.new_sentence = englishSentence;
                    this.translation_sentence = russianSentence;
                    console.log('🔍 [PAGE_WORD_SENTENCES] Set english sentence:', englishSentence);
                    console.log('🔍 [PAGE_WORD_SENTENCES] Set russian sentence:', russianSentence);
                } else {
                    // Если нет разделителя, определяем язык предложения
                    const cleanedText = pastedText.trim();
                    const isRussian = /[а-яА-ЯёЁ]/.test(cleanedText);
                    console.log('🔍 [PAGE_WORD_SENTENCES] Is Russian text:', isRussian);

                    if (isRussian) {
                        this.translation_sentence = cleanedText;
                        console.log('🔍 [PAGE_WORD_SENTENCES] Set as translation:', cleanedText);
                    } else {
                        this.new_sentence = cleanedText;
                        console.log('🔍 [PAGE_WORD_SENTENCES] Set as new sentence:', cleanedText);
                    }
                }
            },
            // Открыть модалку изучения предложений
            openLearnModal() {
                console.log('🔍 [PAGE_WORD_SENTENCES] openLearnModal called');
                // Вызов openLearnModal у дочернего компонента через референцию
                this.$refs.modalLearnSentence.openLearnModal();
                this.bool_learn_sentences = true;
                console.log('🔍 [PAGE_WORD_SENTENCES] bool_learn_sentences set to:', this.bool_learn_sentences);
            },
            // Закрыть модалку создания предложения
            closeCreateSentenceModal() {
                console.log('🔍 [PAGE_WORD_SENTENCES] closeCreateSentenceModal called');
                const modalElement = document.getElementById('create_sentence');
                if (modalElement) {
                    console.log('🔍 [PAGE_WORD_SENTENCES] Create modal element found');
                    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                        const modal = bootstrap.Modal.getInstance(modalElement);
                        if (modal) {
                            modal.hide();
                            console.log('🔍 [PAGE_WORD_SENTENCES] Bootstrap create modal hidden');
                        }
                    } else {
                        modalElement.style.display = 'none';
                        modalElement.classList.remove('show');
                        document.body.classList.remove('modal-open');
                        // Восстанавливаем скролл body
                        document.body.style.overflow = '';
                        const backdrop = document.querySelector('.modal-backdrop');
                        if (backdrop) {
                            backdrop.remove();
                        }
                        console.log('🔍 [PAGE_WORD_SENTENCES] Create modal hidden manually');
                    }
                } else {
                    console.log('🔍 [PAGE_WORD_SENTENCES] Create modal element not found');
                }
            },
            // Обработчик изменения чекбокса повтора
            handleRepeatCheckboxChange() {
                console.log('🔍 [PAGE_WORD_SENTENCES] Repeat checkbox changed');
                console.log('🔍 [PAGE_WORD_SENTENCES] Previous repeat_bool:', this.speak.repeat_bool);
                this.speak.repeat_bool = !this.speak.repeat_bool;
                console.log('🔍 [PAGE_WORD_SENTENCES] New repeat_bool:', this.speak.repeat_bool);
                console.log('🔍 [PAGE_WORD_SENTENCES] count_repeat:', this.speak.count_repeat);
            },
            // Обработчик изменения количества повторений
            handleRepeatCountChange() {
                console.log('🔍 [PAGE_WORD_SENTENCES] Repeat count changed');
                console.log('🔍 [PAGE_WORD_SENTENCES] New count_repeat:', this.speak.count_repeat);
            },
            // Закрыть модалку обновления предложения
            closeUpdateSentenceModal() {
                console.log('🔍 [PAGE_WORD_SENTENCES] closeUpdateSentenceModal called');
                const modalElement = document.getElementById('update_sentence');
                if (modalElement) {
                    console.log('🔍 [PAGE_WORD_SENTENCES] Update modal element found');
                    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                        const modal = bootstrap.Modal.getInstance(modalElement);
                        if (modal) {
                            modal.hide();
                            console.log('🔍 [PAGE_WORD_SENTENCES] Bootstrap update modal hidden');
                        }
                    } else {
                        modalElement.style.display = 'none';
                        modalElement.classList.remove('show');
                        document.body.classList.remove('modal-open');
                        // Восстанавливаем скролл body
                        document.body.style.overflow = '';
                        const backdrop = document.querySelector('.modal-backdrop');
                        if (backdrop) {
                            backdrop.remove();
                        }
                        console.log('🔍 [PAGE_WORD_SENTENCES] Update modal hidden manually');
                    }
                } else {
                    console.log('🔍 [PAGE_WORD_SENTENCES] Update modal element not found');
                }
            },
        },
        created() {
            console.log('🔍 [PAGE_WORD_SENTENCES] Component created');
        },
        mounted() {
            console.log('🔍 [PAGE_WORD_SENTENCES] Component mounted');
            console.log('🔍 [LIFECYCLE] PageWordSentences component mounted');
            this.initialData();
            $(".modal").on("hidden.bs.modal", () => {
                console.log('🔍 [LIFECYCLE] Modal hidden event triggered in PageWordSentences');
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
            console.log('🔍 [LIFECYCLE] PageWordSentences component destroying');
            $('.btn_sentence').off('click');
            $('#clear_search').unbind('click');
            $(".memorable_checkbox").unbind('change');
        },
        name: "PageWordSentences.vue"
    }
</script>

<style lang="scss" scoped>

.page-word-sentences {
    padding: var(--spacing-6) 0;

    .container {
        padding: 0 var(--spacing-4);

        @media (min-width: 640px) {
            padding: 0 var(--spacing-6);
        }
    }

    .max-w-7xl {
        max-width: 80rem;
    }

    .mx-auto {
        margin-left: auto;
        margin-right: auto;
    }

    .flex {
        display: flex;
    }

    .items-center {
        align-items: center;
    }

    .justify-between {
        justify-content: space-between;
    }

    .space-y-1 > * + * {
        margin-top: var(--spacing-1);
    }

    .space-y-3 > * + * {
        margin-left: var(--spacing-3);
    }

    .space-y-4 > * + * {
        margin-top: var(--spacing-4);
    }

    .mb-6 {
        margin-bottom: var(--spacing-6);
    }

    .mr-2 {
        margin-right: var(--spacing-2);
    }

    .text-3xl {
        font-size: 1.875rem;
        line-height: 2.25rem;
    }

    .text-lg {
        font-size: 1.125rem;
        line-height: 1.75rem;
    }

    .text-sm {
        font-size: 0.875rem;
        line-height: 1.25rem;
    }

    .font-bold {
        font-weight: 700;
    }

    .font-semibold {
        font-weight: 600;
    }

    .font-medium {
        font-weight: 500;
    }

    .tracking-tight {
        letter-spacing: -0.025em;
    }

    .text-muted-foreground {
        color: var(--muted-foreground);
    }

    .w-4 {
        width: 1rem;
    }

    .h-4 {
        height: 1rem;
    }

    // Прямые стили для таблицы предложений
    .vgt-table.sentence {
        background-color: var(--card);
        color: var(--card-foreground);
        border-radius: var(--radius);
        border: 1px solid var(--border);
        width: 100%;
        max-width: 100%;
        box-shadow: none !important;
        transition: none !important;

        &:hover {
            transform: none !important;
            box-shadow: none !important;
        }
    }

    .p-0 {
        padding: 0;
    }

    .text-center {
        text-align: center;
    }

    .space-x-2 > * + * {
        margin-left: var(--spacing-2);
    }

    // Sound controls
    .box-sound {
        display: flex;
        align-items: flex-end;
        gap: var(--spacing-3);

        #block_repeat {
            .title_repeat {
                line-height: 1.25rem;
                margin-bottom: 0.25rem;
                font-size: 0.875rem;
                margin-top: 0;
                color: var(--foreground);
            }

            .block_input_repeat {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: var(--spacing-2);

                .checkbox_repeat {
                    height: 1.125rem;
                    width: 1.125rem;
                    cursor: pointer;
                    border-radius: var(--radius);
                    border: 1px solid var(--border);

                    &:checked {
                        background-color: var(--primary);
                        border-color: var(--primary);
                    }
                }

                .number_repeat {
                    width: 4rem;
                    height: 2rem;
                    padding: 0.25rem 0.5rem;
                    font-size: 0.875rem;
                    text-align: center;
                    margin-left: 0.5rem;
                }
            }
        }

        button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--radius);
            font-weight: 500;
            transition: all 200ms ease-in-out;
            border: 1px solid transparent;
            cursor: pointer;
            font-size: 0.875rem;
            line-height: 1.25rem;
            padding: 0.5rem 1rem;

            &:disabled {
                opacity: 0.5;
                cursor: not-allowed;
            }
        }
    }

    // Modal styles
    .modal {
        .modal-content {
            background-color: var(--card);
            color: var(--card-foreground);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-lg);
        }

        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: var(--spacing-6);
            border-bottom: 1px solid var(--border);
        }

        .modal-body {
            padding: var(--spacing-4) 0;

            .form-group {
                margin-top: 0.5rem;

                .form-label {
                    display: block;
                    font-size: 0.875rem;
                    font-weight: 500;
                    color: var(--foreground);
                    margin-bottom: 0.5rem;
                }
            }

            form {
                padding: 0 var(--spacing-4);
            }
        }

        .modal-footer {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding: var(--spacing-6);
            border-top: 1px solid var(--border);
            gap: var(--spacing-3);
        }
    }

    // Responsive adjustments
    @media (max-width: 768px) {
        .flex.items-center.justify-between {
            flex-direction: column;
            align-items: flex-start;
            gap: var(--spacing-4);
        }

        .space-x-3 {
            flex-wrap: wrap;
            gap: var(--spacing-2);
        }

        .box-sound {
            flex-direction: column;
            align-items: flex-start;
            gap: var(--spacing-2);
        }
    }
}

</style>
