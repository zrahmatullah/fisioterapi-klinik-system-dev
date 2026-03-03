<template>
  <div class="layout">
    <!-- NAVBAR -->
    <Navbar />

    <div class="main">
      <!-- SIDEBAR (FIXED) -->
      <Sidebar />

      <!-- CONTENT (SCROLL DI SINI) -->
      <div class="content">
        <router-view />
      </div>
    </div>
  </div>
</template>

<script setup>
import Navbar from '../components/Navbar.vue'
import Sidebar from '../components/Sidebar.vue'
</script>

<style scoped>
/* ROOT */
.layout {
  height: 100vh;
  overflow: hidden; /* 🔴 penting: body tidak scroll */
  display: flex;
  flex-direction: column;
}

/* NAVBAR */
:deep(.navbar) {
  height: 64px;
  position: sticky;
  top: 0;
  z-index: 50;
  background: white;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* MAIN WRAPPER */
.main {
  flex: 1;
  display: flex;
  position: relative;
}

/* SIDEBAR FIX */
:deep(.sidebar) {
  position: fixed;
  top: 64px;              /* di bawah navbar */
  left: 0;
  width: 260px;
  height: calc(100vh - 64px);
  z-index: 40;
}

/* CONTENT */
.content {
  margin-left: 260px;     /* offset sidebar */
  flex: 1;
  padding: 25px;
  background: #f8f6ff;
  font-family: 'Poppins', sans-serif;

  height: calc(100vh - 64px);
  overflow-y: auto;       /* ✅ scroll hanya di sini */
}
</style>
