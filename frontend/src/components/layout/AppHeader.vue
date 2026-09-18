<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import MobileSidebar from './MobileSidebar.vue'
import UserMenu from './UserMenu.vue'
import { Search, Bell, HelpCircle } from '@lucide/vue'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip'

const route = useRoute()

const pageTitle = computed(() => {
  return route.meta?.title || 'Dashboard'
})
</script>

<template>
  <header class="sticky top-0 z-30 flex h-16 w-full items-center justify-between border-b border-slate-200 bg-white/95 px-4 backdrop-blur-sm sm:px-6">
    <div class="flex items-center gap-4 flex-1">
      <MobileSidebar />
      <h1 class="text-xl font-semibold text-slate-800 md:hidden">{{ pageTitle }}</h1>
      <!-- Search Input -->
      <div class="hidden md:flex relative w-full max-w-md ml-2">
        <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-slate-400" />
        <Input 
          type="search" 
          placeholder="Cari pasien, No. RM, NIK, atau dokter..." 
          class="w-full bg-slate-50 pl-9 border-slate-200 focus-visible:ring-slate-300 rounded-md"
          disabled
        />
      </div>
    </div>

    <div class="flex items-center gap-3 md:gap-4">
      <TooltipProvider>
        <Tooltip>
          <TooltipTrigger asChild>
            <Button variant="ghost" size="icon" class="text-slate-500 relative" disabled>
              <Bell class="h-5 w-5" />
              <span class="absolute top-2 right-2 h-2 w-2 rounded-full bg-destructive"></span>
            </Button>
          </TooltipTrigger>
          <TooltipContent>
            <p>Fitur segera tersedia</p>
          </TooltipContent>
        </Tooltip>
      </TooltipProvider>

      <TooltipProvider>
        <Tooltip>
          <TooltipTrigger asChild>
            <Button variant="ghost" size="icon" class="text-slate-500 hidden sm:flex" disabled>
              <HelpCircle class="h-5 w-5" />
            </Button>
          </TooltipTrigger>
          <TooltipContent>
            <p>Fitur segera tersedia</p>
          </TooltipContent>
        </Tooltip>
      </TooltipProvider>
      
      <div class="h-6 w-px bg-slate-200 mx-1 hidden sm:block"></div>

      <UserMenu />
    </div>
  </header>
</template>
