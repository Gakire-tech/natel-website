import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { projectsAPI } from '../services/api';
import { useTranslation } from '../contexts/i18n/TranslationContext';

const ProjectsPage = () => {
  const { t } = useTranslation();
  const [projects, setProjects] = useState([]);
  const [loading, setLoading] = useState(false);
  const [filter, setFilter] = useState('all');

  useEffect(() => {
    fetchProjects();
  }, []);

  const fetchProjects = async () => {
    try {
      const response = await projectsAPI.getAll();
      setProjects(response.data.data);
    } catch (error) {
      console.error('Error fetching projects:', error);
    } finally {
      setLoading(false);
    }
  };

  const filteredProjects = filter === 'all' 
    ? projects 
    : projects.filter(project => project.status === filter);

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
            {t('projects')}
          </motion.h1>
          <motion.p
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.8, delay: 0.2 }}
            className="text-xl text-primary-100 max-w-3xl mx-auto"
          >
            {t('explorePortfolio')}
          </motion.p>
        </div>
      </section>

      {/* Filter Section */}
      <section className="py-10 bg-light">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex flex-wrap justify-center gap-3 mb-7">
            <button
              onClick={() => setFilter('all')}
              className={`px-5 py-2 rounded-full font-medium transition-colors ${
                filter === 'all'
                  ? 'bg-primary-600 text-white'
                  : 'bg-white text-gray-700 hover:bg-gray-100'
              }`}
            >
              {t('allProjects')}
            </button>
            <button
              onClick={() => setFilter('active')}
              className={`px-6 py-2 rounded-full font-medium transition-colors ${
                filter === 'active'
                  ? 'bg-primary-600 text-white'
                  : 'bg-white text-gray-700 hover:bg-gray-100'
              }`}
            >
              {t('activeProjects')}
            </button>
            <button
              onClick={() => setFilter('completed')}
              className={`px-6 py-2 rounded-full font-medium transition-colors ${
                filter === 'completed'
                  ? 'bg-primary-600 text-white'
                  : 'bg-white text-gray-700 hover:bg-gray-100'
              }`}
            >
              {t('completedProjects')}
            </button>
          </div>
        </div>
      </section>

      {/* Projects Grid */}
      <section className="section bg-light">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-7">
            {filteredProjects.map((project, index) => (
              <motion.div
                key={project.id}
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.5, delay: index * 0.1 }}
                className="project-card"
              >
                <div className="relative overflow-hidden">
                  {project.image_path ? (
                    <img
                      src={`/backend/uploads/${project.image_path}`}
                      alt={project.name}
                      className="w-full h-56 object-cover transition-transform duration-300 hover:scale-105"
                    />
                  ) : (
                    <div className="w-full h-56 bg-gray-200 flex items-center justify-center">
                      <span className="text-gray-500">No Image</span>
                    </div>
                  )}
                  <div className="absolute top-3 right-3">
                    <span className={`px-2 py-1 rounded-full text-xs font-medium ${
                      project.status === 'active' 
                        ? 'bg-green-100 text-green-800' 
                        : project.status === 'completed'
                          ? 'bg-blue-100 text-blue-800'
                          : 'bg-red-100 text-red-800'
                    }`}>
                      {project.status}
                    </span>
                  </div>
                </div>
                <div className="p-5">
                  <h3 className="text-lg font-semibold text-gray-900 mb-2">{project.name}</h3>
                  {project.client && (
                    <p className="text-gray-600 mb-2 text-sm">Client: {project.client}</p>
                  )}
                  <p className="text-gray-600 mb-3 text-sm line-clamp-3">{project.description}</p>
                  {project.technologies && (
                    <div className="mb-3">
                      <p className="text-xs font-medium text-gray-700 mb-1">Technologies:</p>
                      <div className="flex flex-wrap gap-2">
                        {project.technologies.split(',').map((tech, idx) => (
                          <span
                            key={idx}
                            className="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded"
                          >
                            {tech.trim()}
                          </span>
                        ))}
                      </div>
                    </div>
                  )}
                  {project.project_date && (
                    <p className="text-sm text-gray-500">
                      {new Date(project.project_date).toLocaleDateString()}
                    </p>
                  )}
                </div>
              </motion.div>
            ))}
          </div>

          {filteredProjects.length === 0 && (
            <div className="text-center py-12">
              <p className="text-gray-500 text-lg">{t('noProjects')}</p>
            </div>
          )}
        </div>
      </section>

      {/* CTA Section */}
      <section className="section hero-section">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <h2 className="text-2xl md:text-3xl font-bold mb-5">
            {t('contactCTA')}
          </h2>
          <p className="text-lg text-primary-100 mb-7 max-w-2xl mx-auto">
            {t('contactCTADescription')}
          </p>
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

export default ProjectsPage;