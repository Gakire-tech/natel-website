import React from 'react'
import ReactDOM from 'react-dom/client'
import './index.css'

// Dynamically import App for better initial load performance
const renderApp = async () => {
  const { default: App } = await import('./App.jsx')
  
  ReactDOM.createRoot(document.getElementById('root')).render(
    <React.StrictMode>
      <App />
    </React.StrictMode>,
  )
}

// Start rendering when DOM is ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', renderApp)
} else {
  renderApp()
}