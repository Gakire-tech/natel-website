import React, { useState, useEffect, useRef } from 'react';
import { Link } from 'react-router-dom';
import { motion } from 'framer-motion';
import { useTranslation } from '../contexts/i18n/TranslationContext';
import { settingsAPI, servicesAPI, aboutAPI } from '../services/api';


const HomePage = () => {
  const [settings, setSettings] = useState(null);
  const [dynamicServices, setDynamicServices] = useState([]);
  const [aboutData, setAboutData] = useState(null);
  const [loading, setLoading] = useState(false);
  const [selectedService, setSelectedService] = useState(null);
  const { t, dbLanguage } = useTranslation();

  useEffect(() => {
    fetchSettings();
    fetchServices();
    fetchAboutData();
  }, [dbLanguage]);

  const fetchAboutData = async () => {
      try {
        setLoading(true);
        const response = await aboutAPI.getAbout();
        if (response.data.success) {
          setAboutData(response.data.data);
        }
      } catch (error) {
        console.error('Error fetching about data:', error);
      } finally {
        setLoading(false);
      }
    };
  const fetchServices = async () => {
    try {
      const response = await servicesAPI.getAll(dbLanguage);
      console.log('Services API Response:', response.data);
      if (response.data.success && response.data.data) {
        // Map database services to the format expected by the UI
        const mappedServices = response.data.data.slice(0, 4).map(service => ({
          id: service.id,
          title: service.name || service.title || service.title_fr || 'Service',
          description: service.description || service.description_fr || 'Service description',
          icon: service.icon || '🔧',
          icon_path: service.icon_path // This is what we need for images
        }));
        console.log('Mapped services:', mappedServices);
        setDynamicServices(mappedServices);
      }
    } catch (error) {
      console.error('Error fetching services:', error);
      // Fallback to static services if API fails
      setDynamicServices(services);
    }
  };

  const fetchSettings = async () => {
    try {
      const response = await settingsAPI.getAll();
      if (response.data.success) {
        setSettings(response.data.data);
      }
    } catch (error) {
      console.error('Error fetching settings:', error);
    } finally {
      setLoading(false);
    }
  };

  const services = [
    {
      title: t('webDevelopment'),
      description: t('webDevelopmentDescription') || 'Professional web development services to build responsive and scalable websites and applications.',
      icon: '💻'
    },
    {
      title: t('mobileApplications'),
      description: t('mobileApplicationsDescription') || 'Cross-platform mobile application development for iOS and Android devices.',
      icon: '📱'
    },
    {
      title: t('digitalMarketing'),
      description: t('digitalMarketingDescription') || 'Comprehensive digital marketing strategies to grow your online presence and reach customers.',
      icon: '📈'
    },
    {
      title: t('cloudSolutions'),
      description: t('cloudSolutionsDescription') || 'Scalable cloud infrastructure and migration services for modern businesses.',
      icon: '☁️'
    }
  ];

  // Fallback truncation: approximate 4 lines of text
  const clampText = (text, maxChars = 420) => {
    if (!text) return '';
    if (typeof text !== 'string') return String(text);
    if (text.length <= maxChars) return text;
    const truncated = text.slice(0, maxChars);
    return truncated.replace(/\s+\S*$/, '') + '...';
  };

  // Heuristic truncation based on container width and font size
  const descRef = useRef(null);

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
      <section className="relative hero-section" style={{
        backgroundImage: 'url(http://localhost/infinity/backend/api/uploads/back.jpg)',
        backgroundSize: 'cover',
        backgroundPosition: 'center',
        backgroundRepeat: 'no-repeat',
        backgroundAttachment: 'fixed'
      }}>
        {/* Blue tint overlay - for less brightness */}
        <div className="absolute inset-0 bg-primary-900/60"></div>
        <div className="absolute inset-0 bg-blue-900/40"></div>
        
      <br/>
      <br/>
        <div className="max-w-7xl mx-auto px-4  sm:px-6 lg:px-8 relative z-10">
          {/* Centered Text */}
          <div className="flex justify-center ">
            <motion.span
              initial={{ opacity: 0, scale: 0.3 }}
              animate={{ opacity: 2, scale: 1 }}
              transition={{ duration: 1, delay: 0.8, type: 'spring', stiffness: 80 }}
              className="inline-block text-white text-2xl animate-pulse shadow-glow md:text-3xl font-bold"
            >
              {t('professionalDigitalSolutions') || 'Solutions Numériques Professionnelles'}
            </motion.span>
          </div>
          
      <br/>
      <br/>
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center mt-8">
            {/* Left side - Logo and company branding */}
            <motion.div
              initial={{ opacity: 0, x: -50 }}
              animate={{ opacity: 1, x: 0 }}
              transition={{ duration: 0.8 }}
              className="flex flex-col items-center justify-center space-y-6"
            >
              {/* Animated website logo */}
              <div className="relative">
                
                
                <motion.div
                  animate={{
                    scale: [1, 1.1, 1],
                    rotate: [0, 5, -5, 0],
                    y: [0, -10, 0]
                  }}
                  transition={{
                    duration: 4,
                    repeat: Infinity,
                    ease: "easeInOut"
                  }}
                  className="w-48 h-48 md:w-56 md:h-56 rounded-full bg-gradient-to-br from-accent-500 to-primary-500 flex items-center justify-center shadow-2xl"
                >
                  <div className="bg-white rounded-full w-40 h-40 md:w-48 md:h-48 flex items-center justify-center">
                    <img 
                      src="http://localhost/infinity/backend/uploads/natel.jpeg" 
                      alt="Natel Logo" 
                      className="w-32 h-32 md:w-40 md:h-40 object-contain rounded-full"
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
            </motion.div>
            
            {/* Right side - Digitalization showcase */}
            <motion.div
              initial={{ opacity: 0, x: 50 }}
              animate={{ opacity: 1, x: 0 }}
              transition={{ duration: 0.8 }}
              className="space-y-6"
            >
              <div className="space-y-3">{/* Animated N symbol above the logo */}
                {/* <motion.div
                  animate={{
                    scale: [1, 1.2, 1],
                    rotate: [0, 10, -10, 0],
                    y: [0, -10, 0]
                  }}
                  transition={{
                    duration: 3,
                    repeat: Infinity,
                    ease: "easeInOut"
                  }}
                  className="absolute -top-8 left-1/2 transform -translate-x-1/2 z-20"
                >
                  <img 
                    src="/backend/api/uploads/natel-mini-logo.png" 
                    alt="Natel Mini Logo" 
                    className="w-8 h-8 object-contain"
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
                </motion.div> */}
                {/* <motion.div 
                  initial={{ opacity: 0, y: 20 }}
                  animate={{ opacity: 1, y: 0 }}
                  transition={{ duration: 0.6, delay: 0.2 }}
                  className="inline-flex items-center px-3 py-2 rounded-full bg-gradient-to-r from-accent-500/20 to-primary-500/20 backdrop-blur-sm border border-white/30 text-white text-xs md:text-sm font-medium"
                >
                  <span className="mr-2">🚀</span>
                  {t('creatingDigitalSolutions') || 'CRÉATION DE SOLUTIONS NUMÉRIQUES POUR LE MONDE MODERNE'}
                </motion.div> */}
                
                <h1 className="text-white block text-sm md:text-base lg:text-lg mt-2 italic">
                  <span className="text-white">
                    {t('transformYourWay') || 'Ameliorez votre façon de faire'}
                    <span className="text-transparent bg-clip-text bg-gradient-to-r from-accent-400 to-primary-400">
                      {' '}{t('Businesss') || 'des affaires'}
                    </span>
                    {' '}{t('withOurDigitalSolutions') || 'avec nos Solutions Numériques'}
                  </span>
                </h1>
                                
                <motion.p 
                  initial={{ opacity: 0, y: 20 }}
                  animate={{ opacity: 1, y: 0 }}
                  transition={{ duration: 0.6, delay: 0.4 }}
                  className="text-white block text-xs md:text-sm lg:text-base mt-2 italic"
                >
                  {t('creatingDigitalSolutionsDesc') || "Nous développons des applications sur mesure et proposons des services adaptés aux défis d'aujourd'hui et de demain."}
                </motion.p>
              </div>
              
              <motion.div 
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.6, delay: 0.6 }}
                className="flex flex-col sm:flex-row gap-3"
              >
                {/* <Link
                  to="/services"
                  className="bg-gradient-to-r from-accent-500 to-primary-500 hover:from-accent-600 hover:to-primary-500 text-white font-bold py-3 px-6 rounded-xl transition-all duration-300 transform hover:-translate-y-1 hover:shadow-2xl text-center text-base shadow-xl"
                >
                  {t('discoverOurSolutions') || 'Découvrir nos Solutions'}
                </Link> */}
                <Link
                  to="/contact"
                  className="border-2 border-white text-white hover:bg-white hover:text-primary-800 font-bold py-1 px-3 rounded-md transition-all duration-300 text-center text-xs italic backdrop-blur-sm bg-white/10"
                >
                  {t('contactUs') || 'Nous Contacter'}
                </Link>
              </motion.div>
            </motion.div>
          </div>
        </div>
      <br/>
      <br/>
      <br/>
      <br/>
      <br/>
      <br/>
      <br/>
      <br/>
      </section>

      {/* Industries Focus Section - Motivational and Animated */}
      <section className="py-8 bg-gradient-to-r from-accent-50 to-primary-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-10">
            <motion.h2 
              initial={{ opacity: 0, y: 10 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.05 }}
              className="text-3xl md:text-4xl font-bold text-primary-800 mb-3"
            >
              {t('industriesWeTransform')}
            </motion.h2>
            <motion.p 
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.8, delay: 0.2 }}
              className="text-lg text-gray-600 max-w-2xl mx-auto"
            >
              {t('drivingInnovation')}
            </motion.p>
          </div>
          
          <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
            {[
              { name: t('hospitalitySector') || 'Hospitality', icon: '🏨', color: 'from-pink-500 to-pink-600' },
              { name: t('governmentSector') || 'Government', icon: '🏛️', color: 'from-blue-500 to-blue-600' },
              { name: t('manufacturingSector') || 'Manufacturing', icon: '🏭', color: 'from-purple-500 to-purple-600' },
              { name: t('retailSupermarketSector') || 'Retail & Supermarket', icon: '🏪', color: 'from-green-500 to-green-600' },
              { name: t('educationSector') || 'Education', icon: '🎓', color: 'from-indigo-500 to-indigo-600' },
              { name: t('healthcareSector') || 'Healthcare', icon: '🏥', color: 'from-red-500 to-red-600' },
              { name: t('logisticsSector') || 'Logistics', icon: '🚚', color: 'from-orange-500 to-orange-600' }
            ].map((industry, index) => (
              <motion.div
                key={industry.name}
                initial={{ opacity: 0, scale: 0.8 }}
                animate={{ opacity: 1, scale: 1 }}
                transition={{ 
                  duration: 5.5, 
                  delay: index * 0.9,
                  repeat: Infinity,
                  repeatType: "reverse",
                  repeatDelay: 2
                }}
                whileHover={{ scale: 1.05, y: -5 }}
                className={`bg-white rounded-xl p-5 text-center shadow-lg hover:shadow-xl transition-all duration-300 cursor-pointer group`}
              >
                <div className={`inline-flex items-center justify-center w-14 h-14 rounded-full bg-gradient-to-br ${industry.color} mb-3 group-hover:rotate-12 transition-transform duration-300`}>
                  <span className="text-2xl">{industry.icon}</span>
                </div>
                <h3 className="font-semibold text-gray-800 text-sm">{industry.name}</h3>
                <div className="mt-2 w-full h-1 bg-gradient-to-r from-transparent via-accent-400 to-transparent rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
              </motion.div>
            ))}
          </div>
          
          <motion.div 
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.8, delay: 0.8 }}
            className="text-center mt-10"
          >
            <p className="text-gray-600 italic">
              "{t('transformingIndustries')}"
            </p>
          </motion.div>
        </div>
      </section>

      {/* Services Section */}
      <section className="section bg-light py-12">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-14">
            <h2 className="section-title text-primary-800">
              {t('ourServices')}
            </h2>
            <div className="max-w-3xl mx-auto">
              <p className="section-subtitle text-left clamp-4">
                {settings?.servicesDescription || t('servicesDescription')}
              </p>
            </div>
          </div>

          <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            {(dynamicServices.length > 0 ? dynamicServices : services)
              .sort((a, b) => (a.id || 0) - (b.id || 0))
              .map((service, index) => (
              <motion.div
                key={service.id || index}
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.5, delay: index * 0.1 }}
                className="relative overflow-hidden rounded-xl shadow-lg group cursor-pointer"
              >
                {/* Large Image - Full Width */}
                {service.icon_path ? (
                  <img
                    src={`http://localhost/infinity/backend/api/uploads/${encodeURIComponent(service.icon_path)}`}
                    alt={service.title}
                    className="w-full h-54 md:h-80 object-cover cursor-pointer rounded-lg shadow-lg"
                    loading="lazy"
                    onClick={() => setSelectedService(service)}
                    onError={(e) => {
                      e.target.onerror = null;
                      e.target.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgZmlsbD0iI2NjYyIvPjx0ZXh0IHg9IjUwIiB5PSI1MCIgZm9udC1zaXplPSIxNCIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iIGZpbGw9IiM4ODgiPk5vIEltYWdlPC90ZXh0Pjwvc3ZnPg==';
                    }}
                  />
                ) : (
                  <div className="w-full h-64 md:h-80 bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center cursor-pointer rounded-lg shadow-lg"
                    onClick={() => setSelectedService(service)}>
                    <span className="text-5xl">{service.icon || '⚙️'}</span>
                  </div>
                )}
                
                {/* Title Overlay */}
                <div className="absolute inset-0 flex items-end justify-center p-0.5 pointer-events-none">
                  <h3 className="text-sm md:text-base font-bold text-center pointer-events-auto">
                    {service.title}
                  </h3>
                </div>
              </motion.div>
            ))}
          </div>

          <div className="text-center mt-10">
            <Link
              to="/services"
              className="btn-primary font-medium py-2 px-7 rounded-lg transition duration-300"
            >
              {t('viewAllServices')}
            </Link>
          </div>
        </div>
      </section>

      {/* About Section */}
      <section className="section bg-white py-12">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
            <motion.div
              initial={{ opacity: 0, x: -20 }}
              animate={{ opacity: 1, x: 0 }}
              transition={{ duration: 0.8 }}
            >
              <h2 className="section-title text-left text-primary-800 mb-5">
                {t('aboutUs')}
              </h2>
              {/* <p className="text-base text-gray-600 mb-5">
                {t('aboutDescription')}
              </p> */}
              <p className="text-base text-gray-600 mb-7">
                {t('homeHeroSubtitle')}
              </p>
              <Link
                to="/about"
                className="btn-primary font-medium py-2 px-7 rounded-lg transition duration-300"
              >
                {t('learnMore')}
              </Link>
            </motion.div>
            <motion.div
              initial={{ opacity: 0, x: 20 }}
              animate={{ opacity: 1, x: 0 }}
              transition={{ duration: 0.8, delay: 0.2 }}
            >
              <img
                src="http://localhost/infinity/backend/api/uploads/about.png"
                alt="About Infinity"
                className="rounded-lg shadow-lg w-full"
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

      {/* Testimonials Preview Section
      <section className="py-14 bg-gradient-to-br from-gray-50 to-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-10">
            <motion.h2 
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.6 }}
              className="text-3xl md:text-4xl font-bold text-primary-800 mb-3"
            >
              {t('clientSuccessStories') || 'Client Success Stories'}
            </motion.h2>
            <motion.p 
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.6, delay: 0.2 }}
              className="text-lg text-gray-600 max-w-2xl mx-auto"
            >
              {t('hearFromOurClients') || 'Hear from businesses that transformed with our solutions'}
            </motion.p>
          </div>
          
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            {[1, 2, 3].map((index) => (
              <motion.div
                key={index}
                initial={{ opacity: 0, y: 30 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.6, delay: index * 0.1 }}
                className="bg-white rounded-xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100"
              >
                <div className="flex items-center mb-4">
                  <div className="w-12 h-12 bg-gradient-to-br from-accent-500 to-primary-500 rounded-full flex items-center justify-center text-white font-bold text-lg mr-4">
                    {t(`testimonial${index}Initials`) || ['PD', 'SD', 'LM'][index-1]}
                  </div>
                  <div>
                    <h4 className="font-semibold text-gray-900">{t(`testimonial${index}Name`) || ['Professor Davis', 'Sarah Director', 'Logistics Manager'][index-1]}</h4>
                    <p className="text-sm text-gray-500">{t(`testimonial${index}Title`) || ['Dean, University College', 'Operations Director, EduTech Solutions', 'Supply Chain Manager, Global Logistics'][index-1]}</p>
                  </div>
                </div>
                <div className="flex text-yellow-400 mb-3">
                  {'★'.repeat(5)}
                </div>
                <p className="text-gray-600 text-sm italic">
                  {t(`testimonial${index}Quote`) || [
                    '"Revolutionary educational technology that enhanced our learning outcomes."',
                    '"Streamlined our logistics operations with innovative digital solutions."',
                    '"Transformed our supply chain management with cutting-edge platforms."'
                  ][index-1]}
                </p>
              </motion.div>
            ))}
          </div>
          
          <motion.div 
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6, delay: 0.6 }}
            className="text-center mt-8"
          >
            <Link
              to="/testimonials"
              className="inline-flex items-center text-accent-600 hover:text-accent-700 font-medium group"
            >
              {t('viewAllTestimonials') || 'View All Success Stories'}
              <svg className="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
              </svg>
            </Link>
          </motion.div>
        </div>
      </section> */}

      {/* Values Section */}
      <section className="section bg-white py-12">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-14">
            <h2 className="section-title text-primary-800">
              {t('ourCoreValues')}
            </h2>
            <p className="section-subtitle">
              {t('coreValuesDescription')}
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-7">
            <div className="text-center p-5 rounded-lg border border-gray-200 hover:shadow-lg transition-shadow duration-300">
              <div className="text-2xl mb-3">🚀</div>
              <h3 className="text-lg font-semibold text-gray-900 mb-2">{t('innovation')}</h3>
              <p className="text-gray-600 text-sm">{t('innovationDescription')}</p>
            </div>
            <div className="text-center p-5 rounded-lg border border-gray-200 hover:shadow-lg transition-shadow duration-300">
              <div className="text-2xl mb-3">🛡️</div>
              <h3 className="text-lg font-semibold text-gray-900 mb-2">{t('Fiabilité ')}</h3>
              <p className="text-gray-600 text-sm">{t('integrityDescription')}</p>
            </div>
            <div className="text-center p-5 rounded-lg border border-gray-200 hover:shadow-lg transition-shadow duration-300">
              <div className="text-2xl mb-3">⭐</div>
              <h3 className="text-lg font-semibold text-gray-900 mb-2">{t('Collaboration ')}</h3>
              <p className="text-gray-600 text-sm">{t('excellenceDescription')}</p>
            </div>
          </div>
        </div>
      </section>

      {/* Partners Section */}
      <section className="py-12 bg-gray-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-12">
            <motion.h2 
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.6 }}
              className="text-3xl md:text-4xl font-bold text-primary-800 mb-3"
            >
              {t('ourPartners') || 'Nos Partenaires'}
            </motion.h2>
            <motion.p 
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.6, delay: 0.2 }}
              className="text-lg text-gray-600 max-w-2xl mx-auto"
            >
              {/* {t('trustedByLeadingCompanies') || 'Fait confiance par les entreprises leaders dans leurs domaines'} */}
            </motion.p>
          </div>
          
          {/* CONTENU PARTENAIRES - CENTRE AU MILLIEU */}
          <div className="flex justify-center w-full">
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-6 max-w-xl w-full">
            {[
              { 
                // name: t('partnerTechCorp') || 'Hope Design', 
                logo: '/backend/api/uploads/logo_design.jpg', 
                url: 'https://hopedesign.bi/',
              },
              { 
                // name: t('partnerInnovateX') || 'Burundi Eco', 
                logo: '/backend/api/uploads/burundi-eco.jpg', 
                url: 'https://burundi-eco.com/'
              }
            ].map((partner, index) => (
              <motion.a
                key={partner.name}
                href={partner.url}
                target="_blank"
                rel="noopener noreferrer"
                initial={{ opacity: 0, y: 30 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.6, delay: index * 0.1 }}
                whileHover={{ y: -5, scale: 1.05 }}
                className="block bg-white rounded-xl p-6 shadow-md hover:shadow-xl transition-all duration-300 border border-gray-100 group"
              >
                <div className="flex items-center justify-center h-20 mb-3">
                  <img 
                    src={partner.logo} 
                    alt={`${partner.name} Logo`}
                    className="max-h-16 max-w-full object-contain transition-all duration-300 group-hover:scale-110"
                    loading="lazy"
                    onError={(e) => {
                      e.target.onerror = null;
                      e.target.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgZmlsbD0iI2NjYyIvPjx0ZXh0IHg9IjUwIiB5PSI1MCIgZm9udC1zaXplPSIxNCIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iIGZpbGw9IiM4ODgiPk5vIEltYWdlPC90ZXh0Pjwvc3ZnPg==';
                    }}
                  />
                </div>
                <div className="text-center">
                  <h3 className="font-semibold text-gray-900 text-sm mb-1 group-hover:text-accent-600 transition-colors">
                    {partner.name}
                  </h3>
                </div>
              </motion.a>
            ))}
            </div>
          </div>
          
          <motion.div 
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6, delay: 0.8 }}
            className="text-center mt-10"
          >
            <p className="text-gray-600 italic">
              {t('becomeAPartner') || 'Souhaitez-vous devenir partenaire ?'}{' '}
              <Link 
                to="/contact" 
                className="text-accent-600 hover:text-accent-700 font-medium underline"
              >
                {t('contactUs') || 'Contactez-nous'}
              </Link>
            </p>
          </motion.div>
        </div>
      </section>

      {/* Service Detail Modal */}
      {selectedService && (
        <div className="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4" onClick={() => setSelectedService(null)}>
          <div className="bg-white rounded-xl max-w-2xl w-full max-h-[90vh] overflow-hidden" onClick={(e) => e.stopPropagation()}>
            <div className="p-6 flex justify-between items-center">
              <h2 className="text-2xl font-bold text-gray-800">{selectedService.title}</h2>
              <button 
                onClick={() => setSelectedService(null)}
                className="text-gray-500 hover:text-gray-700 text-3xl font-bold"
              >
                &times;
              </button>
            </div>
            <div className="p-6 flex items-center justify-center">
              {selectedService.icon_path ? (
                <img
                  src={`http://localhost/infinity/backend/api/uploads/${encodeURIComponent(selectedService.icon_path)}`}
                  alt={selectedService.title}
                  className="max-h-[70vh] w-auto object-cover rounded-lg shadow-xl"
                  onClick={(e) => e.stopPropagation()}
                  onError={(e) => {
                    e.target.onerror = null;
                    e.target.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgZmlsbD0iI2NjYyIvPjx0ZXh0IHg9IjUwIiB5PSI1MCIgZm9udC1zaXplPSIxNCIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iIGZpbGw9IiM4ODgiPk5vIEltYWdlPC90ZXh0Pjwvc3ZnPg==';
                  }}
                />
              ) : (
                <div className="max-h-[70vh] w-full flex items-center justify-center bg-gradient-to-br from-primary-500 to-primary-700 rounded-lg">
                  <span className="text-7xl">{selectedService.icon || '⚙️'}</span>
                </div>
              )}
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default HomePage;