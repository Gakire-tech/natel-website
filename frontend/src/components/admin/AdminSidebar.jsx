import React from 'react';
import { Link, useLocation } from 'react-router-dom';
import { useTranslation } from '../../contexts/i18n/TranslationContext';
import { 
  FaTachometerAlt, 
  FaCog, 
  FaServer, 
  FaProjectDiagram, 
  FaEnvelope, 
  FaUsers, 
  FaInfoCircle,
  FaFileInvoice
} from 'react-icons/fa';

const AdminSidebar = () => {
  const location = useLocation();
  const { t } = useTranslation();

  const menuItems = [
    {
      title: t('dashboard'),
      path: '/admin/dashboard',
      icon: <FaTachometerAlt className="text-lg" />
    },
    {
      title: t('siteSettings'),
      path: '/admin/settings',
      icon: <FaCog className="text-lg" />
    },
    {
      title: t('services'),
      path: '/admin/services',
      icon: <FaServer className="text-lg" />
    },
    // {
    //   title: t('projects'),
    //   path: '/admin/projects',
    //   icon: <FaProjectDiagram className="text-lg" />
    // },
    {
      title: t('messages'),
      path: '/admin/messages',
      icon: <FaEnvelope className="text-lg" />
    },
    {
      title: t('usersManagement'),
      path: '/admin/users',
      icon: <FaUsers className="text-lg" />
    },
    {
      title: t('aboutManagement'),
      path: '/admin/about',
      icon: <FaInfoCircle className="text-lg" />
    },
    // {
    //   title: 'Quotes',
    //   path: '/admin/quotes',
    //   icon: <FaFileInvoice className="text-lg" />
    // }
  ];

  return (
    <div className="w-64 bg-white shadow-md flex flex-col">
      <div className="p-4 border-b border-gray-200">
        <h1 className="text-xl font-bold text-primary-600">Natel Admin</h1>
      </div>

      <nav className="flex-1 p-4">
        <ul className="space-y-2">
          {menuItems.map((item) => (
            <li key={item.path}>
              <Link
                to={item.path}
                className={`flex items-center p-3 rounded-lg transition ${
                  location.pathname === item.path
                    ? 'bg-primary-100 text-primary-700'
                    : 'text-gray-700 hover:bg-gray-100'
                }`}
              >
                <span className="mr-3">{item.icon}</span>
                <span className="font-medium">{item.title}</span>
              </Link>
            </li>
          ))}
        </ul>
      </nav>

      <div className="p-4 border-t border-gray-200 text-center">
        <p className="text-xs text-gray-500">
          © 2026 Natel SYSTEMS
        </p>
      </div>
    </div>
  );
};

export default AdminSidebar;