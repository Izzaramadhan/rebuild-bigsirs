<script setup>
import { RouterView } from 'vue-router'
import AppSidebar from '@/components/layout/AppSidebar.vue'
import AppHeader from '@/components/layout/AppHeader.vue'
</script>

<template>
  <div class="flex min-h-screen w-full bg-slate-50 text-slate-900 font-sans">
    <!-- Desktop Sidebar -->
    <AppSidebar />

    <!-- Main Content Area -->
    <div class="flex flex-col flex-1 min-w-0">
      <!-- Header -->
      <AppHeader />

      <!-- Page Content -->
      <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-x-hidden">
        <RouterView />
      </main>
    </div>
  </div>
</template>
