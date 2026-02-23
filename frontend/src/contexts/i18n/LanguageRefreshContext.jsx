import React, { createContext, useContext, useState, useEffect } from 'react';
import { languageManager } from './LanguageManager';

// Create context
const LanguageRefreshContext = createContext();

// Provider component
export const LanguageRefreshProvider = ({ children }) => {
  const [refreshToken, setRefreshToken] = useState(0); // Simple counter to trigger refresh

  useEffect(() => {
    // Set up a listener for language changes
    const unsubscribe = languageManager.onLanguageChange(() => {
      // Increment the refresh token to trigger re-renders in components that depend on it
      setRefreshToken(prev => prev + 1);
    });

    return () => {
      unsubscribe();
    };
  }, []);

  return (
    <LanguageRefreshContext.Provider value={{ refreshToken }}>
      {children}
    </LanguageRefreshContext.Provider>
  );
};

// Custom hook to use the language refresh context
export const useLanguageRefresh = () => {
  const context = useContext(LanguageRefreshContext);
  if (!context) {
    throw new Error('useLanguageRefresh must be used within a LanguageRefreshProvider');
  }
  return context;
};