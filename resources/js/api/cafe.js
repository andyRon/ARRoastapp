import { ROAST_CONFIG } from "../config.js";
import axios from "axios";

export default {
    getCafes: function () {
        return axios.get(ROAST_CONFIG.API_URL + '/cafes');
    },
    getCafe: function (cafeID) {
        return axios.get(ROAST_CONFIG.API_URL + '/cafes/' + cafeID);
    },
    postAddNewCafe: function (name, address, city, state, zip) {
        return axios.post(ROAST_CONFIG.API_URL + '/cafes', {
            name: name,
            address: address,
            city: city,
            state: state,
            zip: zip
        })
    }
}
