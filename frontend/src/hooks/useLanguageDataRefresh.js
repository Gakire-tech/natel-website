import { useEffect, useRef } from 'react';

// Custom hook to handle data refresh on language change
export const useLanguageDataRefresh = (refetchFunction) => {
  const refetchRef = useRef(refetchFunction);
  
  // Update the ref when the refetch function changes
  useEffect(() => {
    refetchRef.current = refetchFunction;
  }, [refetchFunction]);

  useEffect(() => {
    const handleLanguageChange = () => {
      if (refetchRef.current) {
        refetchRef.current();
      }
    };

    // Listen for the custom language change event
    window.addEventListener('languageChanged', handleLanguageChange);

    // Cleanup event listener on unmount
    return () => {
      window.removeEventListener('languageChanged', handleLanguageChange);
    };
  }, []);

  // Return a manual refetch function in case components need to call it directly
  return {
    refetch: () => {
      if (refetchRef.current) {
        refetchRef.current();
      }
    }
  };
};