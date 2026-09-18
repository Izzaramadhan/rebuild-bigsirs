import { describe, it, expect, vi, beforeEach } from 'vitest'
import { setActivePinia, createPinia } from 'pinia'
import { useAuthStore } from './auth'
import authService from '../services/authService'

vi.mock('../services/authService')

describe('Auth Store', () => {
  let store

  beforeEach(() => {
    setActivePinia(createPinia())
    store = useAuthStore()
    vi.clearAllMocks()
  })

  it('initially has no user and is not authenticated', () => {
    expect(store.user).toBeNull()
    expect(store.isAuthenticated).toBe(false)
    expect(store.initialized).toBe(false)
    expect(store.captchaImage).toBeNull()
  })

  it('fetchCaptcha updates captchaImage', async () => {
    authService.getCaptcha.mockResolvedValueOnce({
      data: { success: true, data: { image: 'data:image/svg+xml,...' } }
    })

    await store.fetchCaptcha()

    expect(authService.getCaptcha).toHaveBeenCalledTimes(1)
    expect(store.captchaImage).toBe('data:image/svg+xml,...')
  })

  it('login calls CSRF before POST login and fetches active user', async () => {
    authService.csrf.mockResolvedValueOnce({})
    authService.login.mockResolvedValueOnce({})
    authService.getCurrentUser.mockResolvedValueOnce({ data: { id: 1, username: 'tester' } })

    store.captchaImage = 'old_image'
    await store.login({ username: 'tester', password: 'password', captcha: 'CODE' })

    expect(authService.csrf).toHaveBeenCalledTimes(1)
    expect(authService.login).toHaveBeenCalledWith({
      username: 'tester',
      password: 'password',
      captcha: 'CODE'
    })
    expect(authService.getCurrentUser).toHaveBeenCalledTimes(1)
    expect(store.user).toEqual({ id: 1, username: 'tester' })
    expect(store.isAuthenticated).toBe(true)
    expect(store.captchaImage).toBeNull() // Cleared on success
  })

  it('login failure displays errors and refreshes captcha', async () => {
    authService.csrf.mockResolvedValueOnce({})
    authService.getCaptcha.mockResolvedValueOnce({
      data: { success: true, data: { image: 'data:image/svg+xml,...new' } }
    })
    const error = new Error('Login failed')
    error.response = {
      status: 422,
      data: {
        errors: { username: ['Username is required.'] },
      },
    }
    authService.login.mockRejectedValueOnce(error)

    await expect(store.login({})).rejects.toThrow('Login failed')
    expect(store.errors).toEqual({ username: ['Username is required.'] })
    expect(authService.getCaptcha).toHaveBeenCalledTimes(1)
    expect(store.captchaImage).toBe('data:image/svg+xml,...new')
  })

  it('logout clears user', async () => {
    store.user = { id: 1, username: 'tester' }
    authService.logout.mockResolvedValueOnce({})

    await store.logout()

    expect(authService.logout).toHaveBeenCalledTimes(1)
    expect(store.user).toBeNull()
    expect(store.isAuthenticated).toBe(false)
  })

  it('initialize handles 401 as guest', async () => {
    const error = { response: { status: 401 } }
    authService.getCurrentUser.mockRejectedValueOnce(error)

    await store.initialize()

    expect(store.user).toBeNull()
    expect(store.initialized).toBe(true)
  })
})
