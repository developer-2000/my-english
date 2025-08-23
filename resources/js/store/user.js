// user.js

const state = {
    currentUser: null,
};

const mutations = {
    setUser(state, user) {
        state.currentUser = user;
    },
};

const actions = {
    updateUser({ commit }, newUser) {
        commit('setUser', newUser);
    },
};

const getters = {
    getUser(state) {
        return state.currentUser;
    },
    getCodeInterfaceLanguage(state) {
        const result = state.currentUser?.language_user?.interface_language?.language || null;
        return result;
    },
    getCodeLearnLanguage2(state) {
        const result = state.currentUser?.language_user?.learn_language?.language || null;
        return result;
    },
};

export default {
    state,
    mutations,
    actions,
    getters,
};
