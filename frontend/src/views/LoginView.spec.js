import { mount } from '@vue/test-utils'
import { describe, it, expect, vi, beforeEach } from 'vitest'
import { createTestingPinia } from '@pinia/testing'
import { useAuthStore } from '../stores/auth'
import LoginView from './LoginView.vue'
import { createRouter, createWebHistory } from 'vue-router'

// Setup dummy router
const router = createRouter({
  history: createWebHistory(),
  routes: [{ path: '/', name: 'home', component: { template: '<div>Home</div>' } }]
})

describe('LoginView.vue', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  const mountComponent = (initialState = {}) => {
    return mount(LoginView, {
      global: {
        plugins: [
          createTestingPinia({
            initialState: {
              auth: {
                loading: false,
                errors: {},
                captchaImage: null,
                captchaLoading: false,
                ...initialState
              }
            },
            createSpy: vi.fn
          }),
          router
        ],
        stubs: {
          Eye: true,
          EyeOff: true,
          Loader2: true,
          AlertCircle: true,
          RefreshCw: true
        }
      }
    })
  }

  it('renders form with username, password, and captcha inputs', () => {
    const wrapper = mountComponent()
    
    expect(wrapper.find('input[type="text"][id="username"]').exists()).toBe(true)
    expect(wrapper.find('input[type="email"]').exists()).toBe(false)
    expect(wrapper.find('input[type="password"]').exists()).toBe(true)
    expect(wrapper.find('input[type="text"][id="captcha"]').exists()).toBe(true)
    expect(wrapper.find('button[type="submit"]').exists()).toBe(true)
  })

  it('fetches captcha on mount', () => {
    mountComponent()
    const authStore = useAuthStore()
    expect(authStore.fetchCaptcha).toHaveBeenCalledTimes(1)
  })

  it('refresh button calls refreshCaptcha action', async () => {
    const wrapper = mountComponent()
    const authStore = useAuthStore()
    
    const refreshBtn = wrapper.find('button[aria-label="Muat ulang kode keamanan"]')
    await refreshBtn.trigger('click')
    
    expect(authStore.refreshCaptcha).toHaveBeenCalledTimes(1)
  })

  it('submit calls login action with correct payload containing username and captcha', async () => {
    const wrapper = mountComponent()
    const authStore = useAuthStore()
    
    await wrapper.find('input[id="username"]').setValue('tester')
    await wrapper.find('input[id="password"]').setValue('password123')
    await wrapper.find('input[id="captcha"]').setValue('ABCDE')
    
    await wrapper.find('form').trigger('submit.prevent')
    
    expect(authStore.login).toHaveBeenCalledTimes(1)
    expect(authStore.login).toHaveBeenCalledWith({
      username: 'tester',
      password: 'password123',
      captcha: 'ABCDE'
    })
  })

  it('displays loading state and disables inputs/submit when loading', () => {
    const wrapper = mountComponent({ loading: true })
    
    expect(wrapper.find('input[id="username"]').element.disabled).toBe(true)
    expect(wrapper.find('input[id="password"]').element.disabled).toBe(true)
    expect(wrapper.find('input[id="captcha"]').element.disabled).toBe(true)
    expect(wrapper.find('button[type="submit"]').element.disabled).toBe(true)
    expect(wrapper.find('button[aria-label="Muat ulang kode keamanan"]').element.disabled).toBe(true)
  })

  it('displays username, password, and captcha validation errors', () => {
    const wrapper = mountComponent({
      errors: {
        username: ['Username tidak ditemukan.'],
        password: ['Password salah.'],
        captcha: ['Kode keamanan tidak sesuai.']
      }
    })
    
    expect(wrapper.text()).toContain('Username tidak ditemukan.')
    expect(wrapper.text()).toContain('Password salah.')
    expect(wrapper.text()).toContain('Kode keamanan tidak sesuai.')
  })

  it('HTTP 422 (or other error with response) does not show network error message', async () => {
    const wrapper = mountComponent()
    const authStore = useAuthStore()
    
    authStore.login.mockRejectedValueOnce({
      response: { status: 422 }
    })
    
    await wrapper.find('input[id="username"]').setValue('tester')
    await wrapper.find('input[id="password"]').setValue('password123')
    await wrapper.find('input[id="captcha"]').setValue('ABCDE')
    await wrapper.find('form').trigger('submit.prevent')
    
    await new Promise(resolve => setTimeout(resolve, 0))
    expect(wrapper.text()).not.toContain('Tidak dapat terhubung ke server.')
  })

  it('Error without response shows network error message', async () => {
    const wrapper = mountComponent()
    const authStore = useAuthStore()
    
    authStore.login.mockRejectedValueOnce(new Error('Network Error'))
    
    await wrapper.find('input[id="username"]').setValue('tester')
    await wrapper.find('input[id="password"]').setValue('password123')
    await wrapper.find('input[id="captcha"]').setValue('ABCDE')
    await wrapper.find('form').trigger('submit.prevent')
    
    await new Promise(resolve => setTimeout(resolve, 0))
    await wrapper.vm.$nextTick()
    
    expect(wrapper.text()).toContain('Tidak dapat terhubung ke server.')
  })

  it('renders Sisfomedika branding (logo and wordmark)', () => {
    const wrapper = mountComponent()
    
    // Test the wordmark
    expect(wrapper.text()).toContain('SISFOMEDIKA')
    
    // Ensure the text only appears once (no repetition from alt text, ignoring the footer "Sisfomedika")
    const textOccurrences = (wrapper.text().match(/SISFOMEDIKA/g) || []).length
    expect(textOccurrences).toBe(1)
    
    // Test the logo accessibility (should be decorative)
    const logo = wrapper.find('img[aria-hidden="true"]')
    expect(logo.exists()).toBe(true)
    expect(logo.attributes('alt')).toBe('')
  })

  it('renders Captcha Image when available', () => {
    const wrapper = mountComponent({
      captchaImage: 'data:image/svg+xml,...'
    })
    const captchaImg = wrapper.find('img[alt="Kode keamanan visual"]')
    expect(captchaImg.exists()).toBe(true)
    expect(captchaImg.attributes('src')).toBe('data:image/svg+xml,...')
  })

  it('does not render Vue scaffold elements', () => {
    const wrapper = mountComponent()
    expect(wrapper.text()).not.toContain('You did it!')
    expect(wrapper.find('img[alt="Vue logo"]').exists()).toBe(false)
  })
})
