// DNS Administration main.js
import { CONFIG } from './config.js';
import { APIService } from './api.js';
import { StateManager } from './state.js';
import { ZoneManager } from './zones.js';
import { RecordManager } from './records.js';
import { UIService } from './ui.js';
import { KeyboardManager } from './keyboard.js';

export const DNSApp = {
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
