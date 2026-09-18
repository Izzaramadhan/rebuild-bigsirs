import api from './api'

export default {
  csrf() {
    return api.get('/sanctum/csrf-cookie')
  },

  login(credentials) {
    return api.post('/login', credentials)
  },

  logout() {
    return api.post('/logout')
  },

  getCurrentUser() {
    return api.get('/api/user')
  }
}
