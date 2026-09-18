<script setup>
import { ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import { navigation } from '@/config/navigation'
import sisfoLogoUrl from '@/assets/logo/logo-sisfo.webp'
import { Button } from '@/components/ui/button'
import {
  Sheet,
  SheetContent,
  SheetHeader,
  SheetTitle,
  SheetTrigger,
} from '@/components/ui/sheet'
import { Menu } from '@lucide/vue'

const route = useRoute()
const isOpen = ref(false)

const isRouteActive = (routeName) => {
  return route.name === routeName
}

// Close drawer on route change
watch(route, () => {
  isOpen.value = false
})
</script>

<template>
  <Sheet v-model:open="isOpen">
    <SheetTrigger asChild>
      <Button variant="ghost" size="icon" class="md:hidden" aria-label="Buka menu navigasi">
        <Menu class="h-6 w-6 text-slate-700" />
      </Button>
    </SheetTrigger>
    <SheetContent side="left" class="p-0 w-72 flex flex-col">
      <SheetHeader class="h-16 flex items-center px-6 border-b border-slate-200 justify-start">
        <SheetTitle class="flex items-center gap-3">
          <img :src="sisfoLogoUrl" alt="" aria-hidden="true" class="h-8 w-8 object-contain" />
          <div class="flex flex-col text-left">
            <span class="text-sm font-bold leading-tight tracking-wide text-slate-800">BigSirs SIMRS</span>
            <span class="text-[10px] font-semibold text-slate-500 tracking-wider">PT SISFOMEDIKA</span>
          </div>
        </SheetTitle>
      </SheetHeader>

      <div class="flex-1 py-4 overflow-y-auto">
        <nav aria-label="Mobile Navigation" class="px-4 space-y-6">
          <div v-for="(group, index) in navigation" :key="index" class="space-y-2">
            <div v-if="!group.children" class="mb-2">
              <router-link
                :to="{ name: group.routeName }"
                class="flex items-center gap-3 px-3 py-2 rounded-md transition-colors text-sm font-medium"
                :class="[
                  isRouteActive(group.routeName) 
                    ? 'bg-teal-50 text-teal-700' 
                    : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                  group.disabled ? 'opacity-50 cursor-not-allowed pointer-events-none' : ''
                ]"
                :aria-current="isRouteActive(group.routeName) ? 'page' : undefined"
                :aria-disabled="group.disabled ? 'true' : undefined"
                :tabindex="group.disabled ? -1 : 0"
                @click="!group.disabled && (isOpen = false)"
              >
                <component :is="group.icon" class="h-5 w-5" aria-hidden="true" />
                <span>{{ group.label }}</span>
              </router-link>
            </div>

            <div v-else>
              <h3 class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">
                {{ group.label }}
              </h3>
              <ul class="space-y-1">
                <li v-for="(item, itemIndex) in group.children" :key="itemIndex">
                  <component
                    :is="item.disabled ? 'div' : 'router-link'"
                    :to="item.disabled ? undefined : { name: item.routeName }"
                    class="flex items-center gap-3 px-3 py-2 rounded-md transition-colors text-sm font-medium"
                    :class="[
                      item.disabled ? 'opacity-50 cursor-not-allowed text-slate-400' : 
                      isRouteActive(item.routeName) 
                        ? 'bg-teal-50 text-teal-700' 
                        : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
                    ]"
                    :aria-current="!item.disabled && isRouteActive(item.routeName) ? 'page' : undefined"
                    :aria-disabled="item.disabled ? 'true' : undefined"
                    :tabindex="item.disabled ? -1 : 0"
                    @click="!item.disabled && (isOpen = false)"
                  >
                    <component :is="item.icon" class="h-5 w-5" aria-hidden="true" />
                    <span class="flex-1">{{ item.label }}</span>
                    <span v-if="item.disabled" class="text-[10px] uppercase font-bold bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded">
                      Segera
                    </span>
                  </component>
                </li>
              </ul>
            </div>
          </div>
        </nav>
      </div>
    </SheetContent>
  </Sheet>
</template>
