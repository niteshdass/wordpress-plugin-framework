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

// Create and mount Vue app
const app = createApp(App);
app.use(ElementPlus);
app.use(router);
app.mount('#restaurant-menu-mange-and-order-app');
