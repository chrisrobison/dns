// config.js - Configuration and Constants
export const CONFIG = {
    API_ENDPOINTS: {
        GET_ZONES: `api/?x=get_zones`,
        GET_ZONE: `api/?x=get_zone&zone={{ZONE}}`,
        UPDATE_RECORD: `api/?x=update`,
        GET_RECORD: `api/?x=get_record&zone={{ZONE}}&id={{ID}}`,
    },
    KEYCODES: {
        UP: 38,
        DOWN: 40,
        LEFT: 37,
        RIGHT: 39,
        ESC: 27,
        TAB: 9
    }
};
