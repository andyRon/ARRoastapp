var api_url = '';
var gaode_maps_js_api_key = 'af5985d1449b1e1978a2487e732c0114';
var app_url = '';

switch (process.env.NODE_ENV) {
    case 'development':
        api_url = 'http://127.0.0.1:8000/api/';
        break;
    case 'production':
        api_url = 'http://arroast.test/api/v1';
        break;
}


export const ROAST_CONFIG = {
    API_URL: api_url,
    GAODE_MAPS_JS_API_KEY: gaode_maps_js_api_key,
    APP_URL: app_url
}
