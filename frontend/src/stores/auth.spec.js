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
  })

  it('login calls CSRF before POST login and fetches active user', async () => {
    authService.csrf.mockResolvedValueOnce({})
    authService.login.mockResolvedValueOnce({})
    authService.getCurrentUser.mockResolvedValueOnce({ data: { id: 1, name: 'Test' } })

    await store.login({ email: 'test@example.com', password: 'password' })

    expect(authService.csrf).toHaveBeenCalledTimes(1)
    expect(authService.login).toHaveBeenCalledWith({
      email: 'test@example.com',
      password: 'password',
    })
    expect(authService.getCurrentUser).toHaveBeenCalledTimes(1)
    expect(store.user).toEqual({ id: 1, name: 'Test' })
    expect(store.isAuthenticated).toBe(true)
  })

  it('login failure displays errors', async () => {
    authService.csrf.mockResolvedValueOnce({})
    const error = new Error('Login failed')
    error.response = {
      status: 422,
      data: {
        errors: { email: ['Email is required.'] },
      },
    }
    authService.login.mockRejectedValueOnce(error)

    await expect(store.login({})).rejects.toThrow('Login failed')
    expect(store.errors).toEqual({ email: ['Email is required.'] })
  })

  it('logout clears user', async () => {
    store.user = { id: 1, name: 'Test' }
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
