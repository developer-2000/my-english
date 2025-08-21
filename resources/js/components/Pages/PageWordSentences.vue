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

                <div class="flex items-center space-x-3">
                    <!-- кнопки озвучки предложений -->
                    <div class="box-sound" v-show="getCodeLearnLanguage2 == 'en'">
                        <div id="block_repeat" v-if="!speak.start" class="flex items-center space-x-3">
                            <div class="text-center">
                                <div class="title_repeat text-sm font-medium">
                                    {{ $t('all.repeat') }}
                                </div>
                                <div class="block_input_repeat flex items-center justify-center space-x-2">
                                    <input :checked="speak.repeat_bool" 
                                           @change="speak.repeat_bool = !speak.repeat_bool"
                                           class="checkbox_repeat" 
                                           type="checkbox">
                                    <input class="number_repeat" 
                                           min="1" 
                                           max="10" 
                                           type="number"
                                           v-if="speak.repeat_bool" 
                                           v-model="speak.count_repeat">
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
            <vue-good-table
                :columns="filteredColumns"
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
                styleClass="vgt-table sentence"
            >
                <template slot="loadingContent">
                    <div></div>
                </template>
            </vue-good-table>
        </div>

        <!-- Modals создать предложение -->
        <div aria-hidden="true" aria-labelledby="create_sentence" class="modal fade"
             id="create_sentence" role="dialog" tabindex="-1">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <!-- header -->
                    <div class="modal-header">
                        <h5 class="modal-title text-lg font-semibold">
                            {{ $t('all.create_new_sentence') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
             id="update_sentence" role="dialog" tabindex="-1">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <!-- header -->
                    <div class="modal-header">
                        <h5 class="modal-title text-lg font-semibold">
                            {{ $t('all.update_sentence') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
    import good_table_mixin from "../../mixins/good_table_mixin";
    // soft methods
    import soft_methods_mixin from "../../mixins/response_methods_mixin";
    // help_search_word
    import help_search_word_mixin from "../../mixins/help_search_word_mixin";
    import helpSearchWord from "../details/HelpSearchWord";
    // sound_word
    import sound_word_mixin from "../../mixins/sound_word_mixin";
    // bootstrap toggle
    import BootstrapToggle from 'vue-bootstrap-toggle'
    import translation_i18n_mixin from "../../mixins/translation_i18n_mixin";
    import {mapGetters} from "vuex";
    import user_mixin from "../../mixins/user_mixin";
    import $ from "jquery";
    // components
    import ModalLearnSentence from "../details/ModalLearnSentence";

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
                    page: 0,
                    perPage: 50,
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
            ModalLearnSentence
        },
        computed: {
            ...mapGetters({
                // Геттер для получения текущего языка изучения
                getLearnLanguage: 'getLearnLanguage'
            }),
            filteredColumns() {
                return this.table.columns.filter(column => {
                    // Condition to hide the Озвучка column based on language code
                    if (column.label === 'Озвучка' && this.getCodeLearnLanguage2 !== 'en') {
                        return false;
                    }
                    return true;
                });
            }
        },
        watch: {
            getLearnLanguage: {
                handler: 'learnAnotherLanguage', // Вызывает метод при изменении getLearnLanguage - язык изучения
                immediate: false // Не Вызов loadData сразу после создания компонента
            }
        },
        methods: {
            async bindCheckboxSound(sentence_id, status) {
                try {
                    let data = {
                        sentence_id: sentence_id,
                        status: status,
                    };
                    const response = await this.$http.post(`${this.$http.webUrl()}sentence/bind-checkbox-sound`, data);
                    if (this.checkSuccess(response)) {
                        this.initialData();
                    }
                } catch (e) {
                    console.log(e);
                }
            },
            // --- предложения
            async loadSentences() {
                try {
                    this.isLoading = true;
                    let field = this.serverParams.sort[0].field;
                    // заменить название столбца для сортировки checkbox sound
                    if (typeof field !== "string" && field.name == "field") {
                        field = 'sound';
                    }

                    const url = `selection_type_id=&search=${this.serverParams.search}&page=${this.serverParams.page}&perPage=${this.serverParams.perPage}&sortField=${field}&sortType=${this.serverParams.sort[0].type}`
                    const response = await this.$http.get(`${this.$http.webUrl()}sentence?${url}`
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
                    const response = await this.$http.post(`${this.$http.webUrl()}sentence`, data);
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
                        sentence_id: this.sentence_id,
                        sentence: this.new_sentence,
                        translation: this.translation_sentence,
                    };
                    const response = await this.$http.post(`${this.$http.webUrl()}sentence/update-sentence`, data);
                    if (this.checkSuccess(response)) {
                        this.initialData();
                        $('#update_sentence').modal('hide');
                        $('.modal-backdrop.fade.show').remove();
                    }
                } catch (e) {
                    console.log(e);
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
                this.loadSentences();
                this.initialClickButSentenceUpdate();
                this.initialCheckbox();
                this.makeButtonClearSearch();
            },
            // --- checkbox
            initialCheckbox() {
                this.activationButtonSoundInMenu(); // активация кнопки Sound в меню
            },
            activationButtonSoundInMenu() {
                setTimeout(() => {
                    // состояние кнопки sound по умолчанию
                    this.disabled_play = $('.memorable_checkbox:checked').length ? false : true;
                    // Сначала отвязываем предыдущие обработчики
                    $(".memorable_checkbox").off('change');
                    // изменнеие одного из sound checkbox
                    $(".memorable_checkbox").on('change', (e) => {
                        this.disabled_play = $('.memorable_checkbox:checked').length ? false : true;
                        this.bindCheckboxSound($(e.target).attr('data-id'), e.target.checked);
                    });
                }, 1000);
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
                // открываем редактирование предложения
                setTimeout(() => {
                    $('.btn_sentence').off('click');
                    $('.btn_sentence').on('click', (e) => {
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
            // очистка параметров пагинации
            clearServerParams(){
                this.serverParams.search = ''
                this.serverParams.page = 0
                this.serverParams.sort[0].field = ''
                this.serverParams.sort[0].type = ''
            },
            // операции после смены языка изучения
            learnAnotherLanguage(){
                this.clearServerParams()
                this.initialData()
            },
            handlePaste(event) {
                event.preventDefault();
                const pastedText = event.clipboardData.getData('text');

                // Ищем пары предложений, разделенные тире с пробелами по бокам
                const parts = pastedText.split(/\s+[-–—]\s+/);

                if (parts.length >= 2) {
                    // Очищаем и получаем первое предложение (английское)
                    const englishSentence = parts[0].trim();

                    // Очищаем и получаем второе предложение (русское)
                    const russianSentence = parts[1].trim();

                    this.new_sentence = englishSentence;
                    this.translation_sentence = russianSentence;
                } else {
                    // Если нет разделителя, определяем язык предложения
                    const cleanedText = pastedText.trim();
                    const isRussian = /[а-яА-ЯёЁ]/.test(cleanedText);

                    if (isRussian) {
                        this.translation_sentence = cleanedText;
                    } else {
                        this.new_sentence = cleanedText;
                    }
                }
            },
            // Открыть модалку изучения предложений
            openLearnModal() {
                // Вызов openLearnModal у дочернего компонента через референцию
                this.$refs.modalLearnSentence.openLearnModal();
                this.bool_learn_sentences = true;
                // событие закрытия модалки
                $('#learn_sentences').on('hidden.bs.modal', () => {
                    this.bool_learn_sentences = false;
                })
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
        align-items: center;
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
                    height: 1.125rem;
                    font-size: 0.875rem;
                    width: 3rem;
                    margin-left: 0;
                    padding: 0 0.25rem;
                    border: 1px solid var(--border);
                    border-radius: var(--radius);
                    text-align: center;
                    
                    &:focus {
                        outline: 2px solid transparent;
                        outline-offset: 2px;
                        border-color: var(--ring);
                        box-shadow: 0 0 0 2px var(--ring);
                    }
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
