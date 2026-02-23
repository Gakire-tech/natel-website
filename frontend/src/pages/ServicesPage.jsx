import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { servicesAPI } from '../services/api';
import { useTranslation } from '../contexts/i18n/TranslationContext';

const ServicesPage = () => {
  const { t } = useTranslation();
  const [services, setServices] = useState([]);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    fetchServices();
  }, []);

  const fetchServices = async () => {
    try {
      const response = await servicesAPI.getAll();
      setServices(response.data.data);
    } catch (error) {
      console.error('Error fetching services:', error);
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return (
      <div className="flex justify-center items-center h-screen">
        <div className="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary-600"></div>
      </div>
    );
  }

  return (
    <div className="min-h-screen">
      {/* Hero Section */}
     <section className="hero-section pt-20 pb-12">
        {/* Animated background logo */}
        <div className="absolute inset-0 opacity-30 pointer-events-none">
          <motion.div
            animate={{ 
              x: [-100, window.innerWidth + 100],
              y: [0.100, -0.100]
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
                fetchPriority="low"
                style={{ visibility: 'hidden' }}
                onError={(e) => {
                  e.target.onerror = null;
                  e.target.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgZmlsbD0iI2NjYyIvPjx0ZXh0IHg9IjUwIiB5PSI1MCIgZm9udC1zaXplPSIxNCIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iIGZpbGw9IiM4ODgiPk5vIEltYWdlPC90ZXh0Pjwvc3ZnPg==';
                }}
                onLoad={(e) => {
                  e.target.style.visibility = 'visible';
                }}
              />
            </div>
          </motion.div>
        </div>
        
        <div className="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
          <motion.h1
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.8 }}
            className="text-4xl md:text-5xl font-bold mb-6"
          >
            {t('ourServices')}
          </motion.h1>
          <motion.p
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.8, delay: 0.2 }}
            className="text-xl text-primary-100 max-w-3xl mx-auto"
          >
            {/* {t('servicesDescription')} */}
          </motion.p>
        </div>
      </section>

      {/* Services Grid */}
      <section className="section bg-light">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {services.map((service, index) => (
              <motion.div
                key={service.id}
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.5, delay: index * 0.1 }}
                className="rounded-xl shadow-lg overflow-hidden"
              >
                {/* Large Image - Full Width */}
                {service.icon_path ? (
                  <img
                    src={`http://localhost/infinity/backend/uploads/${encodeURIComponent(service.icon_path)}`}
                    alt={service.title}
                    className="w-full h-48 object-cover"
                    loading="lazy"
                    onError={(e) => {
                      try {
                        const el = e.target;
                        if (!el.dataset.attempt) {
                          el.dataset.attempt = 'api';
                          el.src = `http://localhost/infinity/backend/api/uploads/${encodeURIComponent(service.icon_path)}`;
                          return;
                        }
                      } catch (err) {
                        // ignore
                      }
                      e.target.onerror = null;
                      e.target.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgZmlsbD0iI2NjYyIvPjx0ZXh0IHg9IjUwIiB5PSI1MCIgZm9udC1zaXplPSIxNCIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iIGZpbGw9IiM4ODgiPk5vIEltYWdlPC90ZXh0Pjwvc3ZnPg==';
                    }}
                  />
                ) : (
                  <div className="w-full h-48 bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center">
                    <span className="text-5xl">⚙️</span>
                  </div>
                )}
                
                {/* Content with Title, Description, Status */}
                <div className="p-5">
                  <h3 className="text-xl font-semibold text-gray-900 mb-2">{service.title}</h3>
                  <p className="text-gray-600 mb-4 text-sm text-left">{service.description}</p>
                  <span className={`inline-block px-2 py-1 rounded-full text-xs font-medium ${service.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`}>
                    {service.status}
                  </span>
                </div>
              </motion.div>
            ))}
          </div>

          {services.length === 0 && (
            <div className="text-center py-12">
              <p className="text-gray-500 text-lg">{t('noServices')}</p>
            </div>
          )}
        </div>
      </section>

      {/* Why Choose Us */}
      <section className="section bg-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-14">
            <h2 className="section-title text-primary-800">
              {t('whyChooseTitle')}
            </h2>
            <p className="section-subtitle">
              {t('whyChooseDescription')}
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-7">
            {[
              {
                title: t('expertTeam'),
                description: t('expertTeamDescription') || 'Highly skilled professionals with years of experience in their respective fields.',
                icon: '👥'
              },
              {
                title: t('qualityAssurance'),
                description: t('qualityAssuranceDescription') || 'Rigorous testing and quality control processes to ensure excellence.',
                icon: '✅'
              },
              {
                title: t('timelyDelivery'),
                description: t('timelyDeliveryDescription') || 'We respect deadlines and deliver projects on time without compromising quality.',
                icon: '⏰'
              },
              {
                title: t('support'),
                description: t('supportDescription') || 'Round-the-clock customer support to assist you with any queries or issues.',
                icon: '📞'
              }
            ].map((feature, index) => (
              <motion.div
                key={index}
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.5, delay: index * 0.1 }}
                className="service-card text-center"
              >
                <div className="text-3xl mb-3">{feature.icon}</div>
                <h3 className="text-lg font-semibold text-gray-900 mb-2">{feature.title}</h3>
                <p className="text-gray-600 text-sm">{feature.description}</p>
              </motion.div>
            ))}
          </div>
        </div>
      </section>

      {/* CTA Section */}
      <section className="section hero-section">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <h2 className="text-2xl md:text-3xl font-bold mb-5">
            {t('readyToTransform')}
          </h2>
          {/* <p className="text-lg text-primary-100 mb-7 max-w-2xl mx-auto">
            {t('readyToTransformDescription')}
          </p> */}
          <a
            href="/contact"
            className="btn-light font-semibold py-2 px-7 rounded-lg transition duration-300 inline-block"
          >
            {t('getInTouch')}
          </a>
        </div>
      </section>
    </div>
  );
};

export default ServicesPage;