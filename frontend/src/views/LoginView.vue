<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Alert, AlertDescription } from '@/components/ui/alert'
import { Eye, EyeOff, Loader2, AlertCircle, RefreshCw } from '@lucide/vue'

import loginBackgroundUrl from '@/assets/background/background-login.webp'
import sisfoLogoUrl from '@/assets/logo/logo-sisfo.webp'

const router = useRouter()
const authStore = useAuthStore()

const username = ref('')
const password = ref('')
const captcha = ref('')
const showPassword = ref(false)
const networkError = ref(null)

const togglePassword = () => {
  showPassword.value = !showPassword.value
}

const handleSubmit = async () => {
  networkError.value = null
  if (authStore.clearErrors) {
    authStore.clearErrors()
  }

  try {
    await authStore.login({
      username: username.value,
      password: password.value,
      captcha: captcha.value
    })
    router.push({ name: 'home' })
  } catch (err) {
    if (!err.response) {
      networkError.value = 'Tidak dapat terhubung ke server.'
    }
    // Clear captcha input on failure since the challenge is refreshed by the store
    captcha.value = ''
  }
}

onMounted(() => {
  authStore.fetchCaptcha()
})
</script>

<template>
  <div class="relative min-h-dvh w-full flex flex-col items-center justify-center p-4 lg:p-8">
    <!-- Full viewport background: Removed blur and overlay for maximum sharpness -->
    <img
      :src="loginBackgroundUrl"
      alt=""
      aria-hidden="true"
      class="absolute inset-0 w-full h-full object-cover object-center z-0"
      style="image-rendering: auto;"
    />

    <!-- Main Content Container -->
    <div class="relative z-10 w-full max-w-lg flex flex-col items-center">

      <!-- Logo -->
      <div class="mb-8 flex items-center justify-center gap-3">
        <img
          :src="sisfoLogoUrl"
          alt=""
          aria-hidden="true"
          class="h-10 w-10 object-contain sm:h-12 sm:w-12"
        />
        <span class="text-2xl font-bold tracking-[0.08em] text-slate-800 sm:text-3xl uppercase">
          SISFOMEDIKA
        </span>
      </div>

      <!-- Login Card -->
      <Card class="w-full shadow-xl border-0 rounded-2xl bg-white/95 backdrop-blur-sm">
        <CardHeader class="space-y-1 text-center px-8 pt-8 pb-4">
          <CardTitle class="text-2xl font-bold tracking-tight text-slate-800">Masuk ke BigSIRS</CardTitle>
          <CardDescription class="text-slate-500">
            Masukkan username, kata sandi, dan kode keamanan untuk mengakses sistem
          </CardDescription>
        </CardHeader>

        <CardContent class="px-8 pb-8">
          <form @submit.prevent="handleSubmit" class="space-y-5">

            <!-- General/Network Error Alert -->
            <Alert v-if="networkError || authStore.errors.general" variant="destructive" role="alert" aria-live="assertive">
              <AlertCircle class="w-4 h-4" />
              <AlertDescription>
                {{ networkError || authStore.errors.general[0] }}
              </AlertDescription>
            </Alert>

            <!-- Username Field -->
            <div class="space-y-2">
              <Label for="username" :class="{'text-destructive': authStore.errors.username}">Username</Label>
              <Input
                id="username"
                v-model="username"
                type="text"
                placeholder="Masukkan username"
                required
                autofocus
                autocomplete="username"
                :disabled="authStore.loading"
                :aria-invalid="!!authStore.errors.username"
                class="h-11 bg-slate-50 border-slate-200"
              />
              <p v-if="authStore.errors.username" class="text-sm text-destructive font-medium" role="alert" aria-live="polite">
                {{ authStore.errors.username[0] }}
              </p>
            </div>

            <!-- Password Field -->
            <div class="space-y-2">
              <Label for="password" :class="{'text-destructive': authStore.errors.password}">Kata Sandi</Label>
              <div class="relative">
                <Input
                  id="password"
                  v-model="password"
                  :type="showPassword ? 'text' : 'password'"
                  placeholder="Masukkan kata sandi"
                  required
                  autocomplete="current-password"
                  :disabled="authStore.loading"
                  :aria-invalid="!!authStore.errors.password"
                  class="pr-10 h-11 bg-slate-50 border-slate-200"
                />
                <button
                  type="button"
                  @click="togglePassword"
                  :aria-label="showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
                  class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 focus:outline-none focus:ring-2 focus:ring-teal-500 rounded-md h-full"
                  :disabled="authStore.loading"
                >
                  <EyeOff v-if="showPassword" class="h-5 w-5" aria-hidden="true" />
                  <Eye v-else class="h-5 w-5" aria-hidden="true" />
                </button>
              </div>
              <p v-if="authStore.errors.password" class="text-sm text-destructive font-medium" role="alert" aria-live="polite">
                {{ authStore.errors.password[0] }}
              </p>
            </div>

            <!-- Captcha Field -->
            <div class="space-y-2">
              <Label for="captcha" :class="{'text-destructive': authStore.errors.captcha}">Kode Keamanan</Label>
              <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex items-center gap-2">
                  <div class="h-11 w-[120px] bg-slate-100 rounded-md border border-slate-200 overflow-hidden flex-shrink-0">
                    <img
                      v-if="authStore.captchaImage"
                      :src="authStore.captchaImage"
                      alt="Kode keamanan visual"
                      class="h-full w-full object-cover"
                    />
                    <div v-else class="h-full w-full flex items-center justify-center text-slate-400">
                      <Loader2 class="h-5 w-5 animate-spin" />
                    </div>
                  </div>
                  <Button
                    type="button"
                    variant="outline"
                    size="icon"
                    class="h-11 w-11 flex-shrink-0"
                    @click="authStore.refreshCaptcha"
                    :disabled="authStore.captchaLoading || authStore.loading"
                    aria-label="Muat ulang kode keamanan"
                  >
                    <RefreshCw :class="{'animate-spin': authStore.captchaLoading}" class="h-5 w-5 text-slate-600" aria-hidden="true" />
                  </Button>
                </div>
                <Input
                  id="captcha"
                  v-model="captcha"
                  type="text"
                  placeholder="Masukkan kode di atas"
                  required
                  autocomplete="off"
                  autocapitalize="characters"
                  spellcheck="false"
                  :disabled="authStore.loading"
                  :aria-invalid="!!authStore.errors.captcha"
                  class="h-11 bg-slate-50 border-slate-200 uppercase"
                />
              </div>
              <p v-if="authStore.errors.captcha" class="text-sm text-destructive font-medium" role="alert" aria-live="polite">
                {{ authStore.errors.captcha[0] }}
              </p>
            </div>

            <!-- Submit Button -->
            <Button type="submit" class="w-full h-11 mt-4 bg-teal-600 hover:bg-teal-700 text-white text-base font-semibold transition-colors" :disabled="authStore.loading">
              <Loader2 v-if="authStore.loading" class="mr-2 h-5 w-5 animate-spin" aria-hidden="true" />
              {{ authStore.loading ? 'Memproses...' : 'Masuk ke Sistem' }}
            </Button>
          </form>
        </CardContent>
      </Card>
    </div>

    <!-- Footer -->
    <div class="relative z-10 mt-12 text-sm text-slate-600 font-medium">
      &copy; 2026 BigSIRS | PT Sisfomedika
    </div>
  </div>
</template>
