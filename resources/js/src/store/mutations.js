import axios from '../axios.js'
const mutations = {

  // Vertical NavMenu

  TOGGLE_IS_VERTICAL_NAV_MENU_ACTIVE(state, value) {
    state.isVerticalNavMenuActive = value
  },
  TOGGLE_REDUCE_BUTTON(state, val) {
    state.reduceButton = val
  },
  UPDATE_MAIN_LAYOUT_TYPE(state, val) {
    state.mainLayoutType = val
  },
  UPDATE_VERTICAL_NAV_MENU_ITEMS_MIN(state, val) {
    state.verticalNavMenuItemsMin = val
  },
  UPDATE_VERTICAL_NAV_MENU_WIDTH(state, width) {
    state.verticalNavMenuWidth = width
  },


  // VxAutoSuggest

  UPDATE_STARRED_PAGE(state, payload) {

    // find item index in search list state
    const index = state.navbarSearchAndPinList["pages"].data.findIndex((item) => item.url == payload.url)

    // update the main list
    state.navbarSearchAndPinList["pages"].data[index].is_bookmarked = payload.val

    // if val is true add it to starred else remove
    if (payload.val) {
      state.starredPages.push(state.navbarSearchAndPinList["pages"].data[index])
    }
    else {
      // find item index from starred pages
      const index = state.starredPages.findIndex((item) => item.url == payload.url)

      // remove item using index
      state.starredPages.splice(index, 1)
    }
  },

  // Navbar-Vertical

  ARRANGE_STARRED_PAGES_LIMITED(state, list) {
    const starredPagesMore = state.starredPages.slice(10)
    state.starredPages     = list.concat(starredPagesMore)
  },
  ARRANGE_STARRED_PAGES_MORE(state, list) {
    let downToUp                 = false
    let lastItemInStarredLimited = state.starredPages[10]
    const starredPagesLimited    = state.starredPages.slice(0, 10)
    state.starredPages           = starredPagesLimited.concat(list)

    state.starredPages.slice(0, 10).map((i) => {
      if (list.indexOf(i) > -1) downToUp = true
    })

    if (!downToUp) {
      state.starredPages.splice(10, 0, lastItemInStarredLimited)
    }
  },


  // ////////////////////////////////////////////
  // UI
  // ////////////////////////////////////////////

  TOGGLE_CONTENT_OVERLAY(state, val) { state.bodyOverlay       = val },
  UPDATE_PRIMARY_COLOR(state, val)   { state.themePrimaryColor = val },
  UPDATE_THEME(state, val)           { state.theme             = val },
  UPDATE_WINDOW_WIDTH(state, width)  { state.windowWidth       = width },
  UPDATE_WINDOW_SCROLL_Y(state, val) { state.scrollY = val },


  // /////////////////////////////////////////////
  // User/Account
  // /////////////////////////////////////////////

  // Updates user info in state and localstorage
  UPDATE_USER_INFO(state, payload) {

    // Get Data localStorage
    let userInfo = JSON.parse(localStorage.getItem("userInfo")) || state.AppActiveUser

    for (const property of Object.keys(payload)) {

      if (payload[property] != null) {
        // If some of user property is null - user default property defined in state.AppActiveUser
        state.AppActiveUser[property] = payload[property]

        // Update key in localStorage
        userInfo[property] = payload[property]
      }


    }
    // Store data in localStorage
    localStorage.setItem("userInfo", JSON.stringify(userInfo))
  },

  // Project mutations

  UPDATE_CAMPAIGNS(state, payload){
    state.campaigns = payload;
  },
  UPDATE_FILES(state, payload){
    state.files = payload;
  },
  UPDATE_PERCENTAGE(state, val){
    state.uploadPercent = val;
  },
  UPDATE_SMTPS(state, payload){
    state.smtps = payload;
  },
  SAVE_CAMPAIGN(state, campaign){
    state.campaigns.map(c => {
      if (c.id == campaign.id)
        c = campaign;
      return c;
    });
    let camp = state.campaigns.filter(c => c.id == campaign.id);
    if (!camp.length)
      state.campaigns.push(campaign);
  },
  ADD_FILE(state, file){
    state.files.push(file);
  },
  SET_CAMPAIGN(state, campaign){
    state.campaign = campaign;
  },
  SET_CAMPAIGN_ID(state, id){
    state.campaign_id = id;
  },
  DETACH_CAMPAIGN_FILE(state, data){
    state.campaign.files = state.campaign.files.filter(f => f.id != data.file);
  },
  ATTACH_CAMPAIGN_FILE(state, data){
    let file = state.files.filter(f => f.id == data.file)
    state.campaign.files.push(file[0]);
  },
  UPDATE_FILE(state, data){
    state.files.map(f => {
      if (f.id == data.file)
        f.name = data.name;
      return f;
    });
    if (state.campaign)
      state.campaign.files.map(f => {
        if (f.id == data.file)
          f.name = data.name;
        return f;
      });
  },
  REMOVE_SMTP(state, id){
    state.smtps = state.smtps.filter(s => s.id != id);
  },
  UPDATE_SMTP(state, data){
    state.smtps.map(s => {
      if (s.id == data.smtp)
        s.name = data.name;
      return s;
    })
  },
  UPDATE_SMTP_DATA(state, data){
    state.smtps.map(s => {
      if (s.id == data.id)
        s.smtps = data.smtps;
      return s;
    })
  },
  REMOVE_CAMPAIGN(state, id){
    state.campaigns = state.campaigns.filter(c => c.id != id);
  },
  ADD_SMTP(state, payload){
    state.smtps.push(payload);
  },
  REMOVE_FILE(state, id){
    state.files = state.files.filter(s => s.id != id);
  },
  MERGE_FILES(state, payload){
    payload.old.forEach(id => {
      state.files = state.files.filter(f => f.id != id);
    });
    state.files.push(payload.file);
  },
  UPDATE_SET_EMAIL(state, data){
    state.settings.email = data.email;
  },
  SET_SETTINGS(state, data){
    state.settings.email = data.email;
  },
  SET_STATS(state, data){
    state.stats = data.stats;
  },
  SET_CAMPAIGN_STATS(state, data){
    state.campaignStats = data;
  },
  SET_CAMPAIGN_SPEC_STATS(state, stats){
    state.stats.map(c => {
      if (c.campaign_id == stats.campaign_id)
        c = stats;
      return c;
    })
  },
  UPDATE_CAMPAIGN_STATUS(state, campaign){
    state.campaigns.map(c => {
      if (c.id == campaign.id)
        c.status = campaign.status;
      return c;
    })
  },
  RESET_DATA(state){
    state.campaign = null;
    state.campaignStats = null;
  },
	LOGIN(state, payload){
		state.isLogged = true;
    state.currentUser = Object.assign({}, payload.user, {token: payload.access_token});
		localStorage.setItem("user", JSON.stringify(state.currentUser));
		axios.defaults.headers.common["Authorization"] = `Bearer ${state.currentUser.token}`
	},
	LOGOUT(state) {
		localStorage.removeItem("user");
		state.isLogged = false;
		state.currentUser = null;
  },
}

export default mutations

