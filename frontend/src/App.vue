<template>
  <div id="app-wrapper">
    <nav v-if="auth.isAuthenticated" class="navbar">
      <div class="navbar-brand">
        <span class="navbar-icon">⚕</span>
        Pharmacovigilance
      </div>
      <div class="navbar-menu">
        <router-link to="/orders" class="nav-link">Search Orders</router-link>
        <router-link to="/alerts" class="nav-link">Alert Log</router-link>
        <span class="nav-user">{{ auth.user?.username }}</span>
        <button @click="handleLogout" class="btn-logout">Logout</button>
      </div>
    </nav>

    <router-view />
  </div>
</template>

<script setup>
import { useAuthStore } from './stores/auth'
import { useRouter } from 'vue-router'

const auth = useAuthStore()
const router = useRouter()

async function handleLogout() {
  await auth.logout()
  router.push('/login')
}
</script>

<style>
/* Global styles */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Inter', sans-serif;
  background: #f0f2f5;
  color: #333;
}

.navbar {
  background: #1a2744;
  color: white;
  padding: 0 24px;
  height: 56px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

.navbar-brand {
  font-size: 18px;
  font-weight: 700;
  display: flex;
  align-items: center;
  gap: 8px;
  letter-spacing: 0.5px;
}

.navbar-icon {
  font-size: 22px;
}

.navbar-menu {
  display: flex;
  align-items: center;
  gap: 20px;
}

.nav-link {
  color: #a8b8d8;
  text-decoration: none;
  font-size: 14px;
  font-weight: 500;
  transition: color 0.2s;
}

.nav-link:hover,
.nav-link.router-link-active {
  color: white;
}

.nav-user {
  color: #a8b8d8;
  font-size: 14px;
  border-left: 1px solid #2e3f66;
  padding-left: 20px;
}

.btn-logout {
  background: transparent;
  border: 1px solid #2e3f66;
  color: #a8b8d8;
  padding: 6px 14px;
  border-radius: 6px;
  cursor: pointer;
  font-size: 13px;
  transition: all 0.2s;
}

.btn-logout:hover {
  background: #2e3f66;
  color: white;
}
</style>
