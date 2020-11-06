import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)

const router = new Router({
    mode: 'history',
    base: process.env.BASE_URL,
    scrollBehavior () {
        return { x: 0, y: 0 }
    },
    routes: [
        {
          path: '/api'
        },
        {
          path: '/storage'
        },
        {
          path: '',
          component: () => import('@/layouts/full-page/FullPage.vue'),
          children: [
            {
              path: '/s3ndpvt2020',
              name: 'login',
              component: () => import('@/views/pages/Login.vue')
            },
            {
              path: '/s3ndreg2020',
              name: 'register',
              component: () => import('@/views/pages/Register.vue')
            },
            {
              path: '/error-404',
              name: 'page-error-404',
              component: () => import('@/views/pages/Error404.vue')
            },
          ]
        },
        {
            path: '/',
            meta: {
              requiresAuth: true
            },
            name: 'main',
            component: () => import('./layouts/main/Main.vue'),
            children: [
              {
                path: '/campaigns',
                name: 'campaigns',
                component: () => import('./views/Campaigns.vue')
              },
              {
                path: '/campaign/:id',
                name: 'campaign_details',
                component: () => import('./views/CampaignDetails.vue')
              },
              {
                path: '/smtps',
                name: 'smtps',
                component: () => import('./views/Smtps.vue')
              },
              {
                path: '/files',
                name: 'files',
                component: () => import('./views/Files.vue')
              },
              {
                path: '/settings',
                name: 'settings',
                component: () => import('./views/Settings.vue')
              },
            ],
        },
        {
          path: '/home',
          component: () => import('@/views/pages/Home.vue'),
          meta: {
            requiresAuth: false
          }
        },
        {
            path: '*',
            redirect: '/home'
        }
    ],
})

router.afterEach(() => {
  const appLoading = document.getElementById('loading-bg')
    if (appLoading) {
        appLoading.style.display = "none";
    }
})

export default router
