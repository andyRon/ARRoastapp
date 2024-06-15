import { ROAST_CONFIG } from '../config.js';

export default {

    getCities: function(){
        return axios.get( ROAST_CONFIG.API_URL + '/cities' );
    },

    getCity: function( slug ){
        return axios.get( ROAST_CONFIG.API_URL + '/cities/' + slug );
    }
}
