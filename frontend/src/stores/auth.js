import { defineStore } from 'pinia'
import authService from '../services/authService'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    initialized: false,
    loading: false,
    errors: {},
    captchaImage: null,
    captchaLoading: false,
  }),
  getters: {
    isAuthenticated: (state) => !!state.user,
  },
  actions: {
    clearErrors() {
      this.errors = {}
    },

    async fetchCaptcha() {
      this.captchaLoading = true
      try {
        const response = await authService.getCaptcha()
        if (response.data && response.data.success) {
          this.captchaImage = response.data.data.image
        }
      } catch (error) {
        console.error('Failed to fetch captcha', error)
      } finally {
        this.captchaLoading = false
      }
    },

    async refreshCaptcha() {
      await this.fetchCaptcha()
    },

    async fetchUser() {
      try {
        const response = await authService.getCurrentUser()
        this.user = response.data
      } catch (error) {
        this.user = null
        if (error.response?.status !== 401) {
          throw error
        }
      }
    },

    async initialize() {
      if (this.initialized) return

      this.loading = true
      try {
        await this.fetchUser()
      } finally {
        this.initialized = true
        this.loading = false
      }
    },

    async login(credentials) {
      this.loading = true
      this.clearErrors()
      try {
        await authService.csrf()
        await authService.login(credentials)
        await this.fetchUser()
        this.captchaImage = null // Clear captcha on success
      } catch (error) {
        // Automatically refresh captcha on login failure
        await this.refreshCaptcha()

        if (error.response?.status === 422) {
          this.errors = error.response.data.errors || { general: [error.response.data.message] }
        } else if (error.response?.status === 419) {
          this.errors = { general: ['Sesi kedaluwarsa. Silakan coba lagi.'] }
        } else {
          this.errors = { general: ['Kredensial tidak valid atau terjadi kesalahan server.'] }
        }
        throw error
      } finally {
        this.loading = false
      }
    },

    async logout() {
      this.loading = true
      try {
        await authService.logout()
      } catch (error) {
        console.error('Logout error', error)
      } finally {
        this.user = null
        this.loading = false
      }
    },
  },
})
