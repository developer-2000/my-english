import 'sweetalert2/dist/sweetalert2.min.css';

export default {
    data() {
        return { }
    },
    methods: {
        checkSuccess(response) {
            // json response
            if(response?.data?.success && response.data.success === true){
                return true;
            }
            // not correct validate laravel
            else if(response?.data?.status && response.data.status === 'error'){
                if(response?.data?.code === 422){
                    this.message(response.data.message, 'error');
                }
            }
            return false;
        },
        message(msg = '', icon) {
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
        confirmMessage(msg = '', icon, id) {
            this.$swal({
                title: '',
                text: msg,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.deleteWord(id);
                }
            })
        },
    },
    props: [ ],
    mounted() { }
}

