/*
|-------------------------------------------------------------------------------
| VUEX modules/cities.js
|-------------------------------------------------------------------------------
| The Vuex data store for the cities state
*/
import CitiesAPI from '../api/cities.js';

export const cities = {
    state: {
        cities: [],
        citiesLoadStatus: 0,

        city: {},
        cityLoadStatus: 0
    },
    actions: {
        loadCities( { commit } ){
            commit('setCitiesLoadStatus', 1);

            CitiesAPI.getCities()
                .then( function( response ){
                    commit( 'setCities', response.data );
                    commit( 'setCitiesLoadStatus', 2 );
                })
                .catch( function(){
                    commit( 'setCities', [] );
                    commit( 'setCitiesLoadStatus', 3 );
                });
        },

        loadCity( { commit }, data ){
            commit( 'setCityLoadStatus', 1 );

            CitiesAPI.getCity( data.slug )
                .then( function( response ){
                    commit( 'setCity', response.data );
                    commit( 'setCityLoadStatus', 2 );
                })
                .catch( function(){
                    commit( 'setCity', {} );
                    commit( 'setCityLoadStatus', 3 );
                });
        }
    },

    mutations: {
        setCities( state, cities ){
            state.cities = cities;
        },

        setCitiesLoadStatus( state, status ){
            state.citiesLoadStatus = status;
        },

        setCity( state, city ){
            state.city = city;
        },

        setCityLoadStatus( state, status ){
            state.cityLoadStatus = status;
        }
    },

    getters: {
        getCities( state ){
            return state.cities;
        },

        getCitiesLoadStatus( state ){
            return state.citiesLoadStatus;
        },

        getCity( state ){
            return state.city;
        },

        getCityLoadStatus( state ){
            return state.cityLoadStatus;
        }
    }
}
