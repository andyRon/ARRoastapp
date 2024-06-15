import UserAPI from '../api/user.js'

export const users = {
    state: {
        user: {},
        userUpdateStatus: 0,
    },
    actions: {
        editUser({commit, state, dispatch}, data) {
            commit('setUserUpdateStatus', 1);

            UserAPI.putUpdateUser(data.public_visibility, data.favorite_coffee, data.flavor_notes, data.city, data.state)
                .then(function (response) {
                    commit('setUserUpdateStatus', 2);
                    dispatch('loadUser');
                })
                .catch(function () {
                    commit('setUserUpdateStatus', 3);
                });
        },
    },
    mutations: {
        setUserUpdateStatus(state, status) {
            state.userUpdateStatus = status;
        }
    },
    getters: {
        getUserUpdateStatus( state, status ){
            return state.userUpdateStatus;
        }
    }
}
