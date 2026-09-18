<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { computed } from 'vue'

import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import { Avatar, AvatarFallback } from '@/components/ui/avatar'
import { Button } from '@/components/ui/button'
import { User, LogOut, Loader2 } from '@lucide/vue'

const router = useRouter()
const authStore = useAuthStore()

const userInitials = computed(() => {
  if (!authStore.user) return 'U'
  const name = authStore.user.name || authStore.user.username || 'User'
  return name.substring(0, 2).toUpperCase()
})

const displayName = computed(() => {
  if (!authStore.user) return 'Pengguna'
  return authStore.user.name || authStore.user.username
})

const handleLogout = async () => {
  await authStore.logout()
  router.replace({ name: 'login' })
}
</script>

<template>
  <DropdownMenu>
    <DropdownMenuTrigger asChild>
      <Button variant="ghost" class="relative h-10 w-10 rounded-full" aria-label="Menu Pengguna">
        <Avatar class="h-10 w-10 border border-slate-200">
          <AvatarFallback class="bg-teal-50 text-teal-700 font-semibold">{{ userInitials }}</AvatarFallback>
        </Avatar>
      </Button>
    </DropdownMenuTrigger>
    <DropdownMenuContent class="w-56" align="end">
      <DropdownMenuLabel class="font-normal">
        <div class="flex flex-col space-y-1">
          <p class="text-sm font-medium leading-none">{{ displayName }}</p>
          <p class="text-xs leading-none text-muted-foreground" v-if="authStore.user?.username">
            @{{ authStore.user.username }}
          </p>
        </div>
      </DropdownMenuLabel>
      <DropdownMenuSeparator />
      <DropdownMenuItem disabled>
        <User class="mr-2 h-4 w-4" aria-hidden="true" />
        <span>Profil (Segera)</span>
      </DropdownMenuItem>
      <DropdownMenuSeparator />
      <DropdownMenuItem @click="handleLogout" :disabled="authStore.loading" class="text-destructive focus:text-destructive cursor-pointer">
        <Loader2 v-if="authStore.loading" class="mr-2 h-4 w-4 animate-spin" aria-hidden="true" />
        <LogOut v-else class="mr-2 h-4 w-4" aria-hidden="true" />
        <span>{{ authStore.loading ? 'Keluar...' : 'Keluar' }}</span>
      </DropdownMenuItem>
    </DropdownMenuContent>
  </DropdownMenu>
</template>
