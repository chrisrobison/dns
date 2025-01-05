// api.js - API Communication Module
import { CONFIG } from './config.js';
import { UIService } from './ui.js';

export const APIService = {
    async fetchData(url) {
        try {
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return await response.json();
        } catch (error) {
            console.error('API Error:', error);
            UIService.showError('Failed to fetch data');
            throw error;
        }
    },

    async getZones() {
        return await this.fetchData(CONFIG.API_ENDPOINTS.GET_ZONES);
    },

    async getZone(zoneName) {
        return await this.fetchData(CONFIG.API_ENDPOINTS.GET_ZONE + zoneName);
    }
};


