
import axios from "axios";

export default {
    async get(url, data = {}, headers = {}) {
        return await axios.get(url, {data, headers});
    },
    async post(url, data = {}, headers = {}) {
        return await axios.post(url, data, {headers: headers})
    },
    async patch(url, data = {}, headers = {}) {
        return await axios.patch(url, data, {headers: headers})
    },
    apiUrl() {
        return 'http://english.my/api/';
    },
}
