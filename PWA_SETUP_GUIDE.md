# Panduan Pembuatan PWA Wali Santri SIMPels

Dokumen ini berisi panduan langkah demi langkah untuk membuat Progressive Web App (PWA) untuk fitur Wali Santri pada SIMPels menggunakan VSCode.

## Daftar Isi

1. [Persiapan Awal](#persiapan-awal)
2. [Setup Proyek](#setup-proyek)
3. [Konfigurasi PWA](#konfigurasi-pwa)
4. [Struktur Proyek](#struktur-proyek)
5. [Implementasi Autentikasi](#implementasi-autentikasi)
6. [Implementasi Fitur Utama](#implementasi-fitur-utama)
7. [Testing dan Debugging](#testing-dan-debugging)
8. [Optimasi dan Performance](#optimasi-dan-performance)
9. [Deployment](#deployment)
10. [Maintenance dan Update](#maintenance-dan-update)

## Persiapan Awal

### Prasyarat

Pastikan Anda memiliki software berikut terinstal di komputer Anda:

1. Node.js (versi 16.x atau lebih baru)
2. npm (versi 8.x atau lebih baru)
3. VSCode dengan ekstensi berikut:
   - ESLint
   - Prettier
   - Vetur (untuk Vue.js) atau ES7 React/Redux (untuk React)
   - Debugger for Chrome/Edge

### Memahami API SIMPels

Sebelum memulai pengembangan PWA, pastikan Anda memahami struktur API SIMPels yang telah dibuat:

- Endpoint API untuk autentikasi wali santri
- Endpoint untuk mengambil data santri
- Endpoint untuk mengambil data tagihan dan pembayaran
- Endpoint untuk fitur perizinan

## Setup Proyek

### 1. Membuat Proyek Vue.js dengan PWA Support

Kami akan menggunakan Vue.js untuk membuat PWA Wali Santri. Pilihan ini menyesuaikan dengan stack teknologi Laravel yang sudah digunakan di SIMPels.

Buka terminal dan jalankan perintah berikut:

```bash
# Install Vue CLI secara global
npm install -g @vue/cli

# Buat proyek baru
vue create simpels-wali-santri-pwa

# Pilih opsi berikut saat diminta:
# - Manually select features
# - Pilih: Babel, PWA, Router, Vuex, CSS Pre-processors, Linter
# - Pilih: Vue 3
# - Pilih: history mode untuk router (Yes)
# - Pilih: SCSS/SASS dengan dart-sass
# - Pilih: ESLint + Prettier
# - Pilih: Lint on save
# - Pilih: In dedicated config files
# - Pilih: No untuk save as preset

# Masuk ke direktori proyek
cd simpels-wali-santri-pwa

# Install dependensi tambahan
npm install axios vuex-persistedstate tailwindcss@latest postcss@latest autoprefixer@latest moment

# Setup Tailwind CSS
npx tailwindcss init -p
```

### 2. Konfigurasi Tailwind CSS

Edit file `tailwind.config.js` untuk menyesuaikan dengan tema SIMPels:

```javascript
/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        primary: '#4F46E5', // Sesuaikan dengan warna utama SIMPels
        secondary: '#10B981',
        accent: '#F59E0B',
        danger: '#EF4444',
        warning: '#F59E0B',
        info: '#3B82F6',
        success: '#10B981',
      },
      fontFamily: {
        sans: ['Inter', 'sans-serif'],
      },
    },
  },
  plugins: [],
}
```

Buat file `src/assets/styles/tailwind.css`:

```css
@import 'tailwindcss/base';
@import 'tailwindcss/components';
@import 'tailwindcss/utilities';

/* Tambahkan custom styles SIMPels di sini */
```

### 3. Update `main.js` untuk mengimpor Tailwind CSS

Edit file `src/main.js`:

```javascript
import { createApp } from 'vue'
import App from './App.vue'
import './registerServiceWorker'
import router from './router'
import store from './store'
import './assets/styles/tailwind.css'

createApp(App).use(store).use(router).mount('#app')
```

## Konfigurasi PWA

### 1. Edit file `vue.config.js`

Buat atau edit file `vue.config.js` di root proyek:

```javascript
module.exports = {
  publicPath: process.env.NODE_ENV === 'production'
    ? '/wali-santri/'
    : '/',
  pwa: {
    name: 'SIMPels Wali Santri',
    themeColor: '#4F46E5',
    msTileColor: '#4F46E5',
    appleMobileWebAppCapable: 'yes',
    appleMobileWebAppStatusBarStyle: 'black-translucent',
    workboxPluginMode: 'GenerateSW',
    workboxOptions: {
      skipWaiting: true,
      clientsClaim: true,
      exclude: [/\.map$/, /_redirects/],
      runtimeCaching: [
        {
          urlPattern: new RegExp('^https://yourdomain\\.com/api/wali-santri/'),
          handler: 'NetworkFirst',
          options: {
            cacheName: 'api-cache',
            expiration: {
              maxEntries: 100,
              maxAgeSeconds: 60 * 60 * 24 // 1 day
            },
            cacheableResponse: {
              statuses: [0, 200]
            }
          }
        },
        {
          urlPattern: new RegExp('^https://yourdomain\\.com/storage/'),
          handler: 'CacheFirst',
          options: {
            cacheName: 'image-cache',
            expiration: {
              maxEntries: 100,
              maxAgeSeconds: 60 * 60 * 24 * 7 // 1 week
            }
          }
        }
      ]
    },
    iconPaths: {
      favicon32: 'img/icons/favicon-32x32.png',
      favicon16: 'img/icons/favicon-16x16.png',
      appleTouchIcon: 'img/icons/apple-touch-icon.png',
      maskIcon: 'img/icons/safari-pinned-tab.svg',
      msTileImage: 'img/icons/mstile-150x150.png'
    },
    manifestOptions: {
      name: 'SIMPels Wali Santri',
      short_name: 'SIMPels',
      theme_color: '#4F46E5',
      background_color: '#FFFFFF',
      icons: [
        {
          src: './img/icons/android-chrome-192x192.png',
          sizes: '192x192',
          type: 'image/png'
        },
        {
          src: './img/icons/android-chrome-512x512.png',
          sizes: '512x512',
          type: 'image/png'
        },
        {
          src: './img/icons/android-chrome-maskable-192x192.png',
          sizes: '192x192',
          type: 'image/png',
          purpose: 'maskable'
        },
        {
          src: './img/icons/android-chrome-maskable-512x512.png',
          sizes: '512x512',
          type: 'image/png',
          purpose: 'maskable'
        }
      ],
      start_url: '.',
      display: 'standalone',
      orientation: 'portrait'
    }
  }
}
```

### 2. Konfigurasi Service Worker

Edit file `src/registerServiceWorker.js` untuk menambahkan custom logic:

```javascript
/* eslint-disable no-console */

import { register } from 'register-service-worker'

if (process.env.NODE_ENV === 'production') {
  register(`${process.env.BASE_URL}service-worker.js`, {
    ready () {
      console.log(
        'App is being served from cache by a service worker.\n' +
        'For more details, visit https://goo.gl/AFskqB'
      )
    },
    registered (registration) {
      console.log('Service worker has been registered.')
      
      // Check for updates every hour
      setInterval(() => {
        registration.update()
      }, 1000 * 60 * 60)
    },
    cached () {
      console.log('Content has been cached for offline use.')
    },
    updatefound () {
      console.log('New content is downloading.')
    },
    updated (registration) {
      console.log('New content is available; please refresh.')
      
      // Dispatch an event to notify the app that an update is available
      document.dispatchEvent(
        new CustomEvent('swUpdated', { detail: registration })
      )
    },
    offline () {
      console.log('No internet connection found. App is running in offline mode.')
    },
    error (error) {
      console.error('Error during service worker registration:', error)
    }
  })
}
```

## Struktur Proyek

Organisasikan proyek dengan struktur berikut:

```
src/
├── api/                  # API service modules
│   ├── index.js          # API setup (axios)
│   ├── auth.js           # Authentication API
│   ├── santri.js         # Santri data API
│   ├── tagihan.js        # Tagihan API
│   └── perizinan.js      # Perizinan API
├── assets/               # Static assets
│   ├── icons/            # Icons
│   ├── images/           # Images
│   └── styles/           # CSS/SCSS files
├── components/           # Reusable components
│   ├── common/           # Common UI components
│   ├── layout/           # Layout components
│   └── widgets/          # Widget components
├── router/               # Vue Router
│   └── index.js          # Route definitions
├── store/                # Vuex store
│   ├── index.js          # Store setup
│   ├── modules/          # Store modules
│   │   ├── auth.js       # Authentication store
│   │   ├── santri.js     # Santri data store
│   │   ├── tagihan.js    # Tagihan store
│   │   └── perizinan.js  # Perizinan store
│   └── plugins/          # Store plugins
├── utils/                # Utility functions
├── views/                # Page components
│   ├── auth/             # Authentication pages
│   ├── santri/           # Santri pages
│   ├── tagihan/          # Tagihan pages
│   ├── transaksi/        # Transaksi pages
│   └── perizinan/        # Perizinan pages
├── App.vue               # Root component
├── main.js              # App entry point
└── registerServiceWorker.js # Service worker registration
```

## Implementasi Autentikasi

### 1. Buat API Service untuk Autentikasi

Buat file `src/api/index.js`:

```javascript
import axios from 'axios'
import store from '@/store'
import router from '@/router'

// Buat instance axios dengan konfigurasi default
const api = axios.create({
  baseURL: 'https://yourdomain.com/api/wali-santri',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})

// Tambahkan interceptor untuk menambahkan token ke setiap request
api.interceptors.request.use(
  config => {
    const token = store.state.auth.token
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  error => Promise.reject(error)
)

// Tambahkan interceptor untuk menangani error response
api.interceptors.response.use(
  response => response,
  error => {
    const { status } = error.response || {}
    
    // Jika token tidak valid atau expired
    if (status === 401) {
      store.dispatch('auth/logout')
      router.push('/login')
    }
    
    return Promise.reject(error)
  }
)

export default api
```

Buat file `src/api/auth.js`:

```javascript
import api from './index'

export default {
  // Login dengan email dan password
  login(credentials) {
    return api.post('/login', credentials)
  },
  
  // Register wali santri baru
  register(userData) {
    return api.post('/register', userData)
  },
  
  // Ambil data user yang sedang login
  getUser() {
    return api.get('/user')
  },
  
  // Logout
  logout() {
    return api.post('/logout')
  }
}
```

### 2. Buat Vuex Store untuk Autentikasi

Buat file `src/store/modules/auth.js`:

```javascript
import authApi from '@/api/auth'
import router from '@/router'

const state = {
  user: null,
  token: null,
  santriList: [],
  isLoading: false,
  error: null
}

const getters = {
  isAuthenticated: state => !!state.token,
  currentUser: state => state.user,
  santriList: state => state.santriList,
  hasError: state => !!state.error,
  error: state => state.error,
  isLoading: state => state.isLoading
}

const actions = {
  async login({ commit }, credentials) {
    try {
      commit('setLoading', true)
      commit('clearError')
      
      const response = await authApi.login(credentials)
      const { token, user, santri } = response.data
      
      commit('setToken', token)
      commit('setUser', user)
      commit('setSantriList', santri)
      
      router.push('/')
      return true
    } catch (error) {
      commit('setError', error.response?.data?.message || 'Login gagal')
      return false
    } finally {
      commit('setLoading', false)
    }
  },
  
  async register({ commit }, userData) {
    try {
      commit('setLoading', true)
      commit('clearError')
      
      const response = await authApi.register(userData)
      const { token, user } = response.data
      
      commit('setToken', token)
      commit('setUser', user)
      
      router.push('/')
      return true
    } catch (error) {
      commit('setError', error.response?.data?.message || 'Registrasi gagal')
      return false
    } finally {
      commit('setLoading', false)
    }
  },
  
  async fetchUser({ commit }) {
    try {
      commit('setLoading', true)
      
      const response = await authApi.getUser()
      const { user, santri } = response.data
      
      commit('setUser', user)
      commit('setSantriList', santri)
      
      return true
    } catch (error) {
      commit('setError', error.response?.data?.message || 'Gagal mengambil data user')
      return false
    } finally {
      commit('setLoading', false)
    }
  },
  
  async logout({ commit }) {
    try {
      await authApi.logout()
    } catch (error) {
      console.log('Logout error:', error)
    } finally {
      commit('clearAuth')
      router.push('/login')
    }
  }
}

const mutations = {
  setUser(state, user) {
    state.user = user
  },
  
  setToken(state, token) {
    state.token = token
  },
  
  setSantriList(state, santriList) {
    state.santriList = santriList
  },
  
  setLoading(state, isLoading) {
    state.isLoading = isLoading
  },
  
  setError(state, error) {
    state.error = error
  },
  
  clearError(state) {
    state.error = null
  },
  
  clearAuth(state) {
    state.user = null
    state.token = null
    state.santriList = []
    state.error = null
  }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}
```

### 3. Update Main Store

Edit file `src/store/index.js`:

```javascript
import { createStore } from 'vuex'
import createPersistedState from 'vuex-persistedstate'
import auth from './modules/auth'
// Import modul store lainnya di sini

export default createStore({
  modules: {
    auth,
    // Tambahkan modul store lainnya di sini
  },
  plugins: [
    createPersistedState({
      key: 'simpels-wali-santri',
      paths: ['auth.token', 'auth.user', 'auth.santriList'],
    })
  ]
})
```

### 4. Buat Route Guards

Edit file `src/router/index.js`:

```javascript
import { createRouter, createWebHistory } from 'vue-router'
import store from '@/store'

// Lazy-load views
const Home = () => import('@/views/Home.vue')
const Login = () => import('@/views/auth/Login.vue')
const Register = () => import('@/views/auth/Register.vue')
const SantriProfile = () => import('@/views/santri/Profile.vue')
const TagihanList = () => import('@/views/tagihan/List.vue')
const TagihanDetail = () => import('@/views/tagihan/Detail.vue')
const TransaksiList = () => import('@/views/transaksi/List.vue')
const TransaksiDetail = () => import('@/views/transaksi/Detail.vue')
const PerizinanList = () => import('@/views/perizinan/List.vue')
const PerizinanForm = () => import('@/views/perizinan/Form.vue')
const PerizinanDetail = () => import('@/views/perizinan/Detail.vue')
const NotFound = () => import('@/views/NotFound.vue')

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home,
    meta: { requiresAuth: true }
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { guest: true }
  },
  {
    path: '/register',
    name: 'Register',
    component: Register,
    meta: { guest: true }
  },
  {
    path: '/santri/:id',
    name: 'SantriProfile',
    component: SantriProfile,
    meta: { requiresAuth: true }
  },
  {
    path: '/tagihan',
    name: 'TagihanList',
    component: TagihanList,
    meta: { requiresAuth: true }
  },
  {
    path: '/tagihan/:id',
    name: 'TagihanDetail',
    component: TagihanDetail,
    meta: { requiresAuth: true }
  },
  {
    path: '/transaksi',
    name: 'TransaksiList',
    component: TransaksiList,
    meta: { requiresAuth: true }
  },
  {
    path: '/transaksi/:id',
    name: 'TransaksiDetail',
    component: TransaksiDetail,
    meta: { requiresAuth: true }
  },
  {
    path: '/perizinan',
    name: 'PerizinanList',
    component: PerizinanList,
    meta: { requiresAuth: true }
  },
  {
    path: '/perizinan/buat',
    name: 'PerizinanCreate',
    component: PerizinanForm,
    meta: { requiresAuth: true }
  },
  {
    path: '/perizinan/:id',
    name: 'PerizinanDetail',
    component: PerizinanDetail,
    meta: { requiresAuth: true }
  },
  {
    path: '/perizinan/:id/edit',
    name: 'PerizinanEdit',
    component: PerizinanForm,
    meta: { requiresAuth: true }
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: NotFound
  }
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes,
  scrollBehavior() {
    return { top: 0 }
  }
})

// Navigation guards
router.beforeEach((to, from, next) => {
  const isAuthenticated = store.getters['auth/isAuthenticated']
  
  if (to.matched.some(record => record.meta.requiresAuth)) {
    if (!isAuthenticated) {
      next({
        path: '/login',
        query: { redirect: to.fullPath }
      })
    } else {
      next()
    }
  } else if (to.matched.some(record => record.meta.guest) && isAuthenticated) {
    next({ path: '/' })
  } else {
    next()
  }
})

export default router
```

## Implementasi Fitur Utama

Berikut adalah contoh implementasi satu fitur utama (Perizinan) untuk menggambarkan pola yang digunakan. Fitur lain dapat diimplementasikan dengan pola yang sama.

### 1. Buat API Service untuk Perizinan

Buat file `src/api/perizinan.js`:

```javascript
import api from './index'

export default {
  // Get all perizinan
  getList(params = {}) {
    return api.get('/perizinan', { params })
  },
  
  // Get detail perizinan
  getDetail(id) {
    return api.get(`/perizinan/${id}`)
  },
  
  // Create new perizinan
  create(perizinanData) {
    // Gunakan FormData jika perlu upload file
    const formData = new FormData()
    
    Object.keys(perizinanData).forEach(key => {
      if (key === 'bukti' && perizinanData[key] instanceof File) {
        formData.append(key, perizinanData[key])
      } else {
        formData.append(key, perizinanData[key])
      }
    })
    
    return api.post('/perizinan', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
  },
  
  // Update perizinan
  update(id, perizinanData) {
    // Gunakan FormData jika perlu upload file
    const formData = new FormData()
    formData.append('_method', 'PUT') // Laravel putanya method spoofing
    
    Object.keys(perizinanData).forEach(key => {
      if (key === 'bukti' && perizinanData[key] instanceof File) {
        formData.append(key, perizinanData[key])
      } else {
        formData.append(key, perizinanData[key])
      }
    })
    
    return api.post(`/perizinan/${id}`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
  },
  
  // Delete perizinan
  delete(id) {
    return api.delete(`/perizinan/${id}`)
  }
}
```

### 2. Buat Vuex Store untuk Perizinan

Buat file `src/store/modules/perizinan.js`:

```javascript
import perizinanApi from '@/api/perizinan'

const state = {
  perizinanList: [],
  currentPerizinan: null,
  isLoading: false,
  error: null
}

const getters = {
  perizinanList: state => state.perizinanList,
  currentPerizinan: state => state.currentPerizinan,
  isLoading: state => state.isLoading,
  error: state => state.error
}

const actions = {
  async fetchPerizinanList({ commit }, params = {}) {
    try {
      commit('setLoading', true)
      commit('clearError')
      
      const response = await perizinanApi.getList(params)
      commit('setPerizinanList', response.data.data || [])
      
      return response.data
    } catch (error) {
      commit('setError', error.response?.data?.message || 'Gagal mengambil data perizinan')
      return null
    } finally {
      commit('setLoading', false)
    }
  },
  
  async fetchPerizinanDetail({ commit }, id) {
    try {
      commit('setLoading', true)
      commit('clearError')
      
      const response = await perizinanApi.getDetail(id)
      commit('setCurrentPerizinan', response.data.data)
      
      return response.data
    } catch (error) {
      commit('setError', error.response?.data?.message || 'Gagal mengambil detail perizinan')
      return null
    } finally {
      commit('setLoading', false)
    }
  },
  
  async createPerizinan({ commit }, perizinanData) {
    try {
      commit('setLoading', true)
      commit('clearError')
      
      const response = await perizinanApi.create(perizinanData)
      
      return response.data
    } catch (error) {
      commit('setError', error.response?.data?.message || 'Gagal membuat perizinan')
      return null
    } finally {
      commit('setLoading', false)
    }
  },
  
  async updatePerizinan({ commit }, { id, data }) {
    try {
      commit('setLoading', true)
      commit('clearError')
      
      const response = await perizinanApi.update(id, data)
      
      return response.data
    } catch (error) {
      commit('setError', error.response?.data?.message || 'Gagal memperbarui perizinan')
      return null
    } finally {
      commit('setLoading', false)
    }
  },
  
  async deletePerizinan({ commit }, id) {
    try {
      commit('setLoading', true)
      commit('clearError')
      
      const response = await perizinanApi.delete(id)
      
      return response.data
    } catch (error) {
      commit('setError', error.response?.data?.message || 'Gagal menghapus perizinan')
      return null
    } finally {
      commit('setLoading', false)
    }
  }
}

const mutations = {
  setPerizinanList(state, perizinanList) {
    state.perizinanList = perizinanList
  },
  
  setCurrentPerizinan(state, perizinan) {
    state.currentPerizinan = perizinan
  },
  
  setLoading(state, isLoading) {
    state.isLoading = isLoading
  },
  
  setError(state, error) {
    state.error = error
  },
  
  clearError(state) {
    state.error = null
  }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}
```

### 3. Buat Komponen UI untuk Perizinan

Sebagai contoh, ini adalah implementasi untuk halaman list perizinan.

Buat file `src/views/perizinan/List.vue`:

```vue
<template>
  <div class="px-4 py-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Daftar Perizinan</h1>
      <router-link
        to="/perizinan/buat"
        class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark"
      >
        Ajukan Izin Baru
      </router-link>
    </div>

    <!-- Filter dan pencarian -->
    <div class="mb-6 bg-white p-4 rounded-lg shadow">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="flex flex-col">
          <label for="santri-filter" class="text-sm text-gray-600 mb-1">Santri</label>
          <select
            v-model="filters.santriId"
            id="santri-filter"
            class="border border-gray-300 rounded-md px-3 py-2 focus:ring-primary focus:border-primary"
            @change="loadData"
          >
            <option value="">Semua Santri</option>
            <option v-for="santri in santriList" :key="santri.id" :value="santri.id">
              {{ santri.nama }}
            </option>
          </select>
        </div>
        <div class="flex flex-col">
          <label for="status-filter" class="text-sm text-gray-600 mb-1">Status</label>
          <select
            v-model="filters.status"
            id="status-filter"
            class="border border-gray-300 rounded-md px-3 py-2 focus:ring-primary focus:border-primary"
            @change="loadData"
          >
            <option value="">Semua Status</option>
            <option value="menunggu">Menunggu</option>
            <option value="disetujui">Disetujui</option>
            <option value="ditolak">Ditolak</option>
          </select>
        </div>
        <div class="flex flex-col">
          <label for="jenis-filter" class="text-sm text-gray-600 mb-1">Jenis Izin</label>
          <select
            v-model="filters.jenisIzin"
            id="jenis-filter"
            class="border border-gray-300 rounded-md px-3 py-2 focus:ring-primary focus:border-primary"
            @change="loadData"
          >
            <option value="">Semua Jenis</option>
            <option value="sakit">Sakit</option>
            <option value="pulang">Pulang</option>
            <option value="keluar">Keluar</option>
            <option value="kegiatan">Kegiatan</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Loading indicator -->
    <div v-if="isLoading" class="flex justify-center my-12">
      <div class="spinner"></div>
    </div>

    <!-- Empty state -->
    <div
      v-else-if="perizinanList.length === 0"
      class="bg-white rounded-lg shadow p-8 text-center"
    >
      <div class="text-gray-500 mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
      </div>
      <h3 class="text-lg font-medium text-gray-900 mb-1">Belum Ada Perizinan</h3>
      <p class="text-gray-500 mb-4">
        Anda belum memiliki perizinan apa pun saat ini
      </p>
      <router-link
        to="/perizinan/buat"
        class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
        </svg>
        Ajukan Izin Baru
      </router-link>
    </div>

    <!-- Perizinan list -->
    <div v-else class="space-y-4">
      <div
        v-for="perizinan in perizinanList"
        :key="perizinan.id"
        class="bg-white rounded-lg shadow overflow-hidden"
      >
        <div class="px-6 py-4 flex justify-between items-center">
          <div>
            <div class="flex items-center mb-1">
              <span
                :class="{
                  'bg-yellow-100 text-yellow-800': perizinan.jenis_izin === 'sakit',
                  'bg-blue-100 text-blue-800': perizinan.jenis_izin === 'pulang',
                  'bg-purple-100 text-purple-800': perizinan.jenis_izin === 'keluar',
                  'bg-green-100 text-green-800': perizinan.jenis_izin === 'kegiatan',
                }"
                class="px-2 py-1 rounded-full text-xs font-medium mr-2"
              >
                {{ perizinan.jenis_izin === 'sakit' ? 'Sakit' : 
                   perizinan.jenis_izin === 'pulang' ? 'Pulang' : 
                   perizinan.jenis_izin === 'keluar' ? 'Keluar' : 
                   'Kegiatan' }}
              </span>
              <span
                :class="{
                  'bg-gray-100 text-gray-800': perizinan.status === 'menunggu',
                  'bg-green-100 text-green-800': perizinan.status === 'disetujui',
                  'bg-red-100 text-red-800': perizinan.status === 'ditolak',
                }"
                class="px-2 py-1 rounded-full text-xs font-medium"
              >
                {{ perizinan.status === 'menunggu' ? 'Menunggu' : 
                   perizinan.status === 'disetujui' ? 'Disetujui' : 
                   'Ditolak' }}
              </span>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-1">
              {{ perizinan.santri.nama }} ({{ perizinan.santri.nis }})
            </h3>
            <p class="text-sm text-gray-600">
              {{ formatDate(perizinan.tanggal_mulai) }} - {{ formatDate(perizinan.tanggal_selesai) }}
            </p>
          </div>
          <div class="flex items-center">
            <router-link
              :to="`/perizinan/${perizinan.id}`"
              class="text-primary hover:text-primary-dark mr-4"
            >
              <span class="sr-only">Detail</span>
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
            </router-link>
            <router-link
              v-if="perizinan.status === 'menunggu'"
              :to="`/perizinan/${perizinan.id}/edit`"
              class="text-primary hover:text-primary-dark mr-4"
            >
              <span class="sr-only">Edit</span>
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
            </router-link>
            <button
              v-if="perizinan.status === 'menunggu'"
              @click="confirmDelete(perizinan.id)"
              class="text-red-500 hover:text-red-700"
            >
              <span class="sr-only">Delete</span>
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Confirmation modal -->
    <div
      v-if="showDeleteModal"
      class="fixed inset-0 z-10 flex items-center justify-center bg-black bg-opacity-50"
    >
      <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Konfirmasi Hapus</h3>
        <p class="text-gray-600 mb-6">
          Apakah Anda yakin ingin menghapus perizinan ini? Tindakan ini tidak dapat dikembalikan.
        </p>
        <div class="flex justify-end">
          <button
            @click="showDeleteModal = false"
            class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 mr-2"
          >
            Batal
          </button>
          <button
            @click="deletePerizinan"
            class="px-4 py-2 text-white bg-red-500 rounded-lg hover:bg-red-600"
          >
            Hapus
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { computed, onMounted, ref } from 'vue'
import { useStore } from 'vuex'
import { useRouter } from 'vue-router'
import moment from 'moment'

export default {
  name: 'PerizinanList',
  
  setup() {
    const store = useStore()
    const router = useRouter()
    
    const filters = ref({
      santriId: '',
      status: '',
      jenisIzin: ''
    })
    
    const showDeleteModal = ref(false)
    const perizinanIdToDelete = ref(null)
    
    const isLoading = computed(() => store.getters['perizinan/isLoading'])
    const perizinanList = computed(() => store.getters['perizinan/perizinanList'])
    const santriList = computed(() => store.getters['auth/santriList'])
    const error = computed(() => store.getters['perizinan/error'])
    
    const loadData = async () => {
      const params = {}
      
      if (filters.value.santriId) {
        params.santri_id = filters.value.santriId
      }
      
      if (filters.value.status) {
        params.status = filters.value.status
      }
      
      if (filters.value.jenisIzin) {
        params.jenis_izin = filters.value.jenisIzin
      }
      
      await store.dispatch('perizinan/fetchPerizinanList', params)
    }
    
    const formatDate = (dateString) => {
      return moment(dateString).format('DD/MM/YYYY')
    }
    
    const confirmDelete = (id) => {
      perizinanIdToDelete.value = id
      showDeleteModal.value = true
    }
    
    const deletePerizinan = async () => {
      const result = await store.dispatch('perizinan/deletePerizinan', perizinanIdToDelete.value)
      
      if (result) {
        // Reload data after successful deletion
        await loadData()
      }
      
      showDeleteModal.value = false
      perizinanIdToDelete.value = null
    }
    
    onMounted(() => {
      loadData()
    })
    
    return {
      isLoading,
      perizinanList,
      santriList,
      error,
      filters,
      showDeleteModal,
      loadData,
      formatDate,
      confirmDelete,
      deletePerizinan
    }
  }
}
</script>

<style scoped>
.spinner {
  border: 4px solid rgba(0, 0, 0, 0.1);
  border-radius: 50%;
  border-top: 4px solid #4F46E5;
  width: 40px;
  height: 40px;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}
</style>
```

## Testing dan Debugging

### 1. Setup Unit Testing

Buat file pengujian dasar untuk komponen, misalnya untuk Login.vue:

```javascript
// tests/unit/views/auth/Login.spec.js
import { shallowMount, createLocalVue } from '@vue/test-utils'
import Vuex from 'vuex'
import VueRouter from 'vue-router'
import Login from '@/views/auth/Login.vue'

const localVue = createLocalVue()
localVue.use(Vuex)
localVue.use(VueRouter)

describe('Login.vue', () => {
  let store
  let router
  
  beforeEach(() => {
    store = new Vuex.Store({
      modules: {
        auth: {
          namespaced: true,
          state: {
            error: null,
            isLoading: false
          },
          actions: {
            login: jest.fn()
          },
          getters: {
            error: state => state.error,
            isLoading: state => state.isLoading
          }
        }
      }
    })
    
    router = new VueRouter()
  })
  
  it('renders login form', () => {
    const wrapper = shallowMount(Login, {
      store,
      localVue,
      router
    })
    
    expect(wrapper.find('form').exists()).toBe(true)
    expect(wrapper.find('input[type="email"]').exists()).toBe(true)
    expect(wrapper.find('input[type="password"]').exists()).toBe(true)
    expect(wrapper.find('button[type="submit"]').exists()).toBe(true)
  })
  
  // Add more tests here...
})
```

### 2. Setup E2E Testing dengan Cypress

Buat file konfigurasi Cypress:

```javascript
// cypress.json
{
  "baseUrl": "http://localhost:8080",
  "viewportWidth": 1280,
  "viewportHeight": 720,
  "defaultCommandTimeout": 5000,
  "pageLoadTimeout": 10000
}
```

Buat test dasar untuk halaman login:

```javascript
// cypress/integration/auth/login.spec.js
describe('Login Page', () => {
  beforeEach(() => {
    cy.visit('/login')
  })
  
  it('displays the login form', () => {
    cy.get('form').should('be.visible')
    cy.get('input[type="email"]').should('be.visible')
    cy.get('input[type="password"]').should('be.visible')
    cy.get('button[type="submit"]').should('be.visible')
  })
  
  it('shows error with invalid credentials', () => {
    cy.intercept('POST', '**/api/wali-santri/login', {
      statusCode: 401,
      body: {
        success: false,
        message: 'Email atau password salah'
      }
    }).as('loginRequest')
    
    cy.get('input[type="email"]').type('invalid@example.com')
    cy.get('input[type="password"]').type('wrongpassword')
    cy.get('button[type="submit"]').click()
    
    cy.wait('@loginRequest')
    cy.contains('Email atau password salah').should('be.visible')
  })
  
  it('redirects to home page after successful login', () => {
    cy.intercept('POST', '**/api/wali-santri/login', {
      statusCode: 200,
      body: {
        success: true,
        token: 'fake-token',
        user: {
          id: 1,
          name: 'Test User',
          email: 'test@example.com'
        },
        santri: [
          {
            id: 1,
            nama: 'Test Santri',
            nis: '123456',
            kelas: 'VII A',
            asrama: 'Asrama Putra 1',
            foto: null,
            jenis_kelamin: 'L',
            status: 'aktif'
          }
        ]
      }
    }).as('loginRequest')
    
    cy.get('input[type="email"]').type('test@example.com')
    cy.get('input[type="password"]').type('password')
    cy.get('button[type="submit"]').click()
    
    cy.wait('@loginRequest')
    cy.url().should('include', '/')
    cy.contains('Test Santri').should('be.visible')
  })
})
```

## Optimasi dan Performance

### 1. Lazy Loading Komponen

Gunakan lazy loading untuk semua komponen besar seperti yang sudah diimplementasikan di file router/index.js:

```javascript
const Home = () => import('@/views/Home.vue')
```

### 2. Optimasi Gambar

Gunakan tools seperti [Squoosh](https://squoosh.app/) untuk mengoptimasi gambar sebelum dimasukkan ke dalam aplikasi.

### 3. Implementasi Caching

Gunakan strategi caching yang tepat di service worker seperti yang sudah dikonfigurasi di vue.config.js:

```javascript
runtimeCaching: [
  {
    urlPattern: new RegExp('^https://yourdomain\\.com/api/wali-santri/'),
    handler: 'NetworkFirst',
    options: {
      cacheName: 'api-cache',
      expiration: {
        maxEntries: 100,
        maxAgeSeconds: 60 * 60 * 24 // 1 day
      },
      cacheableResponse: {
        statuses: [0, 200]
      }
    }
  }
]
```

## Deployment

### 1. Build untuk Production

```bash
# Pastikan .env.production sudah dikonfigurasi dengan benar
npm run build
```

### 2. Deployment ke Hosting

Upload folder `dist` ke server hosting atau gunakan platform seperti Netlify, Vercel, atau Firebase Hosting.

### 3. Konfigurasi Server

Pastikan server dikonfigurasi untuk:

1. Mengaktifkan HTTPS
2. Mengaktifkan cache headers yang sesuai
3. Mengaktifkan gzip compression
4. Mengarahkan semua request ke index.html untuk SPA routing

Contoh konfigurasi untuk Nginx:

```nginx
server {
    listen 80;
    server_name wali.yourdomain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name wali.yourdomain.com;

    ssl_certificate /path/to/certificate.crt;
    ssl_certificate_key /path/to/private.key;

    root /path/to/dist;
    index index.html;

    location / {
        try_files $uri $uri/ /index.html;
    }

    location ~* \.(js|css|png|jpg|jpeg|gif|svg|ico|woff|woff2|ttf|eot)$ {
        expires 30d;
        add_header Cache-Control "public, max-age=2592000";
        access_log off;
    }

    gzip on;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript;
}
```

## Maintenance dan Update

### 1. Monitoring

Gunakan tools seperti Google Analytics, Sentry, atau LogRocket untuk monitoring performa dan error di aplikasi.

### 2. Update Dependencies

Secara berkala update dependencies untuk keamanan dan fitur baru:

```bash
npm update
npm audit fix
```

### 3. Backup Konfigurasi

Pastikan untuk backup file konfigurasi penting:

- `.env.production`
- `vue.config.js`
- Konfigurasi deployment

## Kesimpulan

Panduan ini memberikan langkah-langkah komprehensif untuk membuat PWA Wali Santri yang terintegrasi dengan API SIMPels yang sudah ada. Dengan mengikuti struktur dan pola yang disediakan, Anda dapat mengembangkan aplikasi yang responsif, performant, dan memberikan pengalaman pengguna yang baik untuk wali santri.

Untuk pengembangan lebih lanjut, pertimbangkan untuk:

1. Mengimplementasikan fitur notifikasi push
2. Menambahkan animasi dan transisi untuk UX yang lebih baik
3. Mengimplementasikan mode offline yang lebih robust
4. Menambahkan fitur berbagi konten via Web Share API
5. Mengintegrasikan sistem analitik untuk memahami perilaku pengguna

Semoga panduan ini membantu Anda dalam mengembangkan PWA Wali Santri SIMPels yang sukses!
