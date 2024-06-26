import CafeAPI from '../api/cafe.js'

export const cafes = {
    state: {
        cafes: [],
        cafesLoadStatus: 0,

        cafe: {},
        cafeLoadStatus: 0,

        // 分别用于监听喜欢、取消喜欢动作的加载状态以及用户是否已经喜欢过这个咖啡店
        cafeLikeActionStatus: 0,
        cafeUnlikeActionStatus: 0,
        cafeLiked: false,

        cafeAdded: {},
        cafeAddStatus: 0,
        cafeAddText: '',

        cafeDeletedStatus: 0,
        cafeDeleteText: '',

        cafesView: 'map'
    },

    actions: {
        loadCafes({commit}){
            commit('setCafesLoadStatus', 1);

            CafeAPI.getCafes()
                .then(function (response) {
                    commit('setCafes', response.data);
                    dispatch('orderCafes', {
                        order: rootState.filters.orderBy,
                        direction: rootState.filters.orderDirection
                    });
                    commit('setCafesLoadStatus', 2);
                })
                .catch(function () {
                    commit('setCafes', []);
                    commit('setCafesLoadStatus', 3);
                });
        },

        loadCafe({commit}, data){
            commit('setCafeLikedStatus', false);
            commit('setCafeLoadStatus', 1);

            CafeAPI.getCafe(data.id)
                .then(function(response){
                    commit('setCafe', response.data);
                    if (response.data.user_like.length > 0) {
                        commit('setCafeLikedStatus', true);
                    }
                    commit('setCafeLoadStatus', 2);
                })
                .catch(function(){
                    commit('setCafe', {});
                    commit('setCafeLoadStatus', 3);
                });
        },

        addCafe( { commit, state, dispatch }, data ){
            // 状态1表示开始添加
            commit( 'setCafeAddStatus', 1 );

            CafeAPI.postAddNewCafe(data.name, data.locations, data.website, data.description, data.roaster, data.picture)
                .then(function (response){
                    // 状态2表示添加成功
                    commit( 'setCafeAddStatus', 2 );
                    dispatch( 'loadCafes' );
                }).catch(function () {
                // 状态3表示添加失败
                commit( 'setCafeAddStatus', 3 );
            })
        },

        likeCafe({commit, state}, data) {
            commit('setCafeLikeActionStatus', 1);

            CafeAPI.postLikeCafe(data.id)
                .then(function (response) {
                    commit('setCafeLikedStatus', true);
                    commit('setCafeLikeActionStatus', 2);
                })
                .catch(function () {
                    commit('setCafeLikeActionStatus', 3);
                });
        },

        unlikeCafe({commit, state}, data) {
            commit('setCafeUnlikeActionStatus', 1);

            CafeAPI.deleteLikeCafe(data.id)
                .then(function (response) {
                    commit('setCafeLikedStatus', false);
                    commit('setCafeUnlikeActionStatus', 2);
                })
                .catch(function () {
                    commit('setCafeUnlikeActionStatus', 3);
                });
        },

        changeCafesView({commit, state, dispatch}, view) {
            commit('setCafesView', view);
        },

        orderCafes({commit, state, dispatch}, data) {
            let localCafes = state.cafes;

            switch (data.order) {
                case 'name':
                    localCafes.sort(function (a, b) {
                        if (data.direction === 'desc') {
                            return ((a.company.name === b.company.name) ? 0 : ((a.company.name < b.company.name) ? 1 : -1));
                        } else {
                            return ((a.company.name === b.company.name) ? 0 : ((a.company.name > b.company.name) ? 1 : -1));
                        }
                    });
                    break;
                case 'most-liked':
                    localCafes.sort(function (a, b) {
                        if (data.direction === 'desc') {
                            return ((a.likes_count === b.likes_count) ? 0 : ((a.likes_count < b.likes_count) ? 1 : -1));
                        } else {
                            return ((a.likes_count === b.likes_count) ? 0 : ((a.likes_count > b.likes_count) ? 1 : -1));
                        }
                    });
                    break;
            }

            commit('setCafes', localCafes);
        }
    },

    mutations: {
        setCafesLoadStatus(state, status) {
            state.cafesLoadStatus = status;
        },

        setCafes(state, cafes) {
            state.cafes = cafes;
        },

        setCafeLoadStatus(state, status) {
            state.cafeLoadStatus = status;
        },

        setCafe(state, cafe) {
            state.cafe = cafe;
        },

        setCafeAddStatus(state, status) {
            state.cafeAddStatus = status;
        },

        setCafeAdded( state, cafe ){
            state.cafeAdded = cafe;
        },

        setCafeAddedText( state, text ){
            state.cafeAddText = text;
        },

        setCafeLikedStatus(state, status) {
            state.cafeLiked = status;
        },

        setCafeLikeActionStatus(state, status) {
            state.cafeLikeActionStatus = status;
        },

        setCafeUnlikeActionStatus(state, status) {
            state.cafeUnlikeActionStatus = status;
        },

        setCafesView(state, view) {
            state.cafesView = view
        }
    },

    getters: {
        getCafesLoadStatus(state) {
            return state.cafesLoadStatus;
        },

        getCafes(state) {
            return state.cafes;
        },

        getCafeLoadStatus(state) {
            return state.cafeLoadStatus;
        },

        getCafe(state) {
            return state.cafe;
        },

        getCafeAddStatus(state) {
            return state.cafeAddStatus;
        },

        getAddedCafe( state ){
            return state.cafeAdded;
        },

        getCafeAddText( state ){
            return state.cafeAddText;
        },

        getCafeLikedStatus(state) {
            return state.cafeLiked;
        },

        getCafeLikeActionStatus(state) {
            return state.cafeLikeActionStatus;
        },

        getCafeUnlikeActionStatus(state) {
            return state.cafeUnlikeActionStatus;
        },

        getCafesView(state) {
            return state.cafesView;
        }
    }
}
