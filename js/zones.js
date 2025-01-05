// zones.js - Zone Management
const ZoneManager = {
    async loadZones() {
        const data = await APIService.getZones();
        return this.renderZoneList(data);
    },

    renderZoneList(zones) {
        return zones.map(zone => `
            <li>${zone} 
                <a title="Delete entire zone" 
                   onclick="DNSApp.zones.confirmDelete('${zone}')" 
                   class="delete">
                    <i class="fas fa-trash-can"></i>
                </a>
            </li>
        `).join('');
    },

    async selectZone(zoneName) {
        StateManager.setState({ currentZone: zoneName });
        const zoneData = await APIService.getZone(zoneName);
        RecordManager.loadZoneRecords(zoneData);
        UIService.updateZoneUI(zoneName);
    },

    confirmDelete(zoneName) {
        if (confirm(`Are you sure you want to delete the entire zone ${zoneName}?`)) {
            this.deleteZone(zoneName);
        }
    },

    async deleteZone(zoneName) {
        // Implement zone deletion logic
    }
};


