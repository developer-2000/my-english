import 'sweetalert2/dist/sweetalert2.min.css';

export default {
    data() {
        return { }
    },
    methods: {
        // проверка backup данных axios
        checkSuccess(response) {
            if(response?.data?.error === null){
                return true;
            }

            return false;
        },
        // alert сообщение на странице
        message(msg = '', icon) {
            console.log('🔍 [RESPONSE_METHODS] message called, msg:', msg, 'icon:', icon);
            const Toast = this.$swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true,
            });
            Toast.fire({
                icon: icon,
                title: msg
            })
        },
        // confirm сообщение на странице
        confirmMessage(msg = '', icon, id) {
            console.log('🔍 [RESPONSE_METHODS] confirmMessage called, msg:', msg, 'icon:', icon, 'id:', id);
            this.$swal({
                title: '',
                text: msg,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                console.log('🔍 [RESPONSE_METHODS] Confirm dialog result:', result);
                if (result.isConfirmed) {
                    console.log('🔍 [RESPONSE_METHODS] User confirmed deletion, calling deleteWord with id:', id);
                    this.deleteWord(id);
                }
            })
        },
    },
    props: [ ],
    mounted() { }
}

