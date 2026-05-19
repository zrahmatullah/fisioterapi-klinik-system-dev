<template>

  <!-- =========================
       GLOBAL LOADING
  ========================== -->
  <transition name="loader">

    <div
      v-if="loading"
      class="
        fixed inset-0 z-[9999]
        flex items-center justify-center
        overflow-hidden
      "
    >

      <!-- SOFT OVERLAY -->
      <div
        class="
          absolute inset-0
          bg-slate-900/10
          backdrop-blur-[10px]
          transition-all duration-500
        "
      ></div>

      <!-- EXTRA LIGHT -->
      <div
        class="
          absolute inset-0
          bg-gradient-to-br
          from-white/10
          via-transparent
          to-indigo-500/5
        "
      ></div>

      <!-- TOP LOADING -->
      <div class="top-loader"></div>

      <!-- CARD -->
      <div
        class="
          relative
          w-[290px]
          rounded-[30px]
          border border-white/20
          bg-white/55
          backdrop-blur-2xl
          shadow-[0_10px_60px_rgba(0,0,0,0.12)]
          px-8 py-8
          overflow-hidden
          animate-loader
        "
      >

        <!-- GLOW -->
        <div
          class="
            absolute -top-20 -right-20
            w-44 h-44
            bg-indigo-400/20
            rounded-full blur-3xl
          "
        ></div>

        <div
          class="
            absolute -bottom-20 -left-20
            w-44 h-44
            bg-fuchsia-400/20
            rounded-full blur-3xl
          "
        ></div>

        <!-- CONTENT -->
        <div class="relative flex flex-col items-center">

          <!-- SPINNER -->
          <div class="relative w-20 h-20">

            <!-- RING -->
            <div
              class="
                absolute inset-0
                rounded-full
                border-[5px]
                border-white/30
              "
            ></div>

            <!-- SPIN -->
            <div
              class="
                absolute inset-0
                rounded-full
                border-[5px]
                border-transparent
                border-t-indigo-500
                border-r-fuchsia-500
                animate-spin
              "
            ></div>

            <!-- CENTER -->
            <div
              class="
                absolute inset-3
                rounded-full
                bg-white/80
                backdrop-blur-md
                flex items-center justify-center
                shadow-inner
              "
            >

              <img
                src="/logo-abqary.png"
                class="
                  w-9 h-9
                  object-contain
                  animate-pulse
                "
              />

            </div>

          </div>

          <!-- TEXT -->
          <div class="mt-6 text-center">

            <h2
              class="
                text-lg
                font-semibold
                text-slate-800
                tracking-wide
              "
            >
              Loading
            </h2>

            <p class="text-sm text-slate-500 mt-1">
              Menyiapkan halaman...
            </p>

          </div>

          <!-- DOT -->
          <div class="flex gap-2 mt-5">

            <span class="dot"></span>
            <span class="dot delay-150"></span>
            <span class="dot delay-300"></span>

          </div>

        </div>

      </div>

    </div>

  </transition>

  <!-- PAGE -->
  <router-view />

</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const loading = ref(false)

onMounted(() => {

  router.beforeEach((to, from, next) => {

    loading.value = true

    next()
  })

  router.afterEach(() => {

    setTimeout(() => {
      loading.value = false
    }, 500)

  })

})
</script>

<style scoped>

/* =========================
   PAGE TRANSITION
========================= */
.loader-enter-active,
.loader-leave-active{
  transition: all .35s ease;
}

.loader-enter-from,
.loader-leave-to{
  opacity: 0;
}

/* =========================
   TOP LOADER
========================= */
.top-loader{
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 3px;
  overflow: hidden;
  background: rgba(255,255,255,.08);
}

.top-loader::before{
  content: '';
  position: absolute;
  left: -40%;
  width: 40%;
  height: 100%;

  background: linear-gradient(
    90deg,
    #6366f1,
    #a855f7
  );

  animation: loadingBar 1s infinite;
}

@keyframes loadingBar{

  0%{
    left: -40%;
  }

  100%{
    left: 100%;
  }
}

/* =========================
   CARD ANIMATION
========================= */
.animate-loader{
  animation: smoothPop .35s ease;
}

@keyframes smoothPop{

  from{
    opacity: 0;
    transform: scale(.96) translateY(8px);
  }

  to{
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

/* =========================
   DOT ANIMATION
========================= */
.dot{
  width: 8px;
  height: 8px;
  border-radius: 999px;
  background: #6366f1;
  animation: bounce 1s infinite;
}

.delay-150{
  animation-delay: .15s;
}

.delay-300{
  animation-delay: .3s;
}

@keyframes bounce{

  0%,80%,100%{
    transform: scale(.5);
    opacity: .4;
  }

  40%{
    transform: scale(1);
    opacity: 1;
  }
}
</style>