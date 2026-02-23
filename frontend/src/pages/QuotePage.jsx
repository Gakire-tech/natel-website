import React, { useState } from 'react';
import { motion } from 'framer-motion';
import { useTranslation } from '../contexts/i18n/TranslationContext';
import { quotesAPI } from '../services/api';
import { toast } from 'react-hot-toast';

const QuotePage = () => {
  const { t } = useTranslation();
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    phone: '',
    service: '',
    message: ''
  });

  const handleChange = (e) => {
    setFormData({
      ...formData,
      [e.target.name]: e.target.value
    });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    
    try {
      // Map form data to API format
      const quoteData = {
        client_name: formData.name,
        client_email: formData.email,
        client_phone: formData.phone,
        project_type: formData.service,
        project_details: formData.message,
        company_name: '',
        budget_range: '',
        timeline: '',
        additional_requirements: ''
      };
      
      await quotesAPI.create(quoteData);
      toast.success("Quote submitted successfully! We'll contact you soon.");
      
      // Reset form
      setFormData({
        name: '',
        email: '',
        phone: '',
        service: '',
        message: ''
      });
    } catch (error) {
      console.error('Error submitting quote:', error);
      toast.error('Failed to submit quote. Please try again.');
    }
  };

  return (
    <div className="min-h-screen bg-light py-14">
      <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6 }}
          className="text-center mb-10"
        >
          <h1 className="text-2xl md:text-3xl font-bold text-primary-800 mb-3">
            {t('getQuoteTitle') || 'Get a Free Quote'}
          </h1>
          <p className="text-base text-gray-600">
            {t('getQuoteSubtitle') || 'Fill out the form below and our team will get back to you within 24 hours with a custom quote.'}
          </p>
        </motion.div>

        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6, delay: 0.2 }}
          className="bg-white rounded-lg shadow-lg p-7"
        >
          <form onSubmit={handleSubmit} className="space-y-5">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
              <div>
                <label htmlFor="name" className="block text-sm font-medium text-gray-700 mb-2">
                  {t('yourName') || 'Your Name'}
                </label>
                <input
                  type="text"
                  id="name"
                  name="name"
                  value={formData.name}
                  onChange={handleChange}
                  required
                  className="form-input"
                  placeholder={t('enterName') || 'Enter your full name'}
                />
              </div>
              <div>
                <label htmlFor="email" className="block text-sm font-medium text-gray-700 mb-2">
                  {t('yourEmail') || 'Your Email'}
                </label>
                <input
                  type="email"
                  id="email"
                  name="email"
                  value={formData.email}
                  onChange={handleChange}
                  required
                  className="form-input"
                  placeholder={t('enterEmail') || 'Enter your email address'}
                />
              </div>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
              <div>
                <label htmlFor="phone" className="block text-sm font-medium text-gray-700 mb-2">
                  {t('phone') || 'Phone'}
                </label>
                <input
                  type="tel"
                  id="phone"
                  name="phone"
                  value={formData.phone}
                  onChange={handleChange}
                  className="form-input"
                  placeholder="+250 000 000 000"
                />
              </div>
              <div>
                <label htmlFor="service" className="block text-sm font-medium text-gray-700 mb-2">
                  {t('service') || 'Service'}
                </label>
                <select
                  id="service"
                  name="service"
                  value={formData.service}
                  onChange={handleChange}
                  required
                  className="form-input"
                >
                  <option value="">{t('selectService') || 'Select a service'}</option>
                  <option value="web-development">{t('webDevelopment') || 'Web Development'}</option>
                  <option value="mobile-applications">{t('mobileApplications') || 'Mobile Applications'}</option>
                  <option value="digital-marketing">{t('digitalMarketing') || 'Digital Marketing'}</option>
                  <option value="cloud-solutions">{t('cloudSolutions') || 'Cloud Solutions'}</option>
                  <option value="e-commerce">{t('eCommece') || 'E-commerce'}</option>
                  <option value="ui-ux-design">{t('uiUxDesign') || 'UI/UX Design'}</option>
                </select>
              </div>
            </div>

            <div>
              <label htmlFor="message" className="block text-sm font-medium text-gray-700 mb-2">
                {t('yourMessage') || 'Your Message'}
              </label>
              <textarea
                id="message"
                name="message"
                value={formData.message}
                onChange={handleChange}
                rows={4}
                required
                className="form-input"
                placeholder={t('enterMessage') || 'Enter your message here...'}
              ></textarea>
            </div>

            <div className="text-center">
              <button
                type="submit"
                className="btn-primary font-medium py-2 px-7 rounded-lg transition duration-300"
              >
                {t('sendRequest') || 'Send Request'}
              </button>
            </div>
          </form>
        </motion.div>
      </div>
    </div>
  );
};

export default QuotePage;