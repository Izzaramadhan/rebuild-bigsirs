import { describe, it, expect, beforeEach, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import { createTestingPinia } from '@pinia/testing'
import { createRouter, createWebHistory } from 'vue-router'
import AppLayout from './AppLayout.vue'
import { useAuthStore } from '@/stores/auth'

import { flushPromises } from '@vue/test-utils'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      redirect: { name: 'dashboard' }
    },
    {
      path: '/dashboard',
      name: 'dashboard',
      component: { template: '<div class="dashboard-content">Dashboard Content</div>' },
      meta: { title: 'Dashboard' }
    },
    {
      path: '/login',
      name: 'login',
      component: { template: '<div>Login</div>' }
    }
  ]
})

describe('AppLayout.vue', () => {
  beforeEach(async () => {
    router.push('/dashboard')
    await router.isReady()
  })

  const mountLayout = (initialState = {}) => {
    return mount(AppLayout, {
      global: {
        plugins: [
          router,
          createTestingPinia({
            createSpy: vi.fn,
            initialState: {
              auth: {
                user: { username: 'johndoe', name: 'John Doe' },
                ...initialState
              }
            }
          })
        ],
        stubs: {
          RouterView: true // Keep it true or stub it, we can also use real router-view
        }
      }
    })
  }

  it('renders sidebar, header, and content area', () => {
    const wrapper = mountLayout()
    expect(wrapper.find('aside').exists()).toBe(true)
    expect(wrapper.find('header').exists()).toBe(true)
    expect(wrapper.find('main').exists()).toBe(true)
  })

  it('displays user name in the user menu', async () => {
    const wrapper = mountLayout()
    const userMenu = wrapper.findComponent({ name: 'UserMenu' })
    expect(userMenu.vm.displayName).toBe('John Doe')
  })

  it('uses username as fallback when name is empty', async () => {
    const wrapper = mountLayout({ user: { username: 'johndoe', name: '' } })
    const userMenu = wrapper.findComponent({ name: 'UserMenu' })
    expect(userMenu.vm.displayName).toBe('johndoe')
  })

  it('generates correct initial avatar', async () => {
    const wrapper = mountLayout()
    const header = wrapper.find('header')
    expect(header.text()).toContain('JO') // "John Doe" -> "JO"
  })

  it('displays page title from route.meta.title', async () => {
    const wrapper = mountLayout()
    const header = wrapper.find('header')
    expect(header.text()).toContain('Dashboard')
  })

  it('marks dashboard as active in sidebar', async () => {
    const wrapper = mountLayout()
    const activeLink = wrapper.find('aside [aria-current="page"]')
    expect(activeLink.exists()).toBe(true)
    expect(activeLink.text()).toContain('Dashboard')
  })

  it('sets aria-disabled="true" on disabled menus and prevents route change', async () => {
    const wrapper = mountLayout()
    const disabledLink = wrapper.find('aside [aria-disabled="true"]')
    expect(disabledLink.exists()).toBe(true)
    
    await disabledLink.trigger('click')
    expect(router.currentRoute.value.name).toBe('dashboard') // route unchanged
  })

  it('contains mobile sidebar toggle button', async () => {
    const wrapper = mountLayout()
    const header = wrapper.find('header')
    // Mobile toggle is usually visible on small screens (in JSDOM we just check if the element exists in DOM)
    // The MobileSidebar renders a SheetTrigger containing a Menu icon
    const toggleBtn = header.find('button:has(svg.lucide-menu)')
    expect(toggleBtn.exists()).toBe(true)
  })

  it('calls authStore.logout and redirects to login on logout', async () => {
    const wrapper = mountLayout()
    const authStore = useAuthStore()
    authStore.logout.mockResolvedValue(true)
    
    const userMenu = wrapper.findComponent({ name: 'UserMenu' })
    await userMenu.vm.handleLogout()
    await flushPromises()

    expect(authStore.logout).toHaveBeenCalledTimes(1)
    
    // Wait for route change
    await router.isReady()
    expect(router.currentRoute.value.name).toBe('login')
  })
})
