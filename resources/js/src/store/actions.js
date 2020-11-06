import axios from '../axios.js'
const actions = {

    // /////////////////////////////////////////////
    // COMPONENTS
    // /////////////////////////////////////////////

    // Vertical NavMenu
    updateVerticalNavMenuWidth({ commit }, width) {
      commit('UPDATE_VERTICAL_NAV_MENU_WIDTH', width)
    },

    // VxAutoSuggest
    updateStarredPage({ commit }, payload) {
      commit('UPDATE_STARRED_PAGE', payload)
    },

    // The Navbar
    arrangeStarredPagesLimited({ commit }, list) {
      commit('ARRANGE_STARRED_PAGES_LIMITED', list)
    },
    arrangeStarredPagesMore({ commit }, list) {
      commit('ARRANGE_STARRED_PAGES_MORE', list)
    },

    // /////////////////////////////////////////////
    // UI
    // /////////////////////////////////////////////

    toggleContentOverlay({ commit }) {
      commit('TOGGLE_CONTENT_OVERLAY')
    },
    updateTheme({ commit }, val) {
      commit('UPDATE_THEME', val)
    },

    // /////////////////////////////////////////////
    // User/Account
    // /////////////////////////////////////////////

    updateUserInfo({ commit }, payload) {
      commit('UPDATE_USER_INFO', payload)
    },

    // Project actions
    saveCampaign({commit}, campaign){
      return new Promise((resolve, err) => {
        axios.post('/api/campaign/save', {campaign: campaign} )
        .then((res) => {
          commit('SAVE_CAMPAIGN', res.data.campaign);
          resolve(res);
        })
        .catch(error => {
          console.log(error.response);
          err(error)
        })
      });  
    },
    getCampaigns({commit}){
      return new Promise((resolve, err) => {
        axios.get(`/api/campaign/all`)
        .then((res) => {
          commit('UPDATE_CAMPAIGNS', res.data.campaigns);
          resolve();
        });
      });
    },
    getCampaign({commit}, id){
      return new Promise((resolve, error) => {
        axios.get(`/api/campaign/${id}`)
        .then((res) => {
          commit('SET_CAMPAIGN', res.data.campaign);
          resolve(res);
        });
      });
    },
    getSmtps({commit}){
      return new Promise((resolve, error) => {
        axios.get(`/api/smtp/group/all`)
        .then((res) => {
          commit('UPDATE_SMTPS', res.data.groups);
          resolve(res);
        });
      });
    },
    getFiles({commit}){
      return new Promise((resolve, error) => {
        axios.get(`/api/files`)
        .then((res) => {
          commit('UPDATE_FILES', res.data.files);
          resolve(res);
        });
      });
    },
    uploadFile({commit}, file){
      commit('UPDATE_PERCENTAGE', 0);
      var formData = new FormData();
      formData.append("file", file.file);
      if (file.name)
        formData.append("name", file.name);
      formData.append("type", file.type);
      axios.post('/api/files/add', formData, 
        {
          headers: {'Content-Type': 'multipart/form-data'},
          onUploadProgress: function( progressEvent ) {
            let uploadPercentage = parseInt( Math.round( ( progressEvent.loaded * 100 ) / progressEvent.total ) );
            commit('UPDATE_PERCENTAGE', uploadPercentage);
          }.bind(this)
        }
      ).then(function(res){
        commit('UPDATE_PERCENTAGE', null);
        commit('ADD_FILE', res.data.file);
      })
      .catch(function(){
        commit('UPDATE_PERCENTAGE', null);
      });
    },
    attachFile({commit}, data){
      return new Promise((resolve, error) => {
        axios.post(`/api/campaign/file/attach`, data)
        .then((res) => {
          commit('ATTACH_CAMPAIGN_FILE', data);
          resolve(res);
        });
      });
    },
    detachFile({commit}, data){
      return new Promise((resolve, error) => {
        axios.post(`/api/campaign/file/detach`, data)
        .then((res) => {
          commit('DETACH_CAMPAIGN_FILE', data);
          resolve(res);
        });
      });
    },
    updateFileName({commit}, data){
      return new Promise((resolve, error) => {
        axios.post(`/api/file/name`, data)
        .then((res) => {
          commit('UPDATE_FILE', data);
          resolve(res);
        });
      });
    },
    updateSmtp({commit}, data){
      return new Promise((resolve, error) => {
        axios.post(`/api/smtp/group/name`, data)
        .then((res) => {
          commit('UPDATE_SMTP', data);
          resolve(res);
        });
      });
    },
    deleteSmtp({commit}, id){
      return new Promise((resolve, error) => {
        axios.get(`/api/smtp/group/delete/${id}`)
        .then((res) => {
          commit('REMOVE_SMTP', id);
          resolve(res);
        });
      });
    },
    deleteCampaign({commit}, id){
      return new Promise((resolve, error) => {
        axios.get(`/api/campaign/delete/${id}`)
        .then((res) => {
          commit('REMOVE_CAMPAIGN', id);
          resolve(res);
        });
      });
    },
    addSmtp({commit}, data){
      commit('UPDATE_PERCENTAGE', 0);
      var formData = new FormData();
      formData.append("file", data.file);
      formData.append("name", data.name);
      return new Promise((resolve, error) => {
        axios.post('/api/smtp/add', formData, 
          {
            headers: {'Content-Type': 'multipart/form-data'},
            onUploadProgress: function( progressEvent ) {
              let uploadPercentage = parseInt( Math.round( ( progressEvent.loaded * 100 ) / progressEvent.total ) );
              commit('UPDATE_PERCENTAGE', uploadPercentage);
            }.bind(this)
          }
        ).then(function(res){
          commit('UPDATE_PERCENTAGE', null);
          commit('ADD_SMTP', res.data.group);
        })
        .catch(function(){
          commit('UPDATE_PERCENTAGE', null);
        });
      });
    },
    getSmtpsData({commit}, id){
      return new Promise((resolve, error) => {
        axios.get(`/api/smtp/${id}`)
        .then((res) => {
          commit('UPDATE_SMTP_DATA', res.data.group);
          resolve(res);
        });
      });
    },
    deleteFile({commit}, id){
      return new Promise((resolve, error) => {
        axios.get(`/api/file/delete/${id}`)
        .then((res) => {
          commit('REMOVE_FILE', id);
          resolve(res);
        });
      });
    },
    saveMerge({commit}, data){
      return new Promise((resolve, error) => {
        axios.post(`/api/files/merge`, data)
        .then((res) => {
          commit('MERGE_FILES', res.data);
          resolve(res);
        });
      });
    },
    saveSetEmail({commit}, email){
      return new Promise((resolve, error) => {
        let data = {email: email}
        axios.post(`/api/settings/email`, data)
        .then((res) => {
          commit('UPDATE_SET_EMAIL', res.data);
          resolve(res);
        });
      });
    },
    fetchSettings({commit}){
      return new Promise((resolve, error) => {
        axios.get(`/api/settings`)
        .then((res) => {
          commit('SET_SETTINGS', res.data);
          resolve(res);
        });
      });
    },
    getStats({commit}){
      axios.get(`/api/campaign/stats`)
      .then((res) => {
        commit('SET_STATS', res.data);
      });
    },
    getCampaignStats({commit}, id){
      axios.get(`/api/campaign/stats/${id}`)
      .then((res) => {
        commit('SET_CAMPAIGN_STATS', res.data.stats);
      });
    },
    launch({commit}, id){
      return new Promise((resolve, error) => {
        axios.get(`/api/campaign/launch/${id}`)
        .then((res) => {
          resolve(res);
        });
      });
    },
    launchDebug({commit}, id){
      return new Promise((resolve, error) => {
        axios.get(`/api/campaign/launch/debug/${id}`)
        .then((res) => {
          resolve(res);
        });
      });
    },
    register({commit}, data){
      return new Promise((res,rej)=> {
        axios.post('/api/create', data).then((response) => {
          res(response.data);
        });
      })
    },
    checkUser({commit}){
      return new Promise((res,rej)=> {
        axios.get('/api/check').then((response) => {
          res(response.data);
        });
      })
    },
    retryCampaign({commit}, data){
      return new Promise((res,rej)=> {
        axios.get(`/api/campaign/retry/${data.id}`);
      })
    },
    duplicateCampaign({commit}, id){
      return new Promise((resolve,rej)=> {
        axios.get(`/api/campaign/duplicate/${id}`)
        .then((res) => {
          commit('SAVE_CAMPAIGN', res.data.campaign);
          resolve(res);
        })
        .catch(error => {
          console.log(error.response);
          rej(error)
        })
      })
    },
}

export default actions
