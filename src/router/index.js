import { createRouter, createWebHistory } from 'vue-router'

import Start from '../Start.vue'
import Login from '../auth/Login.vue'
import Register from '../auth/Register.vue'
import Home from '../views/Home.vue'
import Write from '../views/Write.vue'
import View from '../views/View.vue'
import Edit from '../views/Edit.vue'

const routes = [
  {
    path: '/start',
    name: 'Start',
    component: Start
  },
  {
    path: '/login',
    name: 'Login',
    component: Login
  },
  {
    path: '/register',
    name: 'Register',
    component: Register
  },
  {
    path: '/home',
    name: 'Home',
    component: Home
  },
  {
    path: '/write',
    name: 'Write',
    component: Write
  },
  {
    path: '/view/:id',
    name: 'View',
    component: View
  },
  {
    path: '/edit/:id',
    name: 'Edit',
    component: Edit
  },
  {
    path: '/',
    redirect: '/start' // 如果有人访问根路径，就重定向到 /start
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
