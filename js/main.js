// main.js - Application Entry Point
const DNSApp = {
    api: APIService,
    state: StateManager,
    zones: ZoneManager,
    records: RecordManager,
    ui: UIService,
    keyboard: KeyboardManager,

    async init() {
        try {
            const zones = await this.api.getZones();
            this.state.setState({ 
                loaded: true,
                zones: zones 
            });
            
            document.querySelector('#zones').innerHTML = 
                this.zones.renderZoneList(zones);
            
            this.ui.init();
        } catch (error) {
            this.ui.showError('Failed to initialize application');
        }
    }
};

// Initialize the application
document.addEventListener('DOMContentLoaded', () => DNSApp.init());
