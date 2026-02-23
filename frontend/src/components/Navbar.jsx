import React, { useState, useEffect, useCallback } from 'react';
import { Link, useLocation } from 'react-router-dom';
import { useTranslation } from '../contexts/i18n/TranslationContext';
import { settingsAPI } from '../services/api';
import { languageManager } from '../contexts/i18n/LanguageManager';

const Navbar = () => {
  const [isMenuOpen, setIsMenuOpen] = useState(false);
  const [scrolled, setScrolled] = useState(false);
  const [settings, setSettings] = useState(null);
  const location = useLocation();
  const { changeLanguage, language } = useTranslation();
  
  useEffect(() => {
    const fetchSettings = async () => {
      try {
        const response = await settingsAPI.getAll();
        if (response.data.success) {
          setSettings(response.data.data);
        }
      } catch (error) {
        console.error('Error fetching settings:', error);
      }
    };
    
    fetchSettings();
  }, []);

  // Memoize nav links to prevent re-creation on every render
  const { t } = useTranslation();
  
  const navLinks = React.useMemo(() => [
    { name: t('home'), path: '/' },
    { name: t('about'), path: '/about' },
    { name: t('services'), path: '/services' },
    // { name: t('projects'), path: '/projects' },
    { name: t('contact'), path: '/contact' }
  ], [t]);

  // Handle scroll event with useCallback for performance
  const handleScroll = useCallback(() => {
    setScrolled(window.scrollY > 20);
  }, []);

  useEffect(() => {
    window.addEventListener('scroll', handleScroll);
    return () => window.removeEventListener('scroll', handleScroll);
  }, [handleScroll]);

  // Close mobile menu when route changes
  useEffect(() => {
    setIsMenuOpen(false);
  }, [location.pathname]);



  // Handle keyboard navigation for mobile menu toggle
  const handleMenuToggleKeyDown = (e) => {
    if (e.key === 'Enter' || e.key === ' ') {
      e.preventDefault();
      setIsMenuOpen(!isMenuOpen);
    }
  };



  return (
    <nav 
      className="w-full z-50 bg-white sticky top-0 shadow-sm"
      aria-label="Main navigation"
    >
      {/* Top Bar - Compact contact info */}
      <div className="bg-primary-800 text-white text-sm">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between items-center py-2">
            <div className="flex items-center space-x-4">
              {settings && (
                <>
                  <div className="flex items-center">
                    <svg className="h-3 w-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                    </svg>
                    <span className="text-xs">{settings.phone || '+250 785030772'}</span>
                  </div>
                  <div className="flex items-center">
                    <svg className="h-3 w-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                      <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                    </svg>
                    <span className="text-xs">{settings.email || 'info@nateldigital.com'}</span>
                  </div>
                </>
              )}
            </div>
            <div className="flex items-center space-x-2">
              <div className="flex items-center text-xs">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" className="mr-1">
                  <circle cx="12" cy="12" r="10"></circle>
                  <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"></path>
                  <path d="M2 12h20"></path>
                </svg>
                <select 
                  className="bg-primary-800 text-white border-none focus:outline-none text-xs cursor-pointer"
                  onChange={(e) => {
                    const lang = e.target.value;
                    changeLanguage(lang);
                  }}
                  value={language}
                >
                  <option value="en">English</option>
                  <option value="fr">Francais</option>
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Main Navigation Bar */}
      <div className="py-4">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between items-center">
            <div className="flex items-center">
              <Link 
                to="/" 
                className="flex items-center focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 rounded"
                aria-label="Go to homepage"
              >
                {settings?.logo_path && (
                  <img 
                    src={`http://localhost/infinity/backend/uploads/${settings.logo_path}`} 
                    alt={settings.site_title || 'Company Logo'}
                    className="h-10 w-auto object-contain mr-3"
                    onError={(e) => {
                      // Hide broken image
                      e.target.style.display = 'none';
                    }}
                  />
                )}
                <div className="flex flex-col">
                  <span className="text-2xl font-bold text-primary-800">
                    {settings ? settings.site_title || 'NATEL SYSTEMS' : 'NATEL SYSTEMS'}
                  </span>
                  <span className="text-[10px] text-primary-600 -mt-1">Le coeur de la digitalisation</span>
                </div>
              </Link>
            </div>

            {/* Desktop Navigation */}
            <div className="hidden md:flex items-center space-x-10">
              {navLinks.map((link) => (
                <Link
                  key={link.path}
                  to={link.path}
                  className={`font-medium text-sm transition-colors duration-200 px-1 py-2 ${
                    location.pathname === link.path
                      ? 'text-primary-700 border-b-2 border-primary-700'
                      : 'text-gray-600 hover:text-primary-700'
                  }`}
                  aria-current={location.pathname === link.path ? 'page' : undefined}
                >
                  {link.name}
                </Link>
              ))}
            </div>

            <div className="hidden md:flex items-center space-x-3">
              {/* <Link
                to="/quote"
                className="text-primary-700 hover:text-primary-800 font-medium text-sm px-4 py-2 transition-colors duration-200"
              >
                {t('getQuote')}
              </Link> */}
              <Link
                to="/contact"
                className="bg-primary-700 hover:bg-primary-800 text-white px-3 py-1 rounded-lg font-medium text-sm transition duration-300 shadow-sm hover:shadow-md"
              >
                {t('contactUs')}
              </Link>
            </div>

            {/* Mobile menu button */}
            <div className="md:hidden flex items-center space-x-3">
              <Link
                to="/contact"
                className="md:hidden bg-primary-700 text-white px-3 py-1.5 rounded-lg text-sm font-medium"
              >
                {t('contactUs')}
              </Link>
              <button
                onClick={() => setIsMenuOpen(!isMenuOpen)}
                onKeyDown={handleMenuToggleKeyDown}
                className="text-gray-700 hover:text-primary-700 focus:outline-none rounded p-1"
                aria-expanded={isMenuOpen}
                aria-label={isMenuOpen ? 'Close navigation menu' : 'Open navigation menu'}
                type="button"
              >
                <svg
                  className="h-6 w-6"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                  aria-hidden="true"
                >
                  {isMenuOpen ? (
                    <path
                      strokeLinecap="round"
                      strokeLinejoin="round"
                      strokeWidth={2}
                      d="M6 18L18 6M6 6l12 12"
                    />
                  ) : (
                    <path
                      strokeLinecap="round"
                      strokeLinejoin="round"
                      strokeWidth={2}
                      d="M4 6h16M4 12h16M4 18h16"
                    />
                  )}
                </svg>
              </button>
            </div>
          </div>

          {/* Mobile Navigation */}
          {isMenuOpen && (
            <div 
              className="md:hidden py-4 border-t border-gray-100 bg-white absolute top-full left-0 right-0 shadow-lg z-50"
              role="dialog"
              aria-modal="true"
              aria-label="Mobile navigation menu"
            >
              <div className="px-4 pb-4">
                <div className="flex flex-col space-y-2 py-2">
                  {navLinks.map((link) => (
                    <Link
                      key={link.path}
                      to={link.path}
                      className={`font-medium py-3 px-2 rounded-lg transition-colors duration-200 ${
                        location.pathname === link.path
                          ? 'text-primary-700 bg-primary-50'
                          : 'text-gray-700 hover:text-primary-700 hover:bg-gray-50'
                      }`}
                      onClick={() => setIsMenuOpen(false)}
                      aria-current={location.pathname === link.path ? 'page' : undefined}
                    >
                      {link.name}
                    </Link>
                  ))}
                </div>
                <div className="pt-3 border-t border-gray-100">
                  <div className="flex flex-col space-y-2 pt-2">
                    <Link
                      to="/quote"
                      className="text-primary-700 hover:text-primary-800 py-3 px-2 rounded-lg font-medium transition duration-200 text-center border border-primary-200 hover:border-primary-300"
                      onClick={() => setIsMenuOpen(false)}
                    >
                      {t('getQuote')}
                    </Link>
                    <Link
                      to="/contact"
                      className="bg-primary-700 hover:bg-primary-800 text-white py-3 px-2 rounded-lg font-medium transition duration-300 text-center shadow-sm hover:shadow-md"
                      onClick={() => setIsMenuOpen(false)}
                    >
                      {t('contactUs')}
                    </Link>
                  </div>
                </div>
              </div>
            </div>
          )}
        </div>
      </div>
    </nav>
  );
};

export default Navbar;