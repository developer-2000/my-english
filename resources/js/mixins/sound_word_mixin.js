export default {
    data() {
        return {
            speak: {
                repeat_bool: false,
                count_repeat: 1,
                stop: false,
                start: false,
                pause: false,
                synthesis: window.speechSynthesis,
                arrText: [],
                arrIdCollText: [],
                pauseArrText: [],
                lang: [
                    {
                        lang: 'en-US',
                        alpha: ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z']
                    },
                    {
                        lang: 'ru-RU',
                        alpha: ['а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ь', 'ы', 'ъ', 'э', 'ю', 'я']
                    },
                ]
            },
        }
    },
    methods: {
        initialSpeak() {
            this.voiceActingStatus({name:'start_true'});
            // остановить возможно предыдущий запущеный sound
            this.speak.synthesis.cancel();
            this.voiceActingStatus({name:'stop_true'});
            if (this.setText()) {
                this.forSpeak();
            }
        },
        // сбор текста в выбранных checkbox eng и перевод
        // [ ['eng','ru'], [] ]
        setText() {
            let checkboxes = document.getElementsByClassName('memorable_checkbox');
            this.speak.arrText = [];
            this.speak.arrIdCollText = [];
            let id = 0;
            let arrBox = [];
            // все checkboxes
            for (let i = 0; i < checkboxes.length; i++) {
                // он выбран
                if (checkboxes[i].checked) {
                    id = checkboxes[i].getAttribute('data-id');

                    for (let r = 0; r < this.table.rows.length; r++) {
                        arrBox = [];

                        if (id == this.table.rows[r].id) {
                            // без повторений
                            if(!this.speak.repeat_bool){
                                this.speak.arrText.push([
                                    this.table.rows[r].sentence,
                                    this.table.rows[r].translation
                                ]);
                            }
                            else{
                                arrBox.push(this.table.rows[r].translation);
                                for (let s = 0; s < this.speak.count_repeat; s++) {
                                    arrBox.push(this.table.rows[r].sentence);
                                }
                                this.speak.arrText.push(arrBox);
                            }
                            this.speak.arrIdCollText.push(id);
                        }
                    }
                }
            }
            this.speak.pauseArrText = [...this.speak.arrText];
            this.speak.pauseIdCollText = [...this.speak.arrIdCollText];
            return true;
        },
        forSpeak() {
            console.log('🔍 [SOUND] forSpeak called, arrText length:', this.speak.arrText.length);
            console.log('🔍 [SOUND] speak.start:', this.speak.start);
            console.log('🔍 [SOUND] repeat_bool:', this.speak.repeat_bool);
            console.log('🔍 [SOUND] count_repeat:', this.speak.count_repeat);
            
            let item_count = 0;
            let arr_count = this.speak.arrText.length;

            setTimeout(() => {
                this.voiceActingStatus({name:'stop_false'});
                // 1 прокрутка к тексту
                this.scrollingToText(this.speak.arrIdCollText[0]);

                this.speak.arrText.forEach((arrRow, index) => {
                    // по очереди прочесть эти языки
                    Promise.all(arrRow.map(this.readSound)).then(data => {
                        if(this.speak.start){
                            // количество отработаных индексов озвучки
                            item_count++;
                            // прокрутка к тексту
                            this.scrollingToText(this.speak.arrIdCollText[item_count]);
                            this.voiceActingStatus({name:'arr_count', arr_count:arr_count, data:data, item_count:item_count});
                        }
                    });
                })
            }, 200);
        },
        async readSound(text, index) {
            console.log('🔍 [SOUND] readSound called for:', text);
            return new Promise(resolve => {
                let utterance = new SpeechSynthesisUtterance(text);
                // определить язык текста
                let index_lang = this.getIndexLanguage(text, this.speak.synthesis.getVoices());
                // установить переводчика
                utterance.voice = this.speak.synthesis.getVoices()[index_lang];
                // озвучить текст
                this.speak.synthesis.speak(utterance);

                // событие завершения озвучки
                utterance.addEventListener('end', (event) => {
                    if (!this.speak.stop) {
                        // чистка масива для pause
                        if (index == 1) {
                            this.speak.pauseArrText.shift();
                            this.speak.pauseIdCollText.shift();
                        }
                        return resolve(text);
                    }
                });
            });
        },
        getIndexLanguage(text, synthVoices) {
            // все языки
            for (let i = 0; i < this.speak.lang.length; i++) {
                // все буквы языка
                for (let s = 0; s < this.speak.lang[i].alpha.length; s++) {
                    if (text.indexOf(this.speak.lang[i].alpha[s]) !== -1) {
                        // доступные языки в обьекте озвучки
                        for (let l = 0; l < synthVoices.length; l++) {
                            if (synthVoices[l].lang === this.speak.lang[i].lang) {
                                // вернуть индекс доступного языка
                                return l;
                            }
                        }
                    }
                }
            }
        },
        pauseReadSound() {
            console.log('🔍 [SOUND] pauseReadSound called');
            console.log('🔍 [SOUND] speak.stop before:', this.speak.stop);
            console.log('🔍 [SOUND] speak.pause before:', this.speak.pause);
            this.voiceActingStatus({name:'stop_true'});
            this.voiceActingStatus({name:'pause_true'});
            this.speak.synthesis.cancel();
            this.speak.arrText = [...this.speak.pauseArrText];
            this.speak.arrIdCollText = [...this.speak.pauseIdCollText];
            console.log('🔍 [SOUND] speak.stop after:', this.speak.stop);
            console.log('🔍 [SOUND] speak.pause after:', this.speak.pause);
        },
        stopReadSound() {
            console.log('🔍 [SOUND] stopReadSound called');
            console.log('🔍 [SOUND] speak.stop before:', this.speak.stop);
            console.log('🔍 [SOUND] speak.pause before:', this.speak.pause);
            console.log('🔍 [SOUND] speak.start before:', this.speak.start);
            this.voiceActingStatus({name:'stop_false'});
            this.voiceActingStatus({name:'pause_false'});
            this.voiceActingStatus({name:'start_false'});
            this.speak.synthesis.cancel();
            this.changeColorLineSound();
            console.log('🔍 [SOUND] speak.stop after:', this.speak.stop);
            console.log('🔍 [SOUND] speak.pause after:', this.speak.pause);
            console.log('🔍 [SOUND] speak.start after:', this.speak.start);
        },
        continueReadSound() {
            console.log('🔍 [SOUND] continueReadSound called');
            console.log('🔍 [SOUND] speak.pause before:', this.speak.pause);
            this.voiceActingStatus({name:'pause_false'});
            this.forSpeak();
            console.log('🔍 [SOUND] speak.pause after:', this.speak.pause);
        },
        scrollingToText(id) {
            if (id !== undefined) {
                let parent = $('#content-wrapper');
                // положение скроллинга
                let scrolling = parent.scrollTop();
                let elTop = document.getElementById('memorable_checkbox_' + id).getBoundingClientRect().top;
                elTop = scrolling == 0 ? elTop : (elTop + scrolling);
                let elHeight = $('#memorable_checkbox_' + id).height();
                let parentHeight = parent.height();
                let offset = elTop - ((parentHeight - elHeight) / 2);

                this.changeColorLineSound($('#memorable_checkbox_' + id));
                parent.animate({scrollTop: offset}, 700);
            }
        },
        changeColorLineSound(obj = false) {
            $('tr').css({'outline': 'none', 'background': 'none'});
            if (obj) {
                obj.parent().parent().parent().css({'outline': '1px solid rgb(192, 249, 190)', 'background': '#ecffed'});
            }
        },
        voiceActingStatus(object) {
            switch (object.name) {
                // разрешить озвучивание текста
                case 'stop_false':
                    this.speak.stop = false;
                    break;
                // блокировать озвучивание текста
                case 'stop_true':
                    this.speak.stop = true;
                    break;
                // показать кнопку Sound translation
                case 'start_false':
                    this.speak.start = false;
                    break;
                // спрятать кнопку Sound translation
                case 'start_true':
                    this.speak.start = true;
                    break;
                // показать кнопку continue
                case 'pause_true':
                    this.speak.pause = true;
                    break;
                // спрятать кнопку continue
                case 'pause_false':
                    this.speak.pause = false;
                    break;
                // востановить кнопку озвучки
                case 'arr_count':
                    if (object.item_count === object.arr_count) {
                        this.voiceActingStatus({name:'start_false'});
                        this.changeColorLineSound();
                    }
                    break;
            }
        },
        preloadLanguages() {
            let object = new SpeechSynthesisUtterance('hello');
            this.speak.synthesis.speak(object);
            this.speak.synthesis.cancel();
        },
    },
    mounted() {
        this.preloadLanguages();

        window.speechSynthesis.onvoiceschanged = function() {
            // console.log(window.speechSynthesis.getVoices())
        }
    },
}

