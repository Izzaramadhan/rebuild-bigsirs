import { describe, it, expect, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import { createTestingPinia } from '@pinia/testing'
import DashboardView from './DashboardView.vue'

describe('DashboardView.vue', () => {
  const mountDashboard = (initialState = {}) => {
    return mount(DashboardView, {
      global: {
        plugins: [
          createTestingPinia({
            createSpy: vi.fn,
            initialState: {
              auth: {
                user: { name: 'Dr. John Doe', username: 'johndoe' },
                ...initialState
              }
            }
          })
        ],
        stubs: {
          // Stub components to simplify rendering
          Button: true,
          Input: true
        }
      }
    })
  }

  describe('Welcome Banner', () => {
    it('displays greeting and user name', () => {
      const wrapper = mountDashboard()
      expect(wrapper.text()).toContain('Selamat Datang,')
      expect(wrapper.text()).toContain('Dr. John Doe')
    })

    it('uses username if name is not available', () => {
      const wrapper = mountDashboard({ user: { name: '', username: 'janedoe' } })
      expect(wrapper.text()).toContain('janedoe')
    })

    it('renders the decorative dashboard background image with correct attributes', () => {
      const wrapper = mountDashboard()
      // Find the image inside the welcome banner
      const bannerImg = wrapper.find('img[src*="dashboard-background"]')
      
      expect(bannerImg.exists()).toBe(true)
      expect(bannerImg.attributes('alt')).toBe('')
      expect(bannerImg.attributes('aria-hidden')).toBe('true')
    })

    it('does not render the old Building2 placeholder icon in the banner', () => {
      const wrapper = mountDashboard()
      // Ensure Building2 (usually rendered as an SVG with lucide classes) is not in the banner
      // Since it's a specific decorative element, let's just make sure the image is there and SVG with same purpose isn't.
      const banner = wrapper.find('.bg-gradient-to-r')
      const svgs = banner.findAll('svg')
      
      // The Building2 icon might have a class name or just check that there are no SVGs at all in the banner if we removed it
      // Actually we removed Building2, so there should be no svgs in the welcome banner anymore
      expect(svgs.length).toBe(0)
    })
  })
})
