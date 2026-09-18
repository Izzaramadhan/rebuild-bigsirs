<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const email = ref('')
const password = ref('')
const networkError = ref(null)

const handleSubmit = async () => {
  networkError.value = null
  try {
    await authStore.login({
      email: email.value,
      password: password.value
    })
    router.push({ name: 'home' })
  } catch (err) {
    if (!err.response) {
      networkError.value = 'Tidak dapat terhubung ke server.'
    }
  }
}
</script>

<template>
  <main class="login-container">
    <div class="login-card">
      <h1>Login</h1>
      <form @submit.prevent="handleSubmit">
        <div v-if="networkError" class="error-message general-error">
          {{ networkError }}
        </div>
        <div v-if="authStore.errors.general" class="error-message general-error">
          {{ authStore.errors.general[0] }}
        </div>

        <div class="form-group">
          <label for="email">Email</label>
          <input
            id="email"
            v-model="email"
            type="email"
            required
            autofocus
            :disabled="authStore.loading"
          />
          <span v-if="authStore.errors.email" class="error-message">
            {{ authStore.errors.email[0] }}
          </span>
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input
            id="password"
            v-model="password"
            type="password"
            required
            :disabled="authStore.loading"
          />
          <span v-if="authStore.errors.password" class="error-message">
            {{ authStore.errors.password[0] }}
          </span>
        </div>

        <button type="submit" :disabled="authStore.loading">
          {{ authStore.loading ? 'Memproses...' : 'Masuk' }}
        </button>
      </form>
    </div>
  </main>
</template>

<style scoped>
.login-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background-color: #f5f5f5;
}

.login-card {
  background: white;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0,0,0,0.1);
  width: 100%;
  max-width: 400px;
}

h1 {
  text-align: center;
  margin-bottom: 1.5rem;
}

.form-group {
  margin-bottom: 1rem;
}

label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: bold;
}

input {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #ccc;
  border-radius: 4px;
}

input:disabled {
  background-color: #e9ecef;
}

button {
  width: 100%;
  padding: 0.75rem;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 1rem;
  cursor: pointer;
  margin-top: 1rem;
}

button:disabled {
  background-color: #6c757d;
  cursor: not-allowed;
}

.error-message {
  color: #dc3545;
  font-size: 0.875rem;
  margin-top: 0.25rem;
  display: block;
}

.general-error {
  background-color: #f8d7da;
  border: 1px solid #f5c6cb;
  padding: 0.75rem;
  border-radius: 4px;
  margin-bottom: 1rem;
}
</style>
