export const filters = {
    state: {
        cityFilter: '',
        textSearch: '',
        activeLocationFilter: 'all',
        onlyLiked: false,
        brewMethodsFilter: [],
        hasMatcha: false,
        hasTea: false,
        hasSubscription: false,
        orderBy: 'name',
        orderDirection: 'asc'
    },
    actions: {
        updateCityFilter({commit}, data) {
            commit('setCityFilter', data);
        },
        updateSetTextSearch({commit}, data) {
            commit('setTextSearch', data);
        },
        updateActiveLocationFilter({commit}, data) {
            commit('setActiveLocationFilter', data);
        },
        updateOnlyLiked({commit}, data) {
            commit('setOnlyLiked', data);
        },
        updateBrewMethodsFilter({commit}, data) {
            commit('setBrewMethodsFilter', data);
        },
        updateHasMatcha({commit}, data) {
            commit('setHasMatcha', data);
        },
        updateHasTea({commit}, data) {
            commit('setHasTea', data);
        },
        updateHasSubscription({commit}, data) {
            commit('setHasSubscription', data);
        },
        updateOrderBy({commit, state, dispatch}, data) {
            commit('setOrderBy', data);
            dispatch('orderCafes', {order: state.orderBy, direction: state.orderDirection});
        },
        updateOrderDirection({commit, state, dispatch}, data) {
            commit('setOrderDirection', data);
            dispatch('orderCafes', {order: state.orderBy, direction: state.orderDirection});
        },
        resetFilters({commit}, data) {
            commit('resetFilters');
        }
    },
    mutations: {
        setCityFilter(state, city) {
            state.cityFilter = city;
        },
        setTextSearch(state, search) {
            state.textSearch = search;
        },
        setActiveLocationFilter(state, activeLocationFilter) {
            state.activeLocationFilter = activeLocationFilter;
        },
        setOnlyLiked(state, onlyLiked) {
            state.onlyLiked = onlyLiked;
        },
        setBrewMethodsFilter(state, brewMethods) {
            state.brewMethodsFilter = brewMethods;
        },
        setHasMatcha(state, matcha) {
            state.hasMatcha = matcha;
        },
        setHasTea(state, tea) {
            state.hasTea = tea;
        },
        setHasSubscription(state, subscription) {
            state.hasSubscription = subscription;
        },
        setOrderBy(state, orderBy) {
            state.orderBy = orderBy;
        },
        setOrderDirection(state, orderDirection) {
            state.orderDirection = orderDirection;
        },
        resetFilters(state) {
            state.cityFilter = '';
            state.textSearch = '';
            state.activeLocationFilter = 'all';
            state.onlyLiked = false;
            state.brewMethodsFilter = [];
            state.hasMatcha = false;
            state.hasTea = false;
            state.hasSubscription = false;
            state.orderBy = 'name';
            state.orderDirection = 'desc';
        }
    },

    getters: {
        getCityFilter(state) {
            return state.cityFilter;
        },
        getTextSearch(state) {
            return state.textSearch;
        },
        getActiveLocationFilter(state) {
            return state.activeLocationFilter;
        },
        getOnlyLiked(state) {
            return state.onlyLiked;
        },
        getBrewMethodsFilter(state) {
            return state.brewMethodsFilter;
        },
        getHasMatcha(state) {
            return state.hasMatcha;
        },
        getHasTea(state) {
            return state.hasTea;
        },
        getHasSubscription(state) {
            return state.hasSubscription;
        },
        getOrderBy(state) {
            return state.orderBy;
        },
        getOrderDirection(state) {
            return state.orderDirection;
        }
    }
};
