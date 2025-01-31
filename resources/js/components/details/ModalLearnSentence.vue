<template>
    <!-- Modals учить слова  -->
    <div class="modal fade" id="learn_sentences" tabindex="-1" role="dialog"
         aria-labelledby="learn_sentences_label" aria-hidden="true"
    >
        <div class="modal-dialog custom-modal" role="document">
            <div class="modal-content">
                <!-- header -->
                <div class="modal-header">
                    <h5 class="modal-title">
                        Изучать предложения
                    </h5>
                    <div class="box-right">
                        <!-- закрыть модалку -->
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                <!-- body -->
                <div class="modal-body">
                    <template v-if="currentSentence">
                        <!-- слово и кнопки -->
                        <div class="box-word">
                            <!-- слово -->
                            <div class="learn-word-trigger"
                                 v-text="currentSentence.sentence"
                            ></div>
                            <!-- кнопки -->
                            <div class="box-button">
                                <!-- button Не знаю -->
                                <a class="btn btn-success" role="button"
                                   @click="loadLearnSentence('up')"
                                >
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M214.6 41.4c-12.5-12.5-32.8-12.5-45.3 0l-160 160c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L160 141.2V448c0 17.7 14.3 32 32 32s32-14.3 32-32V141.2L329.4 246.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3l-160-160z"/></svg>
                                {{ $t('all.do_not_know') }}
                                <div class="alert-number" v-text="not_know"></div>
                                </a>
                                <!-- button Знаю -->
                                <a class="btn btn-warning" role="button"
                                   @click="loadLearnSentence('down')"
                                >
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M169.4 470.6c12.5 12.5 32.8 12.5 45.3 0l160-160c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L224 370.8 224 64c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 306.7L54.6 265.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l160 160z"/></svg>
                                {{ $t('all.know') }}
                                <div class="alert-number" v-text="know"></div>
                                </a>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import response_methods_mixin from "../../mixins/response_methods_mixin";
import translation_i18n_mixin from "../../mixins/translation_i18n_mixin";
import {tippy} from "vue-tippy";
import $ from "jquery";

export default {
    data() {
        return {
            know: 0,
            not_know: 0,
            last_updated_at: null,
            currentSentence: null,    // изучаемое слово
        };
    },
    components: { },
    mixins: [
        response_methods_mixin,
        translation_i18n_mixin
    ],
    computed: { },
    watch: { },
    methods: {
        // загрузка изучаемого слова
        async loadLearnSentence(action = null) {
            if(action == "up"){
                this.not_know++
            }
            else if(action == "down"){
                this.know++
            }

            try {
                let data = {
                    action: action,
                    sentence_id: this.currentSentence ? this.currentSentence.id : null,
                }

                const response = await this.$http.post(`${this.$http.webUrl()}sentence/learn/get-sentence`, data);

                if(this.checkSuccess(response)){
                    this.currentSentence = response.data.data.nextSentence
                    console.log(this.currentSentence)
                }
            }
            catch (e) {
                console.log(e);
            }
        },
        // открываем модалку изучения слов
        openLearnModal() {
            $('#learn_sentences').modal('show');
            // инициализация hover на изучаемое слово
            $('body').on('mouseover', '.learn-word-trigger', (event) => {
                this.outputHelperAlertInLearn(event)
            });
            this.loadLearnSentence()
        },
        // вывод подсказки при наведении на слово в таблице
        outputHelperAlertInLearn(event){
            let html = `<div style="text-align: left;">
<div style="font-weight: 700;">${this.currentSentence.translation == null ? '' : this.currentSentence.translation.toLowerCase()}</div>
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
    },
    mounted() {

    },
    name: "ModalLearnSentence"
}
</script>

<style lang="scss" scoped>

#learn_sentences{
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
