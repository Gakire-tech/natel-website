import React, { createContext, useContext, useState } from 'react';
import { languageManager } from './LanguageManager';

// Translation dictionary
const translations = {
  en: {
    // Navigation
    home: 'Home',
    about: 'About',
    services: 'Services',
    projects: 'Projects',
    contact: 'Contact',
    getQuote: 'Get Quote',
    admin: 'Admin',
    
    // Auth
    login: 'Login',
    logout: 'Logout',
    email: 'Email',
    password: 'Password',
    rememberMe: 'Remember me',
    forgotPassword: 'Forgot password?',
    signIn: 'Sign In',
    signUp: 'Sign Up',
    
    // Dashboard
    dashboard: 'Dashboard',
    welcome: 'Welcome',
    totalUsers: 'Total Users',
    totalServices: 'Total Services',
    totalProjects: 'Total Projects',
    totalMessages: 'Total Messages',
    newMessages: 'New Messages',
    
    // Settings
    settings: 'Settings',
    siteSettings: 'Site Settings',
    siteTitle: 'Site Title',
    address: 'Address',
    phone: 'Phone',
    emailLabel: 'Email',
    googleMapsUrl: 'Google Maps URL',
    footerText: 'Footer Text',
    facebookUrl: 'Facebook URL',
    linkedinUrl: 'LinkedIn URL',
    whatsappUrl: 'WhatsApp URL',
    save: 'Save',
    saveChanges: 'Save Changes',
    
    // Services
    servicesManagement: 'Services Management',
    addNewService: 'Add New Service',
    editService: 'Edit Service',
    serviceName: 'Service Name',
    serviceDescription: 'Description',
    status: 'Status',
    active: 'Active',
    inactive: 'Inactive',
    delete: 'Delete',
    update: 'Update',
    add: 'Add',
    
    // Projects
    projectsManagement: 'Projects Management',
    addNewProject: 'Add New Project',
    editProject: 'Edit Project',
    projectName: 'Project Name',
    projectDescription: 'Description',
    client: 'Client',
    technologies: 'Technologies',
    projectDate: 'Project Date',
    completed: 'Completed',
    
    // Messages
    messages: 'Messages',
    allMessages: 'All Messages',
    messageDetails: 'Message Details',
    name: 'Name',
    subject: 'Subject',
    message: 'Message',
    date: 'Date',
    markAsRead: 'Mark as Read',
    
    // Users
    usersManagement: 'Users Management',
    addUser: 'Add User',
    editUser: 'Edit User',
    fullName: 'Full Name',
    role: 'Role',
    editor: 'Editor',
    
    // About
    aboutManagement: 'About Management',
    mainContent: 'Main Content',
    mission: 'Mission',
    vision: 'Vision',
    values: 'Values',
    
    // Contact
    sendMessage: 'Send Message',
    yourName: 'Your Name',
    yourEmail: 'Your Email',
    yourSubject: 'Subject',
    yourMessage: 'Your Message',
    contactUs: 'Contact Us',
    
    // Footer
    quickLinks: 'Quick Links',
    ourServices: 'Our Services',
    
    // Common
    cancel: 'Cancel',
    actions: 'Actions',
    noData: 'No data available',
    loading: 'Loading...',
    error: 'Error',
    success: 'Success',
    confirmDelete: 'Are you sure you want to delete this item?',
    quickActions: 'Quick Actions',
    getInstantQuote: 'Get Instant Quote',
    callNow: 'Call Now',
    sendEmail: 'Send Email',
    
    // Pages
    homeHeroTitle: 'Building Digital Solutions for the Modern World',
    homeHeroSubtitle: 'We create innovative technology solutions that help businesses grow and thrive in the digital age.',
    servicesDescription: 'That will help your businesses succeed in the digital world.',
    aboutUs: 'About Us',
    aboutDescription: 'We are a leading technology company providing innovative solutions to businesses worldwide.',
    contactTitle: 'Ready to Start Your Project?',
    contactSubtitle: 'Contact us today to discuss how we can help bring your vision to life with our innovative technology solutions.',
    viewAllServices: 'View All Services',
    learnMore: 'Learn More',
    getInTouch: 'Get in Touch',
    viewAllProjects: 'View All Projects',
    contactCTA: 'Interested in Our Work?',
    contactCTADescription: 'Trusted IT Architect.',
    readyToTransform: 'Contact us for more details!',
    readyToTransformDescription: 'Contact us today to discuss how our services can help achieve your business goals.',
    whyChooseUs: 'Why Choose Our Services?',
    expertTeam: 'Cybersecurity Experts',
    qualityAssurance: 'Quality Assurance',
    timelyDelivery: 'Punctuality',
    support: '24/7 Support',
    projectFilter: 'Project Filter',
    allProjects: 'All Projects',
    activeProjects: 'Active',
    completedProjects: 'Completed',
    noProjects: 'No projects found for the selected filter.',
    noServices: 'No services available at the moment.',
    send: 'Send',
    sending: 'Sending...',
    
    // About Page
    ourStory: 'Our Story',
    ourCorePrinciples: 'Our Core Principles',
    meetOurTeam: 'Our Team',
    ceoFounder: 'CEO & Founder',
    cto: 'CTO',
    leadDeveloper: 'Lead Developer',
    designDirector: 'Design Director',
    
    // Services Page
    whyChooseTitle: 'Why Choose Our Services?',
    whyChooseDescription: 'Exceptional quality that highlights security, innovation, and customer satisfaction.',
    
    // Projects Page
    explorePortfolio: 'Explore our portfolio of successful projects and innovative solutions',
    
    // Contact Page
    getInTouchDescription: 'We are a young, dynamic, innovative, and trustworthy team.',
    sendUsMessage: 'Send us a Message',
    haveProject: 'Have a project in mind or any questions? Feel free to reach out to us using the contact information below.',
    findUs: 'Find us on Google Maps',
    businessHours: 'Business Hours',
    viewOnGoogleMaps: 'View on Google Maps',
    mapLoading: 'Map loading...',
    
    // Admin Login
    adminLogin: 'Admin Login',
    signInToAccount: 'Sign in to your admin account',
    backToHomepage: '← Back to homepage',
    
    // Dashboard
    addEditRemove: 'Add, edit, or remove services',
    updatePortfolio: 'Update project portfolio',
    checkMessages: 'Check new contact messages',
    addUserAdmin: 'Add or edit admin users',
    recentActivity: 'Recent Activity',
    newUserRegistered: 'New user registered: John Doe',
    newProjectAdded: 'New project added: E-commerce Platform',
    newMessageReceived: 'New message received from Jane Smith',
    
    // Footer
    footerDescription: 'We are a leading technology company providing innovative solutions to businesses worldwide. Our mission is to deliver cutting-edge technology solutions that drive business growth.',
    allRightsReserved: 'All rights reserved.',
    
    // Common service names
    webDevelopment: 'Web Development',
    webDevelopmentDescription: 'Professional web development services to build responsive and scalable websites and applications.',
    mobileApplications: 'Mobile Applications',
    mobileApplicationsDescription: 'Cross-platform mobile application development for iOS and Android devices.',
    digitalMarketing: 'Digital Marketing',
    digitalMarketingDescription: 'Comprehensive digital marketing strategies to grow your online presence and reach customers.',
    cloudSolutions: 'Cloud Solutions',
    cloudSolutionsDescription: 'Scalable cloud infrastructure and migration services for modern businesses.',
    eCommece: 'E-commerce',
    uiUxDesign: 'UI/UX Design',
    other: 'Other',
    howContactYou: 'How should we contact you?',
    
    // Service features
    expertTeamDescription: 'Highly skilled professionals with years of experience in their respective fields.',
    qualityAssuranceDescription: 'Rigorous testing and quality control processes to ensure excellence.',
    timelyDeliveryDescription: 'We respect deadlines and deliver projects on time without compromising quality.',
    supportDescription: 'Round-the-clock customer support to assist you with any queries or issues.',
    
    // Common project details
    technologiesUsed: 'Technologies:',
    
    // Stats
    projectsDelivered: 'Projects Delivered',
    happyClients: 'Happy Clients',
    yearsExperience: 'Years Experience',
    projectsCompleted: 'Projects Completed',
    
    // Industries Focus Section
    industriesWeTransform: 'Our domain of expertise',
    drivingInnovation: 'Driving digital innovation across all sectors that shape our world.',
    transformingIndustries: 'Transforming industries through cutting-edge technology solutions',
    hospitalitySector: 'Hospitality',
    governmentSector: 'Government',
    manufacturingSector: 'Manufacturing',
    retailSupermarketSector: 'Retail & Supermarket',
    educationSector: 'Education',
    healthcareSector: 'Healthcare',
    logisticsSector: 'Logistics',
    
    // Core Values
    ourCoreValues: 'Our Core Values',
    coreValuesDescription: 'They guide everything we do and shape the culture of our company.',
    innovation: 'Innovation',
    innovationDescription: 'We constantly seek innovative solutions to meet our clients\' needs and stay at the forefront of technology.',
    integrity: 'Reliability',
    integrityDescription: 'We are committed to providing reliable and secure solutions to ensure client satisfaction and protect their data.',
    excellence: 'Collaboration',
    excellenceDescription: 'We work as a team to share our knowledge and skills, and with our clients to understand their needs and offer personalized solutions.',
    
    // Quote Page
    getQuoteTitle: 'Get a Free Quote',
    getQuoteSubtitle: 'Fill out the form below and our team will get back to you within 24 hours with a custom quote.',
    service: 'Service',
    selectService: 'Select a service',
    sendRequest: 'Send Request',
    
    // Form placeholders
    enterName: 'Enter your full name',
    enterEmail: 'Enter your email address',
    enterSubject: 'What is this regarding?',
    enterMessage: 'Enter your message here...',
    enterPhone: 'Enter your phone number',
    enterCompanyName: 'Enter your company name',
    serviceType: 'Service Type',
    preferredContactMethod: 'Preferred Contact Method',
    companyName: 'Company Name',
    
    // Status messages
    messageSent: 'Message sent successfully!',
    loginSuccessful: 'Login successful!',
    settingsUpdated: 'Settings updated successfully!',
    serviceCreated: 'Service created successfully!',
    serviceUpdated: 'Service updated successfully!',
    serviceDeleted: 'Service deleted successfully!',
    projectCreated: 'Project created successfully!',
    projectUpdated: 'Project updated successfully!',
    projectDeleted: 'Project deleted successfully!',
    userCreated: 'User created successfully!',
    userUpdated: 'User updated successfully!',
    userDeleted: 'User deleted successfully!',
    aboutUpdated: 'About page updated successfully!',
    
    // Errors
    failedToSendMessage: 'Failed to send message',
    failedToLogin: 'Login failed',
    failedToUpdateSettings: 'Failed to update settings',
    failedToSaveService: 'Failed to save service',
    failedToDeleteService: 'Failed to delete service',
    failedToSaveProject: 'Failed to save project',
    failedToDeleteProject: 'Failed to delete project',
    failedToSaveUser: 'Failed to save user',
    failedToDeleteUser: 'Failed to delete user',
    failedToUpdateAbout: 'Failed to update about page',
    emailRequired: 'Email is required',
    passwordRequired: 'Password is required',
    nameRequired: 'Name is required',
    titleRequired: 'Title is required',
    descriptionRequired: 'Description is required',
    nameEmailMessageRequired: 'Name, email, and message are required',
    
    // Partners Section
    ourPartners: 'Our Partners',
    trustedByLeadingCompanies: 'Trusted by leading companies in their respective fields',
    becomeAPartner: 'Want to become a partner?',
    
    // Partner names and categories
    partnerTechCorp: 'TechCorp',
    partnerInnovateX: 'InnovateX',
    partnerDigitalPro: 'DigitalPro',
    partnerCloudFirst: 'CloudFirst',
    partnerDataFlow: 'DataFlow',
    partnerSecureNet: 'SecureNet',
    technology: 'Technology',
    digitalSolutions: 'Digital Solutions',
    cloudServices: 'Cloud Services',
    dataAnalytics: 'Data Analytics',
    cybersecurity: 'Cybersecurity',
    
    // Hero section translations
    professionalDigitalSolutions: 'Welcome to the Heart of Digitalization',
    creatingDigitalSolutions: 'CREATING DIGITAL SOLUTIONS FOR THE MODERN WORLD',
    transformYourWay: 'Improve your way of doing',
    business: 'Business',
    withOurDigitalSolutions: 'with our digital solutions',
    creatingDigitalSolutionsDesc: 'We develop custom applications and offer services tailored to the challenges of today and tomorrow.',
    discoverOurSolutions: 'Discover our Solutions',
    modernInnovative: 'Modern & Innovative',
    development: 'Development',
    solutions: 'Solutions',
    customized: 'Customized',
  },
  fr: {
    // Navigation
    home: 'Accueil',
    about: 'À propos',
    services: 'Services',
    projects: 'Projets',
    contact: 'Contact',
    getQuote: 'Obtenir un devis',
    admin: 'Admin',
    Businesss: 'des affaires',
    
    // Auth
    login: 'Connexion',
    logout: 'Déconnexion',
    email: 'Email',
    password: 'Mot de passe',
    rememberMe: 'Se souvenir de moi',
    forgotPassword: 'Mot de passe oublié ?',
    signIn: 'Se connecter',
    signUp: 'S\'inscrire',
    
    // Dashboard
    dashboard: 'Tableau de bord',
    welcome: 'Bienvenue',
    totalUsers: 'Utilisateurs totaux',
    totalServices: 'Services totaux',
    totalProjects: 'Projets totaux',
    totalMessages: 'Messages totaux',
    newMessages: 'Nouveaux messages',
    
    // Settings
    settings: 'Paramètres',
    siteSettings: 'Paramètres du site',
    siteTitle: 'Titre du site',
    address: 'Adresse',
    phone: 'Téléphone',
    emailLabel: 'Email',
    googleMapsUrl: 'URL Google Maps',
    footerText: 'Texte du pied de page',
    facebookUrl: 'URL Facebook',
    linkedinUrl: 'URL LinkedIn',
    whatsappUrl: 'URL WhatsApp',
    save: 'Enregistrer',
    saveChanges: 'Enregistrer les modifications',
    
    // Services
    servicesManagement: 'Gestion des services',
    addNewService: 'Ajouter un service',
    editService: 'Modifier le service',
    serviceName: 'Nom du service',
    serviceDescription: 'Description',
    status: 'Statut',
    active: 'Actif',
    inactive: 'Inactif',
    delete: 'Supprimer',
    update: 'Mettre à jour',
    add: 'Ajouter',
    
    // Projects
    projectsManagement: 'Gestion des projets',
    addNewProject: 'Ajouter un projet',
    editProject: 'Modifier le projet',
    projectName: 'Nom du projet',
    projectDescription: 'Description',
    client: 'Client',
    technologies: 'Technologies',
    projectDate: 'Date du projet',
    completed: 'Terminé',
    
    // Messages
    messages: 'Messages',
    allMessages: 'Tous les messages',
    messageDetails: 'Détails du message',
    name: 'Nom',
    subject: 'Sujet',
    message: 'Message',
    date: 'Date',
    markAsRead: 'Marquer comme lu',
    
    // Users
    usersManagement: 'Gestion des utilisateurs',
    addUser: 'Ajouter un utilisateur',
    editUser: 'Modifier l\'utilisateur',
    fullName: 'Nom complet',
    role: 'Rôle',
    editor: 'Éditeur',
    
    // About
    aboutManagement: 'Gestion de la page À propos',
    mainContent: 'Contenu principal',
    mission: 'Mission',
    vision: 'Vision',
    values: 'Valeurs',
    
    // Contact
    sendMessage: 'Envoyer le message',
    yourName: 'Votre nom',
    yourEmail: 'Votre email',
    yourSubject: 'Sujet',
    yourMessage: 'Votre message',
    contactUs: 'Contactez-nous',
    
    // Footer
    quickLinks: 'Liens rapides',
    ourServices: 'Nos Services',
    
    // Common
    cancel: 'Annuler',
    actions: 'Actions',
    noData: 'Aucune donnée disponible',
    loading: 'Chargement...',
    error: 'Erreur',
    success: 'Succès',
    confirmDelete: 'Êtes-vous sûr de vouloir supprimer cet élément ?',
    quickActions: 'Actions rapides',
    getInstantQuote: 'Obtenir un devis',
    callNow: 'Appeler maintenant',
    sendEmail: 'Envoyer un email',
    
    // Pages
    homeHeroTitle: 'Création de solutions numériques pour le monde moderne',
    homeHeroSubtitle: 'Nous créons des solutions technologiques innovantes qui aident les entreprises à croître et à prospérer dans l\'ère numérique.',
    servicesDescription: 'qui aideront vos entreprises a reussir dans le monde numeriques.',
    aboutUs: 'À propos de nous',
    aboutDescription: 'Nous sommes une entreprise technologique de premier plan qui fournit des solutions innovantes aux entreprises du monde entier.',
    contactTitle: 'Prêt à démarrer votre projet ?',
    contactSubtitle: 'Contactez-nous dès aujourd\'hui pour discuter de la manière dont nous pouvons vous aider à concrétiser votre vision avec nos solutions technologiques innovantes.',
    viewAllServices: 'Voir tous les services',
    learnMore: 'En savoir plus',
    getInTouch: 'Nous contacter',
    viewAllProjects: 'Voir tous les projets',
    contactCTA: 'Intéressé par notre travail ?',
    contactCTADescription: 'Architecte en informatique digne de confiance',
    readyToTransform: 'contactez nous pour avoir plus de détails!',
    readyToTransformDescription: 'Contactez-nous aujourd\'hui pour discuter de la manière dont nos services peuvent vous aider à atteindre vos objectifs.',
    whyChooseUs: 'Pourquoi choisir nos services ?',
    expertTeam: ' Experts en sécurité informatique',
    qualityAssurance: 'Assurance qualité',
    timelyDelivery: 'ponctualité ',
    support: 'Support 24/7',
    projectFilter: 'Filtre des projets',
    allProjects: 'Tous les projets',
    activeProjects: 'Actifs',
    completedProjects: 'Terminés',
    noProjects: 'Aucun projet trouvé pour le filtre sélectionné.',
    noServices: 'Aucun service disponible pour le moment.',
    send: 'Envoyer',
    sending: 'Envoi...',
    
    // About Page
    ourStory: 'Notre Histoire',
    ourCorePrinciples: 'Nos Principes Fondamentaux',
    meetOurTeam: 'Notre Équipe',
    ceoFounder: 'PDG & Fondateur',
    cto: 'Directeur Technique',
    leadDeveloper: 'Développeur Principal',
    designDirector: 'Directeur Artistique',
    
    // Services Page
    whyChooseTitle: 'Pourquoi Choisir Nos Services ?',
    whyChooseDescription: 'Une qualité exceptionnelle qui met en valeur la sécurité, l\'innovation et la satisfaction client.',
    
    // Projects Page
    explorePortfolio: 'Explorez notre portfolio de projets et solutions innovantes',
    
    // Contact Page
    getInTouchDescription: 'Nous sommes une équipe jeune,dynamique ,pleine d innovations et digne de confiance.',
    sendUsMessage: 'Envoyez-nous un message',
    haveProject: 'Vous avez un projet en tête ou des questions ? N\'hésitez pas à nous contacter en utilisant les informations de contact ci-dessous.',
    findUs: 'Trouvez-nous sur Google Maps',
    businessHours: 'Horaires d\'ouverture',
    viewOnGoogleMaps: 'Voir sur Google Maps',
    mapLoading: 'Chargement de la carte...',
    
    // Admin Login
    adminLogin: 'Connexion Admin',
    signInToAccount: 'Connectez-vous à votre compte admin',
    backToHomepage: '← Retour à l\'accueil',
    
    // Dashboard
    addEditRemove: 'Ajouter, modifier ou supprimer des services',
    updatePortfolio: 'Mettre à jour le portfolio de projets',
    checkMessages: 'Vérifier les nouveaux messages de contact',
    addUserAdmin: 'Ajouter ou modifier des utilisateurs admin',
    recentActivity: 'Activité récente',
    newUserRegistered: 'Nouvel utilisateur enregistré: Jean Dupont',
    newProjectAdded: 'Nouveau projet ajouté: Plateforme e-commerce',
    newMessageReceived: 'Nouveau message reçu de Marie Martin',
    
    // Footer
    footerDescription: 'Nous sommes une entreprise technologique de premier plan qui fournit des solutions innovantes aux entreprises du monde entier. Notre mission est de fournir des solutions technologiques de pointe qui stimulent la croissance des entreprises.',
    allRightsReserved: 'Tous droits réservés.',
    
    // Common service names
    webDevelopment: 'Développement Web',
    webDevelopmentDescription: 'Services de développement web professionnels pour créer des sites Web et applications réactifs et évolutifs.',
    mobileApplications: 'Applications Mobiles',
    mobileApplicationsDescription: 'Développement d\'applications mobiles multiplateformes pour les appareils iOS et Android.',
    digitalMarketing: 'Marketing Digital',
    digitalMarketingDescription: 'Stratégies de marketing numérique complètes pour développer votre présence en ligne et atteindre les clients.',
    cloudSolutions: 'Solutions Cloud',
    cloudSolutionsDescription: 'Infrastructure cloud évolutive et services de migration pour les entreprises modernes.',
    eCommece: 'E-commerce',
    uiUxDesign: 'Design UI/UX',
    other: 'Autre',
    howContactYou: 'Comment devrions-nous vous contacter ?',
    
    // Service features
    expertTeamDescription: 'Professionnels hautement qualifiés avec des années d\'expérience dans leurs domaines respectifs.',
    qualityAssuranceDescription: 'Processus rigoureux de test et de contrôle de qualité pour garantir l\'excellence.',
    timelyDeliveryDescription: 'Nous respectons les délais et livrons les projets à temps sans compromettre la qualité.',
    supportDescription: 'Support clientèle disponible 24h/24 et 7j/7 pour vous aider avec toutes vos questions ou problèmes.',
    
    // Common project details
    technologiesUsed: 'Technologies :',
    
    // Stats
    projectsDelivered: 'Projets Livrés',
    happyClients: 'Clients Satisfaits',
    yearsExperience: 'Années d\'Expérience',
    projectsCompleted: 'Projets Terminés',
    
    // Industries Focus Section
    industriesWeTransform: 'Notre terrain d\'expertise',
    drivingInnovation: 'Stimuler l\’innovation digitale dans tous les secteurs qui façonnent notre monde',
    transformingIndustries: 'Transformer les industries grâce à des solutions technologiques de pointe',
    hospitalitySector: 'Hôtellerie',
    governmentSector: 'Gouvernement',
    manufacturingSector: 'Industrie',
    retailSupermarketSector: 'Distribution & Supermarchés',
    educationSector: 'Éducation',
    healthcareSector: 'Santé',
    logisticsSector: 'Logistique',
    
    // Core Values
    ourCoreValues: 'Nos Valeurs Fondamentales',
    coreValuesDescription: 'Elles guident  tout ce que nous faisons et façonnent la culture de notre entreprise.',
    innovation: 'Innovation',
    innovationDescription: 'Nous cherchons constamment des solutions innovantes pour répondre aux besoins de nos clients et rester a la pointe de la technologie.',
    integrity: 'Fiabilité ',
    integrityDescription: 'Nous nous engageons a fournir des solutions fiables et sécurisées pour garantir la satisfaction de nos clients et la protection de leurs données.',
    excellence: 'Collaboration ',
    excellenceDescription: 'Nous travaillons en équipe pour partager nos connaissances et nos compétences ; et avec nos clients pour comprendre leurs besoins et leur offrir des solutions personnalisées.',
    
    // Quote Page
    getQuoteTitle: 'Obtenir un Devis Gratuit',
    getQuoteSubtitle: 'Remplissez le formulaire ci-dessous et notre équipe vous répondra dans les 24 heures avec un devis personnalisé.',
    service: 'Service',
    selectService: 'Sélectionnez un service',
    sendRequest: 'Envoyer la Demande',
    
    // Form placeholders
    enterName: 'Entrez votre nom complet',
    enterEmail: 'Entrez votre adresse email',
    enterSubject: 'À quel sujet ?',
    enterMessage: 'Entrez votre message ici...',
    enterPhone: 'Entrez votre numéro de téléphone',
    enterCompanyName: 'Entrez le nom de votre entreprise',
    serviceType: 'Type de service',
    preferredContactMethod: 'Méthode de contact préférée',
    companyName: 'Nom de l\'entreprise',
    
    // Status messages
    messageSent: 'Message envoyé avec succès !',
    loginSuccessful: 'Connexion réussie !',
    settingsUpdated: 'Paramètres mis à jour avec succès !',
    serviceCreated: 'Service créé avec succès !',
    serviceUpdated: 'Service mis à jour avec succès !',
    serviceDeleted: 'Service supprimé avec succès !',
    projectCreated: 'Projet créé avec succès !',
    projectUpdated: 'Projet mis à jour avec succès !',
    projectDeleted: 'Projet supprimé avec succès !',
    userCreated: 'Utilisateur créé avec succès !',
    userUpdated: 'Utilisateur mis à jour avec succès !',
    userDeleted: 'Utilisateur supprimé avec succès !',
    aboutUpdated: 'Page À propos mise à jour avec succès !',
    
    // Errors
    failedToSendMessage: 'Échec de l\'envoi du message',
    failedToLogin: 'Échec de la connexion',
    failedToUpdateSettings: 'Échec de la mise à jour des paramètres',
    failedToSaveService: 'Échec de l\'enregistrement du service',
    failedToDeleteService: 'Échec de la suppression du service',
    failedToSaveProject: 'Échec de l\'enregistrement du projet',
    failedToDeleteProject: 'Échec de la suppression du projet',
    failedToSaveUser: 'Échec de l\'enregistrement de l\'utilisateur',
    failedToDeleteUser: 'Échec de la suppression de l\'utilisateur',
    failedToUpdateAbout: 'Échec de la mise à jour de la page À propos',
    emailRequired: 'L\'email est requis',
    passwordRequired: 'Le mot de passe est requis',
    nameRequired: 'Le nom est requis',
    titleRequired: 'Le titre est requis',
    descriptionRequired: 'La description est requise',
    nameEmailMessageRequired: 'Le nom, l\'email et le message sont requis',
    
    // Partners Section
    ourPartners: 'Nos Partenaires',
    trustedByLeadingCompanies: 'Fait confiance par les entreprises leaders dans leurs domaines',
    becomeAPartner: 'Souhaitez-vous devenir partenaire ?',
    
    // Partner names and categories
    partnerTechCorp: 'TechCorp',
    partnerInnovateX: 'InnovateX',
    partnerDigitalPro: 'DigitalPro',
    partnerCloudFirst: 'CloudFirst',
    partnerDataFlow: 'DataFlow',
    partnerSecureNet: 'SecureNet',
    technology: 'Technologie',
    digitalSolutions: 'Solutions Numériques',
    cloudServices: 'Services Cloud',
    dataAnalytics: 'Analyse de Données',
    cybersecurity: 'Cybersécurité',
    
    // Hero section translations
    professionalDigitalSolutions: 'Bienvenu au Cœur de la Digitalisation',
    creatingDigitalSolutions: 'CRÉATION DE SOLUTIONS NUMÉRIQUES POUR LE MONDE MODERNE',
    transformYourWay: 'Ameliorez votre façon de faire',
    businesss: 'Business',

    withOurDigitalSolutions: 'avec nos solutions numériques',
    creatingDigitalSolutionsDesc: 'Nous développons des applications sur mesure et proposons des services adaptés aux défis d\’aujourd\’hui et de demain.',
    discoverOurSolutions: 'Découvrir nos Solutions',
    modernInnovative: 'Moderne & Innovante',
    development: 'Développement',
    solutions: 'Solutions',
    customized: 'Personnalisées',
  }
};

// Create context
const TranslationContext = createContext();

// Translation Provider Component
export const TranslationProvider = ({ children }) => {
  // Get initial language from localStorage, default to 'fr' if not set
  const getInitialLanguage = () => {
    return localStorage.getItem('selectedLanguage') || 'fr';
  };
  
  const [language, setLanguage] = useState(getInitialLanguage);
  const [dbLanguage, setDbLanguage] = useState(getInitialLanguage); // Language for database content
  
  const [dataRefreshToken, setDataRefreshToken] = useState(0);
  
  const refreshData = () => {
    setDataRefreshToken(prev => prev + 1);
  };
  
  const changeLanguage = (lang) => {
    setLanguage(lang);
    setDbLanguage(lang); // Also update database language when UI language changes
    languageManager.setLanguage(lang); // Sync with global language manager
    localStorage.setItem('selectedLanguage', lang); // Persist language preference
    refreshData(); // Refresh data when language changes
    
    // Trigger page reload to ensure all components get fresh data in new language
    window.location.reload();
  };
  
  const t = (key) => {
    return translations[language] && translations[language][key] 
      ? translations[language][key] 
      : key; // Return the key itself if translation not found
  };
  
  const value = {
    t,
    language,
    dbLanguage,
    changeLanguage,
    dataRefreshToken,
    refreshData,
    availableLanguages: ['en', 'fr']
  };

  // Initialize the language manager with the current language
  React.useEffect(() => {
    languageManager.setLanguage(getInitialLanguage());
  }, []);

  return (
    <TranslationContext.Provider value={value}>
      {children}
    </TranslationContext.Provider>
  );
};

// Custom hook to use translation
export const useTranslation = () => {
  const context = useContext(TranslationContext);
  if (!context) {
    throw new Error('useTranslation must be used within a TranslationProvider');
  }
  return context;
};
