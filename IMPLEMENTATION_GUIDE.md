# Multilingual Database Content Implementation Guide

## Overview
This guide explains how to implement language-specific content for database-driven content in your application. The backend has been updated to support multilingual content through dedicated fields for each language.

## Backend Changes Summary
- Added French language columns to all content tables (e.g., `title_fr`, `description_fr`)
- Updated all model classes with language-specific properties and methods
- Updated all controllers to handle language-specific requests
- Updated simple API endpoints to support language parameters
- Updated frontend API service to pass language parameters

## Frontend Implementation Steps

### 1. Update Components to Use Language-Specific API Calls

For services components, replace existing API calls with language-aware versions:

```javascript
// Import the translation context
import { useTranslation } from '../contexts/i18n/TranslationContext';

// In your component
const { language } = useTranslation();

// Use language-specific API calls
const fetchServices = async () => {
  try {
    const response = await servicesAPI.getAll(language);
    setServices(response.data.data);
  } catch (error) {
    console.error('Error fetching services:', error);
  }
};
```

### 2. Example Component Update Pattern

Here's how to update a typical component to use language-specific content:

```javascript
import React, { useEffect, useState } from 'react';
import { servicesAPI } from '../services/api';
import { useTranslation } from '../contexts/i18n/TranslationContext';

const ServicesComponent = () => {
  const { language, t } = useTranslation();
  const [services, setServices] = useState([]);

  useEffect(() => {
    const loadServices = async () => {
      try {
        const response = await servicesAPI.getAll(language);
        setServices(response.data.data);
      } catch (error) {
        console.error('Error loading services:', error);
      }
    };

    loadServices();
  }, [language]); // Re-fetch when language changes

  return (
    <div>
      <h2>{t('services')}</h2>
      {services.map(service => (
        <div key={service.id}>
          <h3>{service.title}</h3> {/* This will now contain the language-specific content */}
          <p>{service.description}</p>
        </div>
      ))}
    </div>
  );
};

export default ServicesComponent;
```

### 3. Key Components to Update

You should update the following types of components:

#### Services Components
- Update services listing and detail components to use `servicesAPI.getAll(language)` and `servicesAPI.getById(id, language)`

#### Projects Components  
- Update projects listing and detail components to use `projectsAPI.getAll(language)` and `projectsAPI.getById(id, language)`

#### About Page Components
- Update about page to use `aboutAPI.getAbout(language)`

#### Other Content Components
- Apply the same pattern to testimonials, team members, gallery, etc.

### 4. Handling Fallback Content

When French content is not available, the system will fall back to English content. The API handles this automatically.

### 5. Language Change Detection

Components will automatically re-render with the appropriate language content when the user changes the language, as they listen to the `language` context value.

## Implementation Checklist

- [ ] Update all services-related components to use language-specific API calls
- [ ] Update all projects-related components to use language-specific API calls  
- [ ] Update about page component to use language-specific API calls
- [ ] Update testimonials components to use language-specific API calls
- [ ] Update team members components to use language-specific API calls
- [ ] Update gallery components to use language-specific API calls
- [ ] Update settings components to use language-specific API calls
- [ ] Test that content changes when language is switched
- [ ] Verify that fallback behavior works when French content is not available

## Testing Instructions

1. Add French content for various items through the admin panel
2. Switch between languages on the frontend
3. Verify that content changes appropriately
4. Test that English content is shown when French content is not available
5. Verify that all CRUD operations work for both languages in the admin panel