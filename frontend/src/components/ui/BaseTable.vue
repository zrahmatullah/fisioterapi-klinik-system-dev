<template>
  <div class="flex-1 min-h-0 flex flex-col bg-white border border-slate-200 rounded-xl overflow-hidden shadow-[0_1px_3px_rgba(15,23,42,0.04)] w-full">
    <!-- TOP TOOLBAR (shrink-0 p-3 border-b border-slate-200 flex flex-wrap items-center justify-between gap-2.5) -->
    <div v-if="showToolbar" class="shrink-0 p-3 border-b border-slate-200 flex flex-wrap items-center justify-between gap-2.5 bg-white">
      <!-- Left: Search & Filter Slot -->
      <div class="flex items-center gap-2 flex-1 min-w-[220px]">
        <div v-if="searchable" class="relative flex-1 max-w-xs">
          <i class="pi pi-search absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
          <input
            :value="search"
            @input="$emit('update:search', $event.target.value)"
            type="text"
            :placeholder="searchPlaceholder"
            class="w-full h-[34px] text-[12.5px] pl-8 pr-2.5 border border-slate-300 rounded-lg outline-none focus:border-[#0F766E] focus:ring-2 focus:ring-[#0F766E]/15 transition bg-white text-slate-800 placeholder:text-slate-400"
          />
          <button
            v-if="search"
            type="button"
            @click="$emit('update:search', '')"
            class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-0.5"
          >
            <i class="pi pi-times text-[10px]"></i>
          </button>
        </div>

        <slot name="filters" />
      </div>

      <!-- Right: Actions Slot / Primary CTA Button -->
      <div class="flex items-center gap-2">
        <slot name="actions">
          <button
            v-if="actionLabel"
            type="button"
            @click="$emit('action')"
            class="h-[34px] px-3.5 rounded-lg bg-[#0F766E] hover:bg-[#115E59] active:scale-95 text-white text-[12.5px] font-medium transition shadow-sm inline-flex items-center gap-1.5 cursor-pointer"
          >
            <i v-if="actionIcon" :class="actionIcon" class="text-xs"></i>
            <span>{{ actionLabel }}</span>
          </button>
        </slot>
      </div>
    </div>

    <!-- SCROLLABLE TABLE CONTAINER (flex-1 min-h-0 overflow-auto custom-scrollbar) -->
    <div class="flex-1 min-h-0 overflow-auto custom-scrollbar">
      <table class="min-w-full text-[12.5px] text-slate-700">
        <!-- HEADER (sticky top-0 z-10 bg-[#F8FAFC] border-b border-slate-200 text-[11px] font-semibold uppercase tracking-wider text-slate-600 py-2 px-3) -->
        <thead class="sticky top-0 z-10 bg-[#F8FAFC] border-b border-slate-200 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-600 select-none">
          <tr>
            <slot name="header">
              <th
                v-for="col in columns"
                :key="col.key"
                class="py-2 px-3"
                :class="[
                  col.align === 'center' ? 'text-center' : col.align === 'right' ? 'text-right' : 'text-left',
                  col.width ? col.width : ''
                ]"
              >
                {{ col.label }}
              </th>
            </slot>
          </tr>
        </thead>

        <!-- BODY (divide-y divide-slate-100 text-[12.5px] text-slate-700, compact rows py-2 px-3 hover:bg-[#F0F6F8]/60, tabular-nums) -->
        <tbody class="divide-y divide-slate-100">
          <!-- SKELETON LOADER DURING FETCH -->
          <template v-if="loading">
            <tr v-for="i in skeletonRows" :key="'skel-' + i" class="animate-pulse">
              <td
                v-for="col in (columns.length || 5)"
                :key="'skel-td-' + col"
                class="py-2 px-3"
              >
                <div class="h-4 bg-slate-200/80 rounded w-3/4"></div>
              </td>
            </tr>
          </template>

          <!-- REAL DATA ROWS -->
          <template v-else-if="items && items.length > 0">
            <tr
              v-for="(item, index) in items"
              :key="item.id || index"
              class="hover:bg-[#F0F6F8]/60 transition-colors cursor-default"
            >
              <slot name="row" :item="item" :index="index">
                <td
                  v-for="col in columns"
                  :key="col.key"
                  class="py-2 px-3 tabular-nums"
                  :class="[
                    col.align === 'center' ? 'text-center' : col.align === 'right' ? 'text-right' : 'text-left',
                    col.bold ? 'font-semibold text-slate-900' : ''
                  ]"
                >
                  {{ item[col.key] ?? '-' }}
                </td>
              </slot>
            </tr>
          </template>

          <!-- EMPTY STATE -->
          <tr v-else>
            <td :colspan="columns.length || 7" class="py-10 px-4 text-center">
              <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mb-2">
                  <i :class="emptyIcon" class="text-lg"></i>
                </div>
                <h4 class="text-[13px] font-semibold text-slate-800">{{ emptyTitle }}</h4>
                <p class="text-[11.5px] text-slate-500 mt-0.5">{{ emptySubtitle }}</p>
                <slot name="empty-action" />
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- ANCHORED FOOTER / PAGINATION (shrink-0 px-3.5 py-2 bg-[#F8FAFC] border-t border-slate-200 text-[11.5px]) -->
    <div
      v-if="showFooter"
      class="shrink-0 px-3.5 py-2 bg-[#F8FAFC] border-t border-slate-200 flex flex-wrap items-center justify-between gap-2 text-[11.5px] text-slate-600"
    >
      <div>
        <span>Menampilkan <strong>{{ items ? items.length : 0 }}</strong> data</span>
        <span v-if="totalItems !== undefined"> dari total <strong>{{ totalItems }}</strong></span>
      </div>

      <slot name="pagination">
        <div v-if="totalPages > 1" class="flex items-center gap-1.5">
          <button
            type="button"
            :disabled="currentPage <= 1"
            @click="$emit('update:currentPage', currentPage - 1)"
            class="h-7 px-2 rounded border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 disabled:opacity-40 disabled:cursor-not-allowed transition inline-flex items-center justify-center"
          >
            <i class="pi pi-chevron-left text-[9px]"></i>
          </button>
          <span class="px-2 font-medium tabular-nums">{{ currentPage }} / {{ totalPages }}</span>
          <button
            type="button"
            :disabled="currentPage >= totalPages"
            @click="$emit('update:currentPage', currentPage + 1)"
            class="h-7 px-2 rounded border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 disabled:opacity-40 disabled:cursor-not-allowed transition inline-flex items-center justify-center"
          >
            <i class="pi pi-chevron-right text-[9px]"></i>
          </button>
        </div>
      </slot>
    </div>
  </div>
</template>

<script setup>
defineProps({
  columns: {
    type: Array,
    default: () => []
  },
  items: {
    type: Array,
    default: () => []
  },
  loading: {
    type: Boolean,
    default: false
  },
  skeletonRows: {
    type: Number,
    default: 5
  },
  showToolbar: {
    type: Boolean,
    default: true
  },
  searchable: {
    type: Boolean,
    default: true
  },
  search: {
    type: String,
    default: ''
  },
  searchPlaceholder: {
    type: String,
    default: 'Cari data...'
  },
  actionLabel: {
    type: String,
    default: ''
  },
  actionIcon: {
    type: String,
    default: 'pi pi-plus'
  },
  emptyTitle: {
    type: String,
    default: 'Belum ada data tersedia'
  },
  emptySubtitle: {
    type: String,
    default: 'Data belum diinput atau tidak cocok dengan filter pencarian.'
  },
  emptyIcon: {
    type: String,
    default: 'pi pi-folder-open'
  },
  showFooter: {
    type: Boolean,
    default: true
  },
  currentPage: {
    type: Number,
    default: 1
  },
  totalPages: {
    type: Number,
    default: 1
  },
  totalItems: {
    type: Number,
    default: undefined
  }
})

defineEmits(['update:search', 'action', 'update:currentPage'])
</script>
