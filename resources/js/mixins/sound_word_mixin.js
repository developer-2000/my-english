export default {
    data() {
        return {
            speak: {
                stop: false,
                // true обычная озвучка
                start: false,
                // true пауза
                pause: false,
                synthesis: window.speechSynthesis,
                arrText: [],
                arrIdCollText: [],
                counter_index_sound: 0,
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
            this.speak.start = true;
            // остановить возможно предыдущий запущеный sound
            this.speak.synthesis.cancel();
            this.speak.stop = true;
            if (this.setText()) {
                this.forSpeak();
            }
        },
        // сбор текста в выбранных checkbox eng и перевод
        // [ ['eng','ru'], [] ]
        setText() {
            let checkboxes = document.getElementsByClassName('check');
            this.speak.arrText = [];
            this.speak.arrIdCollText = [];
            let id = 0;

            // все checkboxes
            for (let i = 0; i < checkboxes.length; i++) {
                // он выбран
                if (checkboxes[i].checked) {
                    id = checkboxes[i].getAttribute('data-id');

                    for (let r = 0; r < this.table.rows.length; r++) {
                        if (id == this.table.rows[r].id) {
                            this.speak.arrText.push([
                                this.table.rows[r].sentence,
                                this.table.rows[r].translation
                            ]);
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
            let item_count = 0;
            let arr_count = this.speak.arrText.length;
            this.speak.counter_index_sound = 0;

            setTimeout(() => {
                this.speak.stop = false;
                // 1 прокрутка к тексту
                this.scrollingScrolling(this.speak.arrIdCollText[0]);

                this.speak.arrText.forEach((arrRow, index) => {
                    // по очереди прочесть эти языки
                    Promise.all(arrRow.map(this.readSound)).then(data => {
                        // считаем сколько отработало индексов озвучки
                        this.speak.counter_index_sound++;

                        // 2 прокрутка к тексту
                        this.scrollingScrolling(this.speak.arrIdCollText[this.speak.counter_index_sound]);

                        // 3 востановить кнопку озвучки
                        item_count = data ? (item_count + 1) : item_count;
                        if (item_count === arr_count) {
                            this.speak.start = false;
                        }
                    });
                })
            }, 200);
        },
        async readSound(text, index) {
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
            this.speak.stop = true;
            this.speak.pause = true;
            this.speak.synthesis.cancel();
            this.speak.arrText = [...this.speak.pauseArrText];
            this.speak.arrIdCollText = [...this.speak.pauseIdCollText];
        },
        stopReadSound() {
            this.speak.stop = false;
            this.speak.pause = false;
            this.speak.start = false;
            this.speak.synthesis.cancel();
        },
        continueReadSound() {
            this.speak.pause = false;
            this.forSpeak();
        },
        scrollingScrolling(id) {
            if (id !== undefined) {
                let parent = $('#content-wrapper');
                // положение скроллинга
                let scrolling = parent.scrollTop();
                let elTop = document.getElementById('check_' + id).getBoundingClientRect().top;
                elTop = scrolling == 0 ? elTop : (elTop + scrolling);
                let elHeight = $('#check_' + id).height();
                let parentHeight = parent.height();
                let offset = elTop - ((parentHeight - elHeight) / 2);

                parent.animate({scrollTop: offset}, 700);
            }
        },
    },
    mounted() {

    },
}

