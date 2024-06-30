<template>
    <div id="page_list_worlds">
        <!-- Контент -->
        <div class="wrapper">
            <!-- верхнее меню -->
            <div class="top-menu">
                <!-- заголовок окна-->
                <h1>{{ $t('all.word_list') }}</h1>
                <!-- кнопки -->
                <div class="box-button">
                    <!-- learn words -->
                    <button class="btn btn-success"
                            @click="openLearnModal()"
                            v-if="!bool_learn_words"
                    >
                        {{ $t('all.learn_words') }}
                    </button>
                    <!-- stop learn -->
                    <button class="btn btn-warning"
                            v-if="bool_learn_words"
                            @click="bool_learn_words = false"
                    >
                        {{ $t('all.stop_learn') }}
                    </button>
                    <button class="btn btn-primary" @click="openModalCreateWord">
                        {{ $t('all.add_word') }}
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
                                <template v-slot:table-actions>
                                    <div style="display: flex; justify-content: flex-end; align-items: center;">
                                        <!-- Select поиска типов слов -->
                                        <select class="form-select search-select-types"
                                                v-model="table.selectedOption"
                                                @change="handleSelectChange"
                                        >
                                            <option value="null">Типы слов</option>
                                            <option v-for="(obj, key) in formattedTypes" :key="key"
                                                    :value="obj.id"
                                                    v-text="obj.type"
                                            ></option>
                                        </select>
                                    </div>
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
                        <h5 class="modal-title" v-if="!objGenerateSentences.boolAddSentences">
                            {{ $t('all.create_new_word') }}
                        </h5>
                        <h5 class="modal-title" v-else>
                            {{ $t('all.loading_generate_sentences') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

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
                                <input type="checkbox" class="form-check-input"
                                       :value="obj"
                                       v-model="objGenerateSentences.selectedSentences"
                                >
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
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M61.7 169.4l101.5 278C92.2 413 43.3 340.2 43.3 256c0-30.9 6.6-60.1 18.4-86.6zm337.9 75.9c0-26.3-9.4-44.5-17.5-58.7-10.8-17.5-20.9-32.4-20.9-49.9 0-19.6 14.8-37.8 35.7-37.8 .9 0 1.8 .1 2.8 .2-37.9-34.7-88.3-55.9-143.7-55.9-74.3 0-139.7 38.1-177.8 95.9 5 .2 9.7 .3 13.7 .3 22.2 0 56.7-2.7 56.7-2.7 11.5-.7 12.8 16.2 1.4 17.5 0 0-11.5 1.3-24.3 2l77.5 230.4L249.8 247l-33.1-90.8c-11.5-.7-22.3-2-22.3-2-11.5-.7-10.1-18.2 1.3-17.5 0 0 35.1 2.7 56 2.7 22.2 0 56.7-2.7 56.7-2.7 11.5-.7 12.8 16.2 1.4 17.5 0 0-11.5 1.3-24.3 2l76.9 228.7 21.2-70.9c9-29.4 16-50.5 16-68.7zm-139.9 29.3l-63.8 185.5c19.1 5.6 39.2 8.7 60.1 8.7 24.8 0 48.5-4.3 70.6-12.1-.6-.9-1.1-1.9-1.5-2.9l-65.4-179.2zm183-120.7c.9 6.8 1.4 14 1.4 21.9 0 21.6-4 45.8-16.2 76.2l-65 187.9C426.2 403 468.7 334.5 468.7 256c0-37-9.4-71.8-26-102.1zM504 256c0 136.8-111.3 248-248 248C119.2 504 8 392.7 8 256 8 119.2 119.2 8 256 8c136.7 0 248 111.2 248 248zm-11.4 0c0-130.5-106.2-236.6-236.6-236.6C125.5 19.4 19.4 125.5 19.4 256S125.6 492.6 256 492.6c130.5 0 236.6-106.1 236.6-236.6z"/></svg>
                                <label for="new_word" class="col-form-label">
                                    {{ $t('all.new_word') }}
                                </label>
                                <input type="text"
                                       class="form-control entry-field-help"
                                       placeholder="Insert new word"
                                       id="new_word"
                                       ref="new_word"
                                       v-model="arrInputsModal.new_word"
                                       @blur="touchNewWord()"
                                       @keyup="searchHelpWord(arrInputsModal.new_word)"
                                       :class="{'is-invalid': $v.arrInputsModal.new_word.$error}"
                                       required
                                >
                                <help-search-word :help-dynamic="help_dynamic"/>
                                <div class="invalid-feedback" v-if="!$v.arrInputsModal.new_word.required">
                                    {{ $t('all.field_is_empty') }}
                                </div>
                                <div class="invalid-feedback" v-if="(!$v.arrInputsModal.new_word.minLength)">
                                    {{ $t('all.number_of_characters') }}
                                    {{ this.arrInputsModal.new_word.length }}
                                    {{ $t('all.less_needed') }}
                                </div>
                            </div>

                            <!-- translation word -->
                            <div class="form-group">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M0 128C0 92.7 28.7 64 64 64H256h48 16H576c35.3 0 64 28.7 64 64V384c0 35.3-28.7 64-64 64H320 304 256 64c-35.3 0-64-28.7-64-64V128zm320 0V384H576V128H320zM178.3 175.9c-3.2-7.2-10.4-11.9-18.3-11.9s-15.1 4.7-18.3 11.9l-64 144c-4.5 10.1 .1 21.9 10.2 26.4s21.9-.1 26.4-10.2l8.9-20.1h73.6l8.9 20.1c4.5 10.1 16.3 14.6 26.4 10.2s14.6-16.3 10.2-26.4l-64-144zM160 233.2L179 276H141l19-42.8zM448 164c11 0 20 9 20 20v4h44 16c11 0 20 9 20 20s-9 20-20 20h-2l-1.6 4.5c-8.9 24.4-22.4 46.6-39.6 65.4c.9 .6 1.8 1.1 2.7 1.6l18.9 11.3c9.5 5.7 12.5 18 6.9 27.4s-18 12.5-27.4 6.9l-18.9-11.3c-4.5-2.7-8.8-5.5-13.1-8.5c-10.6 7.5-21.9 14-34 19.4l-3.6 1.6c-10.1 4.5-21.9-.1-26.4-10.2s.1-21.9 10.2-26.4l3.6-1.6c6.4-2.9 12.6-6.1 18.5-9.8l-12.2-12.2c-7.8-7.8-7.8-20.5 0-28.3s20.5-7.8 28.3 0l14.6 14.6 .5 .5c12.4-13.1 22.5-28.3 29.8-45H448 376c-11 0-20-9-20-20s9-20 20-20h52v-4c0-11 9-20 20-20z"/></svg>
                                <label for="translation_word" class="col-form-label">
                                    {{ $t('all.translation') }}
                                </label>
                                <input type="text" class="form-control"
                                       placeholder="Insert translation a word"
                                       id="translation_word"
                                       v-model="arrInputsModal.translation_word"
                                       @blur="touchTranslationWord()"
                                       :class="{'is-invalid': $v.arrInputsModal.translation_word.$error}"
                                       required
                                >
                                <div class="invalid-feedback" v-if="!$v.arrInputsModal.translation_word.required">
                                    {{ $t('all.field_is_empty') }}
                                </div>
                                <div class="invalid-feedback" v-if="(!$v.arrInputsModal.translation_word.minLength)">
                                    {{ $t('all.number_of_characters') }}
                                    {{ this.arrInputsModal.translation_word.length }}
                                    {{ $t('all.less_needed') }}
                                </div>
                            </div>

                            <!-- url image from out source -->
                            <div class="form-group">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M448 80c8.8 0 16 7.2 16 16V415.8l-5-6.5-136-176c-4.5-5.9-11.6-9.3-19-9.3s-14.4 3.4-19 9.3L202 340.7l-30.5-42.7C167 291.7 159.8 288 152 288s-15 3.7-19.5 10.1l-80 112L48 416.3l0-.3V96c0-8.8 7.2-16 16-16H448zM64 32C28.7 32 0 60.7 0 96V416c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V96c0-35.3-28.7-64-64-64H64zm80 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z"/></svg>
                                <label for="url_image" class="col-form-label">
                                    {{ $t('all.url_image') }}
                                </label>
                                <input type="text" class="form-control" placeholder="Input url" id="url_image"
                                       v-model="arrInputsModal.url_image"
                                >
                            </div>

                            <!-- Word description -->
                            <div class="form-group">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M256 448c141.4 0 256-93.1 256-208S397.4 32 256 32S0 125.1 0 240c0 45.1 17.7 86.8 47.7 120.9c-1.9 24.5-11.4 46.3-21.4 62.9c-5.5 9.2-11.1 16.6-15.2 21.6c-2.1 2.5-3.7 4.4-4.9 5.7c-.6 .6-1 1.1-1.3 1.4l-.3 .3 0 0 0 0 0 0 0 0c-4.6 4.6-5.9 11.4-3.4 17.4c2.5 6 8.3 9.9 14.8 9.9c28.7 0 57.6-8.9 81.6-19.3c22.9-10 42.4-21.9 54.3-30.6c31.8 11.5 67 17.9 104.1 17.9zM224 160c0-8.8 7.2-16 16-16h32c8.8 0 16 7.2 16 16v48h48c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H288v48c0 8.8-7.2 16-16 16H240c-8.8 0-16-7.2-16-16V272H176c-8.8 0-16-7.2-16-16V224c0-8.8 7.2-16 16-16h48V160z"/></svg>
                                <label for="word_description" class="col-form-label">
                                    {{ $t('all.word_description') }}
                                </label>
                                <textarea class="form-control"
                                          id="word_description"
                                          placeholder="Insert description word"
                                          v-model="arrInputsModal.description"
                                ></textarea>
                            </div>

                            <!-- типы значений слова -->
                            <div class="block_type" v-show="getCodeLearnLanguage2 == 'en'">
                                <!-- select значений -->
                                <div class="box-left-site">
                                    <div class="form-group">
                                        <label for="select_type" class="col-form-label">
                                            {{ $t('all.word_type') }}
                                        </label>
                                        <select id="select_type"
                                                v-model="arrInputsModal.select_type_id"
                                                class="custom-select"
                                                size="3"
                                        >
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
                                    <!-- формы времени -->
                                    <div v-if="arrInputsModal.objWordTimeForms !== null"
                                         class="box-time-forms"
                                    >
                                        <!-- прошедшее -->
                                        <div class="box-past">
                                            <label>
                                                {{ $t('all.past_time') }}
                                            </label>
                                            <input type="text" class="form-control" placeholder="Insert word"
                                                   v-model="arrInputsModal.objWordTimeForms.past.word"
                                            >
                                            <input type="text" class="form-control" placeholder="Insert translation"
                                                   v-model="arrInputsModal.objWordTimeForms.past.translation"
                                            >
                                            <input type="text" class="form-control" placeholder="Insert accent"
                                                   v-model="arrInputsModal.objWordTimeForms.past.accent"
                                            >
                                        </div>
                                        <!-- настоящее -->
                                        <div class="box-present">
                                            <label>
                                                {{ $t('all.present_time') }}
                                            </label>
                                            <input type="text" class="form-control" placeholder="Insert word"
                                                   v-model="arrInputsModal.objWordTimeForms.present.word"
                                            >
                                            <input type="text" class="form-control" placeholder="Insert translation"
                                                   v-model="arrInputsModal.objWordTimeForms.present.translation"
                                            >
                                            <input type="text" class="form-control" placeholder="Insert accent"
                                                   v-model="arrInputsModal.objWordTimeForms.present.accent"
                                            >
                                        </div>
                                        <!-- будущее -->
                                        <div class="box-future">
                                            <label>
                                                {{ $t('all.future_time') }}
                                            </label>
                                            <input type="text" class="form-control" placeholder="Insert word"
                                                   v-model="arrInputsModal.objWordTimeForms.future.word"
                                            >
                                            <input type="text" class="form-control" placeholder="Insert translation"
                                                   v-model="arrInputsModal.objWordTimeForms.future.translation"
                                            >
                                            <input type="text" class="form-control" placeholder="Insert accent"
                                                   v-model="arrInputsModal.objWordTimeForms.future.accent"
                                            >
                                        </div>
                                    </div>
                                    <!-- числительные -->
                                    <div v-if="arrInputsModal.objNumber !== null">
                                        <label>
                                            {{ $t('all.enter_digit') }}
                                        </label>
                                        <input type="text" class="form-control" placeholder="Insert number"
                                               v-model="arrInputsModal.objNumber.number"
                                        >
                                    </div>
                                    <!-- союзы -->
                                    <div v-if="arrInputsModal.objConjunction !== null"
                                         class="box-conjunction-select"
                                    >
                                        <select class="form-select" v-model="arrInputsModal.selectedConjunction" @change="updateSelection">
                                            <option v-for="(conjunction, key) in arrInputsModal.objConjunction" :key="key" :value="key">
                                                {{ conjunction.name }}
                                            </option>
                                        </select>
                                        <template v-if="arrInputsModal.objConjunction">
                                            <label v-for="(conjunction, key) in arrInputsModal.objConjunction" v-if="conjunction.select" :key="key">
                                                {{ conjunction.about }}
                                            </label>
                                        </template>
                                        <label v-if="!hasSelectedConjunction">
                                            Выбрать нужный союз
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Предложения -->
                            <div class="box-content-sentences">
                                <!-- переключатель добавления предложений -->
                                <div class="form-check form-switch" @click="toggleSwitch($event, 'toggle1')">
                                    <input ref="toggle1" class="form-check-input" type="checkbox" id="toggle1"
                                           @click="preventDefault"
                                    >
                                    <label class="form-check-label">
                                        {{ $t('all.sentences') }}
                                    </label>
                                </div>
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
                            >
                                {{ $t('all.create') }}
                            </button>
                            <!-- next to generate sentences-->
                            <button type="button" class="btn btn-success"
                                    :class="{'un_active': $v.$invalid, 'active2': !$v.$invalid}"
                                    :disabled="$v.$invalid"
                                    @click="loadGenerateSentences()"
                                    v-if="objGenerateSentences.status_toggle && !objGenerateSentences.boolAddSentences"
                            >
                                {{ $t('all.next_2') }}
                            </button>
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
                        <h5 class="modal-title" v-if="!objGenerateSentences.boolAddSentences">
                            {{ $t('all.update_word') }}
                        </h5>
                        <h5 class="modal-title" v-else>
                            {{ $t('all.loading_generate_sentences') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

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
                                <input type="checkbox" class="form-check-input"
                                       :value="obj"
                                       v-model="objGenerateSentences.selectedSentences"
                                >
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
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M61.7 169.4l101.5 278C92.2 413 43.3 340.2 43.3 256c0-30.9 6.6-60.1 18.4-86.6zm337.9 75.9c0-26.3-9.4-44.5-17.5-58.7-10.8-17.5-20.9-32.4-20.9-49.9 0-19.6 14.8-37.8 35.7-37.8 .9 0 1.8 .1 2.8 .2-37.9-34.7-88.3-55.9-143.7-55.9-74.3 0-139.7 38.1-177.8 95.9 5 .2 9.7 .3 13.7 .3 22.2 0 56.7-2.7 56.7-2.7 11.5-.7 12.8 16.2 1.4 17.5 0 0-11.5 1.3-24.3 2l77.5 230.4L249.8 247l-33.1-90.8c-11.5-.7-22.3-2-22.3-2-11.5-.7-10.1-18.2 1.3-17.5 0 0 35.1 2.7 56 2.7 22.2 0 56.7-2.7 56.7-2.7 11.5-.7 12.8 16.2 1.4 17.5 0 0-11.5 1.3-24.3 2l76.9 228.7 21.2-70.9c9-29.4 16-50.5 16-68.7zm-139.9 29.3l-63.8 185.5c19.1 5.6 39.2 8.7 60.1 8.7 24.8 0 48.5-4.3 70.6-12.1-.6-.9-1.1-1.9-1.5-2.9l-65.4-179.2zm183-120.7c.9 6.8 1.4 14 1.4 21.9 0 21.6-4 45.8-16.2 76.2l-65 187.9C426.2 403 468.7 334.5 468.7 256c0-37-9.4-71.8-26-102.1zM504 256c0 136.8-111.3 248-248 248C119.2 504 8 392.7 8 256 8 119.2 119.2 8 256 8c136.7 0 248 111.2 248 248zm-11.4 0c0-130.5-106.2-236.6-236.6-236.6C125.5 19.4 19.4 125.5 19.4 256S125.6 492.6 256 492.6c130.5 0 236.6-106.1 236.6-236.6z"/></svg>
                                <label for="old_word" class="col-form-label">
                                    {{ $t('all.update_word') }}
                                </label>
                                <input type="text"
                                       class="form-control entry-field-help"
                                       placeholder="Insert word"
                                       id="old_word"
                                       v-model="arrInputsModal.new_word"
                                       @blur="touchNewWord()"
                                       @keyup="searchHelpWord(arrInputsModal.new_word)"
                                       :class="{'is-invalid': $v.arrInputsModal.new_word.$error}"
                                       required
                                >
                                <help-search-word :help-dynamic="help_dynamic"/>
                                <div class="invalid-feedback" v-if="!$v.arrInputsModal.new_word.required">
                                    {{ $t('all.field_is_empty') }}
                                </div>
                                <div class="invalid-feedback" v-if="(!$v.arrInputsModal.new_word.minLength)">
                                    {{ $t('all.number_of_characters') }}
                                    {{ this.arrInputsModal.new_word.length }}
                                    {{ $t('all.less_needed') }}
                                </div>
                            </div>

                            <!-- translation word -->
                            <div class="form-group">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M0 128C0 92.7 28.7 64 64 64H256h48 16H576c35.3 0 64 28.7 64 64V384c0 35.3-28.7 64-64 64H320 304 256 64c-35.3 0-64-28.7-64-64V128zm320 0V384H576V128H320zM178.3 175.9c-3.2-7.2-10.4-11.9-18.3-11.9s-15.1 4.7-18.3 11.9l-64 144c-4.5 10.1 .1 21.9 10.2 26.4s21.9-.1 26.4-10.2l8.9-20.1h73.6l8.9 20.1c4.5 10.1 16.3 14.6 26.4 10.2s14.6-16.3 10.2-26.4l-64-144zM160 233.2L179 276H141l19-42.8zM448 164c11 0 20 9 20 20v4h44 16c11 0 20 9 20 20s-9 20-20 20h-2l-1.6 4.5c-8.9 24.4-22.4 46.6-39.6 65.4c.9 .6 1.8 1.1 2.7 1.6l18.9 11.3c9.5 5.7 12.5 18 6.9 27.4s-18 12.5-27.4 6.9l-18.9-11.3c-4.5-2.7-8.8-5.5-13.1-8.5c-10.6 7.5-21.9 14-34 19.4l-3.6 1.6c-10.1 4.5-21.9-.1-26.4-10.2s.1-21.9 10.2-26.4l3.6-1.6c6.4-2.9 12.6-6.1 18.5-9.8l-12.2-12.2c-7.8-7.8-7.8-20.5 0-28.3s20.5-7.8 28.3 0l14.6 14.6 .5 .5c12.4-13.1 22.5-28.3 29.8-45H448 376c-11 0-20-9-20-20s9-20 20-20h52v-4c0-11 9-20 20-20z"/></svg>
                                <label for="update_translation" class="col-form-label">
                                    {{ $t('all.translation') }}
                                </label>
                                <input type="text" class="form-control"
                                       placeholder="Insert translation"
                                       id="update_translation"
                                       v-model="arrInputsModal.translation_word"
                                       @blur="touchTranslationWord()"
                                       :class="{'is-invalid': $v.arrInputsModal.translation_word.$error}"
                                       required
                                >
                                <div class="invalid-feedback" v-if="!$v.arrInputsModal.translation_word.required">
                                    {{ $t('all.field_is_empty') }}
                                </div>
                                <div class="invalid-feedback" v-if="(!$v.arrInputsModal.translation_word.minLength)">
                                    {{ $t('all.number_of_characters') }}
                                    {{ this.arrInputsModal.translation_word.length }}
                                    {{ $t('all.less_needed') }}
                                </div>
                            </div>

                            <!-- url image from out source -->
                            <div class="form-group">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M448 80c8.8 0 16 7.2 16 16V415.8l-5-6.5-136-176c-4.5-5.9-11.6-9.3-19-9.3s-14.4 3.4-19 9.3L202 340.7l-30.5-42.7C167 291.7 159.8 288 152 288s-15 3.7-19.5 10.1l-80 112L48 416.3l0-.3V96c0-8.8 7.2-16 16-16H448zM64 32C28.7 32 0 60.7 0 96V416c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V96c0-35.3-28.7-64-64-64H64zm80 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z"/></svg>
                                <label for="update_url_image" class="col-form-label">
                                    {{ $t('all.url_image') }}
                                </label>
                                <input type="text"
                                       class="form-control"
                                       placeholder="Input url"
                                       id="update_url_image"
                                       v-model="arrInputsModal.url_image"
                                >
                            </div>

                            <!-- Word description -->
                            <div class="form-group">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M256 448c141.4 0 256-93.1 256-208S397.4 32 256 32S0 125.1 0 240c0 45.1 17.7 86.8 47.7 120.9c-1.9 24.5-11.4 46.3-21.4 62.9c-5.5 9.2-11.1 16.6-15.2 21.6c-2.1 2.5-3.7 4.4-4.9 5.7c-.6 .6-1 1.1-1.3 1.4l-.3 .3 0 0 0 0 0 0 0 0c-4.6 4.6-5.9 11.4-3.4 17.4c2.5 6 8.3 9.9 14.8 9.9c28.7 0 57.6-8.9 81.6-19.3c22.9-10 42.4-21.9 54.3-30.6c31.8 11.5 67 17.9 104.1 17.9zM224 160c0-8.8 7.2-16 16-16h32c8.8 0 16 7.2 16 16v48h48c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H288v48c0 8.8-7.2 16-16 16H240c-8.8 0-16-7.2-16-16V272H176c-8.8 0-16-7.2-16-16V224c0-8.8 7.2-16 16-16h48V160z"/></svg>
                                <label for="update_word_description" class="col-form-label">
                                    {{ $t('all.word_description') }}
                                </label>
                                <textarea v-model="arrInputsModal.description"
                                          class="form-control"
                                          id="update_word_description"
                                          placeholder="Insert description word">
                                </textarea>
                            </div>

                            <!-- select type word-->
                            <div class="block_type" v-show="getCodeLearnLanguage2 == 'en'">
                                <!-- select значений -->
                                <div class="box-left-site">
                                    <div class="form-group">
                                        <label for="update_select_type" class="col-form-label">
                                            {{ $t('all.word_type') }}
                                        </label>
                                        <select id="update_select_type"
                                                v-model="arrInputsModal.select_type_id"
                                                class="custom-select"
                                                size="3"
                                        >
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
                                    <!-- формы времени -->
                                    <div v-if="arrInputsModal.objWordTimeForms !== null"
                                         class="box-time-forms"
                                    >
                                        <!-- прошедшее -->
                                        <div class="box-past">
                                            <label>
                                                {{ $t('all.past_time') }}
                                            </label>
                                            <input type="text" class="form-control" placeholder="Insert word"
                                                   v-model="arrInputsModal.objWordTimeForms.past.word"
                                            >
                                            <input type="text" class="form-control" placeholder="Insert translation"
                                                   v-model="arrInputsModal.objWordTimeForms.past.translation"
                                            >
                                            <input type="text" class="form-control" placeholder="Insert accent"
                                                   v-model="arrInputsModal.objWordTimeForms.past.accent"
                                            >
                                        </div>
                                        <!-- настоящее -->
                                        <div class="box-present">
                                            <label>
                                                {{ $t('all.present_time') }}
                                            </label>
                                            <input type="text" class="form-control" placeholder="Insert word"
                                                   v-model="arrInputsModal.objWordTimeForms.present.word"
                                            >
                                            <input type="text" class="form-control" placeholder="Insert translation"
                                                   v-model="arrInputsModal.objWordTimeForms.present.translation"
                                            >
                                            <input type="text" class="form-control" placeholder="Insert accent"
                                                   v-model="arrInputsModal.objWordTimeForms.present.accent"
                                            >
                                        </div>
                                        <!-- будущее -->
                                        <div class="box-future">
                                            <label>
                                                {{ $t('all.future_time') }}
                                            </label>
                                            <input type="text" class="form-control" placeholder="Insert word"
                                                   v-model="arrInputsModal.objWordTimeForms.future.word"
                                            >
                                            <input type="text" class="form-control" placeholder="Insert translation"
                                                   v-model="arrInputsModal.objWordTimeForms.future.translation"
                                            >
                                            <input type="text" class="form-control" placeholder="Insert accent"
                                                   v-model="arrInputsModal.objWordTimeForms.future.accent"
                                            >
                                        </div>
                                    </div>
                                    <!-- числительные -->
                                    <div v-if="arrInputsModal.objNumber !== null" >
                                        <label>
                                            {{ $t('all.enter_digit') }}
                                        </label>
                                        <input type="text" class="form-control" placeholder="Insert number"
                                               v-model="arrInputsModal.objNumber.number"
                                        >
                                    </div>
                                    <!-- союзы -->
                                    <div v-if="arrInputsModal.objConjunction !== null"
                                         class="box-conjunction-select"
                                    >
                                        <select class="form-select" v-model="arrInputsModal.selectedConjunction" @change="updateSelection">
                                            <option v-for="(conjunction, key) in arrInputsModal.objConjunction" :key="key" :value="key">
                                                {{ conjunction.name }}
                                            </option>
                                        </select>
                                        <template v-if="arrInputsModal.objConjunction">
                                            <label v-for="(conjunction, key) in arrInputsModal.objConjunction" v-if="conjunction.select" :key="key">
                                                {{ conjunction.about }}
                                            </label>
                                        </template>
                                        <label v-if="!hasSelectedConjunction">
                                            Выбрать нужный союз
                                        </label>
                                    </div>

                                </div>
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
                                <div class="form-check form-switch" @click="toggleSwitch($event, 'toggle2')">
                                    <input ref="toggle2" class="form-check-input" type="checkbox" id="toggle2"
                                           @click="preventDefault"
                                    >
                                    <label class="form-check-label">
                                       {{ $t('all.sentences') }}
                                    </label>
                                </div>
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
                            >
                                {{ $t('all.update') }}
                            </button>
                            <!-- next to generate sentences-->
                            <button type="button" class="btn btn-success"
                                    :class="{'un_active': $v.$invalid, 'active2': !$v.$invalid}"
                                    :disabled="$v.$invalid"
                                    @click="loadGenerateSentences()"
                                    v-if="objGenerateSentences.status_toggle && !objGenerateSentences.boolAddSentences"
                            >
                                {{ $t('all.next_2') }}
                            </button>
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
    import translation_i18n_mixin from "../../mixins/translation_i18n_mixin";
    // components
    import ModalLearnWord from "../details/ModalLearnWord";
    import $ from 'jquery';
    import { mapGetters } from 'vuex';
    import user_mixin from "../../mixins/user_mixin";

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
                type_id: 0,
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
                            label: 'Буквы',
                            field: 'letter',
                            width: '5%',
                        },
                        {
                            tdClass: 'text_td',
                            label: 'Слово',
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
                            label: 'Слово',
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
                            label: 'Слово',
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
                            label: 'Слово',
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
                            label: 'Слово',
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
                    selectedOption: null
                },
                serverParams: {
                    selection_type_id: 'null',
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
                objUpdateWord: null,
                objWordFromTable: {
                    bool_click_button_word_from_table: false,
                    word: '',
                },
                arrSentences: [],
                arrInputsModal: {
                    word_id: 0,
                    new_word: '',
                    translation_word: '',
                    url_image: '',
                    select_type_id: 0,
                    description: '',
                    objWordTimeForms: null,
                    objNumber: null,
                    objConjunction: null,
                },
            };
        },
        mixins: [
            response_methods_mixin,
            good_table_mixin,
            help_search_word_mixin,
            translation_i18n_mixin,
            user_mixin
        ],
        props: {
            user: {
                type: Object,
                required: true
            }
        },
        components: { VueGoodTable, helpSearchWord, ModalLearnWord },
        computed: {
            ...mapGetters({
                // Геттер для получения текущего языка изучения
                currentLearnLanguage: 'getLearnLanguage'
            }),
            hasSelectedConjunction() {
                if (this.arrInputsModal.objConjunction) {
                    return Object.values(this.arrInputsModal.objConjunction).some(conjunction => conjunction.select);
                }
                return false;
            },
            formattedTypes() {
                return this.allTypes.map(type => ({
                    ...type,
                    type: type.type.charAt(0).toUpperCase() + type.type.slice(1)
                }));
            }
        },
        watch: {
            currentLearnLanguage: {
                // Вызывает метод loadData при изменении currentLearnLanguage
                handler: 'learnAnotherLanguage',
                immediate: false // Не Вызов loadData сразу после создания компонента
            },
            'arrInputsModal.objConjunction': {
                handler(newVal) {
                    if (newVal) {
                        this.initSelection();
                    }
                },
                immediate: true,
                deep: true
            }
        },
        methods: {
            // Выбор Select типов слов
            handleSelectChange() {
                this.clearServerParams()
                this.serverParams.selection_type_id = this.table.selectedOption
                this.resetButtonClearSearch()
            },
            initSelection() {
                for (const [key, value] of Object.entries(this.arrInputsModal.objConjunction)) {
                    if (value.select) {
                        this.arrInputsModal.selectedConjunction = key;
                        return;
                    }
                }
                this.arrInputsModal.selectedConjunction = '';
            },
            updateSelection() {
                if (this.arrInputsModal.objConjunction) {
                    for (const key in this.arrInputsModal.objConjunction) {
                        this.arrInputsModal.objConjunction[key].select = (key === this.arrInputsModal.selectedConjunction);
                    }
                }
            },
            // Заполнение данных и типе слова для передачи на сервер
            getCustomForms(){
                // типы слова формы времени или числительные
                let forms = null
                // кастом input - свойства object - поля description - таблицы word_types
                if(this.arrInputsModal.objWordTimeForms){
                    forms = this.arrInputsModal.objWordTimeForms
                }
                // кастом input - свойства object - поля description - таблицы word_types
                else if(this.arrInputsModal.objNumber){
                    forms = this.arrInputsModal.objNumber
                }
                // кастом select - свойства object - поля description - таблицы word_types
                else if(this.arrInputsModal.objConjunction){
                    forms = this.arrInputsModal.objConjunction
                }
                return forms
            },
            // Сформированный обьект для передачи на сервер
            getDataSaveServer(){
                return  {
                    word: this.arrInputsModal.new_word,
                    translation: this.arrInputsModal.translation_word,
                    url_image: this.arrInputsModal.url_image,
                    description: this.arrInputsModal.description,
                    arr_new_sentences: this.objGenerateSentences.selectedSentences,
                    type_id: this.arrInputsModal.select_type_id, // id типа из таблицы word_types
                    time_forms: this.getCustomForms(),
                }
            },
            async createWord() {
                try {
                    const response = await this.$http.post(`${this.$http.webUrl()}word`, this.getDataSaveServer());
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
                let data = this.getDataSaveServer()
                data.word_id = this.arrInputsModal.word_id
                try {
                    const response = await this.$http.post(`${this.$http.webUrl()}word/update-word`, data);
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
                    const response = await this.$http.post(`${this.$http.webUrl()}word/delete-word`, data);
                    if(this.checkSuccess(response)){
                        this.$swal.close()
                        this.initialData();
                    }
                } catch (e) {
                    console.log(e);
                }
            },
            // выборка слов и типов слов с пагинацией
            // http://english.my/word?search=&page=0&perPage=50&sortField=&sortType=
            async loadWordsAndTypes() {
                const url = `selection_type_id=${this.serverParams.selection_type_id}&search=${this.serverParams.search}&page=${this.serverParams.page}&perPage=${this.serverParams.perPage}&sortField=${this.serverParams.sort[0].field}&sortType=${this.serverParams.sort[0].type}`

                try {
                    this.isLoading = true;
                    const response = await this.$http.get(`${this.$http.webUrl()}word?${url}`)

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
                    const response = await this.$http.post(`${this.$http.webUrl()}sentence/search-sentences`, data);
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
                    arr_words: Array.isArray(this.arrInputsModal.new_word) ? this.arrInputsModal.new_word : [this.arrInputsModal.new_word],
                };

                try {
                    const response = await this.$http.post(`${this.$http.webUrl()}ai/generate-sentences`, data);
                    if(this.checkSuccess(response)){
                        this.objGenerateSentences.arrGenerateSentences = response.data.data.sentences
                        this.objGenerateSentences.boolLoadingIndicator = true
                    }
                } catch (e) {
                    console.log(e);
                }
            },
            touchNewWord() {
                this.$v.arrInputsModal.new_word.$touch();
            },
            touchTranslationWord() {
                this.$v.arrInputsModal.translation_word.$touch();
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

                // типы слова
                if(row.time_forms !== null){
                    // формы времени
                    if(row.time_forms.past !== undefined){
                        text_description = ' - Прошлое: '+ row.time_forms.past.word + ', ' + row.time_forms.past.translation + ', ' + row.time_forms.past.accent + '.'
                        text_description += ' Настоящее: '+ row.time_forms.present.word + ', ' + row.time_forms.present.translation + ', ' + row.time_forms.present.accent + '.'
                        text_description += ' Будущее: '+ row.time_forms.future.word + ', ' + row.time_forms.future.translation + ', ' + row.time_forms.future.accent + '.'
                    }
                    // числительные
                    else if(row.time_forms.number !== undefined){
                        text_description = ' - '+ row.time_forms.number
                    }
                    // числительные
                    else if(row.time_forms.coordinating !== undefined){
                        if(row.time_forms.coordinating.select){
                            text_description = row.time_forms.coordinating.name+' - '+ row.time_forms.coordinating.about
                        }
                        else if(row.time_forms.correlative.select){
                            text_description = row.time_forms.correlative.name+' - '+ row.time_forms.correlative.about
                        }
                        else if(row.time_forms.subordinating.select){
                            text_description = row.time_forms.subordinating.name+' - '+ row.time_forms.subordinating.about
                        }
                    }
                }
                let span_style = row.type == null ? '' : 'color:'+row.type.color

                // строка html для таблицы
                let html = `<div style="text-align: left;">
<div style="font-weight: 700;">${row.translation == null ? '' : row.translation.toLowerCase()}
<span style="${span_style};">${text_type.charAt(0).toUpperCase() + text_type.slice(1)} ${text_description}</span>
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
                const selectTypeElement = document.getElementById("select_type");
                if (selectTypeElement) {
                    selectTypeElement.addEventListener('change', () => {
                        for(let i=0; i < this.allTypes.length; i++){
                            if(this.allTypes[i].id === this.arrInputsModal.select_type_id){
                                this.setStyleDataModal(this.allTypes[i]);
                                break;
                            }
                        }
                    });
                }

                // в модалке обновления слова - select type
                const updateSelectTypeElement = document.getElementById("update_select_type");
                if (updateSelectTypeElement) {
                    updateSelectTypeElement.addEventListener('change', () => {
                        for(let i = 0; i < this.allTypes.length; i++) {
                            if (this.allTypes[i].id == this.arrInputsModal.select_type_id) {
                                this.setStyleDataModal(this.allTypes[i]);
                                break;
                            }
                        }
                    });
                }
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
                this.arrInputsModal.objWordTimeForms = null
                this.arrInputsModal.objNumber = null
                this.arrInputsModal.objConjunction = null

                if(type.description == null){
                    string = ''
                }
                else{
                    if(type.description['object'] === null){
                        string = type.description['text']
                    }
                    else if(type.description['object'] !== null){
                        // формы времени
                        if(type.description['object']['past'] !== undefined){
                            this.arrInputsModal.objWordTimeForms = type.description['object']
                        }
                        // числительные
                        else if(type.description['object']['number'] !== undefined){
                            this.arrInputsModal.objNumber = type.description['object']
                        }
                        // союзы
                        else if(type.description['object']['coordinating'] !== undefined){
                            this.arrInputsModal.objConjunction = type.description['object']
                        }
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
                this.arrInputsModal.word_id = obj.id || 0;
                this.arrInputsModal.new_word = obj.word || '';
                this.arrInputsModal.translation_word = obj.translation || '';
                this.arrInputsModal.url_image = obj.url_image || '';
                this.arrInputsModal.select_type_id = obj.type.id || 0;
                this.arrInputsModal.description = obj.description || '""';
                this.arrInputsModal.objWordTimeForms = null;
                this.arrInputsModal.objNumber = null;
                this.arrInputsModal.objConjunction = null;

                if(obj.time_forms !== null){
                    // типы слова формы времени
                    if(obj.time_forms.past !== undefined){
                        this.arrInputsModal.objWordTimeForms = obj.time_forms || null;
                    }
                    // типы слова числительные
                    else if(obj.time_forms.number !== undefined){
                        this.arrInputsModal.objNumber = obj.time_forms || null;
                    }
                    // типы слова союзы
                    else if(obj.time_forms.coordinating !== undefined){
                        this.arrInputsModal.objConjunction = obj.time_forms || null;
                    }
                }
            },
            // отключить событие по умолчанию у переключателя input генерации предложений
            preventDefault(event) {
                event.preventDefault();
            },
            // Клик по родителю переключателя генерации предложений
            toggleSwitch(event, ref) {
                setTimeout(()=>{
                    this.$refs[ref].checked = !this.$refs[ref].checked;
                    this.objGenerateSentences.status_toggle = this.$refs[ref].checked

                    // Находим родительский элемент div.form-check.form-switch
                    const parentElement = event.target.closest('.form-switch');
                    if (!parentElement) {
                        return;
                    }
                    // Находим дочерний label элемент внутри родительского элемента
                    const label = parentElement.querySelector('label');

                    // Обновляем текст в зависимости от состояния переключателя
                    if (this.objGenerateSentences.status_toggle) {
                        label.textContent = this.$t('all.generation');
                    }
                    else {
                        label.textContent = this.$t('all.sentences');
                    }
                },100)
            },
            // Очистка переменных модалки
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
            // очистка параметров пагинации
            clearServerParams(){
                this.serverParams.selection_type_id = ''
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
        },
        mounted() {
            this.initialData();
        },
        beforeDestroy: function () {
            $('.btn-warning').unbind('click');
            $('.btn-danger').unbind('click');

            // Удаляем инициализацию перед уничтожением компонента
            $(this.$refs.toggle1).bootstrapToggle('destroy');
            $(this.$refs.toggle2).bootstrapToggle('destroy');
        },
        validations: {
            arrInputsModal: {
                new_word: {
                    required,
                    minLength: minLength(1)
                },
                translation_word: {
                    required,
                    minLength: minLength(1),
                },
            },
        },
        name: "PageListWords.vue"
    }
</script>

<style lang="scss" scoped>
@import '../../../sass/variables.scss';

#page_list_worlds{
    max-height: calc(100vh - 60px);
    overflow-y: auto;
    width: calc(100% - 200px);
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
                .table_wrapper{
                    .search-select-types{
                        padding: 4px 35px 3px 15px;
                        margin-right: 7px;
                        cursor: pointer;
                        option{
                            cursor: pointer;
                            &:first-child{
                                background: #ddd;
                                color: #888;
                                cursor: default;
                            }
                        }
                    }
                }
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
                .box-sentences{
                    div{
                        color: #747474;
                        font-weight: 700;
                        font-size: 13px;
                    }
                }
                .form-switch{
                    display: flex;
                    flex-flow: column nowrap;
                    align-items: center;
                    margin: 20px 0 0 0;
                    border: 1px solid $modal-grey-border;
                    padding: 10px;
                    border-radius: 5px;
                    cursor: pointer;
                    input{
                        margin: 0;
                        cursor: pointer;
                    }
                    label{
                        text-align: center;
                        line-height: 20px;
                        margin-top: 10px;
                        width: 110px;
                        cursor: pointer;
                        font-size: 14px;
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
            .block_type{
                margin-top: 30px;
                .box-left-site{
                    width: 38%;
                    .custom-select{
                        border: 1px solid $modal-grey-border;
                        border-radius: 5px;
                        option{
                            font-weight: 200;
                            font-size: 15px;
                            padding: 0 10px;
                        }
                    }
                }
            }
            .form-group{
                margin-top: 5px;
                svg{
                    fill: #595959;
                }
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
.box-conjunction-select{
    margin-top: 5px;
    label{
        margin-top: 5px;
    }
}

</style>
