
export default {
    data() {
        return {
            speak: {
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
            this.setText().then();
            this.soundSpeak();
        },
        // сбор текста в выбранных checkbox eng и перевод
        // [ ['eng','ru'], [] ]
        async setText() {
            let checkboxes = document.getElementsByClassName('check');
            this.speak.arrText = [];
            let id = 0;

            let promise = await new Promise((resolve, reject) => {
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
            });
            return await promise;
        },
        soundSpeak(last_checkbox = []) {
            let text = '';
            window.speechSynthesis.cancel();
            let synthesis = window.speechSynthesis;

            // вернуть следущий checkbox
            if(last_checkbox.length === 0){
                last_checkbox = this.addNextCheckbox();
            }

            if(last_checkbox){
                // существует елемент текста
                if (this.speak.cycle < last_checkbox.length) {
                    text = last_checkbox[this.speak.cycle];
                    let utterance = new SpeechSynthesisUtterance(text);
                    // определить язык текста
                    let index_lang = this.getIndexLanguage(text, synthesis.getVoices());

                    setTimeout(() => {
                        utterance.voice = synthesis.getVoices()[index_lang];
                        // озвучить текст
                        synthesis.speak(utterance);
                    }, 1000);

                    // событие завершения озвучки
                    utterance.addEventListener('end', (event) => {
                        this.speak.cycle++;
                        this.soundSpeak(last_checkbox);
                    });
                }
                // выбрать следущий checkbox
                else {
                    this.speak.cycle = 0;
                    this.soundSpeak();
                }
            }
        },
        // вернуть следущий checkbox
        addNextCheckbox() {
            if (this.speak.nextCheckbox.length < this.speak.arrText.length) {
                this.speak.nextCheckbox.push(this.speak.arrText[this.speak.nextCheckbox.length]);
                // новый checkbox
                return this.speak.nextCheckbox[this.speak.nextCheckbox.length - 1];
            }
            return false;
        },
        getIndexLanguage(text, synthesis) {
            // все языки
            for (let i = 0; i < this.speak.lang.length; i++) {
                // все буквы языка
                for (let s = 0; s < this.speak.lang[i].alpha.length; s++) {
                    if (text.indexOf(this.speak.lang[i].alpha[s]) !== -1) {
                        // доступные языки в обьекте озвучки
                        for (let l = 0; l < synthesis.length; l++) {
                            if (synthesis[l].lang === this.speak.lang[i].lang) {
                                // вернуть индекс доступного языка
                                return l;
                            }
                        }
                    }
                }
            }
        },
    }
}

