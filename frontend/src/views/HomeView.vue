<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const handleLogout = async () => {
  await authStore.logout()
  router.push({ name: 'login' })
}
</script>

<template>
  <main class="dashboard-container">
    <div class="dashboard-card">
      <h1>Dashboard (Protected)</h1>
      <p>Status: <strong>Telah Login</strong></p>

      <div v-if="authStore.user" class="user-info">
        <p><strong>Nama:</strong> {{ authStore.user.name }}</p>
        <p><strong>Email:</strong> {{ authStore.user.email }}</p>
      </div>

      <button @click="handleLogout" :disabled="authStore.loading" class="logout-btn">
        {{ authStore.loading ? 'Memproses...' : 'Logout' }}
      </button>
    </div>
  </main>
</template>

<style scoped>
.dashboard-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background-color: #f5f5f5;
}

.dashboard-card {
  background: white;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-width: 500px;
}

h1 {
  margin-bottom: 1rem;
}

.user-info {
  margin: 1.5rem 0;
  padding: 1rem;
  background-color: #e9ecef;
  border-radius: 4px;
}

.logout-btn {
  padding: 0.75rem 1.5rem;
  background-color: #dc3545;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 1rem;
  cursor: pointer;
}

.logout-btn:disabled {
  background-color: #6c757d;
  cursor: not-allowed;
}
</style>
