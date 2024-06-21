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
};

export default {
    state,
    mutations,
    actions,
    getters,
};
