
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
            console.log('🔍 [HELP_SEARCH] searchWord called with:', word);
            console.log('🔍 [HELP_SEARCH] isLoading before:', this.isLoading);
            
            try {
                this.isLoading = true;
                console.log('🔍 [HELP_SEARCH] Making HTTP request for word:', word);
                const response = await this.$http.get(
                    `${this.$http.webUrl()}sentence/search-word?word=${word}`
                );
                console.log('🔍 [HELP_SEARCH] Response received:', response.status);
                if(this.checkSuccess(response)){
                    this.help_dynamic = response.data.data.string;
                    console.log('🔍 [HELP_SEARCH] help_dynamic updated, length:', this.help_dynamic.length);
                }
            } catch (e) {
                console.error('🔍 [HELP_SEARCH] Error in searchWord:', e);
            }
            this.isLoading = false;
            console.log('🔍 [HELP_SEARCH] isLoading after:', this.isLoading);
        },
        // логика выборки вводного слова
        searchHelpWord(word){
            console.log('🔍 [HELP_SEARCH] searchHelpWord called with:', word);
            console.log('🔍 [HELP_SEARCH] help_dynamic length before:', this.help_dynamic.length);
            console.log('🔍 [HELP_SEARCH] arrow value:', this.arrow);
            
            let string = word
            let last_word = string.split(" ").pop();
            console.log('🔍 [HELP_SEARCH] last_word:', last_word);
            
            if (last_word.trim() !== '') {
                // найти в базе подходящие слова
                console.log('🔍 [HELP_SEARCH] Calling searchWord for:', last_word);
                this.searchWord(last_word);
            }
            let dynamic_line = this.help_dynamic.split(" ");
            console.log('🔍 [HELP_SEARCH] dynamic_line length:', dynamic_line.length);
            
            // внести слово - символы передаютса, в textarea есть буквы, в строке нет этого слова
            if(
                last_word.trim() === '' &&
                string !== '' &&
                this.arrow === 0 &&
                dynamic_line.length === 1
            ){
                console.log('🔍 [HELP_SEARCH] Clearing help_dynamic (condition 1)');
                this.help_dynamic = '';
            }
            else if(string == ''){
                console.log('🔍 [HELP_SEARCH] Clearing help_dynamic (condition 2)');
                this.help_dynamic = '';
            }
            
            console.log('🔍 [HELP_SEARCH] help_dynamic length after:', this.help_dynamic.length);
        },
        // клавиши на поле ввода
        keysOnInputField(){
            console.log('🔍 [HELP_SEARCH] keysOnInputField called');
            let elem = document.getElementsByClassName('entry-field-help');
            console.log('🔍 [HELP_SEARCH] Found elements:', elem.length);
            
            [].forEach.call(elem,(el, index) => {
                console.log('🔍 [HELP_SEARCH] Adding event listener to element:', index);
                el.addEventListener('keydown', (e) => {
                    console.log('🔍 [HELP_SEARCH] Key pressed:', e.code, 'arrow before:', this.arrow);
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
                    console.log('🔍 [HELP_SEARCH] arrow after:', this.arrow);
                });
            });
        },
    },
    mounted() {
        console.log('🔍 [HELP_SEARCH] Mixin mounted');
        this.keysOnInputField();
    }
}

