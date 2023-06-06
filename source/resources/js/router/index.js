import Vue from 'vue';
import VueRouter from 'vue-router';
import Login from '../components/Login.vue';
import Profile from '../components/Profile.vue';
import PageNotFound from  '../components/PageNotFound.vue';
import Home from  '../components/Home.vue';
import Groups from  '../components/Groups.vue';
import Group from  '../components/Group.vue';
import Users from  '../components/Users.vue';
import User from  '../components/User.vue';
import Companies from  '../components/Companies.vue';
import Company from  '../components/Company.vue';
import Contacts from  '../components/Contacts.vue';
import Contact from  '../components/Contact.vue';
import Products from  '../components/Products.vue';
import Product from  '../components/Product.vue';
import Orders from  '../components/Orders.vue';
import Order from  '../components/Order.vue';
import Licenses from  '../components/Licenses.vue';
import store from '../store';

Vue.use(VueRouter);

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home,
    meta: { requiresLogin: 1 }
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { requiresLogin: -1 }
  },
  {
    path: '/profile',
    name: 'Profile',
    component: Profile,
    meta: { requiresLogin: 1 }
  },
  {
    path: '/groups',
    name: 'Groups',
    component: Groups,
    meta: { requiresLogin: 1, requiresAdmin: 1 }
  },
  {
    path: '/group/:id',
    name: 'Group',
    component: Group,
    meta: { requiresLogin: 1, requiresAdmin: 1 }
  },  
  {
    path: '/users',
    name: 'Users',
    component: Users,
    meta: { requiresLogin: 1, requiresAdmin: 1 }
  },
  {
    path: '/user/:id',
    name: 'User',
    component: User,
    meta: { requiresLogin: 1, requiresAdmin: 1 }
  },
  {
    path: '/products',
    name: 'Products',
    component: Products,
    meta: { requiresLogin: 1, requiresAdmin: 1 }
  },
  {
    path: '/product/:id',
    name: 'Product',
    component: Product,
    meta: { requiresLogin: 1, requiresAdmin: 1 }
  },  
  {
    path: '/companies',
    name: 'Companies',
    component: Companies,
    meta: { requiresLogin: 1 }
  },
  {
    path: '/company/:id',
    name: 'Company',
    component: Company,
    meta: { requiresLogin: 1 }
  },
  {
    path: '/contacts',
    name: 'Contacts',
    component: Contacts,
    meta: { requiresLogin: 1 }
  },
  {
    path: '/contact/:id',
    name: 'Contact',
    component: Contact,
    meta: { requiresLogin: 1 }
  },
  {
    path: '/orders',
    name: 'Orders',
    component: Orders,
    meta: { requiresLogin: 1 }
  },
  {
    path: '/order/:id',
    name: 'Order',
    component: Order,
    meta: { requiresLogin: 1 }
  },
  {
    path: '/licenses',
    name: 'Licenses',
    component: Licenses,
    meta: { requiresLogin: 1 }
  },
  { 
    path: '/:pathMatch(.*)*', 
    name: 'PageNotFound',
    component: PageNotFound,
    meta: { requiresLogin: 0 }
  }
];

const router = new VueRouter({
  mode: 'history',
  routes
});

router.beforeEach((to,from,next) => {
  const isAuth = store.getters.isAuthenticated;
  const isAdmin = store.getters.isAdmin;
  if (to.matched.some(route => route.meta.requiresLogin == -1)) {
    if(!isAuth) return next();

    return next('/');
  } else if (to.matched.some(route => route.meta.requiresLogin == 1)) {
    if(isAuth) {
      if (to.matched.some(route => route.meta.requiresAdmin == 1)) {
        if(!isAdmin) {
          return next('/');
        }
      }
      return next();
    } 

    return next('/login');
  }

  // route.meta.requiresLogin == 0
  next();
});

export default router;