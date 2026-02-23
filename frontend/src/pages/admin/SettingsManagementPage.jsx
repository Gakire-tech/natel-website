import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { settingsAPI } from '../../services/api';
import toast from 'react-hot-toast';

const SettingsPage = () => {
  const [formData, setFormData] = useState({
    site_title: '',
    address: '',
    phone: '',
    email: '',
    google_maps_url: '',
    footer_text: '',
    facebook_url: '',
    linkedin_url: '',
    whatsapp_url: '',
    instagram_url: '',
    twitter_url: '',
    youtube_url: '',
    working_hours: '',
    meta_description: '',
    meta_keywords: '',
    site_keywords: '',
    google_analytics_id: '',
    smtp_host: '',
    smtp_port: 587,
    smtp_username: '',
    smtp_password: '',
    smtp_encryption: 'tls',
    maintenance_mode: 0,
    contact_notifications: 1,
    newsletter_enabled: 1,
    confirmation_message: '',
    email_sender_address: '',
    email_sender_name: '',
    email_enabled: 1,
    site_title_fr: '',
    address_fr: '',
    footer_text_fr: '',
    working_hours_fr: '',
    meta_description_fr: '',
    confirmation_message_fr: ''
  });

  const [logo, setLogo] = useState(null);
  const [previewLogo, setPreviewLogo] = useState(null);
  const [loading, setLoading] = useState(false);
  const [isLoaded, setIsLoaded] = useState(false);

  useEffect(() => {
    fetchSettings();
  }, []);

  const fetchSettings = async () => {
    try {
      const response = await settingsAPI.getAll();
      if (response.data.success) {
        setFormData(response.data.data);
        setIsLoaded(true);
      }
    } catch (error) {
      toast.error('Failed to fetch settings');
      console.error('Error fetching settings:', error);
    }
  };

  const resetToDefaultSettings = async () => {
    const defaultSettings = {
      site_title: 'NATEL SYSTEMS',
      address: '123 Business Avenue, Tech District, NY 10001',
      phone: '+257 76 90 03 43',
      email: 'info@nateldigital.com',
      google_maps_url: 'https://maps.google.com/maps?q=123+Business+Avenue,+NY',
      footer_text: '© 2026 NATEL SYSTEMS. Transforming ideas into digital reality.',
      facebook_url: 'https://facebook.com/nateldigital',
      linkedin_url: 'https://linkedin.com/company/nateldigital',
      whatsapp_url: 'https://wa.me/15551234567',
      instagram_url: '',
      twitter_url: '',
      youtube_url: 'https://youtube.com/company/nateldigital',
      working_hours: 'Monday-Friday: 9AM-5PM, Saturday: 10AM-2PM',
      meta_description: 'NATEL SYSTEMS provides innovative technology solutions for businesses worldwide.',
      meta_keywords: 'technology, digital solutions, software development, IT services',
      site_keywords: 'NATEL SYSTEMS, technology, innovation, software',
      google_analytics_id: '',
      smtp_host: 'smtp.gmail.com',
      smtp_port: 587,
      smtp_username: 'komezaaudelo@gmail.com',
      smtp_password: 'wllgsnekbzaqyeha',
      smtp_encryption: 'tls',
      maintenance_mode: 0,
      contact_notifications: 1,
      newsletter_enabled: 1,
      confirmation_message: 'Thank you for contacting us. We have received your message and will respond shortly.',
      email_sender_address: 'komezaaudelo@gmail.com',
      email_sender_name: 'NATEL SYSTEMS COMPANY',
      email_enabled: 1,
      site_title_fr: 'NATEL SYSTEMS',
      address_fr: 'Avenue des Affaires 123, Quartier Technologique, NY 10001',
      footer_text_fr: '© 2026 NATEL SYSTEMS. Transformation des idées en réalité numérique.',
      working_hours_fr: 'Lundi-Vendredi: 9h-17h, Samedi: 10h-14h',
      meta_description_fr: 'NATEL SYSTEMS fournit des solutions technologiques innovantes pour les entreprises du monde entier.',
      confirmation_message_fr: 'Merci de nous avoir contactés. Nous avons reçu votre message et vous répondrons sous peu.'
    };

    try {
      setLoading(true);
      await settingsAPI.update(1, defaultSettings);
      setFormData(defaultSettings);
      toast.success('Settings reset to default values!');
    } catch (error) {
      toast.error('Failed to reset settings');
      console.error('Error resetting settings:', error);
    } finally {
      setLoading(false);
    }
  };

  const handleInputChange = (e) => {
    const { name, value, type, checked } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: type === 'checkbox' ? (checked ? 1 : 0) : value
    }));
  };

  const handleLogoChange = (e) => {
    const file = e.target.files[0];
    if (file) {
      setLogo(file);
      setPreviewLogo(URL.createObjectURL(file));
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);

    try {
      // Handle logo upload first if a new logo is selected
      let logoPath = formData.logo_path;
      if (logo) {
        const logoFormData = new FormData();
        logoFormData.append('logo', logo);
        
        try {
          const uploadResponse = await settingsAPI.uploadLogo(logoFormData);
          if (uploadResponse.data.success) {
            logoPath = uploadResponse.data.file_path;
            toast.success('Logo uploaded successfully!');
            // Update preview immediately
            setPreviewLogo(URL.createObjectURL(logo));
          }
        } catch (uploadError) {
          toast.error('Failed to upload logo');
          console.error('Error uploading logo:', uploadError);
        }
      }

      // Update settings with the logo path
      const data = { ...formData, logo_path: logoPath };
      await settingsAPI.update(1, data);
      toast.success('Settings updated successfully!');
      
      // Update local state immediately to reflect changes
      setFormData(prev => ({
        ...prev,
        ...data,
        logo_path: logoPath
      }));
      
      // Clear preview and reset logo selection state
      setPreviewLogo(null);
      setLogo(null);
      
      // Refresh settings to get the latest data from server
      setTimeout(() => {
        fetchSettings();
      }, 500);
    } catch (error) {
      toast.error('Failed to update settings');
      console.error('Error updating settings:', error);
    } finally {
      setLoading(false);
    }
  };

  if (!isLoaded) {
    return (
      <div className="flex justify-center items-center h-64">
        <div className="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary-600"></div>
      </div>
    );
  }

  return (
    <motion.div
      initial={{ opacity: 0, y: 20 }}
      animate={{ opacity: 1, y: 0 }}
      transition={{ duration: 0.5 }}
      className="max-w-4xl mx-auto"
    >
      <h1 className="text-3xl font-bold text-gray-800 mb-6">Site Settings</h1>

      <form onSubmit={handleSubmit} className="space-y-6">
        {/* General Information Section */}
        <div className="bg-white rounded-lg shadow p-6">
          <h2 className="text-xl font-semibold text-gray-800 mb-4">General Information</h2>
          
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Site Title (EN)
              </label>
              <input
                type="text"
                name="site_title"
                value={formData.site_title}
                onChange={handleInputChange}
                className="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
                required
              />
            </div>
            
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Site Title (FR)
              </label>
              <input
                type="text"
                name="site_title_fr"
                value={formData.site_title_fr}
                onChange={handleInputChange}
                className="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Phone
              </label>
              <input
                type="text"
                name="phone"
                value={formData.phone}
                onChange={handleInputChange}
                className="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Email
              </label>
              <input
                type="email"
                name="email"
                value={formData.email}
                onChange={handleInputChange}
                className="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Address (EN)
              </label>
              <input
                type="text"
                name="address"
                value={formData.address}
                onChange={handleInputChange}
                className="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
              />
            </div>
            
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Address (FR)
              </label>
              <input
                type="text"
                name="address_fr"
                value={formData.address_fr}
                onChange={handleInputChange}
                className="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
              />
            </div>
          </div>
        </div>

        {/* Logo Section */}
        <div className="bg-white rounded-lg shadow p-6">
          <h2 className="text-xl font-semibold text-gray-800 mb-4">Logo</h2>
          
          <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
            {/* Logo Preview Section */}
            <div className="flex flex-col items-center">
              <div className="mb-4">
                <h3 className="text-lg font-medium text-gray-700 mb-2">Current Logo</h3>
                <div className="flex justify-center">
                  {formData.logo_path ? (
                    <div className="border-2 border-green-300 rounded-lg p-4 bg-green-50">
                      <div className="text-center mb-2">
                        <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                          Saved Logo
                        </span>
                      </div>
                      <img
                        src={`/backend/api/uploads/${encodeURIComponent(formData.logo_path)}`}
                        onError={(e) => {
                          e.target.onerror = null;
                          e.target.src = '/backend/api/uploads/default-logo.png';
                        }}
                        alt="Current Logo"
                        className="max-w-full max-h-32 object-contain mx-auto"
                      />
                      <div className="mt-2 text-xs text-green-700 text-center">
                        {formData.logo_path}
                      </div>
                    </div>
                  ) : (
                    <div className="border-2 border-dashed border-gray-300 rounded-lg p-8 bg-gray-50 w-full flex items-center justify-center min-h-[128px]">
                      <div className="text-center">
                        <svg className="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                          <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 32m8-32V0M8 32l9.172-9.172a4 4 0 015.656 0L28 32" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                        </svg>
                        <p className="mt-2 text-sm text-gray-500">No logo uploaded yet</p>
                      </div>
                    </div>
                  )}
                </div>
              </div>
              
              {formData.logo_path && !previewLogo && (
                <div className="mt-2 text-sm text-gray-500">
                  <p>File: {formData.logo_path}</p>
                </div>
              )}
            </div>
            
            {/* Upload Section */}
            <div>
              <h3 className="text-lg font-medium text-gray-700 mb-2">Upload New Logo</h3>
              <div className="space-y-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">
                    Select Logo Image
                  </label>
                  <input
                    type="file"
                    accept="image/*"
                    onChange={handleLogoChange}
                    className="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100"
                  />
                </div>
                
                <div className="bg-blue-50 border border-blue-200 rounded-md p-4">
                  <h4 className="text-sm font-medium text-blue-800 mb-2">Logo Requirements:</h4>
                  <ul className="text-sm text-blue-700 space-y-1">
                    <li>• Recommended size: 200×80 pixels</li>
                    <li>• Supported formats: JPG, PNG, SVG</li>
                    <li>• Maximum file size: 2MB</li>
                    <li>• Transparent backgrounds work best</li>
                  </ul>
                </div>
                
                {previewLogo && (
                  <div className="bg-green-50 border border-green-200 rounded-md p-3">
                    <p className="text-sm text-green-700 flex items-center">
                      <svg className="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                      </svg>
                      New logo selected. Click "Save Settings" to upload.
                    </p>
                  </div>
                )}
              </div>
            </div>
          </div>
        </div>

        {/* Social Media Section */}
        <div className="bg-white rounded-lg shadow p-6">
          <h2 className="text-xl font-semibold text-gray-800 mb-4">Social Media</h2>
          
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Facebook URL
              </label>
              <input
                type="url"
                name="facebook_url"
                value={formData.facebook_url}
                onChange={handleInputChange}
                className="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
                placeholder="https://facebook.com/yourpage"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                LinkedIn URL
              </label>
              <input
                type="url"
                name="linkedin_url"
                value={formData.linkedin_url}
                onChange={handleInputChange}
                className="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
                placeholder="https://linkedin.com/company/yourcompany"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                WhatsApp URL
              </label>
              <input
                type="url"
                name="whatsapp_url"
                value={formData.whatsapp_url}
                onChange={handleInputChange}
                className="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
                placeholder="https://wa.me/yournumber"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Instagram URL
              </label>
              <input
                type="url"
                name="instagram_url"
                value={formData.instagram_url}
                onChange={handleInputChange}
                className="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
                placeholder="https://instagram.com/yourpage"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Twitter URL
              </label>
              <input
                type="url"
                name="twitter_url"
                value={formData.twitter_url}
                onChange={handleInputChange}
                className="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
                placeholder="https://twitter.com/yourpage"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                YouTube URL
              </label>
              <input
                type="url"
                name="youtube_url"
                value={formData.youtube_url}
                onChange={handleInputChange}
                className="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
                placeholder="https://youtube.com/channel"
              />
            </div>
          </div>
        </div>

        {/* Google Maps Section */}
        <div className="bg-white rounded-lg shadow p-6">
          <h2 className="text-xl font-semibold text-gray-800 mb-4">Google Maps</h2>
          
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">
              Google Maps URL
            </label>
            <input
              type="url"
              name="google_maps_url"
              value={formData.google_maps_url}
              onChange={handleInputChange}
              className="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
              placeholder="https://maps.google.com/maps?q=your+location"
            />
            <p className="text-sm text-gray-500 mt-1">Embed URL for Google Maps</p>
          </div>
        </div>

        {/* SEO & Meta Tags Section */}
        <div className="bg-white rounded-lg shadow p-6">
          <h2 className="text-xl font-semibold text-gray-800 mb-4">SEO & Meta Tags</h2>
          
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Meta Description (EN)
              </label>
              <textarea
                name="meta_description"
                value={formData.meta_description}
                onChange={handleInputChange}
                rows="3"
                className="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
                placeholder="Meta description for SEO..."
              />
            </div>
            
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Meta Description (FR)
              </label>
              <textarea
                name="meta_description_fr"
                value={formData.meta_description_fr}
                onChange={handleInputChange}
                rows="3"
                className="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
                placeholder="Description méta pour le référencement..."
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Meta Keywords
              </label>
              <input
                type="text"
                name="meta_keywords"
                value={formData.meta_keywords}
                onChange={handleInputChange}
                className="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
                placeholder="keyword1, keyword2, keyword3..."
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Site Keywords
              </label>
              <input
                type="text"
                name="site_keywords"
                value={formData.site_keywords}
                onChange={handleInputChange}
                className="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
                placeholder="Main site keywords..."
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Google Analytics ID
              </label>
              <input
                type="text"
                name="google_analytics_id"
                value={formData.google_analytics_id}
                onChange={handleInputChange}
                className="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
                placeholder="UA-XXXXXXXXX-X or G-XXXXXXXXXX"
              />
            </div>
          </div>
        </div>

        {/* Working Hours Section */}
        <div className="bg-white rounded-lg shadow p-6">
          <h2 className="text-xl font-semibold text-gray-800 mb-4">Working Hours</h2>
          
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">
              Working Hours (EN)
            </label>
            <textarea
              name="working_hours"
              value={formData.working_hours}
              onChange={handleInputChange}
              rows="3"
              className="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
              placeholder="Monday-Friday: 9AM-5PM, Saturday: 10AM-2PM..."
            />
          </div>
          
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">
              Working Hours (FR)
            </label>
            <textarea
              name="working_hours_fr"
              value={formData.working_hours_fr}
              onChange={handleInputChange}
              rows="3"
              className="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
              placeholder="Lundi-Vendredi: 9h-17h, Samedi: 10h-14h..."
            />
          </div>
        </div>

        {/* Email Configuration Section */}
        <div className="bg-white rounded-lg shadow p-6">
          <h2 className="text-xl font-semibold text-gray-800 mb-4">Email Configuration</h2>
          
          <div className="space-y-6">
            <div className="flex items-center">
              <input
                type="checkbox"
                name="email_enabled"
                checked={formData.email_enabled}
                onChange={handleInputChange}
                className="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
              />
              <label htmlFor="email_enabled" className="ml-2 block text-sm text-gray-900">
                Enable Email Notifications
              </label>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">
                  SMTP Host
                </label>
                <input
                  type="text"
                  name="smtp_host"
                  value={formData.smtp_host}
                  onChange={handleInputChange}
                  className="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
                  placeholder="smtp.gmail.com"
                />
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">
                  SMTP Port
                </label>
                <input
                  type="number"
                  name="smtp_port"
                  value={formData.smtp_port}
                  onChange={handleInputChange}
                  className="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
                  placeholder="587"
                />
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">
                  SMTP Username
                </label>
                <input
                  type="text"
                  name="smtp_username"
                  value={formData.smtp_username}
                  onChange={handleInputChange}
                  className="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
                  placeholder="your-email@gmail.com"
                />
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">
                  SMTP Password
                </label>
                <input
                  type="password"
                  name="smtp_password"
                  value={formData.smtp_password}
                  onChange={handleInputChange}
                  className="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
                  placeholder="your-app-password"
                />
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">
                  SMTP Encryption
                </label>
                <select
                  name="smtp_encryption"
                  value={formData.smtp_encryption}
                  onChange={handleInputChange}
                  className="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
                >
                  <option value="tls">TLS</option>
                  <option value="ssl">SSL</option>
                  <option value="none">None</option>
                </select>
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">
                  Sender Email Address
                </label>
                <input
                  type="email"
                  name="email_sender_address"
                  value={formData.email_sender_address}
                  onChange={handleInputChange}
                  className="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
                  placeholder="noreply@yoursite.com"
                />
              </div>

              <div className="md:col-span-2">
                <label className="block text-sm font-medium text-gray-700 mb-1">
                  Sender Name
                </label>
                <input
                  type="text"
                  name="email_sender_name"
                  value={formData.email_sender_name}
                  onChange={handleInputChange}
                  className="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
                  placeholder="Your Company Name"
                />
              </div>
            </div>
          </div>
        </div>

        {/* Confirmation Message Section */}
        <div className="bg-white rounded-lg shadow p-6">
          <h2 className="text-xl font-semibold text-gray-800 mb-4">Confirmation Message</h2>
          
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">
              Confirmation Message (EN)
            </label>
            <textarea
              name="confirmation_message"
              value={formData.confirmation_message}
              onChange={handleInputChange}
              rows="4"
              className="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
              placeholder="Enter the confirmation message sent to visitors after they submit a contact form or quote request..."
            />
            <p className="text-sm text-gray-500 mt-1">This message will be sent to visitors who submit contact forms or quote requests.</p>
          </div>
          
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">
              Confirmation Message (FR)
            </label>
            <textarea
              name="confirmation_message_fr"
              value={formData.confirmation_message_fr}
              onChange={handleInputChange}
              rows="4"
              className="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
              placeholder="Entrez le message de confirmation envoyé aux visiteurs après soumission d'un formulaire de contact ou d'une demande de devis..."
            />
            <p className="text-sm text-gray-500 mt-1">Ce message sera envoyé aux visiteurs qui soumettent des formulaires de contact ou des demandes de devis.</p>
          </div>
        </div>

        {/* Footer Section */}
        <div className="bg-white rounded-lg shadow p-6">
          <h2 className="text-xl font-semibold text-gray-800 mb-4">Footer</h2>
          
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">
              Footer Text (EN)
            </label>
            <textarea
              name="footer_text"
              value={formData.footer_text}
              onChange={handleInputChange}
              rows="3"
              className="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
              placeholder="Copyright information, additional text, etc."
            />
          </div>
          
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">
              Footer Text (FR)
            </label>
            <textarea
              name="footer_text_fr"
              value={formData.footer_text_fr}
              onChange={handleInputChange}
              rows="3"
              className="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
              placeholder="Informations sur les droits d'auteur, texte supplémentaire, etc."
            />
          </div>
        </div>

        {/* System Settings Section */}
        <div className="bg-white rounded-lg shadow p-6">
          <h2 className="text-xl font-semibold text-gray-800 mb-4">System Settings</h2>
          
          <div className="space-y-4">
            <div className="flex items-center">
              <input
                type="checkbox"
                name="maintenance_mode"
                checked={formData.maintenance_mode}
                onChange={handleInputChange}
                className="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
              />
              <label htmlFor="maintenance_mode" className="ml-2 block text-sm text-gray-900">
                Maintenance Mode
              </label>
            </div>

            <div className="flex items-center">
              <input
                type="checkbox"
                name="contact_notifications"
                checked={formData.contact_notifications}
                onChange={handleInputChange}
                className="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
              />
              <label htmlFor="contact_notifications" className="ml-2 block text-sm text-gray-900">
                Enable Contact Form Notifications
              </label>
            </div>

            <div className="flex items-center">
              <input
                type="checkbox"
                name="newsletter_enabled"
                checked={formData.newsletter_enabled}
                onChange={handleInputChange}
                className="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
              />
              <label htmlFor="newsletter_enabled" className="ml-2 block text-sm text-gray-900">
                Enable Newsletter
              </label>
            </div>
          </div>
        </div>

        {/* Action Buttons */}
        <div className="flex justify-between items-center">
          <button
            type="button"
            onClick={resetToDefaultSettings}
            disabled={loading}
            className="bg-gray-500 text-white py-2 px-6 rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 disabled:opacity-50 transition"
          >
            Reset to Defaults
          </button>
          <button
            type="submit"
            disabled={loading}
            className="bg-primary-600 text-white py-2 px-6 rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 transition"
          >
            {loading ? 'Saving...' : 'Save Settings'}
          </button>
        </div>
      </form>
    </motion.div>
  );
};

export default SettingsPage;