import { describe, it, expect, beforeEach } from 'vitest'
import { setActivePinia, createPinia } from 'pinia'
import router from './index'
import { useAuthStore } from '../stores/auth'

describe('Router Guards', () => {
  let authStore

  beforeEach(() => {
    setActivePinia(createPinia())
    authStore = useAuthStore()
    // By default, assume app has been initialized to avoid async fetchUser in simple route tests
    authStore.initialized = true
  })

  it('redirects guest to login when accessing protected route', async () => {
    authStore.user = null
    router.push('/')
    await router.isReady()

    expect(router.currentRoute.value.name).toBe('login')
  })

  it('redirects user to home when accessing login route', async () => {
    authStore.user = { id: 1, name: 'Test' }
    await router.push('/') // trigger navigation
    await router.push('/login')
    await router.isReady()

    expect(router.currentRoute.value.name).toBe('home')
  })

  it('allows guest to access login route', async () => {
    authStore.user = null
    await router.push('/') // trigger navigation
    await router.push('/login')
    await router.isReady()

    expect(router.currentRoute.value.name).toBe('login')
  })

  it('allows user to access home route', async () => {
    authStore.user = { id: 1, name: 'Test' }
    await router.push('/login') // trigger navigation away
    await router.push('/')
    await router.isReady()

    expect(router.currentRoute.value.name).toBe('home')
  })
})
