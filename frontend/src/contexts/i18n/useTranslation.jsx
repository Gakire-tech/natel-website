import React from 'react';
import { useTranslation } from './TranslationContext';

// Translation component for simple usage
export const Trans = ({ children, i18nKey }) => {
  const { t } = useTranslation();
  return <>{t(i18nKey) || children}</>;
};

// Language selector component
export const LanguageSelector = () => {
  const { language, changeLanguage, availableLanguages } = useTranslation();

  return (
    <div className="flex items-center space-x-2">
      <select
        value={language}
        onChange={(e) => changeLanguage(e.target.value)}
        className="bg-white border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
      >
        {availableLanguages.map((lang) => (
          <option key={lang} value={lang}>
            {lang.toUpperCase()}
          </option>
        ))}
      </select>
    </div>
  );
};