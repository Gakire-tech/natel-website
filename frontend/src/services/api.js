import axios from 'axios';
import { languageManager } from '../contexts/i18n/LanguageManager';

// Base API URL - update this to match your backend URL
const API_BASE_URL = 'http://localhost/infinity/backend';

// Helper function to get current language
const getCurrentLanguage = () => {
  return languageManager.getLanguage() || 'fr'; // Default to French
};

// Create a simple event system for API refresh
const apiSubscribers = [];

export const subscribeToApiRefresh = (callback) => {
  apiSubscribers.push(callback);
  return () => {
    const index = apiSubscribers.indexOf(callback);
    if (index > -1) {
      apiSubscribers.splice(index, 1);
    }
  };
};

export const broadcastApiRefresh = () => {
  apiSubscribers.forEach(callback => {
    if (typeof callback === 'function') {
      callback();
    }
  });
};

// Listen for language changes and broadcast API refresh
languageManager.onLanguageChange(() => {
  broadcastApiRefresh();
});

// Create axios instance
const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
  },
});

// Add token to requests if available
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Response interceptor to handle errors
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      // Token expired or invalid, redirect to login
      localStorage.removeItem('token');
      localStorage.removeItem('user');
      window.location.href = '/admin/login';
    }
    return Promise.reject(error);
  }
);

// Authentication API
export const authAPI = {
  login: (email, password) => api.post('/api/simple_login.php', { email, password }),
};

// Services API
export const servicesAPI = {
  getAll: (language = null) => api.get(`/api/simple_services.php?language=${language || getCurrentLanguage()}`),
  getById: (id, language = null) => api.get(`/api/simple_services.php/${id}?language=${language || getCurrentLanguage()}`),
  create: (data, iconFile = null) => {
    const formData = new FormData();
    Object.keys(data).forEach(key => {
      formData.append(key, data[key]);
    });
    
    if (iconFile) {
      formData.append('icon', iconFile);
    }
    
    return api.post('/api/simple_services.php', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
  },
  update: (id, data, iconFile = null) => {
    const formData = new FormData();
    Object.keys(data).forEach(key => {
      formData.append(key, data[key]);
    });
    
    // Add action parameter to indicate this is an update
    formData.append('action', 'update');
    formData.append('id', id);
    
    if (iconFile) {
      formData.append('icon', iconFile);
    }
    
    return api.post('/api/simple_services.php', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
  },
  delete: (id) => api.delete(`/api/simple_services.php/${id}`),
};

// Projects API
export const projectsAPI = {
  getAll: (language = null) => api.get(`/api/simple_projects.php?language=${language || getCurrentLanguage()}`),
  getById: (id, language = null) => api.get(`/api/simple_projects.php/${id}?language=${language || getCurrentLanguage()}`),
  create: (data, imageFile = null) => {
    const formData = new FormData();
    Object.keys(data).forEach(key => {
      formData.append(key, data[key]);
    });
    
    if (imageFile) {
      formData.append('image', imageFile);
    }
    
    return api.post('/api/simple_projects.php', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
  },
  update: (id, data, imageFile = null) => {
    const formData = new FormData();
    Object.keys(data).forEach(key => {
      formData.append(key, data[key]);
    });
    
    // Add action parameter to indicate this is an update
    formData.append('action', 'update');
    formData.append('id', id);
    
    if (imageFile) {
      formData.append('image', imageFile);
    }
    
    return api.post('/api/simple_projects.php', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
  },
  delete: (id) => api.delete(`/api/simple_projects.php/${id}`),
};

// Messages API
export const messagesAPI = {
  getAll: () => api.get('/api/simple_messages.php'),
  getById: (id) => api.get(`/api/simple_messages.php/${id}`),
  create: (data) => api.post('/api/simple_messages.php', data),
  updateStatus: (id, status) => api.put(`/api/simple_messages.php/${id}/status`, { status }),
  update: (id, data) => api.put(`/api/simple_messages.php/${id}`, data),
  delete: (id) => api.delete(`/api/simple_messages.php/${id}`),
};

// Users API
export const usersAPI = {
  getAll: () => api.get('/api/simple_users.php'),
  getById: (id) => api.get(`/api/simple_users.php/${id}`),
  create: (data) => api.post('/api/simple_users.php', data),
  update: (id, data) => api.put(`/api/simple_users.php/${id}`, data),
  delete: (id) => api.delete(`/api/simple_users.php/${id}`),
};

// About API
export const aboutAPI = {
  getAbout: (language = null) => api.get(`/api/simple_about.php?language=${language || getCurrentLanguage()}`),
  updateAbout: (data) => api.put('/api/simple_about.php', data),
  uploadTeamImage: (formData) => api.post('/api/simple_about.php', formData, {
    headers: {
      'Content-Type': 'multipart/form-data',
    },
  }),
};

// Gallery API
export const galleryAPI = {
  getAll: (language = null) => api.get(`/api/gallery?language=${language || getCurrentLanguage()}`),
  getByCategory: (category, language = null) => api.get(`/api/gallery/category/${category}?language=${language || getCurrentLanguage()}`),
  getById: (id, language = null) => api.get(`/api/gallery/${id}?language=${language || getCurrentLanguage()}`),
  create: (data, imageFile = null) => {
    const formData = new FormData();
    Object.keys(data).forEach(key => {
      formData.append(key, data[key]);
    });
    
    if (imageFile) {
      formData.append('image', imageFile);
    }
    
    return api.post('/api/gallery', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
  },
  update: (id, data, imageFile = null) => {
    const formData = new FormData();
    Object.keys(data).forEach(key => {
      formData.append(key, data[key]);
    });
    
    if (imageFile) {
      formData.append('image', imageFile);
    }
    
    return api.put(`/api/gallery/${id}`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
  },
  delete: (id) => api.delete(`/api/gallery/${id}`),
  uploadImage: (imageFile) => {
    const formData = new FormData();
    formData.append('image', imageFile);
    
    return api.post('/api/gallery/upload', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
  },
  updateSortOrder: (items) => api.put('/api/gallery/sort', { items }),
};

// Testimonials API
export const testimonialsAPI = {
  getAll: (language = null) => api.get(`/api/testimonials?language=${language || getCurrentLanguage()}`),
  getActive: (language = null) => api.get(`/api/testimonials/active?language=${language || getCurrentLanguage()}`),
  getById: (id, language = null) => api.get(`/api/testimonials/${id}?language=${language || getCurrentLanguage()}`),
  create: (data, imageFile = null) => {
    const formData = new FormData();
    Object.keys(data).forEach(key => {
      formData.append(key, data[key]);
    });
    
    if (imageFile) {
      formData.append('image', imageFile);
    }
    
    return api.post('/api/testimonials', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
  },
  update: (id, data, imageFile = null) => {
    const formData = new FormData();
    Object.keys(data).forEach(key => {
      formData.append(key, data[key]);
    });
    
    if (imageFile) {
      formData.append('image', imageFile);
    }
    
    return api.put(`/api/testimonials/${id}`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
  },
  delete: (id) => api.delete(`/api/testimonials/${id}`),
  uploadImage: (imageFile) => {
    const formData = new FormData();
    formData.append('image', imageFile);
    
    return api.post('/api/testimonials/upload', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
  },
};

// Team Members API
export const teamAPI = {
  getAll: (language = null) => api.get(`/api/team?language=${language || getCurrentLanguage()}`),
  getActive: (language = null) => api.get(`/api/team/active?language=${language || getCurrentLanguage()}`),
  getById: (id, language = null) => api.get(`/api/team/${id}?language=${language || getCurrentLanguage()}`),
  create: (data, imageFile = null) => {
    const formData = new FormData();
    Object.keys(data).forEach(key => {
      formData.append(key, data[key]);
    });
    
    if (imageFile) {
      formData.append('image', imageFile);
    }
    
    return api.post('/api/team', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
  },
  update: (id, data, imageFile = null) => {
    const formData = new FormData();
    Object.keys(data).forEach(key => {
      formData.append(key, data[key]);
    });
    
    if (imageFile) {
      formData.append('image', imageFile);
    }
    
    return api.put(`/api/team/${id}`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
  },
  delete: (id) => api.delete(`/api/team/${id}`),
  uploadImage: (imageFile) => {
    const formData = new FormData();
    formData.append('image', imageFile);
    
    return api.post('/api/team/upload', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
  },
  updateSortOrder: (items) => api.put('/api/team/sort', { items }),
};

// Settings API
export const settingsAPI = {
  getAll: (language = null) => api.get(`/api/simple_settings.php?language=${language || getCurrentLanguage()}`),
  getById: (id, language = null) => api.get(`/api/simple_settings.php/${id}?language=${language || getCurrentLanguage()}`),
  update: (id, data) => api.put(`/api/simple_settings.php/${id}`, data),
  uploadLogo: (formData) => api.post('/api/simple_settings.php', formData, {
    headers: {
      'Content-Type': 'multipart/form-data',
    },
  }),
};

// Quotes API
export const quotesAPI = {
  getAll: () => api.get('/api/simple_quotes.php'),
  getById: (id) => api.get(`/api/simple_quotes.php/${id}`),
  create: (data) => api.post('/api/simple_quotes.php', data),
  update: (id, data) => api.put(`/api/simple_quotes.php/${id}`, data),
  delete: (id) => api.delete(`/api/simple_quotes.php/${id}`),
};

// Team Members API
export const teamMembersAPI = {
  getAll: (language = null) => api.get(`/api/simple_team_members.php?language=${language || getCurrentLanguage()}`),
  getById: (id, language = null) => api.get(`/api/simple_team_members.php/${id}?language=${language || getCurrentLanguage()}`),
  create: (data, imageFile = null) => {
    const formData = new FormData();
    Object.keys(data).forEach(key => {
      formData.append(key, data[key]);
    });
    
    if (imageFile) {
      formData.append('image', imageFile);
    }
    
    return api.post('/api/simple_team_members.php', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
  },
  update: (id, data, imageFile = null) => {
    const formData = new FormData();
    Object.keys(data).forEach(key => {
      formData.append(key, data[key]);
    });
    
    if (imageFile) {
      formData.append('image', imageFile);
    }
    
    return api.put(`/api/simple_team_members.php/${id}`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
  },
  delete: (id) => api.delete(`/api/simple_team_members.php/${id}`),
};

export default api;