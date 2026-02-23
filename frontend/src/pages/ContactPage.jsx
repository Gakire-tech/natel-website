import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { messagesAPI, settingsAPI } from '../services/api';
import toast from 'react-hot-toast';
import { useTranslation } from '../contexts/i18n/TranslationContext';

const ContactPage = () => {
  const { t } = useTranslation();
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    phone: '',
    company: '',
    service: '',
    contactMethod: '',
    subject: '',
    message: ''
  });
  const [loading, setLoading] = useState(false);
  const [settings, setSettings] = useState(null);

  useEffect(() => {
    fetchSettings();
  }, []);

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

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: value
    }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);

    try {
      await messagesAPI.create(formData);
      toast.success(t('messageSent'));
      setFormData({
        name: '',
        email: '',
        phone: '',
        company: '',
        service: '',
        contactMethod: '',
        subject: '',
        message: ''
      });
    } catch (error) {
      toast.error(t('failedToSendMessage'));
      console.error('Error sending message:', error);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen bg-light">
      {/* Hero Section */}
      <section className="relative hero-section pt-20 pb-12 overflow-hidden">
        {/* Animated background logo */}
        <div className="absolute inset-0 opacity-30 pointer-events-none">
          <motion.div
            animate={{ 
              x: [-100, window.innerWidth + 100]
            }}
            transition={{ 
              duration: 20, 
              repeat: Infinity,
              ease: "linear"
            }}
            className="absolute top-1/4 left-0 transform -translate-y-1/4"
          >
            <div className="w-32 h-32 bg-white rounded-full flex items-center justify-center shadow-2xl">
              <img 
                src="/backend/uploads/natel.jpeg" 
                alt="Background Logo" 
                className="w-20 h-20 object-contain"
              />
            </div>
          </motion.div>
        </div>
        
        <div className="max-w-7xl mx-auto px-2 sm:px-4 lg:px-6 text-center relative z-10">
          <motion.h1
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.8 }}
            className="text-4xl md:text-5xl font-bold mb-6"
          >
            {t('contactUs')}
          </motion.h1>
          <motion.p
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.8, delay: 0.2 }}
            className="text-xl text-primary-100 max-w-3xl mx-auto"
          >
            {/* {t('homeHeroSubtitle')} */}
          </motion.p>
        </div>
      </section>

      {/* Main Contact Section */}
      <section className="py-16">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-7">
            {/* Contact Information Cards */}
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.8 }}
              className="lg:col-span-1 space-y-5"
            >
              <div className="bg-white rounded-lg shadow-md p-5">
                <h2 className="text-xl font-bold text-gray-900 mb-5">{t('getInTouch')}</h2>
                
                {/* Address */}
                {settings?.address && (
                  <div className="mb-5">
                    <div className="flex items-center mb-2">
                      <svg className="h-4 w-4 text-primary-600 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                      </svg>
                      <h3 className="text-base font-semibold text-gray-900">{t('address')}</h3>
                    </div>
                    <p className="text-gray-600 ml-7">{settings.address}</p>
                  </div>
                )}

                {/* Phone */}
                {settings?.phone && (
                  <div className="mb-5">
                    <div className="flex items-center mb-2">
                      <svg className="h-4 w-4 text-primary-600 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                      </svg>
                      <h3 className="text-base font-semibold text-gray-900">{t('phone')}</h3>
                    </div>
                    <a href={`tel:${settings.phone}`} className="text-gray-600 hover:text-primary-600 transition ml-7 block">
                      {settings.phone}
                    </a>
                  </div>
                )}

                {/* Email */}
                {settings?.email && (
                  <div className="mb-5">
                    <div className="flex items-center mb-2">
                      <svg className="h-4 w-4 text-primary-600 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                      </svg>
                      <h3 className="text-base font-semibold text-gray-900">{t('emailLabel')}</h3>
                    </div>
                    <a href={`mailto:${settings.email}`} className="text-gray-600 hover:text-primary-600 transition ml-7 block">
                      {settings.email}
                    </a>
                  </div>
                )}

                {/* Business Hours */}
                <div className="mb-5">
                  <div className="flex items-center mb-2">
                    <svg className="h-4 w-4 text-primary-600 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 className="text-base font-semibold text-gray-900">{t('businessHours')}</h3>
                  </div>
                  <p className="text-gray-600 ml-7">24/7 Support Available</p>
                  <p className="text-gray-600 ml-7">Mon-Fri: 8AM-6PM</p>
                </div>

                {/* Quick Actions */}
                <div>
                  <h3 className="text-base font-semibold text-gray-900 mb-3">{t('quickActions')}</h3>
                  <div className="flex flex-wrap gap-2 ml-7">
                    {/* <a 
                      href="/quote" 
                      className="bg-primary-600 text-white px-3 py-2 rounded-md text-xs hover:bg-primary-700 transition"
                    >
                      {t('getInstantQuote')}
                    </a> */}
                    {settings?.phone && (
                      <a 
                        href={`tel:${settings.phone}`} 
                        className="border border-primary-600 text-primary-600 px-3 py-2 rounded-md text-xs hover:bg-primary-50 transition"
                      >
                      {t('callNow')}
                      </a>
                    )}
                    {settings?.email && (
                      <a 
                        href={`mailto:${settings.email}`} 
                        className="border border-primary-600 text-primary-600 px-3 py-2 rounded-md text-xs hover:bg-primary-50 transition"
                      >
                        {t('sendEmail')}
                      </a>
                    )}
                  </div>
                </div>
              </div>
            </motion.div>

            {/* Contact Form */}
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.8, delay: 0.2 }}
              className="lg:col-span-2"
            >
              <div className="bg-white rounded-lg shadow-md p-7">
                <h2 className="text-xl font-bold text-gray-900 mb-5">{t('sendUsMessage')}</h2>
                <form onSubmit={handleSubmit} className="space-y-5" id="contact-form">
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                      <label htmlFor="name" className="block text-sm font-medium text-gray-700 mb-2">
                        {t('yourName')} *
                      </label>
                      <input
                        type="text"
                        id="name"
                        name="name"
                        value={formData.name}
                        onChange={handleInputChange}
                        required
                        className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition"
                        placeholder={t('enterName')}
                      />
                    </div>

                    <div>
                      <label htmlFor="email" className="block text-sm font-medium text-gray-700 mb-2">
                        {t('yourEmail')} *
                      </label>
                      <input
                        type="email"
                        id="email"
                        name="email"
                        value={formData.email}
                        onChange={handleInputChange}
                        required
                        className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition"
                        placeholder={t('enterEmail')}
                      />
                    </div>
                  </div>

                  <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                      <label htmlFor="phone" className="block text-sm font-medium text-gray-700 mb-2">
                        {t('phone')}
                      </label>
                      <input
                        type="tel"
                        id="phone"
                        name="phone"
                        value={formData.phone || ''}
                        onChange={(e) => setFormData({...formData, phone: e.target.value})}
                        className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition"
                        placeholder={t('enterPhone')}
                      />
                    </div>

                    <div>
                      <label htmlFor="company" className="block text-sm font-medium text-gray-700 mb-2">
                        {t('companyName')}
                      </label>
                      <input
                        type="text"
                        id="company"
                        name="company"
                        value={formData.company || ''}
                        onChange={(e) => setFormData({...formData, company: e.target.value})}
                        className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition"
                        placeholder={t('enterCompanyName')}
                      />
                    </div>
                  </div>

                  <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                      <label htmlFor="service" className="block text-sm font-medium text-gray-700 mb-2">
                        {t('serviceType')}
                      </label>
                      <select
                        id="service"
                        name="service"
                        value={formData.service || ''}
                        onChange={(e) => setFormData({...formData, service: e.target.value})}
                        className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition"
                      >
                        <option value="">{t('selectService')}</option>
                        <option value="web-development">{t('webDevelopment')}</option>
                        <option value="mobile-applications">{t('mobileApplications')}</option>
                        <option value="digital-marketing">{t('digitalMarketing')}</option>
                        <option value="cloud-solutions">{t('cloudSolutions')}</option>
                        <option value="ui-ux-design">{t('uiUxDesign')}</option>
                        <option value="other">{t('other')}</option>
                      </select>
                    </div>

                    <div>
                      <label htmlFor="contact-method" className="block text-sm font-medium text-gray-700 mb-2">
                        {t('preferredContactMethod')}
                      </label>
                      <select
                        id="contact-method"
                        name="contactMethod"
                        value={formData.contactMethod || ''}
                        onChange={(e) => setFormData({...formData, contactMethod: e.target.value})}
                        className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition"
                      >
                        <option value="">{t('howContactYou')}</option>
                        <option value="email">{t('email')}</option>
                        <option value="phone">{t('phone')}</option>
                        <option value="whatsapp">WhatsApp</option>
                      </select>
                    </div>
                  </div>

                  <div>
                    <label htmlFor="subject" className="block text-sm font-medium text-gray-700 mb-2">
                      {t('yourSubject')} *
                    </label>
                    <input
                      type="text"
                      id="subject"
                      name="subject"
                      value={formData.subject}
                      onChange={handleInputChange}
                      required
                      className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition"
                      placeholder={t('enterSubject')}
                    />
                  </div>

                  <div>
                    <label htmlFor="message" className="block text-sm font-medium text-gray-700 mb-2">
                      {t('yourMessage')} *
                    </label>
                    <textarea
                      id="message"
                      name="message"
                      rows="4"
                      value={formData.message}
                      onChange={handleInputChange}
                      required
                      className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition"
                      placeholder="Enter your message here..."
                    />
                  </div>

                  <button
                    type="submit"
                    disabled={loading}
                    className="w-full bg-primary-600 text-white py-2 px-5 rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 transition"
                  >
                    {loading ? t('sending') : t('sendMessage')}
                  </button>
                </form>
              </div>
            </motion.div>
          </div>

          {/* Map Section */}
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.8, delay: 0.4 }}
            className="mt-16"
          >
            <div className="bg-white rounded-lg shadow-md p-6">
              <h2 className="text-2xl font-bold text-gray-900 mb-6">{t('findUs')}</h2>
              <div className="mb-4">
                {/* <p className="text-gray-600">{settings?.address || '89 KG 14 Ave, Bujumbura, Burundi'}</p>
                {(settings?.google_maps_url || settings?.address) && (
                  <a 
                    href={settings?.google_maps_url || `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(settings?.address || '89 KG 14 Ave, Bujumbura, Burundi')}`} 
                    target="_blank" 
                    rel="noopener noreferrer"
                    className="text-primary-600 hover:text-primary-700 font-medium inline-flex items-center mt-2"
                  >
                    <svg className="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    {t('viewOnGoogleMaps')}
                  </a>
                )} */}
              </div>
              <div className="bg-gray-200 rounded-lg overflow-hidden h-80 w-full relative shadow-inner">
                {settings?.google_maps_url ? (
                  <motion.iframe
                    initial={{ opacity: 0, scale: 0.95 }}
                    animate={{ opacity: 1, scale: 1 }}
                    transition={{ duration: 0.6 }}
                    src={settings.google_maps_url}
                    width="100%"
                    height="100%"
                    style={{ border: 0 }}
                    allowFullScreen
                    loading="lazy"
                    referrerPolicy="no-referrer-when-downgrade"
                    title="Office Location Map"
                    className="transition-all duration-300 hover:brightness-105"
                    onError={(e) => {
                      e.target.style.display = 'none';
                      // Afficher le placeholder à la place
                      const placeholder = e.target.parentElement.nextElementSibling;
                      if (placeholder) {
                        placeholder.style.display = 'flex';
                      }
                    }}
                  ></motion.iframe>
                ) : (
                  <motion.div 
                    initial={{ opacity: 0 }}
                    animate={{ opacity: 1 }}
                    transition={{ duration: 0.6 }}
                    className="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200"
                  >
                    <div className="text-center p-6 rounded-xl bg-white shadow-lg border border-gray-200 max-w-xs">
                      <motion.div
                        animate={{ scale: [1, 1.1, 1] }}
                        transition={{ duration: 2, repeat: Infinity }}
                        className="inline-block"
                      >
                        <svg className="h-12 w-12 text-primary-500 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                      </motion.div>
                      <h3 className="text-lg font-semibold text-gray-800 mb-2">{t('findUs')}</h3>
                      <p className="text-gray-600 text-sm mb-3">{t('mapLoading')}</p>
                      <div className="bg-blue-50 rounded-lg p-3 border border-blue-100">
                        <p className="text-xs text-gray-700 font-medium">📍 Location:</p>
                        <p className="text-xs text-gray-600 mt-1">89 KG 14 Ave, Kigali, Rwanda</p>
                      </div>
                    </div>
                  </motion.div>
                )}
              </div>
            </div>
          </motion.div>
        </div>
      </section>
    </div>
  );
};

export default ContactPage;