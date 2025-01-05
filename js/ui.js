// ui.js - UI Management and Event Handling
const UIService = {
    elements: {
        zoneList: () => document.querySelector('#zones'),
        recordTable: () => document.querySelector('#zone-details'),
        searchInput: () => document.querySelector('#search'),
        editor: () => document.querySelector('#editor')
    },

    init() {
        this.bindEvents();
        this.initializeUI();
    },

    bindEvents() {
        this.elements.zoneList().addEventListener('click', this.handleZoneClick.bind(this));
        this.elements.recordTable().addEventListener('click', this.handleRecordClick.bind(this));
        this.elements.searchInput().addEventListener('input', this.handleSearch.bind(this));
        document.addEventListener('keydown', this.handleKeyboard.bind(this));
    },

    handleZoneClick(event) {
        if (event.target.tagName === 'LI') {
            this.updateSelection(event.target);
            ZoneManager.selectZone(event.target.innerText);
        }
    },

    handleRecordClick(event) {
        if (event.target.tagName === 'TD') {
            this.makeEditable(event.target);
        }
    },

    handleSearch(event) {
        const searchTerm = event.target.value.toLowerCase();
        document.querySelectorAll('#zones li').forEach(li => {
            const matches = li.innerText.toLowerCase().includes(searchTerm);
            li.classList.toggle('hidden', !matches);
        });
    },

    handleKeyboard(event) {
        const keyHandler = KeyboardManager.getKeyHandler(event.keyCode);
        if (keyHandler) {
            keyHandler(event);
        }
    },

    showError(message) {
        // Implement error notification
        console.error(message);
    },

    updateZoneUI(zoneName) {
        document.querySelector('#zone').textContent = zoneName;
        this.elements.editor().style.display = 'block';
    },

    updateRecordForm(record) {
        // Update form fields with record data
        Object.entries(record).forEach(([key, value]) => {
            const element = document.querySelector(`#${key}`);
            if (element) {
                element.value = value;
            }
        });
    }
};


