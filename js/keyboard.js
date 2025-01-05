// keyboard.js - Keyboard Navigation
import { CONFIG } from './config.js';

export const KeyboardManager = {
    getKeyHandler(keyCode) {
        const handlers = {
            [CONFIG.KEYCODES.UP]: this.handleUpDown.bind(this, 'up'),
            [CONFIG.KEYCODES.DOWN]: this.handleUpDown.bind(this, 'down'),
            [CONFIG.KEYCODES.TAB]: this.handleTab.bind(this)
        };
        return handlers[keyCode];
    },

    handleUpDown(direction, event) {
        const focused = document.querySelector('.focus');
        if (!focused) return;

        const isRecord = focused.tagName === 'TR';
        const selector = isRecord ? 'tr' : 'li';
        const container = isRecord ? '#zone-details' : '#zones';
        
        const items = Array.from(document.querySelectorAll(`${container} ${selector}`));
        const currentIndex = items.indexOf(focused);
        const nextIndex = direction === 'up' ? 
            (currentIndex - 1 + items.length) % items.length : 
            (currentIndex + 1) % items.length;

        this.updateFocus(focused, items[nextIndex]);
        event.preventDefault();
    },

    handleTab(event) {
        const focused = document.querySelector('.focus');
        if (!focused) return;

        const isRecord = focused.tagName === 'TR';
        const nextFocus = isRecord ? 
            document.querySelector('li.current') : 
            document.querySelector('tr.current');

        this.updateFocus(focused, nextFocus);
    },

    updateFocus(oldElement, newElement) {
        oldElement.classList.remove('focus');
        newElement.classList.add('focus');
        newElement.scrollIntoView({ block: 'center', behavior: 'smooth' });
    }
};


