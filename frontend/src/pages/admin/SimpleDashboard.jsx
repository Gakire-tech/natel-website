import React, { useState, useEffect } from 'react';
import { useAuth } from '../../contexts/AuthContext.jsx';

const SimpleDashboard = () => {
  const { user, isAuthenticated, token } = useAuth();
  const [error, setError] = useState(null);

  useEffect(() => {
    try {
      console.log('Dashboard mounted');
      console.log('User:', user);
      console.log('Authenticated:', isAuthenticated);
      console.log('Token:', token);
      console.log('LocalStorage token:', localStorage.getItem('token'));
    } catch (err) {
      console.error('Error in dashboard:', err);
      setError(err.message);
    }
  }, [user, isAuthenticated, token]);

  if (error) {
    return (
      <div className="p-6 bg-red-50 border border-red-200 rounded-lg">
        <h2 className="text-xl font-bold text-red-800">Error Occurred</h2>
        <p className="text-red-700">{error}</p>
        <button 
          onClick={() => window.location.reload()} 
          className="mt-2 px-4 py-2 bg-red-600 text-white rounded"
        >
          Reload Page
        </button>
      </div>
    );
  }

  return (
    <div className="p-6">
      <h1 className="text-3xl font-bold text-gray-800 mb-4">Simple Dashboard</h1>
      
      <div className="bg-white rounded-lg shadow p-6 mb-6">
        <h2 className="text-xl font-semibold mb-4">Auth Status</h2>
        <div className="space-y-2">
          <p><strong>Authenticated:</strong> {isAuthenticated ? '✅ Yes' : '❌ No'}</p>
          <p><strong>User:</strong> {user ? user.name : 'None'}</p>
          <p><strong>Email:</strong> {user ? user.email : 'None'}</p>
          <p><strong>Role:</strong> {user ? user.role : 'None'}</p>
          <p><strong>Token exists:</strong> {token ? '✅ Yes' : '❌ No'}</p>
          <p><strong>LocalStorage token:</strong> {localStorage.getItem('token') ? '✅ Exists' : '❌ Missing'}</p>
        </div>
      </div>

      <div className="bg-white rounded-lg shadow p-6">
        <h2 className="text-xl font-semibold mb-4">Debug Actions</h2>
        <div className="flex space-x-4">
          <button 
            onClick={() => {
              localStorage.clear();
              window.location.reload();
            }}
            className="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
          >
            Clear Storage & Reload
          </button>
          <button 
            onClick={() => window.location.href = '/admin/login'}
            className="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
          >
            Go to Login
          </button>
          <button 
            onClick={() => console.log('Current state:', { user, isAuthenticated, token })}
            className="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
          >
            Log State
          </button>
        </div>
      </div>
    </div>
  );
};

export default SimpleDashboard;