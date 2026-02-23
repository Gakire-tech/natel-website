import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { useTranslation } from '../contexts/i18n/TranslationContext';
import { settingsAPI, servicesAPI } from '../services/api';

const Footer = () => {
  const [settings, setSettings] = useState(null);
  const [dbServices, setDbServices] = useState([]);
  const [loading, setLoading] = useState(true);
  const currentYear = new Date().getFullYear();
  const { t } = useTranslation();

  useEffect(() => {
    const fetchData = async () => {
      try {
        // Fetch settings
        const settingsResponse = await settingsAPI.getAll();
        if (settingsResponse.data.success) {
          setSettings(settingsResponse.data.data);
        } else {
          throw new Error('Settings API returned unsuccessful response');
        }

        // Fetch services
        const servicesResponse = await servicesAPI.getAll();
        if (servicesResponse.data.success) {
          setDbServices(servicesResponse.data.data);
        } else {
          throw new Error('Services API returned unsuccessful response');
        }
      } catch (error) {
        console.error('Error fetching data:', error);
        // Don't set default values - show error state instead
        setSettings(null);
        setDbServices([]);
      } finally {
        setLoading(false);
      }
    };
    
    fetchData();
  }, []);

  const quickLinks = [
    { name: t('home'), path: '/' },
    { name: t('about'), path: '/about' },
    { name: t('services'), path: '/services' },
    // { name: t('projects'), path: '/projects' },
    { name: t('contact'), path: '/contact' }
  ];

  return (
    <footer className="bg-dark text-white">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div className="grid grid-cols-1 md:grid-cols-4 gap-8">
          <div className="col-span-1">
            <h3 className="text-2xl font-bold mb-4 text-accent-500">{settings ? settings.site_title || 'NATEL SYSTEMS' : 'NATEL SYSTEMS'}</h3>
            <p className="text-gray-300 mb-4">
              {t('footerDescription')}
            </p>
            <div className="flex space-x-4">
              {loading ? (
                // Show loading placeholders
                <>
                  <div className="h-6 w-6 bg-gray-700 rounded animate-pulse"></div>
                  <div className="h-6 w-6 bg-gray-700 rounded animate-pulse"></div>
                  <div className="h-6 w-6 bg-gray-700 rounded animate-pulse"></div>
                </>
              ) : (
                // Show actual social links or defaults
                <>
                  <a 
                    href={settings?.facebook_url || '#'} 
                    target="_blank" 
                    rel="noopener noreferrer" 
                    className={`transition ${settings?.facebook_url ? 'text-gray-300 hover:text-accent-500' : 'text-gray-600 cursor-not-allowed'}`}
                  >
                    <span className="sr-only">Facebook</span>
                    <svg className="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                      <path fillRule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clipRule="evenodd" />
                    </svg>
                  </a>
                  <a 
                    href={settings?.linkedin_url || '#'} 
                    target="_blank" 
                    rel="noopener noreferrer" 
                    className={`transition ${settings?.linkedin_url ? 'text-gray-300 hover:text-accent-500' : 'text-gray-600 cursor-not-allowed'}`}
                  >
                    <span className="sr-only">LinkedIn</span>
                    <svg className="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                    </svg>
                  </a>
                  <a 
                    href={settings?.whatsapp_url || '#'} 
                    target="_blank" 
                    rel="noopener noreferrer" 
                    className={`transition ${settings?.whatsapp_url ? 'text-gray-300 hover:text-accent-500' : 'text-gray-600 cursor-not-allowed'}`}
                  >
                    <span className="sr-only">WhatsApp</span>
                    <svg className="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.46-8.435z"/>
                    </svg>
                  </a>
                  <a 
                    href={settings?.instagram_url || '#'} 
                    target="_blank" 
                    rel="noopener noreferrer" 
                    className={`transition ${settings?.instagram_url ? 'text-gray-300 hover:text-accent-500' : 'text-gray-600 cursor-not-allowed'}`}
                  >
                    <span className="sr-only">Instagram</span>
                    <svg className="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.667.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                    </svg>
                  </a>
                  <a 
                    href={settings?.twitter_url || '#'} 
                    target="_blank" 
                    rel="noopener noreferrer" 
                    className={`transition ${settings?.twitter_url ? 'text-gray-300 hover:text-accent-500' : 'text-gray-600 cursor-not-allowed'}`}
                  >
                    <span className="sr-only">Twitter</span>
                    <svg className="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                    </svg>
                  </a>
                </>
              )}
            </div>
          </div>

          <div>
            <h4 className="text-lg font-semibold mb-4">{t('quickLinks')}</h4>
            <ul className="space-y-2">
              {quickLinks.map((link) => (
                <li key={link.path}>
                  <Link to={link.path} className="text-gray-300 hover:text-white transition">
                    {link.name}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          <div>
            <h4 className="text-lg font-semibold mb-4">{t('ourServices')}</h4>
            <ul className="space-y-2">
              {dbServices.length > 0 ? (
                dbServices.map((service) => (
                  <li key={service.id} className="text-gray-300">
                    {service.title}
                  </li>
                ))
              ) : loading ? (
                // Loading skeleton
                <>
                  <li className="h-4 bg-gray-700 rounded animate-pulse"></li>
                  <li className="h-4 bg-gray-700 rounded animate-pulse"></li>
                  <li className="h-4 bg-gray-700 rounded animate-pulse"></li>
                </>
              ) : (
                // Fallback to translated services if no database services
                [
                  t('webDevelopment'),
                  t('mobileApplications'),
                  t('digitalMarketing'),
                  t('cloudSolutions'),
                  t('eCommece'),
                  t('uiUxDesign')
                ].map((service, index) => (
                  <li key={index} className="text-gray-300">
                    {service}
                  </li>
                ))
              )}
            </ul>
          </div>

          <div>
            <h4 className="text-lg font-semibold mb-4">{t('industriesWeTransform')}</h4>
            <ul className="space-y-2">
              {[
                t('hospitalitySector'),
                t('governmentSector'),
                t('manufacturingSector'),
                t('retailSupermarketSector'),
                t('educationSector'),
                t('healthcareSector'),
                t('logisticsSector')
              ].map((industry, index) => (
                <li key={index} className="text-gray-300 flex items-center">
                  <span className="mr-2">•</span>
                  {industry}
                </li>
              ))}
            </ul>
          </div>
          
        </div>

        <div className="mt-12 pt-8 border-t border-gray-800">
          <p className="text-center text-gray-400">
            &copy; {currentYear} {loading ? 'Loading...' : (settings?.footer_text || 'NATEL SYSTEMS')}. {t('allRightsReserved')}
          </p>
        </div>
      </div>
    </footer>
  );
};

export default Footer;