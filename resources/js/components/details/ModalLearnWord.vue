<template>
    <!-- Modals учить слова  -->
    <div class="modal fade" id="learn_word" tabindex="-1" role="dialog"
         aria-labelledby="learn_word_label" aria-hidden="true"
         @click.self="closeModal"
    >
        <div class="modal-dialog modal-dialog-centered custom-modal" role="document">
            <div class="modal-content">
                <!-- header -->
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ $t('all.learn_words') }}
                    </h5>
                    <div class="box-right">
                        <!-- переключатель языка слова -->
                        <div class="language-switch"
                             @click="switchLanguage()"
                             v-text="viewLanguageLine()"
                        ></div>
                        <!-- закрыть модалку -->
                        <button type="button" class="btn-close" @click="closeModal" aria-label="Close"></button>
                    </div>
                </div>
                <!-- body -->
                <div class="modal-body">
                    <template v-if="objLearnWord !== null">
                        <!-- слово и кнопки -->
                        <div class="box-word">
                            <!-- слово -->
                            <div class="learn-word-trigger"
                                 @click="goToEditing(objOldLearnWord.word)"
                                 v-text="objLearnWord.word"
                            ></div>
                            <!-- кнопки -->
                            <div class="box-button">
                                <!-- button Не знаю -->
                                <a class="btn btn-success" role="button"
                                   @click="loadLearnWord('up')"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M214.6 41.4c-12.5-12.5-32.8-12.5-45.3 0l-160 160c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L160 141.2V448c0 17.7 14.3 32 32 32s32-14.3 32-32V141.2L329.4 246.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3l-160-160z"/></svg>
                                    {{ $t('all.do_not_know') }}
                                    <div class="alert-number" v-text="not_know"></div>
                                </a>
                                <!-- button Знаю -->
                                <a class="btn btn-warning" role="button"
                                   @click="loadLearnWord('down')"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M169.4 470.6c12.5 12.5 32.8 12.5 45.3 0l160-160c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L224 370.8 224 64c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 306.7L54.6 265.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l160 160z"/></svg>
                                    {{ $t('all.know') }}
                                    <div class="alert-number" v-text="know"></div>
                                </a>
                            </div>
                        </div>
                        <!-- контент слова -->
                        <div class="box-helper"
                             v-if="objLearnWord.sentences.length > 0 || objLearnWord.url_image !== null"
                        >
                            <!-- изображение слова -->
                            <img v-if="objLearnWord.url_image !== null" :src="objLearnWord.url_image" alt="Image">
                            <!-- предложения -->
                            <div class="box-sentences" v-if="objLearnWord.sentences.length > 0">
                                <div class="sentence"
                                     v-for="(sentence, key) in objLearnWord.sentences" :key="key"
                                     v-text="(objLanguage.languageIndex === 0) ? sentence.sentence : sentence.translation"
                                ></div>
                            </div>
                        </div>
                        <!-- write down word  -->
                        <div class="form-group box-write-word">
                            <div class="box-writing">
                                <label for="write_word" class="col-form-label">
                                    {{ $t('all.correct_spelling_of_the_word') }}
                                </label>
                                <textarea class="form-control entry-field-help"
                                          placeholder="Insert new word"
                                          id="write_word"
                                          v-model="write_word"
                                          @keyup="searchHelpWord(write_word)"
                                ></textarea>
                                <help-search-word :help-dynamic="help_dynamic"/>
                            </div>
                            <svg v-show="objOldLearnWord.word === write_word" class="svg-like" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M323.8 34.8c-38.2-10.9-78.1 11.2-89 49.4l-5.7 20c-3.7 13-10.4 25-19.5 35l-51.3 56.4c-8.9 9.8-8.2 25 1.6 33.9s25 8.2 33.9-1.6l51.3-56.4c14.1-15.5 24.4-34 30.1-54.1l5.7-20c3.6-12.7 16.9-20.1 29.7-16.5s20.1 16.9 16.5 29.7l-5.7 20c-5.7 19.9-14.7 38.7-26.6 55.5c-5.2 7.3-5.8 16.9-1.7 24.9s12.3 13 21.3 13L448 224c8.8 0 16 7.2 16 16c0 6.8-4.3 12.7-10.4 15c-7.4 2.8-13 9-14.9 16.7s.1 15.8 5.3 21.7c2.5 2.8 4 6.5 4 10.6c0 7.8-5.6 14.3-13 15.7c-8.2 1.6-15.1 7.3-18 15.2s-1.6 16.7 3.6 23.3c2.1 2.7 3.4 6.1 3.4 9.9c0 6.7-4.2 12.6-10.2 14.9c-11.5 4.5-17.7 16.9-14.4 28.8c.4 1.3 .6 2.8 .6 4.3c0 8.8-7.2 16-16 16H286.5c-12.6 0-25-3.7-35.5-10.7l-61.7-41.1c-11-7.4-25.9-4.4-33.3 6.7s-4.4 25.9 6.7 33.3l61.7 41.1c18.4 12.3 40 18.8 62.1 18.8H384c34.7 0 62.9-27.6 64-62c14.6-11.7 24-29.7 24-50c0-4.5-.5-8.8-1.3-13c15.4-11.7 25.3-30.2 25.3-51c0-6.5-1-12.8-2.8-18.7C504.8 273.7 512 257.7 512 240c0-35.3-28.6-64-64-64l-92.3 0c4.7-10.4 8.7-21.2 11.8-32.2l5.7-20c10.9-38.2-11.2-78.1-49.4-89zM32 192c-17.7 0-32 14.3-32 32V448c0 17.7 14.3 32 32 32H96c17.7 0 32-14.3 32-32V224c0-17.7-14.3-32-32-32H32z"/></svg>
                        </div>
                    </template>
                    <template v-else>
                        <div class="no-word">
                            {{ $t('all.no_words_in_database_to_study') }}
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import response_methods_mixin from "../../mixins/response_methods_mixin.js";
import {tippy} from "vue-tippy";
import helpSearchWord from "./HelpSearchWord.vue";
import help_search_word_mixin from "../../mixins/help_search_word_mixin.js";
import $ from "jquery";
import translation_i18n_mixin from "../../mixins/translation_i18n_mixin.js";
import {mapGetters} from "vuex";

export default {
    data() {
        return {
            objLanguage:{
                languageIndex:0,
                languages: ['Eng', 'Ru'],
                viewWord: '',
                translationWord: '',
            },
            objLearnWord: null,    // изучаемое слово
            objOldLearnWord: null,
            last_updated_at: null, // дата изменения изучаемого слова
            write_word: "",
            know: 0,
            not_know: 0,
            // Добавляем переменные для отслеживания обработчиков
            escapeHandler: null,
            mouseoverHandler: null,
            eventListenersCount: {
                escape: 0,
                mouseover: 0
            }
        };
    },
    components: { helpSearchWord, },
    mixins: [
        help_search_word_mixin,
        response_methods_mixin,
        translation_i18n_mixin
    ],
    computed: {
        ...mapGetters({
            // Геттер для получения текущего языка изучения
            getLearnLanguage: 'getLearnLanguage'
        }),
    },
    watch: {
        // Событие обновления данных Vuex
        getLearnLanguage: {
            handler: 'clearStudyVariables', // Вызывает метод при изменении getLearnLanguage - язык изучения
            immediate: false // Не Вызов сразу после создания компонента
        }
    },
    methods: {
        // загрузка изучаемого слова
        async loadLearnWord(action = null) {
            this.clearWritingVariables()
            if(action == "up"){
                this.not_know++
            }
            else if(action == "down"){
                this.know++
            }
            try {
                let data = {
                    last_updated_at: this.last_updated_at,
                    last_word_id: this.objLearnWord !== null ? this.objLearnWord.id : null,
                    action_with_word: action,
                }
                const response = await this.$http.post(`${this.$http.webUrl()}word/learn/get-word`, data);

                if(this.checkSuccess(response)){
                    this.objLearnWord = response.data.data
                    // копия обьекта
                    this.objOldLearnWord = JSON.parse(JSON.stringify(this.objLearnWord));
                    this.last_updated_at = this.objLearnWord.updated_at
                    this.switchViewWord()
                    // Вызываем событие для вызова метода родителя
                    this.$emit('callInitialData');
                }
            }
            catch (e) {
                console.log(e);
            }
        },
        // открываем модалку изучения слов
        openLearnModal() {
            console.log('🔍 [MODAL_LEARN_WORD] openLearnModal called');
            
            // Проверяем и удаляем существующий обработчик Escape
            if (this.escapeHandler) {
                console.log('🔍 [MODAL_LEARN_WORD] Removing existing escape handler');
                document.removeEventListener('keydown', this.escapeHandler);
                this.eventListenersCount.escape--;
            }
            
            // Добавляем обработчик клавиши Escape
            this.escapeHandler = this.handleEscapeKey.bind(this);
            document.addEventListener('keydown', this.escapeHandler);
            this.eventListenersCount.escape++;
            console.log('🔍 [MODAL_LEARN_WORD] Escape handler added, count:', this.eventListenersCount.escape);
            
            // Открываем модалку изучения слов
            const modalElement = document.getElementById('learn_word');
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
            
            // Удаляем существующий обработчик mouseover перед добавлением нового
            if (this.mouseoverHandler) {
                console.log('🔍 [MODAL_LEARN_WORD] Removing existing mouseover handler');
                $('body').off('mouseover', '.learn-word-trigger', this.mouseoverHandler);
                this.eventListenersCount.mouseover--;
            }
            
            // инициализация hover на изучаемое слово
            this.mouseoverHandler = (event) => {
                this.outputHelperAlertInLearn(event)
            };
            $('body').on('mouseover', '.learn-word-trigger', this.mouseoverHandler);
            this.eventListenersCount.mouseover++;
            console.log('🔍 [MODAL_LEARN_WORD] Mouseover handler added, count:', this.eventListenersCount.mouseover);
            
            // если уже открывалось слово
            if(this.last_updated_at !== null){
                // Преобразование строки в объект Date
                let date = new Date(this.last_updated_at);
                // Добавление одной секунды
                date.setSeconds(date.getSeconds() + 60);
                // Преобразование обратно в строку ISO 8601
                this.last_updated_at = date.toISOString();
            }
            this.loadLearnWord()
        },
        // Закрыть модалку
        closeModal() {
            console.log('🔍 [MODAL_LEARN_WORD] closeModal called');
            
            // Удаляем обработчик клавиши Escape
            if (this.escapeHandler) {
                document.removeEventListener('keydown', this.escapeHandler);
                this.eventListenersCount.escape--;
                console.log('🔍 [MODAL_LEARN_WORD] Escape handler removed, count:', this.eventListenersCount.escape);
                this.escapeHandler = null;
            }
            
            // Удаляем обработчик mouseover
            if (this.mouseoverHandler) {
                $('body').off('mouseover', '.learn-word-trigger', this.mouseoverHandler);
                this.eventListenersCount.mouseover--;
                console.log('🔍 [MODAL_LEARN_WORD] Mouseover handler removed, count:', this.eventListenersCount.mouseover);
                this.mouseoverHandler = null;
            }
            
            const modalElement = document.getElementById('learn_word');
            if (modalElement) {
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) {
                        modal.hide();
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
                }
            }
            
            // Уведомляем родительский компонент о закрытии модалки
            this.$emit('modalClosed');
        },
        // Обработчик клавиши Escape
        handleEscapeKey(event) {
            console.log('🔍 [MODAL_LEARN_WORD] handleEscapeKey called');
            if (event.key === 'Escape') {
                console.log('🔍 [MODAL_LEARN_WORD] Escape key pressed, closing modal');
                this.closeModal();
            }
        },
        // Поменять местами текст и перевод
        switchViewWord() {
            console.log('🔍 [MODAL_LEARN_WORD] switchViewWord called');
            console.log('🔍 [MODAL_LEARN_WORD] languageIndex:', this.objLanguage.languageIndex);
            // выбран русский
            if(this.objLanguage.languageIndex === 1){
                this.objLearnWord.word = this.objOldLearnWord.translation
                this.objLearnWord.translation = this.objOldLearnWord.word
            }
            else{
                this.objLearnWord.word = this.objOldLearnWord.word
                this.objLearnWord.translation = this.objOldLearnWord.translation
            }
        },
        // переключение языков
        switchLanguage(){
            console.log('🔍 [MODAL_LEARN_WORD] switchLanguage called');
            console.log('🔍 [MODAL_LEARN_WORD] languageIndex before:', this.objLanguage.languageIndex);
            this.objLanguage.languageIndex = this.objLanguage.languageIndex === 0 ? 1 : 0
            console.log('🔍 [MODAL_LEARN_WORD] languageIndex after:', this.objLanguage.languageIndex);
            localStorage.setItem('languageIndex', this.objLanguage.languageIndex);
            this.switchViewWord()
        },
        // отобразить с какого на какой язык перевод
        viewLanguageLine() {
            console.log('🔍 [MODAL_LEARN_WORD] viewLanguageLine called');
            console.log('🔍 [MODAL_LEARN_WORD] languageIndex:', this.objLanguage.languageIndex);
            const result = (this.objLanguage.languageIndex === 0) ?
                this.objLanguage.languages[0] + ' ~ ' + this.objLanguage.languages[1] :
                this.objLanguage.languages[1] + ' ~ ' + this.objLanguage.languages[0];
            console.log('🔍 [MODAL_LEARN_WORD] viewLanguageLine result:', result);
            return result;
        },
        // вывод подсказки при наведении на слово в таблице
        outputHelperAlertInLearn(event){
            let html = `<div style="text-align: left;">
<div style="font-weight: 700;">${this.objLearnWord.translation == null ? '' : this.objLearnWord.translation.toLowerCase()}</div>
${this.objLearnWord.description == null ? '' : this.objLearnWord.description.toLowerCase()}
</div>`;

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
        // закрыть модалку изучения и открыть модалку редактирования
        goToEditing(word){
            this.clearWritingVariables()
            // Закрываем модалку изучения слов
            const modalElement = document.getElementById('learn_word');
            if (modalElement) {
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) {
                        modal.hide();
                    }
                } else {
                    modalElement.style.display = 'none';
                    modalElement.classList.remove('show');
                    document.body.classList.remove('modal-open');
                    const backdrop = document.querySelector('.modal-backdrop');
                    if (backdrop) {
                        backdrop.remove();
                    }
                }
            }
            setTimeout(()=>{
                this.$emit('callOpenUpdateWordModal', word);
            },500)
        },
        // Закрыть модалку
        closeAllModals(){
            $(".modal").on("hidden.bs.modal", () => {
                this.clearWritingVariables()
            })
        },
        clearWritingVariables(){
            this.help_dynamic = "";
            this.write_word = ""
        },
        // при смене языка изучения
        clearStudyVariables(){
            this.objLearnWord = null
            this.last_updated_at = null
            this.know = 0
            this.not_know = 0
        },
    },
    mounted() {
        console.log('🔍 [MODAL_LEARN_WORD] Component mounted');
        let languageIndex = localStorage.getItem('languageIndex');
        if(languageIndex !== null){
            this.objLanguage.languageIndex = parseInt(languageIndex)
        }
    },
    beforeDestroy() {
        console.log('🔍 [MODAL_LEARN_WORD] Component destroying, cleaning up event listeners');
        
        // Очищаем все обработчики событий при уничтожении компонента
        if (this.escapeHandler) {
            document.removeEventListener('keydown', this.escapeHandler);
            console.log('🔍 [MODAL_LEARN_WORD] Escape handler cleaned up on destroy');
        }
        
        if (this.mouseoverHandler) {
            $('body').off('mouseover', '.learn-word-trigger', this.mouseoverHandler);
            console.log('🔍 [MODAL_LEARN_WORD] Mouseover handler cleaned up on destroy');
        }
        
        // Сбрасываем счетчики
        this.eventListenersCount.escape = 0;
        this.eventListenersCount.mouseover = 0;
        console.log('🔍 [MODAL_LEARN_WORD] Event listeners count reset to 0');
    },
    name: "ModalLearnWord"
}
</script>

<style lang="scss" scoped>

#learn_word{
    .custom-modal{
        width: 50%;
        max-width:3000px!important;
        .modal-header{
            display: flex;
            justify-content: space-between;
            .box-right{
                display: flex;
                .language-switch{
                    margin-right: 50px;
                    color: #0800ff;
                    padding: 0 5px;
                    cursor: pointer;
                    transition: background 0.3s ease-in-out;
                    &:hover{
                        background: #e7e7e7;
                    }
                }
            }

        }
        .modal-body{
            .box-word{
                display: flex;
                flex-flow: column nowrap;
                .learn-word-trigger{
                    padding: 2px 10px;
                    line-height: 30px;
                    cursor: pointer;
                    font-size: 29px;
                    font-weight: 700;
                    width: 100%;
                    min-height: 40px;
                    margin-bottom: 10px;
                    &:hover{
                        background: #f2f1f1;
                        font-weight: 700;
                    }
                }
                .box-button{
                    display: flex;
                    width: 100%;
                    a{
                        font-size: 16px;
                        width: 50%;
                        position: relative;
                        svg{
                            height: 16px;
                            margin-right: 5px;
                        }
                        .alert-number{
                            position: absolute;
                            right: -8px;
                            top: -12px;
                            background: #d9d9d9;
                            color: black;
                            line-height: 13px;
                            padding: 5px;
                            border-radius: 20px;
                            outline: 1px solid #b4b4b4;
                        }
                    }
                    .btn-success{
                        margin-right: 10px;
                        svg{
                            fill: white;
                        }
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
                .box-sentences{
                    .sentence:last-child{
                        margin-bottom: 20px;
                    }
                }
            }
            .box-write-word{
                display: flex;
                align-items: center;
                .box-writing{
                    width: 80%;
                    margin-right: 30px;
                }
                .svg-like{
                    fill: #339c03;
                    width: 40px;
                    height: 40px;
                }
            }
        }
    }
}

</style>
