import { LayoutDashboard, UserPlus, ClipboardList, Stethoscope, Receipt, FileText, ArrowRightLeft, PackageCheck, PackageMinus, ShoppingCart } from '@lucide/vue'

export const navigation = [
  {
    label: 'PELAYANAN KLINIS',
    disabled: false,
    children: [
      {
        label: 'Dashboard',
        icon: LayoutDashboard,
        routeName: 'dashboard',
        disabled: false,
      },
      {
        label: 'Pendaftaran',
        icon: UserPlus,
        routeName: 'outpatient.registration',
        disabled: true,
      },
      {
        label: 'Admisi',
        icon: ClipboardList,
        routeName: 'outpatient.admission',
        disabled: true,
      },
      {
        label: 'Pemeriksaan',
        icon: Stethoscope,
        routeName: 'outpatient.examination',
        disabled: true,
      },
      {
        label: 'Billing Rawat Jalan',
        icon: Receipt,
        routeName: 'outpatient.billing',
        disabled: true,
      }
    ]
  },
  {
    label: 'LOGISTIK FARMASI',
    icon: PackageCheck,
    disabled: false,
    children: [
      {
        label: 'Faktur',
        icon: FileText,
        routeName: 'logistics.invoice',
        disabled: true,
      },
      {
        label: 'Mutasi',
        icon: ArrowRightLeft,
        routeName: 'logistics.mutation',
        disabled: true,
      },
      {
        label: 'Stok Opname',
        icon: ClipboardList,
        routeName: 'logistics.stockopname',
        disabled: true,
      },
      {
        label: 'Pengeluaran Barang',
        icon: PackageMinus,
        routeName: 'logistics.expenditure',
        disabled: true,
      },
      {
        label: 'Penjualan Bebas',
        icon: ShoppingCart,
        routeName: 'logistics.sales',
        disabled: true,
      }
    ]
  }
]
