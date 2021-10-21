
export default {
    data() {
        return {
            speak: {
                stop: false,
                synthesis: window.speechSynthesis,
                arrText: [],
                nextCheckbox: [],
                cycle: 0,
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
            this.speak.cycle = 0;
            this.speak.nextCheckbox = [];
            this.speak.synthesis.cancel();
            // остановить возможно предыдущий запущеный sound
            this.speak.stop = true;
            if(this.setText()){
                this.forSpeak();
            }
        },
        // сбор текста в выбранных checkbox eng и перевод
        // [ ['eng','ru'], [] ]
        setText() {
            let checkboxes = document.getElementsByClassName('check');
            this.speak.arrText = [];
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
                        }
                    }
                }
            }
            return 1;
        },
        forSpeak() {
            setTimeout(() => {
                this.speak.stop = false;
                this.speak.arrText.forEach((arrRow, index1) => {
                    Promise.all( arrRow.map(this.readSound) ).then( data => {
                        // console.log(data)
                    } );
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
                    if(!this.speak.stop){
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
    },
}

