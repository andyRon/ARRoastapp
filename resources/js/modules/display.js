export const display = {
    state: {
        showFilters: true,
        showPopOut: false,
        zoomLevel: '',
        lat: 0.0,
        lng: 0.0
    },

    actions: {
        toggleShowFilters({commit}, data) {
            commit('setShowFilters', data.showFilters);
        },

        toggleShowPopOut({commit}, data) {
            commit('setShowPopOut', data.showPopOut);
        },

        applyZoomLevel({commit}, data) {
            commit('setZoomLevel', data);
        },

        applyLat({commit}, data) {
            commit('setLat', data);
        },

        applyLng({commit}, data) {
            commit('setLng', data);
        }
    },

    mutations: {

        setShowFilters(state, show) {
            state.showFilters = show;
        },

        setShowPopOut(state, show) {
            state.showPopOut = show;
        },

        setZoomLevel(state, level) {
            state.zoomLevel = level;
        },

        setLat(state, lat) {
            state.lat = lat;
        },

        setLng(state, lng) {
            state.lng = lng;
        }
    },

    getters: {

        getShowFilters(state) {
            return state.showFilters;
        },

        getShowPopOut(state) {
            return state.showPopOut;
        },

        getZoomLevel(state) {
            return state.zoomLevel;
        },

        getLat(state) {
            return state.lat;
        },

        getLng(state) {
            return state.lng;
        }
    }
};
