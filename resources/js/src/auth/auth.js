import axios from '../axios.js'
export function login(credentials){
    return new Promise((res,rej)=> {
        axios.post('/api/auth/login', credentials).then((response) => {
            res(response.data);
        })
        .catch((err) => {
            rej("Wrong email or password");
        })
    })
}

export function getLocalUser(){
    const userStr = localStorage.getItem("user");
    if(!userStr){
        return null;
    }
    return JSON.parse(userStr);
}

export function initialize(store, router){
    router.beforeEach((to,from,next) => {
        const requiresAuth = to.matched.some(record => record.meta.requiresAuth);
        const currentUser = store.getters.currentUser;
        if (requiresAuth && !currentUser && to.name == 'main')
            next('/home');
        else if(requiresAuth && !currentUser && to.path == '/s3ndpvt2020')
                next('/s3ndpvt2020');
        else if(to.path == '/s3ndpvt2020' && currentUser ){
            next('/campaigns');
        }else{
            axios.interceptors.response.use(null, (error) => {
                if(error.response.status == 401){
                    store.commit('LOGOUT');
                    router.push('/s3ndpvt2020');
                }
                return Promise.reject(error);
            });
            next();
        }
    });

    if (store.getters.currentUser) {
        setAuthorization(store.state.currentUser.token);
    }
}

export function setAuthorization(token) {
    axios.defaults.headers.common["Authorization"] = `Bearer ${token}`
}