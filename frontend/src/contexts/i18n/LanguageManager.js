// Global language state manager
// Get initial language from localStorage, default to 'fr' if not set
let currentLanguage = typeof window !== 'undefined' ? localStorage.getItem('selectedLanguage') || 'fr' : 'fr';
const listeners = [];

// Create a custom event system for language changes
const eventListeners = {};

export const languageManager = {
  getLanguage: () => currentLanguage,
  
  setLanguage: (lang) => {
    const oldLang = currentLanguage;
    currentLanguage = lang;
    
    // Notify all direct listeners about the language change
    listeners.forEach(callback => callback(lang));
    
    // Trigger language change event
    if (oldLang !== lang && eventListeners['languageChange']) {
      eventListeners['languageChange'].forEach(callback => callback(lang, oldLang));
    }
  },
  
  subscribe: (callback) => {
    listeners.push(callback);
    // Return unsubscribe function
    return () => {
      const index = listeners.indexOf(callback);
      if (index > -1) {
        listeners.splice(index, 1);
      }
    };
  },
  
  // Event system for language changes
  onLanguageChange: (callback) => {
    if (!eventListeners['languageChange']) {
      eventListeners['languageChange'] = [];
    }
    eventListeners['languageChange'].push(callback);
    
    // Return unsubscribe function
    return () => {
      const index = eventListeners['languageChange'].indexOf(callback);
      if (index > -1) {
        eventListeners['languageChange'].splice(index, 1);
      }
    };
  }
};