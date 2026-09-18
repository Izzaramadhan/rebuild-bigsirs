<script setup>
import { useAuthStore } from '@/stores/auth'
import { computed } from 'vue'
import { 
  Users, UserPlus, Clock, Stethoscope, CheckCircle, 
  Calendar, Building2, UserCircle2, Settings2, Download
} from '@lucide/vue'
import { Button } from '@/components/ui/button'
import dashboardBackgroundUrl from '@/assets/background-dashboard/dashboard-background.webp'

const authStore = useAuthStore()

const welcomeName = computed(() => {
  if (!authStore.user) return 'Pengguna'
  return authStore.user.name || authStore.user.username
})

const metricCards = [
  { title: 'Kunjungan Hari Ini', icon: Users, color: 'bg-teal-50 text-teal-600' },
  { title: 'Pasien Baru', icon: UserPlus, color: 'bg-teal-50 text-teal-600' },
  { title: 'Pasien Lama', icon: Users, color: 'bg-indigo-50 text-indigo-600' },
  { title: 'Menunggu', icon: Clock, color: 'bg-amber-50 text-amber-600' },
  { title: 'Sedang Diperiksa', icon: Stethoscope, color: 'bg-sky-50 text-sky-600' },
  { title: 'Pelayanan Selesai', icon: CheckCircle, color: 'bg-green-50 text-green-600' }
]

const quickAccessItems = [
  { label: 'Pendaftaran Pasien', desc: 'Antrean Baru', icon: UserPlus },
  { label: 'Pasien Baru', desc: 'Cetak No. RM', icon: UserCircle2 },
  { label: 'Antrean Poli', desc: 'Panggil Pasien', icon: Users },
  { label: 'Input RME Poli', desc: 'SOAP & Resep', icon: Stethoscope },
  { label: 'Kasir Rawat Jalan', desc: 'Kwitansi & Bayar', icon: CheckCircle },
  { label: 'Laporan RL 5.1', desc: 'Pemeriksaan Disusun', icon: Building2 },
]
</script>

<template>
  <div class="space-y-6 max-w-[1600px] mx-auto pb-10">
    
    <!-- DashboardFilters -->
    <div class="flex flex-col sm:flex-row gap-4 items-center justify-between">
      <div class="flex flex-wrap gap-2 text-sm">
        <div class="flex items-center gap-2 bg-white px-3 py-1.5 rounded-md border border-slate-200 text-slate-600 font-medium">
          <Calendar class="w-4 h-4 text-teal-600" />
          <span>Periode:</span>
          <span class="text-teal-700">Hari Ini</span>
        </div>
        <div class="flex items-center gap-2 bg-white px-3 py-1.5 rounded-md border border-slate-200 text-slate-600 font-medium">
          <Building2 class="w-4 h-4 text-teal-600" />
          <span>Poli:</span>
          <span class="text-slate-800">Semua Poliklinik</span>
        </div>
        <div class="flex items-center gap-2 bg-white px-3 py-1.5 rounded-md border border-slate-200 text-slate-600 font-medium">
          <UserCircle2 class="w-4 h-4 text-teal-600" />
          <span>Dokter:</span>
          <span class="text-slate-800">Semua Dokter</span>
        </div>
      </div>
      <div class="flex gap-2 w-full sm:w-auto">
        <Button variant="outline" disabled class="flex-1 sm:flex-none">
          <Settings2 class="w-4 h-4 mr-2" />
          Filter
        </Button>
        <Button class="bg-teal-600 hover:bg-teal-700 flex-1 sm:flex-none text-white" disabled>
          Terapkan
        </Button>
        <Button variant="outline" class="flex-1 sm:flex-none text-teal-600 border-teal-200 hover:bg-teal-50" disabled>
          <Download class="w-4 h-4 mr-2" />
          Unduh
        </Button>
      </div>
    </div>

    <!-- WelcomeBanner -->
    <div class="bg-gradient-to-r from-teal-50 to-white p-6 md:p-8 rounded-xl border border-teal-100 shadow-sm relative overflow-hidden flex items-center">
      <!-- Text container -->
      <div class="relative z-10 w-full md:w-2/3 lg:w-3/4 pr-0 md:pr-8">
        <p class="text-sm font-medium text-teal-600 mb-1">Selamat Datang,</p>
        <h2 class="text-2xl md:text-3xl font-bold text-slate-800 mb-2">{{ welcomeName }}</h2>
        <p class="text-slate-600 text-sm md:text-base">
          Pantau kunjungan dan pelayanan rawat jalan dengan mudah, cepat, dan terintegrasi. 
          Seluruh sinkronisasi data antrean poliklinik dan bridging rujukan berjalan optimal.
        </p>
      </div>
      <!-- Decorative background element -->
      <div class="absolute right-0 bottom-0 h-2/3 md:h-full w-[40%] md:w-1/3 lg:w-1/4 pointer-events-none select-none opacity-20 md:opacity-100 flex justify-end items-end md:items-center pr-0 md:pr-6 lg:pr-8">
        <img 
          :src="dashboardBackgroundUrl" 
          alt="" 
          aria-hidden="true" 
          class="h-full md:max-h-[160px] lg:max-h-[180px] w-auto object-contain object-right-bottom md:object-right"
        />
      </div>
    </div>
    
    <!-- MetricCardsGrid -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
      <div v-for="(metric, idx) in metricCards" :key="idx" class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between">
        <div class="flex justify-between items-start mb-4">
          <h3 class="text-sm font-medium text-slate-600 leading-tight">{{ metric.title }}</h3>
          <div :class="['p-2 rounded-full', metric.color]">
            <component :is="metric.icon" class="w-4 h-4" />
          </div>
        </div>
        <div>
          <div class="text-2xl font-bold text-slate-800">—</div>
          <p class="text-xs text-slate-500 mt-1">Data belum tersedia</p>
        </div>
      </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
      <!-- VisitTrendPlaceholder -->
      <div class="lg:col-span-2 bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col min-h-[300px]">
        <h3 class="font-bold text-slate-800 mb-1">Tren Kunjungan Rawat Jalan</h3>
        <p class="text-sm text-slate-500 mb-6">Perbandingan volume pendaftaran pasien selesai berobat 7 hari terakhir.</p>
        <div class="flex-1 border-2 border-dashed border-slate-100 rounded-lg flex items-center justify-center bg-slate-50">
          <span class="text-sm text-slate-400 font-medium">— Data belum tersedia —</span>
        </div>
      </div>

      <!-- GuarantorCompositionPlaceholder -->
      <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col min-h-[300px]">
        <h3 class="font-bold text-slate-800 mb-1">Komposisi Penjamin</h3>
        <p class="text-sm text-slate-500 mb-6">Distribusi metode pembayaran rawat jalan hari ini.</p>
        <div class="flex-1 border-2 border-dashed border-slate-100 rounded-lg flex flex-col items-center justify-center bg-slate-50 gap-2">
          <div class="w-32 h-32 rounded-full border-8 border-slate-200 flex items-center justify-center">
            <span class="text-xl font-bold text-slate-300">—</span>
          </div>
          <span class="text-sm text-slate-400 font-medium mt-4">Data belum tersedia</span>
        </div>
      </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
      <!-- PolyclinicDistributionPlaceholder -->
      <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col min-h-[300px]">
        <div class="flex justify-between items-center mb-6">
          <div>
            <h3 class="font-bold text-slate-800 mb-1">Distribusi Pasien per Poliklinik</h3>
            <p class="text-sm text-slate-500">5 poliklinik dengan beban kunjungan tertinggi hari ini.</p>
          </div>
          <Button variant="ghost" size="sm" class="text-teal-600 hidden sm:flex" disabled>Lihat Semua</Button>
        </div>
        <div class="flex-1 border-2 border-dashed border-slate-100 rounded-lg flex items-center justify-center bg-slate-50">
          <span class="text-sm text-slate-400 font-medium">— Data belum tersedia —</span>
        </div>
      </div>

      <!-- QuickAccessPanel -->
      <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
        <h3 class="font-bold text-slate-800 mb-1">Akses Cepat Rawat Jalan</h3>
        <p class="text-sm text-slate-500 mb-6">Pintasan operasional front-desk hingga kasir.</p>
        
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
          <button 
            v-for="(item, idx) in quickAccessItems" 
            :key="idx"
            disabled
            class="flex flex-col items-center justify-center p-4 text-center rounded-lg border border-slate-100 bg-slate-50 opacity-60 cursor-not-allowed transition-all relative overflow-hidden"
          >
            <div class="absolute top-0 right-0 bg-slate-200 text-slate-500 text-[9px] font-bold px-2 py-0.5 rounded-bl-lg">
              SEGERA
            </div>
            <component :is="item.icon" class="w-6 h-6 text-teal-600 mb-2" />
            <span class="text-sm font-semibold text-slate-700 leading-tight">{{ item.label }}</span>
            <span class="text-xs text-slate-500 mt-1 hidden sm:block">{{ item.desc }}</span>
          </button>
        </div>
      </div>
    </div>

    <!-- DiagnosisAndProcedurePlaceholder -->
    <div class="grid lg:grid-cols-2 gap-6">
      <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col min-h-[300px]">
        <div class="flex justify-between items-center mb-6">
          <div>
            <h3 class="font-bold text-slate-800 mb-1">10 Besar Penyakit Rawat Jalan</h3>
            <p class="text-sm text-slate-500">Diagnosis tersering (berdasarkan kode ICD-10).</p>
          </div>
          <Button variant="ghost" size="sm" class="text-teal-600 hidden sm:flex" disabled>Lihat Semua</Button>
        </div>
        <div class="flex-1 border-2 border-dashed border-slate-100 rounded-lg flex items-center justify-center bg-slate-50">
          <span class="text-sm text-slate-400 font-medium">— Data belum tersedia —</span>
        </div>
      </div>
      
      <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col min-h-[300px]">
        <div class="flex justify-between items-center mb-6">
          <div>
            <h3 class="font-bold text-slate-800 mb-1">10 Besar Tindakan Medis</h3>
            <p class="text-sm text-slate-500">Tindakan dan prosedur poliklinik terbanyak hari ini.</p>
          </div>
          <Button variant="ghost" size="sm" class="text-teal-600 hidden sm:flex" disabled>Lihat Semua</Button>
        </div>
        <div class="flex-1 border-2 border-dashed border-slate-100 rounded-lg flex items-center justify-center bg-slate-50">
          <span class="text-sm text-slate-400 font-medium">— Data belum tersedia —</span>
        </div>
      </div>
    </div>

    <!-- PolyclinicOperationsPlaceholder -->
    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col min-h-[400px]">
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
          <h3 class="font-bold text-slate-800 mb-1">Operasional Poliklinik Hari Ini</h3>
          <p class="text-sm text-slate-500">Status antrean dokter dan SLA tunggu.</p>
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
          <Input placeholder="Cari poliklinik / dokter..." class="w-full sm:w-64" disabled />
        </div>
      </div>
      <div class="flex-1 border-2 border-dashed border-slate-100 rounded-lg flex items-center justify-center bg-slate-50">
        <span class="text-sm text-slate-400 font-medium">— Data operasional belum tersedia —</span>
      </div>
    </div>

  </div>
</template>
