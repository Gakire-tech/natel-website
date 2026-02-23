import React, { Suspense } from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import { Toaster } from 'react-hot-toast';
import { AuthProvider } from './contexts/AuthContext.jsx';
import { TranslationProvider } from './contexts/i18n/TranslationContext.jsx';
import { LanguageRefreshProvider } from './contexts/i18n/LanguageRefreshContext.jsx';
import { LanguageDataProvider } from './contexts/i18n/LanguageDataContext.jsx';

// Layouts
import PublicLayout from './layouts/PublicLayout.jsx';
import AdminLayout from './layouts/AdminLayout.jsx';

// Lazy load pages for better performance
const HomePage = React.lazy(() => import('./pages/HomePage.jsx'));
const AboutPage = React.lazy(() => import('./pages/AboutPage.jsx'));
const ServicesPage = React.lazy(() => import('./pages/ServicesPage.jsx'));
const ProjectsPage = React.lazy(() => import('./pages/ProjectsPage.jsx'));
const ContactPage = React.lazy(() => import('./pages/ContactPage.jsx'));
const QuotePage = React.lazy(() => import('./pages/QuotePage.jsx'));

// Admin Pages
const AdminLoginPage = React.lazy(() => import('./pages/admin/LoginPage.jsx'));
const DashboardPage = React.lazy(() => import('./pages/admin/SimpleDashboard.jsx'));
const SettingsPage = React.lazy(() => import('./pages/admin/SettingsManagementPage.jsx'));
const ServicesManagementPage = React.lazy(() => import('./pages/admin/ServicesManagementPage.jsx'));
const ProjectsManagementPage = React.lazy(() => import('./pages/admin/ProjectsManagementPage.jsx'));
const MessagesPage = React.lazy(() => import('./pages/admin/MessagesManagementPage.jsx'));
const UsersPage = React.lazy(() => import('./pages/admin/UsersManagementPage.jsx'));
const AboutManagementPage = React.lazy(() => import('./pages/admin/AboutManagementPage.jsx'));
const QuotesManagementPage = React.lazy(() => import('./pages/admin/QuotesManagementPage.jsx'));

// No loading indicator - pages load silently
const LoadingFallback = () => null;

function App() {
  return (
    <TranslationProvider>
      <LanguageRefreshProvider>
        <LanguageDataProvider>
          <AuthProvider>
            <Router>
              <Toaster position="top-right" />
              <Suspense fallback={<LoadingFallback />}>  
                <Routes>
                  {/* Public Routes */}
                  <Route path="/" element={<PublicLayout />}>  
                    <Route index element={<HomePage />} />
                    <Route path="about" element={<AboutPage />} />
                    <Route path="services" element={<ServicesPage />} />
                    <Route path="projects" element={<ProjectsPage />} />
                    <Route path="contact" element={<ContactPage />} />
                    <Route path="quote" element={<QuotePage />} />
                  </Route>

                  {/* Admin Routes */}
                  <Route path="/admin" element={<AdminLayout />}>  
                    <Route index element={<DashboardPage />} />
                    <Route path="login" element={<AdminLoginPage />} />
                    <Route path="dashboard" element={<DashboardPage />} />
                    <Route path="settings" element={<SettingsPage />} />
                    <Route path="services" element={<ServicesManagementPage />} />
                    <Route path="projects" element={<ProjectsManagementPage />} />
                    <Route path="messages" element={<MessagesPage />} />
                    <Route path="users" element={<UsersPage />} />
                    <Route path="about" element={<AboutManagementPage />} />
                    <Route path="quotes" element={<QuotesManagementPage />} />
                  </Route>
                </Routes>
              </Suspense>
            </Router>
          </AuthProvider>
        </LanguageDataProvider>
      </LanguageRefreshProvider>
    </TranslationProvider>
  );
}

export default App;