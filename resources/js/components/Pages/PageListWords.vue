<template>
    <div id="page_list_worlds">
        <!-- Wrapper -->
        <div class="wrapper">
            <!-- верхнее меню -->
            <div class="card card-primary card-outline top_menu">
                <div class="card-header">
                    <button class="btn btn-outline-success" id="coll1" @click="toggleCollapse(1)">
                        Create Type
                    </button>
                    <button class="btn btn-outline-success" id="coll2" @click="toggleCollapse(2)">
                        Update Type
                    </button>
                    <button class="btn bg-gradient-primary" @click="openModalCreateWord">
                        Add word
                    </button>
                </div>
            </div>

            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <div class="content-header">

                    <!-- collapse create -->
                    <div class="collapse" id="collapse1" >
                        <div class="card card-body">
                            <div class="collapse_heder">Create Type</div>

                            <div class="group_type">
                                <!-- new type -->
                                <div class="form-group">
                                    <label for="collapse_type" class="col-form-label">New type</label>
                                    <input type="text" class="form-control" placeholder="Insert new type" id="collapse_type"
                                           v-model="collapse_type"
                                           @keyup="touchCollapse()"
                                           required
                                    >
                                </div>
                                <!-- select type word -->
                                <div class="form-group">
                                    <label for="collapse_select" class="col-form-label">Type color</label>
                                    <select @change="touchCollapse()" id="collapse_select" v-model="collapse_select" class="custom-select" size="3">
                                        <option disabled>Insert color for type</option>
                                        <option v-for="(color, key) in allColor" :key="key"
                                                :style="`background-color:${color}`"
                                                :value="`${color}`">
                                            {{color}}
                                        </option>
                                    </select>
                                </div>
                                <!-- Word description -->
                                <div class="form-group">
                                    <label for="collapse_description" class="col-form-label">Description type</label>
                                    <textarea @keyup="touchCollapse()" v-model="collapse_description" class="form-control" id="collapse_description" placeholder="Insert description type"></textarea>
                                </div>

                                <button type="button" class="btn btn-primary"
                                        :disabled="!collapse_but"
                                        @click="createType"
                                >Save</button>
                            </div>

                        </div>
                    </div>

                    <!-- collapse update -->
                    <div class="collapse" id="collapse2">
                        <div class="card card-body">
                            <div class="collapse_heder">Update Type</div>

                            <div class="group_type">
<!--{{this.allTypes}}-->
                                <!-- select old -->
                                <div class="form-group">
                                    <label for="collapse_select_old" class="col-form-label">Old type</label>
                                    <select @change="touchCollapseOld()" id="collapse_select_old" v-model="collapse_select_old" class="custom-select" size="3">
                                        <option disabled>Select old type</option>
                                        <option v-for="(color, key) in allTypes" :key="key"
                                                :style="`background-color:${color.color}`"
                                                :value="`${color.color}`"
                                                @click="touchOldType(color.id)"
                                        >
                                            {{color.type}}
                                        </option>
                                    </select>
                                </div>
                                <!-- name type -->
                                <div class="form-group">
                                    <label for="collapse_type_new" class="col-form-label">Name type</label>
                                    <input type="text" class="form-control" placeholder="Insert name type" id="collapse_type_new"
                                           v-model="collapse_type"
                                           @keyup="touchCollapseOld()"
                                           required
                                    >
                                </div>
                                <!-- select type word -->
                                <div class="form-group">
                                    <label for="collapse_select_new" class="col-form-label">Type color</label>
                                    <select @change="touchCollapseOld()" id="collapse_select_new" v-model="collapse_select" class="custom-select" size="3">
                                        <option disabled>Insert color for type</option>
                                        <option v-for="(color, key) in allColor" :key="key"
                                                :style="`background-color:${color}`"
                                                :value="`${color}`">
                                            {{color}}
                                        </option>
                                    </select>
                                </div>
                                <!-- Word description -->
                                <div class="form-group">
                                    <label for="collapse_description_old" class="col-form-label">Description type</label>
                                    <textarea @keyup="touchCollapseOld()" v-model="collapse_description" class="form-control" id="collapse_description_old" placeholder="Insert description type"></textarea>
                                </div>

                                <button type="button" class="btn btn-primary"
                                        :disabled="!collapse_but_old"
                                        @click="updateType"
                                >Update</button>
                            </div>

                        </div>
                    </div>

                    <div class="container-fluid">
                        <!-- заголовок окна-->
                        <h1 class="m-0 text-dark">List Words</h1>

                        <!-- body окна-->
                        <div class="card card-primary card-outline block_table">

<!--                            @on-row-click="onRowClick"-->
<!--                            @on-column-filter="onColumnFilter"-->

                            <!-- Table -->
                            <div v-if="table.rows.length" class="table_wrapper">
                                <vue-good-table
                                    :isLoading.sync="table.isLoading"
                                    :mode="table.mode"
                                    :totalRows="table.totalRecords"
                                    :rows="table.rows"
                                    :columns="table.columns"
                                    :pagination-options="table.optionsPaginate"
                                    :search-options="{
                                        enabled: true,
                                        placeholder: 'Search word',
                                    }"
                                    styleClass="vgt-table bordered"
                                    @on-search="onSearch"
                                    @on-page-change="onPageChange"
                                    @on-per-page-change="onPerPageChange"
                                    @on-sort-change="onSortChange"
                                >
                                    <template slot="loadingContent">
                                        <div></div>
                                    </template>
                                </vue-good-table>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- /.content-header -->

            </div>

        </div>
        <!-- / Wrapper -->

        <!-- Modals -->
        <div class="modal fade" id="create_word" tabindex="-1" role="dialog" aria-labelledby="create_word" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Create new word</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="#">
                            <!-- new word -->
                            <div class="form-group">
                                <label for="new_word" class="col-form-label">New word</label>
                                <input type="text" class="form-control" placeholder="Insert new word" id="new_word"
                                    v-model="new_word"
                                    @blur="touchNewWord()"
                                    :class="{'is-invalid': $v.new_word.$error}"
                                    required
                                >
                                <div class="invalid-feedback" v-if="!$v.new_word.required">The field is empty!</div>
                                <div class="invalid-feedback" v-if="(!$v.new_word.minLength)">Number of characters {{ this.new_word.length }} less needed</div>
                            </div>

                            <!-- translation word -->
                            <div class="form-group">
                                <label for="translation_word" class="col-form-label">Translation</label>
                                <input type="text" class="form-control" placeholder="Insert translation a word" id="translation_word"
                                       v-model="translation_word"
                                       @blur="touchTranslationWord()"
                                       :class="{'is-invalid': $v.translation_word.$error}"
                                       required
                                >
                                <div class="invalid-feedback" v-if="!$v.translation_word.required">The field is empty!</div>
                                <div class="invalid-feedback" v-if="(!$v.translation_word.minLength)">Number of characters {{ this.translation_word.length }} less needed</div>
                            </div>

                            <!-- select type word -->
                            <div class="block_type">
                                <div class="form-group">
                                    <label for="select_type" class="col-form-label">Word type</label>
                                    <select id="select_type" v-model="select_type" class="custom-select" size="3">
                                        <option v-for="(type, key) in allTypes" :key="key"
                                                :value="type.id"
                                                :class="type.id === 1 ? 'disabled_select' : ''"
                                        >
                                            {{type.id === 1 ? 'Choose word type' : type.type}}
                                        </option>
                                    </select>
                                </div>
                                <div class="desc_type"></div>
                            </div>

                            <!-- Word description -->
                            <div class="form-group">
                                <label for="word_description" class="col-form-label">Word description</label>
                                <textarea v-model="description" class="form-control" id="word_description" placeholder="Insert description word"></textarea>
                            </div>

                            <!-- button save -->
                            <div class="button_footer">
                                <button type="button" class="btn btn-primary"
                                        :class="{'un_active': $v.$invalid, 'active2': !$v.$invalid}"
                                        :disabled="$v.$invalid"
                                        @click="createWord"
                                >Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- 2 -->
        <div class="modal fade" id="update_word" tabindex="-1" role="dialog" aria-labelledby="create_word" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update word</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="#">
                            <!-- word -->
                            <div class="form-group">
                                <label for="old_word" class="col-form-label">Update word</label>
                                <input type="text" class="form-control" placeholder="Insert word" id="old_word"
                                       v-model="new_word"
                                       @blur="touchNewWord()"
                                       :class="{'is-invalid': $v.new_word.$error}"
                                       required
                                >
                                <div class="invalid-feedback" v-if="!$v.new_word.required">The field is empty!</div>
                                <div class="invalid-feedback" v-if="(!$v.new_word.minLength)">Number of characters {{ this.new_word.length }} less needed</div>
                            </div>

                            <!-- translation word -->
                            <div class="form-group">
                                <label for="update_translation" class="col-form-label">Translation</label>
                                <input type="text" class="form-control" placeholder="Insert translation" id="update_translation"
                                       v-model="translation_word"
                                       @blur="touchTranslationWord()"
                                       :class="{'is-invalid': $v.translation_word.$error}"
                                       required
                                >
                                <div class="invalid-feedback" v-if="!$v.translation_word.required">The field is empty!</div>
                                <div class="invalid-feedback" v-if="(!$v.translation_word.minLength)">Number of characters {{ this.translation_word.length }} less needed</div>
                            </div>

                            <!-- select type word -->
                            <div class="block_type">
                                <div class="form-group">
                                    <label for="update_select_type" class="col-form-label">Word type</label>
                                    <select id="update_select_type" v-model="select_type" class="custom-select" size="3">
                                        <option v-for="(type, key) in allTypes" :key="key"
                                                :value="type.id"
                                                :class="type.id === 1 ? 'disabled_select' : ''"
                                        >
                                            {{type.id === 1 ? 'Choose word type' : type.type}}
                                        </option>
                                    </select>
                                </div>
                                <div class="desc_type"></div>
                            </div>

                            <!-- Word description -->
                            <div class="form-group">
                                <label for="update_word_description" class="col-form-label">Word description</label>
                                <textarea v-model="description" class="form-control" id="update_word_description" placeholder="Insert description word"></textarea>
                            </div>

                            <!-- button save -->
                            <div class="button_footer">
                                <button type="button" class="btn btn-primary"
                                        :class="{'un_active': $v.$invalid, 'active2': !$v.$invalid}"
                                        :disabled="$v.$invalid"
                                        @click="updateWord"
                                >Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
    // hover alert text
    import "tippy.js/themes/light-border.css";
    import { tippy } from "vue-tippy";
    // validate
    import { required, minLength } from 'vuelidate/lib/validators'
    // table
    import 'vue-good-table/dist/vue-good-table.css'
    import { VueGoodTable } from 'vue-good-table';
    // mixins
    import response_methods_mixin from "../../mixins/response_methods_mixin";

    export default {
        data() {
            return {
                word_id: 0,
                type_id: 0,
                new_word: '',
                translation_word: '',
                description: '',
                select_type: 0,
                collapse_type: '',
                collapse_select: '',
                collapse_select_old: '',
                collapse_description: '',
                collapse_but: false,
                collapse_but_old: false,
                table: {
                    // max rows in database
                    totalRecords: 0,
                    origin_rows: [],
                    translation: '',
                    description: '',
                    // settings title
                    columns: [
                        {
                            tdClass: 'id_td',
                            label: 'Letter',
                            field: 'letter',
                            width: '5%',
                        },
                        {
                            tdClass: 'text_td',
                            label: 'Word',
                            width: '19%',
                            sortable: false,
                            html: true,
                            field: (val) => {
                                return '' +
                                    '<div class="trigger">'+val.word1+'</div>' +
                                    '<a class="btn btn-warning btn_word" role="button">\<n></n>' +
                                    '   <span class="fa fa-edit"></span>\n' +
                                    '</a>';
                            }
                        },
                        {
                            tdClass: 'text_td',
                            label: 'Word',
                            width: '19%',
                            sortable: false,
                            html: true,
                            field: (val) => {
                                return '' +
                                    '<div class="trigger">'+val.word2+'</div>' +
                                    '<a class="btn btn-warning btn_word" role="button">\<n></n>' +
                                    '   <span class="fa fa-edit"></span>\n' +
                                    '</a>';
                            }
                        },
                        {
                            tdClass: 'text_td',
                            label: 'Word',
                            width: '19%',
                            sortable: false,
                            html: true,
                            field: (val) => {
                                return '' +
                                    '<div class="trigger">'+val.word3+'</div>' +
                                    '<a class="btn btn-warning btn_word" role="button">\<n></n>' +
                                    '   <span class="fa fa-edit"></span>\n' +
                                    '</a>';
                            }
                        },
                        {
                            tdClass: 'text_td',
                            label: 'Word',
                            width: '19%',
                            sortable: false,
                            html: true,
                            field: (val) => {
                                return '' +
                                    '<div class="trigger">'+val.word4+'</div>' +
                                    '<a class="btn btn-warning btn_word" role="button">\<n></n>' +
                                    '   <span class="fa fa-edit"></span>\n' +
                                    '</a>';
                            }
                        },
                        {
                            tdClass: 'text_td',
                            label: 'Word',
                            width: '19%',
                            sortable: false,
                            html: true,
                            field: (val) => {
                                return '' +
                                    '<div class="trigger">'+val.word5+'</div>' +
                                    '<a class="btn btn-warning btn_word" role="button">\<n></n>' +
                                    '   <span class="fa fa-edit"></span>\n' +
                                    '</a>';
                            }
                        },
                    ],
                    // array objects rows
                    rows: [],
                    // settings paginate
                    optionsPaginate: {
                        enabled: true,
                        perPageDropdown: [3, 50],
                        nextLabel: 'next',
                        prevLabel: 'prev',
                        perPage: 50,
                    },
                    // sorting server data on the client side
                    mode: "remote",
                    isLoading: true,
                },
                serverParams: {
                    search: '',
                    page: 0,
                    perPage: 50,
                    sort: [{
                        field: '',
                        type: '',
                    }],
                },
                allTypes: [],
                allColor: [],
            };
        },
        mixins: [
            response_methods_mixin,
        ],
        components: {
            VueGoodTable,
        },
        methods: {
            touchNewWord() {
                this.$v.new_word.$touch();
            },
            touchTranslationWord() {
                this.$v.translation_word.$touch();
            },
            touchCollapse() {
                this.collapse_but = ( this.collapse_type.length >= 3 && this.collapse_select != '' ) ? true : false;
            },
            touchCollapseOld() {
                if(
                    this.collapse_type.length >= 3 &&
                    this.collapse_select_old != ''
                ){
                    this.collapse_but_old = true;
                }
                else{
                    this.collapse_but_old = false;
                }
            },
            touchOldType(id) {
                for(let i=0; i < this.allTypes.length; i++){
                    if(this.allTypes[i].id == id){
                        this.collapse_type = this.allTypes[i].type;
                        this.collapse_description = this.allTypes[i].description;
                        this.type_id = this.allTypes[i].id;
                        this.collapse_but_old = true;
                        break;
                    }
                }
            },
            initialData() {
                this.loadWordsAndTypes();
                this.hoverWordShowTitle();
                this.showStyleDataOnSelectType();
                this.updateColumnTable();
                this.initialClickButWordUpdate();
            },
            async createWord() {
                try {
                    const response = await this.$http.post(`${this.$http.apiUrl()}word`, {
                        word: this.new_word,
                        translation: this.translation_word,
                        description: this.description,
                        type: this.select_type,
                    });

                    if(this.checkSuccess(response)){
                        this.initialData();
                        $('#create_word').modal('hide');
                        $('.modal-backdrop.fade.show').remove();
                    }
                } catch (e) {
                    console.log(e);
                }

            },
            async updateWord() {
                try {
                    let data = {
                        word: this.new_word,
                        translation: this.translation_word,
                        description: this.description,
                    };
                    if(this.select_type !== 0){
                        data.type = this.select_type;
                    }
                    const response = await this.$http.patch(`${this.$http.apiUrl()}word/${this.word_id}`, data);
                    if(this.checkSuccess(response)){
                        this.initialData();
                        $('#update_word').modal('hide');
                        $('.modal-backdrop.fade.show').remove();
                    }
                } catch (e) {
                    console.log(e);
                }
            },
            async loadWordsAndTypes() {
                try {
                    this.isLoading = true;
                    const response = await this.$http.get(
`${this.$http.apiUrl()}word?search=${this.serverParams.search}&page=${this.serverParams.page}&perPage=${this.serverParams.perPage}&sortField=${this.serverParams.sort[0].field}&sortType=${this.serverParams.sort[0].type}`
                    );
                    if(this.checkSuccess(response)){
                        this.table.totalRecords = response.data.data.total_count;
                        this.makeObjectDataForTable(response.data.data.list);
                        this.table.origin_rows = response.data.data.list;
                        this.allTypes = response.data.data.types;
                        this.deleteColorFromArrColor(response.data.data.colors);
                    }
                } catch (e) {
                    console.log(e);
                }
                this.isLoading = false;
            },
            async createType() {
                try {
                    let data = {
                        type: this.collapse_type,
                        color: this.collapse_select
                    };
                    if(this.collapse_description != ''){
                        data.description = this.collapse_description;
                    }
                    const response = await this.$http.post(`${this.$http.apiUrl()}type`, data);
                    if(this.checkSuccess(response)){
                        $('#collapse1').collapse('hide');
                        this.toggleCollapse(1);
                        this.initialData();
                    }
                } catch (e) {
                    console.log(e);
                }
            },
            async updateType() {
                try {
                    let data = {
                        type: this.collapse_type,
                        color: this.collapse_select == '' ? this.getType(this.type_id).color : this.collapse_select,
                    };
                    if(this.collapse_description != '' && this.collapse_description != null){
                        data.description = this.collapse_description;
                    }
                    const response = await this.$http.patch(`${this.$http.apiUrl()}type/${this.type_id}`, data);
                    if(this.checkSuccess(response)){
                        $('#collapse2').collapse('hide');
                        this.toggleCollapse(2);
                        this.initialData();
                    }
                } catch (e) {
                    console.log(e);
                }
            },
            deleteColorFromArrColor(arrColor) {
                let index = 0;
                this.allColor = arrColor;
                for(let i=0; i < this.allTypes.length; i++){
                    index = this.allColor.indexOf(this.allTypes[i].color);
                    if(index !== -1){
                        this.allColor.splice(index,1)
                    }
                }
            },
            makeObjectDataForTable(list) {
                let row = {letter:"", word1:"", word2: "", word3: "", word4: "", word5: ""};
                this.table.rows = [];
                let tick = 1;
                let last_simbol = "";
                let simbol = "";

                for(let i=0; i < list.length; i++){
                    if(tick == 6){
                        row['letter'] = simbol.substring(0, simbol.length - 1);
                        simbol = '';
                        this.table.rows.push(row);
                        row = {letter:"", word1:"", word2: "", word3: "", word4: "", word5: ""};
                        tick = 1;
                    }
                    if(tick <= 5){
                        last_simbol = list[i]['word'].charAt(0).toLowerCase();
                        simbol += (simbol.indexOf(last_simbol) === -1) ? last_simbol+'-' : '';
                        row['word'+tick] = list[i]['word'].toLowerCase();
                        tick++;
                    }
                }
                row['letter'] = simbol.substring(0, simbol.length - 1);
                this.table.rows.push(row);
            },
            // methods table
            onSearch(search) {
                this.updateParams({search: search.searchTerm});
                this.initialData();
            },
            onPageChange(params) {
                this.updateParams({page: params.currentPage});
                this.initialData();
            },
            onPerPageChange(params) {
                this.updateParams({page: 0, perPage: params.currentPerPage});
                this.initialData();
            },
            onSortChange(params) {
                this.updateParams({page: 0, sort: params});

                // console.log(this.serverParams)
                this.initialData();
            },
            updateParams(newProps) {
                this.serverParams = Object.assign({}, this.serverParams, newProps);
            },
            updateColumnTable(){
                let timerId = setTimeout(() => {
                    let row = '';
                    let prev = '';

                    document.querySelectorAll("#vgt-table td span a").forEach((tag) => {
                        prev = $(tag).prev();
                        // нет слова в столбце
                        if(prev.text() == ''){
                            $(tag).parent().css('display','none');
                        }
                        // преобразовать слово в столбце
                        else{
                            row = this.getRowForWord(prev.text());
                            // if (row == null || row.type == null) { return false; }
                            if(row.translation != '' && row.translation != null){
                                prev.css('color',row.type.color);
                            }
                            else{
                                prev.css({'color':'#959595','font-weight':'bold'});
                            }
                            $(tag).parent().css('display','flex');
                        }
                    });
                }, 500);
            },
            hoverWordShowTitle() {
                // навести на слово
                $('body').on('mouseover', '.trigger', (event) => {
                    // выбрать колекцию слова
                    let row = this.getRowForWord($(event.target).text());
                    if (row == null) { return false; }

                    // 1 создать строку html
                    let html = `<div style="text-align: left;">
<div style="font-weight: 700;">${row.translation == null ? '' : row.translation.toLowerCase()}
<span style="${row.type == null ? '' : 'color:'+row.type.color};">
${row.type == null ? '' : row.type.type} ${row.type.description == null ? '' : ' - '+row.type.description}</span>
</div>
${row.description == null ? '' : row.description.toLowerCase()}
</div>`;

                    // 2 показ подсказки
                    tippy(event.target, {
                        content: html,
                        theme: 'light-border',
                        allowHTML: true,
                    });
                });
            },
            showStyleDataOnSelectType(){
                document.getElementById("select_type").addEventListener('change', () => {
                    for(let i=0; i < this.allTypes.length; i++){
                        if(this.allTypes[i].id == this.select_type){
                            this.setStyleDataModal(this.allTypes[i]);
                            break;
                        }
                    }
                });
                document.getElementById("update_select_type").addEventListener('change', () => {
                    for(let i=0; i < this.allTypes.length; i++){
                        if(this.allTypes[i].id == this.select_type){
                            this.setStyleDataModal(this.allTypes[i]);
                            break;
                        }
                    }
                });
            },
            getRowForWord(word){
                let row = null;
                for (let i = 0; i < this.table.origin_rows.length; i++) {
                    if (this.table.origin_rows[i].word.toLowerCase() == word) {
                        row = this.table.origin_rows[i];
                        break;
                    }
                }
                return row;
            },
            getType(id){
                for (let i = 0; i < this.allTypes.length; i++) {
                    if (this.allTypes[i].id == id) {
                        return this.allTypes[i];
                    }
                }
                return null;
            },
            setStyleDataModal(type){
                let string = type.description == null ? '' : ` - ${type.description}`;
                string = type.type+''+string;
                $('.desc_type')
                    .css('border-color',type.color)
                    .text(string);
            },
            setVariableDefault(word_id=0, word='', translation='', type_id=0, description=''){
                this.word_id = word_id;
                this.new_word = word;
                this.translation_word = translation;
                this.select_type = type_id;
                this.description = description;
            },
            initialClickButWordUpdate(){
                let a = setTimeout(() => {
                    $('.btn_word').bind('click', (e) => {
                        let queryObj = ($(e.target).prop("tagName") !== "A") ? $(e.target).parent() : $(e.target);
                        let word = queryObj.prev(".trigger").text();
                        let row = this.getRowForWord(word);
                        this.setVariableDefault(row.id, row.word, row.translation, row.type.id, row.description);
                        this.setStyleDataModal(row.type);
                        $('#update_word').modal('show');
                    })
                }, 1000);
            },
            openModalCreateWord(){
                this.setVariableDefault();
                this.setStyleDataModal({description:null, type:'', color:'black'});
                $('#create_word').modal('show');
            },
            toggleCollapse(num) {
                let arr = ['collapse1', 'collapse2'];

                // закрыть другой и обратное действие выбранному
                arr.forEach((element) => {
                    if ('collapse' + num != element) {
                        $('#' + element).collapse('hide');
                        arr.splice(arr.indexOf(element), 1);
                        return;
                    }
                });
                $('#collapse' + num).collapse('toggle');

                // изменить заливку кнопок
                let num2 = num == 1 ? 2 : 1;
                $('#coll' + num2).attr('class', 'btn btn-outline-success');

                if ($('#coll' + num).hasClass("btn-outline-success")) {
                    $('#coll' + num).attr('class', 'btn bg-gradient-success');
                } else {
                    $('#coll' + num).attr('class', 'btn btn-outline-success');
                }

                this.collapse_type = '';
                this.collapse_select = '';
                this.collapse_select_old = '';
                this.collapse_description = '';
                this.collapse_but = false;
                this.collapse_but_old = false;
            }
        },
        mounted() {
            this.initialData();

            $('#collapseExample1').collapse('hide');
            $('#collapseExample2').collapse('hide');
        },
        beforeDestroy: function () {
            $('.btn_word').unbind('click');
        },
        validations: {
            new_word: {
                required,
                minLength: minLength(1),
            },
            translation_word: {
                required,
                minLength: minLength(1),
            },
        },
        name: "PageListWords.vue"
    }
</script>

<style lang="scss" scoped>
    #page_list_worlds{
        .content-header{
            padding: 0px .5rem;
            .container-fluid{
                padding-top: 15px;
            }
        }
    }
    #collapse1,
    #collapse2{
        .card{
            margin: 0px -9px;
            box-shadow: none;
            border-left: 1px solid #d2d2d2;
            border-radius: 0px;
            border-bottom: 1px solid #d2d2d2;
            .collapse_heder{
                margin: 0px 0px 15px;
            }
        }
        .group_type{
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
            justify-content: flex-start;
            align-content: stretch;
            align-items: flex-start;
            .form-group{
                width: 20%;
                margin-right: 20px;
            }
            button{
                margin-top: 40px;
            }
            textarea{
                height: 86px;
            }
        }
    }
    #collapse_select,
    #collapse_select_old,
    #collapse_select_new {
        padding: 0px;
    }
</style>
