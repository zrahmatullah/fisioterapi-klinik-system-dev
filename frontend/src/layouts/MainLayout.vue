<template>
  <div class="fixed inset-0 h-dvh w-screen overflow-hidden flex bg-[#F3F6F8] font-sans select-none">
    <!-- 1. LEFT NAVIGATION SIDEBAR -->
    <Sidebar />

    <!-- 2. RIGHT WORKSPACE CONTAINER -->
    <div class="flex-1 h-full min-w-0 flex flex-col overflow-hidden">
      <!-- TOP HEADER BAR -->
      <Navbar />

      <!-- BOUNDED MAIN WORKSPACE -->
      <main class="flex-1 min-h-0 overflow-hidden p-3.5 flex flex-col gap-3 bg-[#F3F6F8]">
        <div class="page-enter-active w-full flex-1 min-h-0 flex flex-col overflow-hidden">
          <router-view />
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import Navbar from '../components/Navbar.vue'
import Sidebar from '../components/Sidebar.vue'
</script>

<style scoped>
.page-enter-active {
  animation: clinicalFadeIn 0.15s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes clinicalFadeIn {
  from {
    opacity: 0;
    transform: translateY(3px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
