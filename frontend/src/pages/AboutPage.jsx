import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { aboutAPI } from '../services/api';
import { useTranslation } from '../contexts/i18n/TranslationContext';

// Get API base URL from the aboutAPI to construct proper image paths
const API_BASE_URL = '/backend';

const AboutPage = () => {
  const { t, dbLanguage } = useTranslation();
  const [aboutData, setAboutData] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchAboutData();
  }, [dbLanguage]);

  const fetchAboutData = async () => {
    try {
      setLoading(true);
      
      // Fetch about content with language preference
      const response = await aboutAPI.getAbout(dbLanguage);
      
      if (response.data.success) {
        setAboutData(response.data.data);
      }
    } catch (error) {
      console.error('Error fetching about data:', error);
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
 <div className="flex justify-center -mt-8">
                          <div className="flex items-center space-x-6">
                            {/* First animated logo - using actual logo image */}
                            <motion.div
                              animate={{ 
                                scale: [1, 1.1, 1],
                                y: [0, -5, 0]
                              }}
                              transition={{ 
                                duration: 4, 
                                repeat: Infinity,
                                ease: "easeInOut"
                              }}
                              className="w-20 h-12 rounded-full overflow-hidden shadow-lg"
                            >
                              <img 
                                src="/backend/uploads/natel.jpeg" 
                                alt="Company Logo 1" 
                                className="w-full h-full object-contain"
                              />
                            </motion.div>
                            
                            {/* Moving text between logos */}
                            <div className="relative w-52 h-7 overflow-hidden flex items-center">
                              <motion.div
                                animate={{ x: [-130, 130] }}
                                transition={{ duration: 5, repeat: Infinity, ease: "easeInOut" }}
                                className="absolute whitespace-nowrap text-accent-300 text-sm font-medium"
                              >
                                Connect • Innovate • Lead
                              </motion.div>
                            </div>
                            
                            {/* Second animated logo - using actual logo image */}
                            <motion.div
                              animate={{ 
                                scale: [1, 1.1, 1],
                                y: [0, -5, 0]}}
                              transition={{ duration: 4, reeat: Infinity }}
                              className="w-24 h-12 rounded-full overflow-hidden shadow-lg"
                            >
                              <img 
                                src="/backend/api/uploads/logo_designa.jpg" 
                                alt="Company Logo 2" 
                                className="w-full h-full object-contain"
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
                            </motion.div>
                          </div>
                        </div>
  return (
    <div className="min-h-screen">
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
        
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
          <motion.h1
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.8 }}
            className="text-4xl md:text-5xl font-bold mb-6"
          >
            {t('aboutUs')}
          </motion.h1>
          <motion.p
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.8, delay: 0.2 }}
            className="text-xl text-primary-100 max-w-3xl mx-auto"
          >
            {t('getInTouchDescription')}
          </motion.p>
        </div>
      </section>

      {/* Main Content */}
      <section className="section bg-light">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
            <motion.div
              initial={{ opacity: 0, x: -20 }}
              animate={{ opacity: 1, x: 0 }}
              transition={{ duration: 0.8 }}
            >
              <h2 className="section-title text-left text-primary-800">
                {t('ourStory')}
              </h2><br/>
             
              <div className="prose prose-base text-gray-600">
                {aboutData?.main_content ? (
                  <p>{aboutData.main_content}</p>
                ) : (
                  <p>
                    We are a leading technology company providing innovative solutions to businesses worldwide. 
                    Our mission is to deliver cutting-edge technology solutions that drive business growth.
                  </p>
                )}
              </div>
            </motion.div>
            <motion.div
              initial={{ opacity: 0, x: 20 }}
              animate={{ opacity: 1, x: 0 }}
              transition={{ duration: 0.8, delay: 0.2 }}
              className="flex items-center justify-center min-h-[400px]"
            >
              <img
                src="/backend/api/uploads/about.png"
                alt="About Infinity"
                className="rounded-lg shadow-lg w-full h-full object-cover"
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
            </motion.div>
          </div>
        </div>
      </section>
      
      {/* Mission, Vision, Values */}
      <section className="py-10 bg-gradient-to-r from-primary-800 to-primary-900">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            <motion.div
              initial={{ opacity: 0, scale: 0.8 }}
              whileInView={{ opacity: 1, scale: 1 }}
              transition={{ duration: 0.5, delay: 0.1 }}
              viewport={{ once: true }}
              whileHover={{ scale: 1.03 }}
              className="bg-white/10 backdrop-blur-sm rounded-xl p-5 border border-white/20 text-center"
            >
              <div className="text-3xl mb-3">🎯</div>
              <h3 className="text-lg font-bold text-white mb-2">{t('mission')}</h3>
              <p className="text-primary-100 text-sm">
                {aboutData?.mission || t('mission')}
              </p>
            </motion.div>
                  
            <motion.div
              initial={{ opacity: 0, scale: 0.8 }}
              whileInView={{ opacity: 1, scale: 1 }}
              transition={{ duration: 0.5, delay: 0.2 }}
              viewport={{ once: true }}
              whileHover={{ scale: 1.03 }}
              className="bg-white/10 backdrop-blur-sm rounded-xl p-5 border border-white/20 text-center"
            >
              <div className="text-3xl mb-3">🔮</div>
              <h3 className="text-lg font-bold text-white mb-2">{t('vision')}</h3>
              <p className="text-primary-100 text-sm">
                {aboutData?.vision || t('vision')}
              </p>
            </motion.div>
                  
            <motion.div
              initial={{ opacity: 0, scale: 0.8 }}
              whileInView={{ opacity: 1, scale: 1 }}
              transition={{ duration: 0.5, delay: 0.3 }}
              viewport={{ once: true }}
              whileHover={{ scale: 1.03 }}
              className="bg-white/10 backdrop-blur-sm rounded-xl p-5 border border-white/20 text-center"
            >
              <div className="text-3xl mb-3">💎</div>
              <h3 className="text-lg font-bold text-white mb-2">{t('values')}</h3>
              <p className="text-primary-100 text-sm">
                {aboutData?.values_content || t('values')}
              </p>
            </motion.div>
          </div>
        </div>
      </section>
      
      
      {/* Team Section */}
      <section className="section bg-light">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-14">
            <h2 className="section-title text-primary-800">
              {t('meetOurTeam')}
            </h2>
            <p className="section-subtitle">
              {t('contactCTADescription')}
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-7">
            {(aboutData?.team_members || []).map((member, index) => (
              <motion.div
                key={member.id || index}
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.8, delay: index * 0.1 }}
                className="text-center"
              >
                <img
                  src={member.image_path 
                    ? `${API_BASE_URL}/api/uploads/${member.image_path}` 
                    : member.image || 'https://via.placeholder.com/200'}
                  alt={member.name}
                  className="w-28 h-28 rounded-full mx-auto mb-3 shadow-lg"
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
                <h3 className="text-lg font-semibold text-gray-900">{member.name}</h3>
                <p className="text-gray-600 text-sm">{member.position || member.role || t('teamMember')}</p>
                {member.description && (
                  <p className="text-gray-500 text-xs mt-1">{member.description}</p>
                )}
              </motion.div>
            ))}
          </div>
        </div>
      </section>
    </div>
  );
};

export default AboutPage;