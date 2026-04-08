<template>
  <div class="login-page">
    <div class="login-card">
      <div class="login-header">
        <span class="login-icon">⚕</span>
        <h2>Pharmacovigilance</h2>
      </div>

      <form @submit.prevent="handleLogin" class="login-form">
        <div class="form-group">
          <input
            v-model="form.username"
            type="text"
            placeholder="Username"
            class="form-input"
            :class="{ 'input-error': errors.username }"
            autocomplete="username"
          />
          <span v-if="errors.username" class="error-text">{{ errors.username }}</span>
        </div>

        <div class="form-group">
          <input
            v-model="form.password"
            type="password"
            placeholder="Password"
            class="form-input"
            :class="{ 'input-error': errors.password }"
            autocomplete="current-password"
          />
          <span v-if="errors.password" class="error-text">{{ errors.password }}</span>
        </div>

        <span v-if="errors.general" class="error-text error-general">{{ errors.general }}</span>

        <button type="submit" class="btn-login" :disabled="loading">
          {{ loading ? 'Logging in...' : 'Login' }}
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const auth = useAuthStore()

const form = reactive({ username: '', password: '' })
const errors = reactive({ username: '', password: '', general: '' })
const loading = ref(false)

async function handleLogin() {
  errors.username = ''
  errors.password = ''
  errors.general = ''

  if (!form.username) { errors.username = 'Username is required'; return }
  if (!form.password) { errors.password = 'Password is required'; return }

  loading.value = true
  try {
    await auth.login(form.username, form.password)
    router.push('/orders')
  } catch (err) {
    errors.general = err.response?.data?.message || 'Invalid credentials'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.login-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f0f2f5;
}

.login-card {
  background: white;
  border-radius: 12px;
  padding: 40px 36px;
  width: 320px;
  box-shadow: 0 4px 24px rgba(0,0,0,0.10);
}

.login-header {
  text-align: center;
  margin-bottom: 28px;
  color: #1a2744;
}

.login-icon {
  font-size: 36px;
  display: block;
  margin-bottom: 8px;
}

.login-header h2 {
  font-size: 20px;
  font-weight: 700;
  letter-spacing: 0.5px;
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.form-input {
  padding: 11px 14px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 14px;
  outline: none;
  transition: border-color 0.2s;
  font-family: inherit;
}

.form-input:focus {
  border-color: #1a2744;
}

.input-error {
  border-color: #e53e3e;
}

.error-text {
  color: #e53e3e;
  font-size: 12px;
}

.error-general {
  text-align: center;
}

.btn-login {
  background: #1a2744;
  color: white;
  border: none;
  padding: 12px;
  border-radius: 8px;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  margin-top: 4px;
  transition: background 0.2s;
  font-family: inherit;
}

.btn-login:hover:not(:disabled) {
  background: #243660;
}

.btn-login:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}
</style>
