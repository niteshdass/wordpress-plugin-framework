import { createApp } from 'vue';
import { createRouter, createWebHashHistory } from 'vue-router';
import App from './App.vue';
import Dashboard from './admin/components/Dashboard.vue';
import Settings from './admin/components/Settings.vue';

import Menu from './admin/modules/menu_page/all_menu.vue';
import Category from './admin/modules/categories/all_category.vue';

import ElementPlus from 'element-plus'
import 'element-plus/dist/index.css'

// Create router
const router = createRouter({
    history: createWebHashHistory(),
    routes: [
        { path: '/', component: Dashboard },
        { path: '/category', component: Category },
        { path: '/menus', component: Menu },
        { path: '/settings', component: Settings }
    ]
});

// Create Vuex store
// const store = createStore({
//     state() {
//         return {
//             couriers: [],
//             shipments: []
//         }
//     },
//     mutations: {
//         setCouriers(state, couriers) {
//             state.couriers = couriers;
//         },
//         addCourier(state, courier) {
//             state.couriers.push(courier);
//         },
//         updateCourier(state, courier) {
//             const index = state.couriers.findIndex(c => c.id === courier.id);
//             if (index !== -1) {
//                 state.couriers.splice(index, 1, courier);
//             }
//         },
//         deleteCourier(state, id) {
//             state.couriers = state.couriers.filter(c => c.id !== id);
//         }
//     },
//     actions: {
//         async fetchCouriers({ commit }) {
//             try {
//                 const response = await fetch(
//                     `${EhxDirectoristData.rest_api}/couriers`,
//                     {
//                         headers: {
//                             'X-WP-Nonce': EhxDirectoristData.nonce
//                         }
//                     }
//                 );
//                 const data = await response.json();
//                 if (data.success) {
//                     commit('setCouriers', data.data);
//                 }
//             } catch (error) {
//                 console.error('Error fetching couriers:', error);
//             }
//         },
//         async createCourier({ commit }, courier) {
//             try {
//                 const response = await fetch(
//                     `${EhxDirectoristData.apiUrl}/couriers`,
//                     {
//                         method: 'POST',
//                         headers: {
//                             'Content-Type': 'application/json',
//                             'X-WP-Nonce': EhxDirectoristData.nonce
//                         },
//                         body: JSON.stringify(courier)
//                     }
//                 );
//                 const data = await response.json();
//                 if (data.success) {
//                     commit('addCourier', data.data);
//                     return data.data;
//                 }
//             } catch (error) {
//                 console.error('Error creating courier:', error);
//                 throw error;
//             }
//         }
//     }
// });

// Create and mount Vue app
const app = createApp(App);
app.use(ElementPlus);
app.use(router);
app.mount('#restaurant-menu-mange-and-order-app');
