
// added so later we can keep breakpoint in sync automatically using this config file
// import tailwindConfig from "../../tailwind.config.js"

const getters = {

	// COMPONENT
		// vx-autosuggest
	// starredPages: state => state.navbarSearchAndPinList.data.filter((page) => page.highlightAction),
  windowBreakPoint: state => {

    // This should be same as tailwind. So, it stays in sync with tailwind utility classes
    if (state.windowWidth >= 1200) return "xl"
    else if (state.windowWidth >= 992) return "lg"
    else if (state.windowWidth >= 768) return "md"
    else if (state.windowWidth >= 576) return "sm"
    else return "xs"
  },
  scrollbarTag: state => {
    return state.is_touch_device ? 'div' : 'VuePerfectScrollbar'
  },

  // Project getters
  campaign_id: state => state.campaign_id,
  campaign: state => state.campaign,
  uploadPercent: state => state.uploadPercent,
  campaigns: state => state.campaigns,
  smtps: state => state.smtps,
  files: state => state.files,
  isLogged : state => state.isLogged,
  email: state => state.settings.email,
  stats: state => state.stats,
  campaignStats: state => state.campaignStats,
  currentUser: state => state.currentUser,
}

export default getters
