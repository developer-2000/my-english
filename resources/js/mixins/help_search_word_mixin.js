
export default {
    data() {
        return {
            help_dynamic: '',
            arrow: 0,
        }
    },
    methods: {
        // найти все что похоже на вводное слово
        async searchWord(word) {
            try {
                this.isLoading = true;
                const response = await this.$http.get(
                    `${this.$http.apiUrl()}sentence/search-word?word=${word}`
                );
                if(this.checkSuccess(response)){
                    this.help_dynamic = response.data.data.string;
                }
            } catch (e) {
                console.log(e);
            }
            this.isLoading = false;
        },
        // логика выборки вводного слова
        searchHelpWord(word){
            let string = word
            let last_word = string.split(" ").pop();
            if (last_word.trim() !== '') {
                // найти в базе подходящие слова
                this.searchWord(last_word);
            }
            let dynamic_line = this.help_dynamic.split(" ");
            // внести слово - символы передаютса, в textarea есть буквы, в строке нет этого слова
            if(
                last_word.trim() === '' &&
                string !== '' &&
                this.arrow === 0 &&
                dynamic_line.length === 1
            ){
                this.help_dynamic = '';
            }
            else if(string == ''){
                this.help_dynamic = '';
            }
        },
        // клавиши на поле ввода
        keysOnInputField(){
            let elem = document.getElementsByClassName('entry-field-help');
            [].forEach.call(elem,(el) => {
                el.addEventListener('keydown', (e) => {
                    if (
                        e.code == 'ArrowUp' ||
                        e.code == 'ArrowDown' ||
                        e.code == 'ArrowLeft' ||
                        e.code == 'ArrowRight' ||
                        e.code == 'Backspace' ||
                        e.code == 'Delete'){
                        this.arrow = 1;
                    }
                    else{
                        this.arrow = 0;
                    }
                });
            });
        },
    },
    mounted() {
        this.keysOnInputField();
    }
}

