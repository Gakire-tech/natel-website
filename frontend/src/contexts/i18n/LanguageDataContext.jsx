import React, { createContext, useContext, useState, useEffect } from 'react';
import { languageManager } from './LanguageManager';

const LanguageDataContext = createContext();

export const LanguageDataProvider = ({ children }) => {
  const [currentLanguage, setCurrentLanguage] = useState(
    typeof window !== 'undefined' ? localStorage.getItem('selectedLanguage') || 'fr' : 'fr'
  );
  const [refreshToken, setRefreshToken] = useState(0);

  // Listen for language changes
  useEffect(() => {
    const handleLanguageChange = (newLang) => {
      setCurrentLanguage(newLang);
      // Increment refresh token to trigger re-renders in dependent components
      setRefreshToken(prev => prev + 1);
    };

    const unsubscribe = languageManager.onLanguageChange(handleLanguageChange);
    
    return () => {
      unsubscribe();
    };
  }, []);

  const value = {
    currentLanguage,
    refreshToken,
    forceRefresh: () => setRefreshToken(prev => prev + 1)
  };

  return (
    <LanguageDataContext.Provider value={value}>
      {children}
    </LanguageDataContext.Provider>
  );
};

export const useLanguageData = () => {
  const context = useContext(LanguageDataContext);
  if (!context) {
    throw new Error('useLanguageData must be used within a LanguageDataProvider');
  }
  return context;
};