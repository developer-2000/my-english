// user.js

const state = {
    currentUser: null,
};

const mutations = {
    setUser(state, user) {
        console.log('🔍 [VUEX_USER] setUser mutation called with:', user);
        console.log('🔍 [VUEX_USER] Old currentUser value:', state.currentUser);
        state.currentUser = user;
        console.log('🔍 [VUEX_USER] New currentUser value:', state.currentUser);
    },
};

const actions = {
    updateUser({ commit }, newUser) {
        console.log('🔍 [VUEX_USER] updateUser action called with:', newUser);
        commit('setUser', newUser);
    },
};

const getters = {
    getUser(state) {
        console.log('🔍 [VUEX_USER] getUser getter called, returning:', state.currentUser);
        return state.currentUser;
    },
    getCodeInterfaceLanguage(state) {
        const result = state.currentUser?.language_user?.interface_language?.language || null;
        console.log('🔍 [VUEX_USER] getCodeInterfaceLanguage getter called, returning:', result);
        return result;
    },
    getCodeLearnLanguage2(state) {
        const result = state.currentUser?.language_user?.learn_language?.language || null;
        console.log('🔍 [VUEX_USER] getCodeLearnLanguage2 getter called, returning:', result);
        return result;
    },
};

export default {
    state,
    mutations,
    actions,
    getters,
};
