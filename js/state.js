// state.js - Application State Management
const StateManager = {
    state: {
        loaded: false,
        currentZone: null,
        currentRecord: null
    },

    setState(newState) {
        this.state = { ...this.state, ...newState };
        return this.state;
    },

    getState() {
        return this.state;
    }
};


