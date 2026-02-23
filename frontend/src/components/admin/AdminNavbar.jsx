import React from 'react';
import { Link } from 'react-router-dom';
import { useAuth } from '../../contexts/AuthContext.jsx';
import { LanguageSelector } from '../../contexts/i18n/useTranslation';

const AdminNavbar = () => {
  const { user, logout } = useAuth();

  const handleLogout = () => {
    logout();
  };

  return (
    <nav className="bg-white shadow-sm border-b border-gray-200 px-4 py-3">
      <div className="flex justify-between items-center">
        <div className="flex items-center">
          <Link to="/admin/dashboard" className="text-xl font-bold text-primary-600">
            Natel Admin
          </Link>
        </div>

        <div className="flex items-center space-x-4">
          <LanguageSelector />
          <div className="text-sm text-gray-700">
            Welcome, <span className="font-medium">{user?.name}</span>
          </div>
          
          <button
            onClick={handleLogout}
            className="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition"
          >
            Logout
          </button>
        </div>
      </div>
    </nav>
  );
};

export default AdminNavbar;