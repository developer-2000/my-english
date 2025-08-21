<template>
    <div id="page_list_worlds" class="page-list-words">
        <div class="container-fluid">
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <div class="space-y-1">
                    <h1 class="text-3xl font-bold tracking-tight">{{ $t('all.word_list') }}</h1>
                    <p class="text-muted-foreground">
                        Управление словарным запасом и изучение новых слов
                    </p>
                </div>

                <div class="flex items-center space-x-3">
                    <button v-if="!bool_learn_words"
                            @click="openLearnModal()"
                            class="btn btn-secondary">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        {{ $t('all.learn_words') }}
                    </button>

                    <button v-if="bool_learn_words"
                            @click="bool_learn_words = false"
                            class="btn btn-outline">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        {{ $t('all.stop_learn') }}
                    </button>

                    <button @click="openModalCreateWord"
                            class="btn btn-primary">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        {{ $t('all.add_word') }}
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="card">
                <div class="card-body p-0">
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
                            styleClass="vgt-table"
                            @on-search="onSearch"
                            @on-page-change="onPageChange"
                            @on-per-page-change="onPerPageChange"
                            @on-sort-change="onSortChange"
                        >
                            <template v-slot:table-actions>
                                <div class="flex items-center justify-end space-x-3">
                                    <!-- Select поиска типов слов -->
                                    <div class="relative">
                                        <select class="form-select search-select-types"
                                                v-model="table.selectedOption"
                                                @change="handleSelectChange">
                                            <option value="null">Типы слов</option>
                                            <option v-for="(obj, key) in formattedTypes"
                                                    :key="key"
                                                    :value="obj.id"
                                                    v-text="obj.type">
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Кнопки для типа 4 -->
                                    <button class="btn btn-primary button-present-tense"
                                            @click="getPresentTenseWords"
                                            v-if="table.selectedOption === 4">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75"></path>
                                        </svg>
                                        Present Tense
                                    </button>
                                </div>
                            </template>
                        </vue-good-table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modals создать слово -->
        <div class="modal fade" id="create_word" tabindex="-1" role="dialog"
             aria-labelledby="create_word" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <!-- header -->
                    <div class="modal-header">
                        <h5 class="modal-title text-lg font-semibold" v-if="!objGenerateSentences.boolAddSentences">
                            {{ $t('all.create_new_word') }}
                        </h5>
                        <h5 class="modal-title text-lg font-semibold" v-else>
                            {{ $t('all.loading_generate_sentences') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <!-- body -->
                    <div class="modal-body">
                        <!-- Чекбоксы сгенерированных предложений -->
                        <div class="box-view-generate-sentences"
                             :class="{ 'visible-generate-sentences': objGenerateSentences.boolAddSentences }">
                            <!-- Индикатор загрузки предложений -->
                            <div class="dots-loader"
                                 v-if="!objGenerateSentences.boolLoadingIndicator">
                                <div class="dot"></div>
                                <div class="dot"></div>
                                <div class="dot"></div>
                            </div>
                            <!-- Сгенерированные предложения -->
                            <div class="box-new-sentence"
                                 v-for="(obj, key) in objGenerateSentences.arrGenerateSentences" :key="key">
                                <input type="checkbox" class="form-check-input"
                                       :value="obj"
                                       v-model="objGenerateSentences.selectedSentences">
                                <div class="box-sentence">
                                    <div class="original-sentence" v-text="obj.original"></div>
                                    <div class="translation-sentence" v-text="obj.translated"></div>
                                </div>
                            </div>
                        </div>
                        <!-- Поля создаваемого слова -->
                        <form action="#" v-show="!objGenerateSentences.boolAddSentences" class="space-y-4">
                            <!-- new word -->
                            <div class="form-group">
                                <label for="new_word" class="form-label">
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
                                       required>
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
                                <label for="translation_word" class="form-label">
                                    {{ $t('all.translation') }}
                                </label>
                                <input type="text" class="form-control"
                                       placeholder="Insert translation a word"
                                       id="translation_word"
                                       v-model="arrInputsModal.translation_word"
                                       @blur="touchTranslationWord()"
                                       :class="{'is-invalid': $v.arrInputsModal.translation_word.$error}"
                                       required>
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
                                <label for="url_image" class="form-label">
                                    {{ $t('all.url_image') }}
                                </label>
                                <input type="text" class="form-control"
                                       placeholder="Input url"
                                       id="url_image"
                                       v-model="arrInputsModal.url_image">
                            </div>

                            <!-- Word description -->
                            <div class="form-group">
                                <label for="word_description" class="form-label">
                                    {{ $t('all.word_description') }}
                                </label>
                                <textarea class="form-control"
                                          id="word_description"
                                          placeholder="Insert description word"
                                          v-model="arrInputsModal.description"
                                          rows="3"></textarea>
                            </div>

                            <!-- типы значений слова -->
                            <div class="block_type" v-show="getCodeLearnLanguage2 == 'en'">
                                <!-- select значений -->
                                <div class="box-left-site">
                                    <div class="form-group">
                                        <label for="select_type" class="form-label">
                                            {{ $t('all.word_type') }}
                                        </label>
                                        <select id="select_type"
                                                v-model="arrInputsModal.select_type_id"
                                                class="custom-select"
                                                size="6">
                                            <option v-for="(type, key) in allTypes" :key="key"
                                                    :value="type.id">
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
                                         class="box-time-forms">
                                        <!-- прошедшее -->
                                        <div class="box-past">
                                            <label class="form-label">
                                                {{ $t('all.past_time') }}
                                            </label>
                                            <input type="text" class="form-control" placeholder="Insert word"
                                                   v-model="arrInputsModal.objWordTimeForms.past.word">
                                            <input type="text" class="form-control" placeholder="Insert translation"
                                                   v-model="arrInputsModal.objWordTimeForms.past.translation">
                                            <input type="text" class="form-control" placeholder="Insert accent"
                                                   v-model="arrInputsModal.objWordTimeForms.past.accent">
                                        </div>
                                        <!-- настоящее -->
                                        <div class="box-present">
                                            <label class="form-label">
                                                {{ $t('all.present_time') }}
                                            </label>
                                            <input type="text" class="form-control" placeholder="Insert word"
                                                   v-model="arrInputsModal.objWordTimeForms.present.word">
                                            <input type="text" class="form-control" placeholder="Insert translation"
                                                   v-model="arrInputsModal.objWordTimeForms.present.translation">
                                            <input type="text" class="form-control" placeholder="Insert accent"
                                                   v-model="arrInputsModal.objWordTimeForms.present.accent">
                                        </div>
                                        <!-- будущее -->
                                        <div class="box-future">
                                            <label class="form-label">
                                                {{ $t('all.future_time') }}
                                            </label>
                                            <input type="text" class="form-control" placeholder="Insert word"
                                                   v-model="arrInputsModal.objWordTimeForms.future.word">
                                            <input type="text" class="form-control" placeholder="Insert translation"
                                                   v-model="arrInputsModal.objWordTimeForms.future.translation">
                                            <input type="text" class="form-control" placeholder="Insert accent"
                                                   v-model="arrInputsModal.objWordTimeForms.future.accent">
                                        </div>
                                    </div>
                                    <!-- числительные -->
                                    <div v-if="arrInputsModal.objNumber !== null">
                                        <label class="form-label">
                                            {{ $t('all.enter_digit') }}
                                        </label>
                                        <input type="text" class="form-control" placeholder="Insert number"
                                               v-model="arrInputsModal.objNumber.number">
                                    </div>
                                    <!-- союзы -->
                                    <div v-if="arrInputsModal.objConjunction !== null"
                                         class="box-conjunction-select">
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
                                           @click="preventDefault">
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
                                    v-if="!objGenerateSentences.status_toggle || objGenerateSentences.boolAddSentences">
                                {{ $t('all.create') }}
                            </button>
                            <!-- next to generate sentences-->
                            <button type="button" class="btn btn-success"
                                    :class="{'un_active': $v.$invalid, 'active2': !$v.$invalid}"
                                    :disabled="$v.$invalid"
                                    @click="loadGenerateSentences()"
                                    v-if="objGenerateSentences.status_toggle && !objGenerateSentences.boolAddSentences">
                                {{ $t('all.next_2') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modals обновить слово  -->
        <div class="modal fade" id="update_word" tabindex="-1" role="dialog"
             aria-labelledby="update_word" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <!-- header -->
                    <div class="modal-header">
                        <h5 class="modal-title text-lg font-semibold" v-if="!objGenerateSentences.boolAddSentences">
                            {{ $t('all.update_word') }}
                        </h5>
                        <h5 class="modal-title text-lg font-semibold" v-else>
                            {{ $t('all.loading_generate_sentences') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <!-- body -->
                    <div class="modal-body">
                        <!-- Чекбоксы сгенерированных предложений -->
                        <div class="box-view-generate-sentences"
                            :class="{ 'visible-generate-sentences': objGenerateSentences.boolAddSentences }">
                            <!-- Индикатор загрузки предложений -->
                            <div class="dots-loader"
                                 v-if="!objGenerateSentences.boolLoadingIndicator">
                                <div class="dot"></div>
                                <div class="dot"></div>
                                <div class="dot"></div>
                            </div>
                            <!-- Сгенерированные предложения -->
                            <div class="box-new-sentence"
                                 v-for="(obj, key) in objGenerateSentences.arrGenerateSentences" :key="key">
                                <input type="checkbox" class="form-check-input"
                                       :value="obj"
                                       v-model="objGenerateSentences.selectedSentences">
                                <div class="box-sentence">
                                    <div class="original-sentence" v-text="obj.original"></div>
                                    <div class="translation-sentence" v-text="obj.translated"></div>
                                </div>
                            </div>
                        </div>
                        <!-- Поля создаваемого слова -->
                        <form action="#" v-show="!objGenerateSentences.boolAddSentences" class="space-y-4">
                            <!-- word -->
                            <div class="form-group">
                                <label for="old_word" class="form-label">
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
                                       required>
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
                                <label for="update_translation" class="form-label">
                                    {{ $t('all.translation') }}
                                </label>
                                <input type="text" class="form-control"
                                       placeholder="Insert translation"
                                       id="update_translation"
                                       v-model="arrInputsModal.translation_word"
                                       @blur="touchTranslationWord()"
                                       :class="{'is-invalid': $v.arrInputsModal.translation_word.$error}"
                                       required>
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
                                <label for="update_url_image" class="form-label">
                                    {{ $t('all.url_image') }}
                                </label>
                                <input type="text"
                                       class="form-control"
                                       placeholder="Input url"
                                       id="update_url_image"
                                       v-model="arrInputsModal.url_image">
                            </div>

                            <!-- Word description -->
                            <div class="form-group">
                                <label for="update_word_description" class="form-label">
                                    {{ $t('all.word_description') }}
                                </label>
                                <textarea v-model="arrInputsModal.description"
                                          class="form-control"
                                          id="update_word_description"
                                          placeholder="Insert description word"
                                          rows="3">
                                </textarea>
                            </div>

                            <!-- select type word-->
                            <div class="block_type" v-show="getCodeLearnLanguage2 == 'en'">
                                <!-- select значений -->
                                <div class="box-left-site">
                                    <div class="form-group">
                                        <label for="update_select_type" class="form-label">
                                            {{ $t('all.word_type') }}
                                        </label>
                                        <select id="update_select_type"
                                                v-model="arrInputsModal.select_type_id"
                                                class="custom-select"
                                                size="6">
                                            <option v-for="(type, key) in allTypes" :key="key"
                                                    :value="type.id">
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
                                         class="box-time-forms">
                                        <!-- прошедшее -->
                                        <div class="box-past">
                                            <label class="form-label">
                                                {{ $t('all.past_time') }}
                                            </label>
                                            <input type="text" class="form-control" placeholder="Insert word"
                                                   v-model="arrInputsModal.objWordTimeForms.past.word">
                                            <input type="text" class="form-control" placeholder="Insert translation"
                                                   v-model="arrInputsModal.objWordTimeForms.past.translation">
                                            <input type="text" class="form-control" placeholder="Insert accent"
                                                   v-model="arrInputsModal.objWordTimeForms.past.accent">
                                        </div>
                                        <!-- настоящее -->
                                        <div class="box-present">
                                            <label class="form-label">
                                                {{ $t('all.present_time') }}
                                            </label>
                                            <input type="text" class="form-control" placeholder="Insert word"
                                                   v-model="arrInputsModal.objWordTimeForms.present.word">
                                            <input type="text" class="form-control" placeholder="Insert translation"
                                                   v-model="arrInputsModal.objWordTimeForms.present.translation">
                                            <input type="text" class="form-control" placeholder="Insert accent"
                                                   v-model="arrInputsModal.objWordTimeForms.present.accent">
                                        </div>
                                        <!-- будущее -->
                                        <div class="box-future">
                                            <label class="form-label">
                                                {{ $t('all.future_time') }}
                                            </label>
                                            <input type="text" class="form-control" placeholder="Insert word"
                                                   v-model="arrInputsModal.objWordTimeForms.future.word">
                                            <input type="text" class="form-control" placeholder="Insert translation"
                                                   v-model="arrInputsModal.objWordTimeForms.future.translation">
                                            <input type="text" class="form-control" placeholder="Insert accent"
                                                   v-model="arrInputsModal.objWordTimeForms.future.accent">
                                        </div>
                                    </div>
                                    <!-- числительные -->
                                    <div v-if="arrInputsModal.objNumber !== null" >
                                        <label class="form-label">
                                            {{ $t('all.enter_digit') }}
                                        </label>
                                        <input type="text" class="form-control" placeholder="Insert number"
                                               v-model="arrInputsModal.objNumber.number">
                                    </div>
                                    <!-- союзы -->
                                    <div v-if="arrInputsModal.objConjunction !== null"
                                         class="box-conjunction-select">
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
                                        {{ capitalizeFirstLetter(sentence.sentence) }}
                                    </div>
                                </div>

                                <!-- переключатель добавления предложений -->
                                <div class="form-check form-switch" @click="toggleSwitch($event, 'toggle2')">
                                    <input ref="toggle2" class="form-check-input" type="checkbox" id="toggle2"
                                           @click="preventDefault">
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
                                    v-if="!objGenerateSentences.status_toggle || objGenerateSentences.boolAddSentences">
                                {{ $t('all.update') }}
                            </button>
                            <!-- next to generate sentences-->
                            <button type="button" class="btn btn-success"
                                    :class="{'un_active': $v.$invalid, 'active2': !$v.$invalid}"
                                    :disabled="$v.$invalid"
                                    @click="loadGenerateSentences()"
                                    v-if="objGenerateSentences.status_toggle && !objGenerateSentences.boolAddSentences">
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
                serverParams: {
                    selection_type_id: null,
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
            // Предложения с большой буквы
            capitalizeFirstLetter(sentence) {
                if (!sentence) return '';
                return sentence.charAt(0).toUpperCase() + sentence.slice(1);
            },
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
                    const data = this.getDataSaveServer()

                    const response = await this.$http.post(`${this.$http.webUrl()}word`, data);

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
            async getPresentTenseWords() {
                try {
                    const response = await this.$http.get(`${this.$http.webUrl()}word/get-present-tense`);

                    if (this.checkSuccess(response)) {
                        // Получаем все слова через запятую
                        const words = response.data.data
                            .map(item => item.word)
                            .join(', ');

                        // Создаем временный textarea элемент
                        const textarea = document.createElement('textarea');
                        textarea.value = words;
                        textarea.style.position = 'fixed';  // Предотвращаем прокрутку до элемента
                        textarea.style.opacity = '0';       // Делаем элемент невидимым
                        document.body.appendChild(textarea);
                        textarea.select();

                        try {
                            // Пытаемся скопировать текст
                            document.execCommand('copy');
                            // Показываем уведомление об успешном копировании
                            this.$swal({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                icon: 'success',
                                title: 'Слова скопированы в буфер обмена'
                            });
                        } catch (err) {
                            console.error('Ошибка при копировании:', err);
                            this.$swal({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                icon: 'error',
                                title: 'Ошибка при копировании в буфер обмена'
                            });
                        } finally {
                            // Удаляем временный элемент
                            document.body.removeChild(textarea);
                        }
                    }
                } catch (e) {
                    console.error('Ошибка при получении слов в настоящем времени:', e);
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

.page-list-words {
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

    .justify-end {
        justify-content: flex-end;
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

    .font-bold {
        font-weight: 700;
    }

    .font-semibold {
        font-weight: 600;
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

    .card {
        background-color: var(--card);
        color: var(--card-foreground);
        border-radius: 0;
        border: 1px solid var(--border);
        box-shadow: none !important;
    }

    .card-body {
        padding: var(--spacing-6);
    }

    .p-0 {
        padding: 0;
    }

    .relative {
        position: relative;
    }

    .form-select {
        display: block;
        width: 100%;
        padding: 0.5rem 2.5rem 0.5rem 0.75rem;
        font-size: 0.875rem;
        font-weight: 500;
        line-height: 1.25rem;
        color: var(--foreground);
        background-color: var(--background);
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 0.5rem center;
        background-repeat: no-repeat;
        background-size: 1.5em 1.5em;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        appearance: none;
        transition: border-color 200ms ease-in-out, box-shadow 200ms ease-in-out;

        &:focus {
            outline: 2px solid transparent;
            outline-offset: 2px;
            border-color: var(--ring);
            box-shadow: 0 0 0 2px var(--ring);
        }

        option {
            cursor: pointer;

            &:first-child {
                background: var(--muted);
                color: var(--muted-foreground);
                cursor: default;
            }
        }
    }

    .search-select-types {
        padding: 0.375rem 2rem 0.375rem 0.75rem;
        margin-right: 0.5rem;
        cursor: pointer;
        min-width: 120px;
        height: 32px;
        line-height: 1.5;
    }

    .form-select {
        border-radius: 0 !important;
    }

    .button-present-tense {
        white-space: nowrap;
        min-width: auto;
        margin-right: 0.25rem;
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
            overflow: hidden;
            padding: var(--spacing-4) 0;

            .box-time-forms {
                label {
                    padding: 0.25rem 0;
                    margin: 0;
                    font-weight: 500;
                    color: var(--foreground);
                }

                .box-past, .box-present, .box-future {
                    margin-bottom: var(--spacing-4);

                    input {
                        margin-bottom: 0.5rem;

                        &:last-child {
                            margin: 0;
                        }
                    }
                }
            }

            .box-content-sentences {
                display: flex;
                justify-content: space-between;
                align-items: flex-end;
                margin-top: var(--spacing-6);

                .box-sentences {
                    div {
                        color: var(--muted-foreground);
                        font-weight: 600;
                        font-size: 0.875rem;
                        margin-bottom: 0.25rem;
                    }
                }

                .form-switch {
                    display: flex;
                    flex-flow: column nowrap;
                    align-items: center;
                    margin: 1.25rem 0 0 0;
                    border: 1px solid var(--border);
                    padding: var(--spacing-4);
                    border-radius: var(--radius);
                    cursor: pointer;
                    background-color: var(--muted/50);

                    input {
                        margin: 0;
                        cursor: pointer;
                    }

                    label {
                        text-align: center;
                        line-height: 1.25rem;
                        margin-top: 0.5rem;
                        width: 110px;
                        cursor: pointer;
                        font-size: 0.875rem;
                        font-weight: 500;
                    }
                }
            }

            .box-view-generate-sentences {
                width: 100%;
                height: 100%;
                background-color: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                position: absolute;
                left: 100%;
                top: 0;
                right: 0;
                bottom: 0;
                z-index: 1;
                transition: left 0.3s ease-in-out;
                border-radius: var(--radius);

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
                        background-color: var(--primary);
                        border-radius: 50%;
                        animation: bounce 1.5s infinite ease-in-out;

                        &:nth-child(2) {
                            animation-delay: -0.5s;
                        }

                        &:nth-child(3) {
                            animation-delay: -1s;
                        }

                        @keyframes bounce {
                            0%, 100% {
                                transform: scale(0);
                            }
                            50% {
                                transform: scale(1);
                            }
                        }
                    }
                }

                .box-new-sentence {
                    display: flex;
                    align-items: center;
                    padding: 0.5rem 1rem;
                    border-bottom: 1px solid var(--border);

                    &:last-child {
                        border: none;
                    }

                    .form-check-input {
                        padding: 0;
                        position: static;
                        cursor: pointer;
                        margin: 0 1rem 0 0;
                        min-width: 16px;
                        min-height: 16px;
                    }

                    .box-sentence {
                        div {
                            line-height: 1.5;

                            &:first-child {
                                margin-bottom: 0.25rem;
                            }
                        }

                        .original-sentence {
                            font-weight: 600;
                            font-size: 1rem;
                            color: var(--foreground);
                        }

                        .translation-sentence {
                            color: var(--muted-foreground);
                            font-size: 0.875rem;
                        }
                    }
                }
            }

            .visible-generate-sentences {
                left: 0;
                position: relative;
            }

            form {
                padding: 0 var(--spacing-4);
            }

            .block_type {
                margin-top: 2rem;

                .box-left-site {
                    width: 38%;

                    .custom-select {
                        border: 1px solid var(--border);
                        border-radius: var(--radius);

                        option {
                            font-weight: 400;
                            font-size: 0.875rem;
                            padding: 0 0.5rem;
                        }
                    }

                    .form-group {
                        margin: 0;
                    }
                }
            }

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

    #create_word {
        .modal-body {
            .box-content-sentences {
                justify-content: flex-end;
            }
        }
    }
}

.box-conjunction-select {
    margin-top: 0.5rem;

    label {
        margin-top: 0.5rem;
        font-size: 0.875rem;
        color: var(--muted-foreground);
    }
}

// Responsive adjustments
@media (max-width: 768px) {
    .page-list-words {
        .flex.items-center.justify-between {
            flex-direction: column;
            align-items: flex-start;
            gap: var(--spacing-4);
        }

        .space-y-3 {
            flex-wrap: wrap;
            gap: var(--spacing-2);
        }

        .modal {
            .modal-body {
                .block_type {
                    .box-left-site {
                        width: 100%;
                        margin-bottom: var(--spacing-4);
                    }
                }
            }
        }
    }
}
</style>
