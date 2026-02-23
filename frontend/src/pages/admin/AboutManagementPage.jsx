import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { aboutAPI, teamMembersAPI } from '../../services/api';
import toast from 'react-hot-toast';
import { useAuth } from '../../contexts/AuthContext.jsx';
import { useTranslation } from '../../contexts/i18n/TranslationContext';

const AboutManagementPage = () => {
  const { user } = useAuth();
  const { dbLanguage } = useTranslation();
  const [aboutData, setAboutData] = useState({
    main_content: '',
    mission: '',
    vision: '',
    values_content: '',
    main_content_fr: '',
    mission_fr: '',
    vision_fr: '',
    values_content_fr: '',
    team_members: []
  });
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState(null);

  // Team member form states
  const [showTeamModal, setShowTeamModal] = useState(false);
  const [editingTeamMember, setEditingTeamMember] = useState(null);
  const [teamFormData, setTeamFormData] = useState({
    name: '',
    position: '',
    bio: '',
    name_fr: '',
    position_fr: '',
    bio_fr: '',
    image: null
  });
  const [teamImagePreview, setTeamImagePreview] = useState(null);

  // Fetch about data
  useEffect(() => {
    fetchAboutData();
  }, []);

  const fetchAboutData = async () => {
    try {
      setLoading(true);
      
      // Fetch about content
      const aboutResponse = await aboutAPI.getAbout();
      
      // Fetch team members
      const teamResponse = await teamMembersAPI.getAll(dbLanguage);
      
      let aboutContent = {
        main_content: '',
        mission: '',
        vision: '',
        values_content: '',
        team_members: []
      };
      
      if (aboutResponse.data.success) {
        aboutContent = {
          ...aboutContent,
          ...aboutResponse.data.data
        };
      }
      
      if (teamResponse.data.success) {
        // Map team members to expected format
        aboutContent.team_members = teamResponse.data.data.map(member => ({
          id: member.id,
          name: member.name,
          position: member.position,
          bio: member.bio,
          name_fr: member.name_fr || '',
          position_fr: member.position_fr || '',
          bio_fr: member.bio_fr || '',
          image_path: member.image_path,
          email: member.email,
          phone: member.phone,
          linkedin_url: member.linkedin_url,
          twitter_url: member.twitter_url,
          status: member.status
        }));
      }
      
      setAboutData(aboutContent);
    } catch (err) {
      console.error('Error fetching about data:', err);
      // Initialize with empty data on error
      setAboutData({
        main_content: '',
        mission: '',
        vision: '',
        values_content: '',
        team_members: []
      });
    } finally {
      setLoading(false);
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      setSaving(true);
      
      await aboutAPI.updateAbout({
        main_content: aboutData.main_content,
        mission: aboutData.mission,
        vision: aboutData.vision,
        values_content: aboutData.values_content,
        main_content_fr: aboutData.main_content_fr,
        mission_fr: aboutData.mission_fr,
        vision_fr: aboutData.vision_fr,
        values_content_fr: aboutData.values_content_fr
      });
      
      toast.success('About page updated successfully!');
      fetchAboutData(); // Refresh the data after successful save
    } catch (err) {
      console.error('Error saving about data:', err);
      toast.error('Failed to save about page');
    } finally {
      setSaving(false);
    }
  };

  const handleTeamSubmit = async (e) => {
    e.preventDefault();
    try {
      setSaving(true);
      
      const teamData = {
        name: teamFormData.name,
        position: teamFormData.position,
        bio: teamFormData.bio,
        name_fr: teamFormData.name_fr,
        position_fr: teamFormData.position_fr,
        bio_fr: teamFormData.bio_fr,
        email: teamFormData.email || null,
        phone: teamFormData.phone || null,
        linkedin_url: teamFormData.linkedin_url || null,
        twitter_url: teamFormData.twitter_url || null,
        status: 'active'
      };
      
      if (editingTeamMember !== null) {
        // Update existing team member
        const memberId = aboutData.team_members[editingTeamMember].id;
        await teamMembersAPI.update(memberId, teamData, teamFormData.image);
        toast.success('Team member updated successfully!');
      } else {
        // Add new team member
        await teamMembersAPI.create(teamData, teamFormData.image);
        toast.success('Team member added successfully!');
      }
      
      resetTeamForm();
      fetchAboutData(); // Refresh the data
    } catch (err) {
      console.error('Error saving team member:', err);
      toast.error('Failed to save team member: ' + (err.response?.data?.error || err.message));
    } finally {
      setSaving(false);
    }
  };

  const handleTeamEdit = (index) => {
    const member = aboutData.team_members[index];
    setEditingTeamMember(index);
    setTeamFormData({
      name: member.name || '',
      position: member.position || '',
      bio: member.bio || '',
      name_fr: member.name_fr || '',
      position_fr: member.position_fr || '',
      bio_fr: member.bio_fr || '',
      image: null
    });
    setTeamImagePreview(member.image_preview || (member.image_path ? `/backend/api/uploads/${encodeURIComponent(member.image_path)}` : null));
    setShowTeamModal(true);
  };

  const handleTeamDelete = async (index) => {
    if (window.confirm('Are you sure you want to remove this team member?')) {
      try {
        const memberId = aboutData.team_members[index].id;
        await teamMembersAPI.delete(memberId);
        toast.success('Team member removed successfully!');
        fetchAboutData(); // Refresh the data
      } catch (err) {
        console.error('Error deleting team member:', err);
        toast.error('Failed to delete team member');
      }
    }
  };

  const handleTeamImageChange = (e) => {
    const file = e.target.files[0];
    if (file) {
      setTeamFormData({...teamFormData, image: file});
      setTeamImagePreview(URL.createObjectURL(file));
    }
  };

  const resetTeamForm = () => {
    setTeamFormData({
      name: '',
      position: '',
      bio: '',
      name_fr: '',
      position_fr: '',
      bio_fr: '',
      image: null
    });
    setTeamImagePreview(null);
    setEditingTeamMember(null);
    setShowTeamModal(false);
  };

  const handleInputChange = (field, value) => {
    setAboutData({
      ...aboutData,
      [field]: value
    });
  };

  if (loading) {
    return (
      <div className="flex justify-center items-center h-64">
        <div className="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary-600"></div>
      </div>
    );
  }

  return (
    <motion.div
      initial={{ opacity: 0, y: 20 }}
      animate={{ opacity: 1, y: 0 }}
      transition={{ duration: 0.5 }}
      className="space-y-6"
    >
      {/* Header */}
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h1 className="text-3xl font-bold text-gray-800">About Management</h1>
          <p className="text-gray-600 mt-1">
            Manage your company's about page content
          </p>
        </div>
      </div>

      <form onSubmit={handleSubmit} className="space-y-6">
        {/* Main Content Section */}
        <div className="bg-white rounded-lg shadow p-6">
          <h2 className="text-xl font-semibold text-gray-800 mb-4">Main Content</h2>
          <div className="space-y-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Company Story (EN)
              </label>
              <textarea
                value={aboutData.main_content}
                onChange={(e) => handleInputChange('main_content', e.target.value)}
                rows="6"
                className="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
                placeholder="Tell your company's story..."
              />
            </div>
            
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Company Story (FR)
              </label>
              <textarea
                value={aboutData.main_content_fr}
                onChange={(e) => handleInputChange('main_content_fr', e.target.value)}
                rows="6"
                className="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
                placeholder="Tell your company's story in French..."
              />
            </div>
          </div>
        </div>

        {/* Mission, Vision, Values */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div className="bg-white rounded-lg shadow p-6">
            <h3 className="text-lg font-semibold text-gray-800 mb-3">Mission (EN)</h3>
            <textarea
              value={aboutData.mission}
              onChange={(e) => handleInputChange('mission', e.target.value)}
              rows="4"
              className="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
              placeholder="Our mission is..."
            />
            <h3 className="text-lg font-semibold text-gray-800 mb-3 mt-4">Mission (FR)</h3>
            <textarea
              value={aboutData.mission_fr}
              onChange={(e) => handleInputChange('mission_fr', e.target.value)}
              rows="4"
              className="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
              placeholder="Our mission in French..."
            />
          </div>
          
          <div className="bg-white rounded-lg shadow p-6">
            <h3 className="text-lg font-semibold text-gray-800 mb-3">Vision (EN)</h3>
            <textarea
              value={aboutData.vision}
              onChange={(e) => handleInputChange('vision', e.target.value)}
              rows="4"
              className="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
              placeholder="Our vision is..."
            />
            <h3 className="text-lg font-semibold text-gray-800 mb-3 mt-4">Vision (FR)</h3>
            <textarea
              value={aboutData.vision_fr}
              onChange={(e) => handleInputChange('vision_fr', e.target.value)}
              rows="4"
              className="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
              placeholder="Our vision in French..."
            />
          </div>
          
          <div className="bg-white rounded-lg shadow p-6">
            <h3 className="text-lg font-semibold text-gray-800 mb-3">Core Values (EN)</h3>
            <textarea
              value={aboutData.values_content}
              onChange={(e) => handleInputChange('values_content', e.target.value)}
              rows="4"
              className="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
              placeholder="Our core values include..."
            />
            <h3 className="text-lg font-semibold text-gray-800 mb-3 mt-4">Core Values (FR)</h3>
            <textarea
              value={aboutData.values_content_fr}
              onChange={(e) => handleInputChange('values_content_fr', e.target.value)}
              rows="4"
              className="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
              placeholder="Our core values in French..."
            />
          </div>
        </div>

        {/* Team Members Section */}
        <div className="bg-white rounded-lg shadow p-6">
          <div className="flex justify-between items-center mb-4">
            <h2 className="text-xl font-semibold text-gray-800">Team Members</h2>
            <button
              type="button"
              onClick={() => {
                resetTeamForm();
                setShowTeamModal(true);
              }}
              className="px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 transition"
            >
              + Add Team Member
            </button>
          </div>
          
          {aboutData.team_members.length === 0 ? (
            <div className="text-center py-8 text-gray-500">
              <p>No team members added yet</p>
              <p className="text-sm mt-1">Click "Add Team Member" to get started</p>
            </div>
          ) : (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              {aboutData.team_members.map((member, index) => (
                <div key={index} className="border rounded-lg p-4">
                  <div className="flex items-start justify-between">
                    <div className="flex items-center space-x-3">
                      {member.image_preview || member.image_path ? (
                        <img
                          src={member.image_preview || `/backend/api/uploads/${encodeURIComponent(member.image_path)}`}
                          alt={member.name}
                          className="w-12 h-12 rounded-full object-cover"
                        />
                      ) : (
                        <div className="w-12 h-12 rounded-full bg-gray-300 flex items-center justify-center">
                          <span className="text-gray-700 font-medium">
                            {member.name?.charAt(0)?.toUpperCase() || '?'}
                          </span>
                        </div>
                      )}
                      <div>
                        <h4 className="font-medium text-gray-900">{member.name}</h4>
                        <p className="text-sm text-gray-500">{member.position}</p>
                      </div>
                    </div>
                    <div className="flex space-x-2">
                      <button
                        type="button"
                        onClick={() => handleTeamEdit(index)}
                        className="text-primary-600 hover:text-primary-900 text-sm"
                      >
                        Edit
                      </button>
                      <button
                        type="button"
                        onClick={() => handleTeamDelete(index)}
                        className="text-red-600 hover:text-red-900 text-sm"
                      >
                        Remove
                      </button>
                    </div>
                  </div>
                  {member.bio && (
                    <p className="mt-3 text-sm text-gray-600 line-clamp-2">
                      {member.bio}
                    </p>
                  )}
                </div>
              ))}
            </div>
          )}
        </div>

        {/* Save Button */}
        <div className="flex justify-end">
          <button
            type="submit"
            disabled={saving}
            className="px-6 py-3 bg-primary-600 text-white rounded-md hover:bg-primary-700 disabled:opacity-50 transition"
          >
            {saving ? 'Saving...' : 'Save Changes'}
          </button>
        </div>
      </form>

      {/* Team Member Modal */}
      {showTeamModal && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
          <div className="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div className="p-6">
              <div className="flex justify-between items-center mb-4">
                <h2 className="text-xl font-semibold text-gray-800">
                  {editingTeamMember !== null ? 'Edit Team Member' : 'Add Team Member'}
                </h2>
                <button
                  onClick={resetTeamForm}
                  className="text-gray-400 hover:text-gray-600"
                >
                  <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                </button>
              </div>
              
              <form onSubmit={handleTeamSubmit} className="space-y-4">
                <div className="flex items-center space-x-6">
                  {teamImagePreview ? (
                    <img
                      src={teamImagePreview}
                      alt="Team member preview"
                      className="w-20 h-20 rounded-full object-cover border-2 border-gray-300"
                    />
                  ) : (
                    <div className="w-20 h-20 rounded-full bg-gray-200 border-2 border-gray-300 flex items-center justify-center">
                      <span className="text-gray-500">No Image</span>
                    </div>
                  )}
                  
                  <div className="flex-1">
                    <label className="block text-sm font-medium text-gray-700 mb-1">
                      Upload Photo
                    </label>
                    <input
                      type="file"
                      accept="image/*"
                      onChange={handleTeamImageChange}
                      className="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
                    />
                  </div>
                </div>
                
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">
                      Full Name (EN) *
                    </label>
                    <input
                      type="text"
                      value={teamFormData.name}
                      onChange={(e) => setTeamFormData({...teamFormData, name: e.target.value})}
                      className="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
                      required
                    />
                  </div>
                  
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">
                      Full Name (FR)
                    </label>
                    <input
                      type="text"
                      value={teamFormData.name_fr}
                      onChange={(e) => setTeamFormData({...teamFormData, name_fr: e.target.value})}
                      className="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
                    />
                  </div>
                </div>
                
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">
                      Position (EN) *
                    </label>
                    <input
                      type="text"
                      value={teamFormData.position}
                      onChange={(e) => setTeamFormData({...teamFormData, position: e.target.value})}
                      className="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
                      required
                    />
                  </div>
                  
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">
                      Position (FR)
                    </label>
                    <input
                      type="text"
                      value={teamFormData.position_fr}
                      onChange={(e) => setTeamFormData({...teamFormData, position_fr: e.target.value})}
                      className="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
                    />
                  </div>
                </div>
                
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">
                    Bio (EN)
                  </label>
                  <textarea
                    value={teamFormData.bio}
                    onChange={(e) => setTeamFormData({...teamFormData, bio: e.target.value})}
                    rows="3"
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
                    placeholder="Brief biography..."
                  />
                </div>
                
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">
                    Bio (FR)
                  </label>
                  <textarea
                    value={teamFormData.bio_fr}
                    onChange={(e) => setTeamFormData({...teamFormData, bio_fr: e.target.value})}
                    rows="3"
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
                    placeholder="Brief biography in French..."
                  />
                </div>
                
                <div className="flex space-x-3 pt-4">
                  <button
                    type="submit"
                    disabled={saving}
                    className="px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 disabled:opacity-50 flex-1"
                  >
                    {saving ? 'Saving...' : (editingTeamMember !== null ? 'Update Member' : 'Add Member')}
                  </button>
                  <button
                    type="button"
                    onClick={resetTeamForm}
                    className="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400"
                  >
                    Cancel
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      )}
    </motion.div>
  );
};

export default AboutManagementPage;