<template>
    <div id="page_list_sentences">
        <!-- body page -->
        <div class="wrapper">
            <!-- верхнее меню -->
            <div class="top-menu">
                <!-- заголовок окна-->
                <h1>
                    {{ $t('all.list_sentences') }}
                </h1>

                <div class="box-button">
                    <!-- кнопки озвучки предложений -->
                    <div class="box-sound" v-show="getCodeLearnLanguage2 == 'en'">
                        <div id="block_repeat" v-if="!speak.start">
                            <div class="title_repeat">
                                {{ $t('all.repeat') }}
                            </div>
                            <div class="block_input_repeat">
                                <input :checked="speak.repeat_bool" @change="speak.repeat_bool = !speak.repeat_bool"
                                       class="checkbox_repeat" type="checkbox"
                                >
                                <input class="number_repeat" min="1" max="10" type="number"
                                       v-if="speak.repeat_bool" v-model="speak.count_repeat"
                                >
                            </div>
                        </div>
                        <!-- start -->
                        <button class="btn btn-success"
                                :disabled="disabled_play"
                                @click="initialSpeak"
                                v-if="!speak.start"
                        >
                            <i class="fas fa-play"></i>
                            {{ $t('all.sound_translation') }}
                        </button>
                        <!-- pause -->
                        <button @click="pauseReadSound" class="btn btn-outline-warning" v-if="speak.start && !speak.pause">
                            <i class="fas fa-pause"></i>
                            {{ $t('all.pause') }}
                        </button>
                        <!-- continue -->
                        <button @click="continueReadSound" class="btn btn-success" v-if="speak.pause">
                            <i class="fas fa-play"></i>
                            {{ $t('all.continue') }}
                        </button>
                        <!-- stop -->
                        <button @click="stopReadSound" class="btn btn-outline-danger" v-if="speak.start">
                            <i class="fas fa-stop"></i>
                            {{ $t('all.stop') }}
                        </button>
                    </div>

                    <!-- Создать предложение -->
                    <button @click="openModalCreateSentence" class="btn btn-primary create-sentence">
                        {{ $t('all.add_sentence') }}
                    </button>

                    <!-- learn sentences -->
                    <button class="btn btn-primary learn-sentence"
                            @click="openLearnModal()"
                            v-if="!bool_learn_sentences"
                    >
                        Learn
                    </button>
                </div>
            </div>
            <!-- Table -->
            <div class="content-wrapper" id="content-wrapper">
                <div class="container-fluid">
                    <div class="card card-primary card-outline block_table">
                        <div class="table_wrapper">
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
        </div>

        <!-- Modals создать предложение -->
        <div aria-hidden="true" aria-labelledby="create_sentence" class="modal fade"
             id="create_sentence" role="dialog" tabindex="-1"
        >
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <!-- header -->
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ $t('all.create_new_sentence') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <!-- body -->
                    <div class="modal-body">
                        <form action="#">
                            <!-- new sentence -->
                            <div class="form-group">
                                <label class="col-form-label" for="new_sentence">
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
                                ></textarea>
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
                                <label class="col-form-label" for="translation_sentence">
                                    {{ $t('all.translation') }}
                                </label>
                                <textarea class="form-control"
                                          :class="{'is-invalid': $v.translation_sentence.$error}"
                                          @blur="touchTranslationSentence()"
                                          id="translation_sentence"
                                          placeholder="Translation sentence"
                                          required
                                          v-model="translation_sentence"
                                ></textarea>
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
                        <!-- button save -->
                        <div class="button_footer">
                            <!-- button save -->
                            <div class="button_footer">
                                <button :class="{'un_active': $v.$invalid, 'active2': !$v.$invalid}"
                                        :disabled="$v.$invalid"
                                        @click="createSentence"
                                        class="btn btn-primary"
                                        type="button"
                                >
                                    {{ $t('all.save') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modals обновить предложение -->
        <div aria-hidden="true" aria-labelledby="update_sentence" class="modal fade"
             id="update_sentence" role="dialog" tabindex="-1"
        >
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <!-- header -->
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ $t('all.update_sentence') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <!-- body -->
                    <div class="modal-body">
                        <form action="#">
                            <!-- sentence -->
                            <div class="form-group">
                                <label class="col-form-label" for="old_sentence">
                                    {{ $t('all.sentence') }}
                                </label>
                                <textarea :class="{'is-invalid': $v.new_sentence.$error}" @blur="touchNewSentence()"
                                          @keyup="searchHelpWord(new_sentence)"
                                          class="form-control entry-field-help"
                                          id="old_sentence"
                                          placeholder="Insert new sentence"
                                          required
                                          v-model="new_sentence"
                                ></textarea>
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
                                <label class="col-form-label" for="old_translation">
                                    {{ $t('all.translation') }}
                                </label>
                                <textarea :class="{'is-invalid': $v.translation_sentence.$error}"
                                          @blur="touchTranslationSentence()"
                                          class="form-control"
                                          id="old_translation"
                                          placeholder="Insert translation sentence"
                                          required
                                          v-model="translation_sentence"
                                ></textarea>
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
                        <!-- button save -->
                        <div class="button_footer">
                            <button :class="{'un_active': $v.$invalid, 'active2': !$v.$invalid}"
                                    :disabled="$v.$invalid"
                                    @click="updateSentence"
                                    class="btn btn-primary"
                                    type="button"
                            >
                                {{ $t('all.update') }}
                            </button>
                        </div>
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

#page_list_sentences{
    max-height: calc(100vh - 60px);
    overflow-y: auto;
    width: calc(100% - 200px);
    .top-menu{
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 7px;
        .box-button{
            display: flex;
            .box-sound{
                display: flex;
                button{
                    i{
                        margin-right: 7px;
                    }
                }
                & > button, & > div{
                    margin-right: 15px;
                }
                #block_repeat{
                    text-align: center;
                    .title_repeat{
                        line-height: 15px;
                        margin-bottom: 3px;
                        font-size: 14px;
                        margin-top: -2px;
                    }
                    .block_input_repeat{
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        .checkbox_repeat{
                            height: 18px;
                            width: 18px;
                            cursor: pointer;
                        }
                        .number_repeat{
                            height: 18px;
                            font-size: 15px;
                            width: 50px;
                            margin-left: 5px;
                            padding-right: 0px;
                        }
                    }
                }
            }
            .create-sentence, .learn-sentence{
                margin-right: 10px;
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

</style>
