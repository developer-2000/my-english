<template>
    <!-- Modals учить слова  -->
    <div class="modal fade" id="learn_word" tabindex="-1" role="dialog" aria-labelledby="learn_word_label" aria-hidden="true">
        <div class="modal-dialog custom-modal" role="document">
            <div class="modal-content">
                <!-- header -->
                <div class="modal-header">
                    <h5 class="modal-title">Learn words</h5>
                    <div class="box-right">
                        <!-- переключатель языка слова -->
                        <div class="language-switch"
                             @click="switchLanguage()"
                             v-text="viewLanguageLine()"
                        ></div>
                        <!-- закрыть модалку -->
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <!-- body -->
                <div class="modal-body">
                    <template v-if="objLearnWord !== null">
                        <!-- слово м кнопки -->
                        <div class="box-word">
                            <!-- слово -->
                            <div class="learn-word-trigger"
                                 @click="goToEditing(objOldLearnWord.word)"
                                 v-text="objLearnWord.word"
                            ></div>
                            <!-- button Не знаю -->
                            <a class="btn btn-success" role="button"
                               @click="loadLearnWord('up')"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M214.6 41.4c-12.5-12.5-32.8-12.5-45.3 0l-160 160c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L160 141.2V448c0 17.7 14.3 32 32 32s32-14.3 32-32V141.2L329.4 246.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3l-160-160z"/></svg>
                                не знаю
                            </a>
                            <!-- button Знаю -->
                            <a class="btn btn-warning" role="button"
                               @click="loadLearnWord('down')"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M169.4 470.6c12.5 12.5 32.8 12.5 45.3 0l160-160c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L224 370.8 224 64c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 306.7L54.6 265.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l160 160z"/></svg>
                                знаю
                            </a>
                        </div>
                        <div class="box-helper"
                             v-if="objLearnWord.sentences.length > 0 || objLearnWord.url_image !== null"
                        >
                            <!-- изображение слова -->
                            <img v-if="objLearnWord.url_image !== null" :src="objLearnWord.url_image" alt="Image">
                            <!-- предложения -->
                            <div class="box-sentences" v-if="objLearnWord.sentences.length > 0">
                                <div class="sentence"
                                     v-for="(sentence, key) in objLearnWord.sentences" :key="key"
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
</template>

<script>
import response_methods_mixin from "../../mixins/response_methods_mixin";
import {tippy} from "vue-tippy";

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
        };
    },
    mixins: [
        response_methods_mixin,
    ],
    methods: {
        // загрузка изучаемого слова
        async loadLearnWord(action = null) {
            try {
                let data = {
                    last_updated_at: this.last_updated_at,
                    last_word_id: this.objLearnWord !== null ? this.objLearnWord.id : null,
                    action_with_word: action,
                }
                const response = await this.$http.post(`${this.$http.apiUrl()}learn/get-word`, data);

                if(this.checkSuccess(response)){
                    this.objLearnWord = response.data.data
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
            $('#learn_word').modal('show');
            // инициализация hover на изучаемое слово
            $('body').on('mouseover', '.learn-word-trigger', (event) => {
                this.outputHelperAlertInLearn(event)
            });
            // если до этого не открывалось слово
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
        // какое слово отобразить а какое перевод
        switchViewWord() {
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
            this.objLanguage.languageIndex = this.objLanguage.languageIndex === 0 ? 1 : 0
            this.switchViewWord()
        },
        // отобразить с какого на какой язык перевод
        viewLanguageLine() {
            return (this.objLanguage.languageIndex === 0) ?
                this.objLanguage.languages[0] + ' ~ ' + this.objLanguage.languages[1] :
                this.objLanguage.languages[1] + ' ~ ' + this.objLanguage.languages[0]
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
            $('#learn_word').modal('hide');
            setTimeout(()=>{
                this.$emit('callOpenUpdateWordModal', word);
            },500)
        }
    },
    mounted() {
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
                .learn-word-trigger{
                    flex: 1 1 auto;
                    display: flex;
                    justify-content: space-between;
                    padding: 2px 10px;
                    line-height: 38px;
                    cursor: pointer;
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
    }


}

</style>
