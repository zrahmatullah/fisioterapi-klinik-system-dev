<template>
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-slate-100 p-6">

  <!-- HEADER -->
  <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">

    <div>
      <h1 class="text-4xl font-bold text-slate-800 tracking-tight">
        Master Profile Anak
      </h1>

      <p class="text-slate-500 mt-2">
        Kelola data profile anak dengan mudah dan modern
      </p>
    </div>

    <button
      @click="openAdd"
      class="
        h-12
        px-5
        rounded-2xl
        bg-indigo-600
        hover:bg-indigo-700
        text-white
        font-semibold
        shadow-lg
        shadow-indigo-500/20
        transition-all
        duration-300
        hover:-translate-y-0.5
        flex
        items-center
        justify-center
        gap-2
      "
    >
      <i class="pi pi-plus"></i>
      Tambah Anak
    </button>

  </div>

  <!-- CARD -->
  <div class="bg-white border border-slate-200/70 shadow-xl shadow-slate-200/40 rounded-[28px] overflow-hidden">

    <!-- TOOLBAR -->
    <div class="p-6 border-b border-slate-100 flex flex-col md:flex-row gap-4 md:items-center md:justify-between">

      <div>
        <h2 class="text-lg font-semibold text-slate-800">
          Data Anak
        </h2>

        <p class="text-sm text-slate-500 mt-1">
          Total {{ filteredData.length }} data ditemukan
        </p>
      </div>

      <!-- SEARCH -->
      <div class="relative w-full md:w-80">

        <i class="pi pi-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>

        <input
          v-model="search"
          type="text"
          placeholder="Cari nama anak..."
          class="
            w-full
            h-12
            rounded-2xl
            border
            border-slate-200
            bg-slate-50
            pl-12
            pr-4
            text-slate-700
            outline-none
            transition-all
            focus:ring-4
            focus:ring-indigo-100
            focus:border-indigo-500
            focus:bg-white
          "
        />

      </div>

    </div>

    <!-- TABLE -->
    <div class="overflow-x-auto">

      <table class="w-full">

        <thead class="bg-slate-50 border-b border-slate-200">

          <tr>

            <th class="px-5 py-4 text-sm font-semibold text-slate-600 text-center">
              No
            </th>

            <th class="px-5 py-4 text-sm font-semibold text-slate-600 text-left">
              Nama Anak
            </th>

            <th class="px-5 py-4 text-sm font-semibold text-slate-600 text-left">
              Orang Tua
            </th>

            <th class="px-5 py-4 text-sm font-semibold text-slate-600 text-left">
              Jenis Kelamin
            </th>

            <th class="px-5 py-4 text-sm font-semibold text-slate-600 text-left">
              Agama
            </th>

            <th class="px-5 py-4 text-sm font-semibold text-slate-600 text-left">
              NIK
            </th>

            <th class="px-5 py-4 text-sm font-semibold text-slate-600 text-left">
              Umur
            </th>

            <th class="px-5 py-4 text-sm font-semibold text-slate-600 text-center">
              Aksi
            </th>

          </tr>

        </thead>

        <tbody>

          <tr
            v-for="(item, index) in filteredData"
            :key="item.id"
            class="
              border-b
              border-slate-100
              hover:bg-slate-50/80
              transition
            "
          >

            <td class="px-5 py-4 text-center text-slate-500 font-medium">
              {{ index + 1 }}
            </td>

            <td class="px-5 py-4">

              <div class="flex items-center gap-3">

                <div class="w-11 h-11 rounded-2xl bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold">
                  {{ item.nama_anak?.charAt(0) }}
                </div>

                <div>

                  <h3 class="font-semibold text-slate-800">
                    {{ item.nama_anak }}
                  </h3>

                  <p class="text-sm text-slate-500">
                    {{ item.tempat_lahir }}
                  </p>

                </div>

              </div>

            </td>

            <td class="px-5 py-4 text-slate-700">
              {{ item.orangTua?.nama }}
            </td>

            <td class="px-5 py-4 text-slate-700">
              {{ item.jenis_kelamin?.nama }}
            </td>

            <td class="px-5 py-4 text-slate-700">
              {{ item.agama?.nama }}
            </td>

            <td class="px-5 py-4 text-slate-700">
              {{ item.NIK }}
            </td>

            <td class="px-5 py-4">

              <span class="px-3 py-1 rounded-xl bg-emerald-100 text-emerald-700 text-sm font-semibold">
                {{ item.umur }} Tahun
              </span>

            </td>

            <td class="px-5 py-4">

              <div class="flex items-center justify-center gap-2">

                <button
                  @click="openEdit(item)"
                  class="
                    w-10
                    h-10
                    rounded-xl
                    bg-amber-100
                    hover:bg-amber-200
                    text-amber-600
                    transition
                  "
                >
                  <i class="pi pi-pencil"></i>
                </button>

                <button
                  @click="deleteRow(item)"
                  class="
                    w-10
                    h-10
                    rounded-xl
                    bg-red-100
                    hover:bg-red-200
                    text-red-600
                    transition
                  "
                >
                  <i class="pi pi-trash"></i>
                </button>

              </div>

            </td>

          </tr>

          <!-- EMPTY -->
          <tr v-if="filteredData.length === 0">

            <td colspan="8" class="py-14 text-center">

              <div class="flex flex-col items-center">

                <div class="w-20 h-20 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 text-3xl">
                  <i class="pi pi-inbox"></i>
                </div>

                <h3 class="mt-4 text-lg font-semibold text-slate-700">
                  Data Tidak Ditemukan
                </h3>

                <p class="text-slate-500 text-sm mt-1">
                  Tidak ada data yang sesuai pencarian
                </p>

              </div>

            </td>

          </tr>

        </tbody>

      </table>

    </div>

  </div>

  <!-- MODAL -->
  <transition name="fade">

    <div
      v-if="dialog"
      class="
        fixed
        inset-0
        z-50
        bg-black/40
        backdrop-blur-sm
        flex
        items-center
        justify-center
        p-4
      "
    >

      <div class="w-full max-w-6xl bg-white rounded-[32px] shadow-2xl overflow-hidden">

        <!-- HEADER -->
        <div class="px-8 py-6 border-b border-slate-100 flex items-center justify-between">

          <div>

            <h2 class="text-2xl font-bold text-slate-800">
              {{ isEdit ? 'Edit Data Anak' : 'Tambah Data Anak' }}
            </h2>

            <p class="text-slate-500 text-sm mt-1">
              Lengkapi informasi profile anak
            </p>

          </div>

          <button
            @click="dialog = false"
            class="
              w-11
              h-11
              rounded-2xl
              bg-slate-100
              hover:bg-slate-200
              text-slate-600
              transition
            "
          >
            <i class="pi pi-times"></i>
          </button>

        </div>

        <!-- BODY -->
        <div class="p-8 max-h-[75vh] overflow-y-auto">

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div>
              <label class="label">Nama Anak</label>
              <input v-model="form.nama_anak" class="input" />
            </div>

            <div>
              <label class="label">Orang Tua</label>

              <select v-model="form.id_orang_tua" class="input">

                <option :value="null" disabled>
                  Pilih orang tua
                </option>

                <option
                  v-for="ot in orangTua"
                  :key="ot.id"
                  :value="ot.id"
                >
                  {{ ot.nama }}
                </option>

              </select>

            </div>

            <div>
              <label class="label">Jenis Kelamin</label>

              <select v-model="form.jenis_kelamin_id" class="input">

                <option :value="null" disabled>
                  Pilih jenis kelamin
                </option>

                <option
                  v-for="jk in jenisKelamin"
                  :key="jk.id"
                  :value="jk.id"
                >
                  {{ jk.nama }}
                </option>

              </select>

            </div>

            <div>
              <label class="label">Agama</label>

              <select v-model="form.agama_id" class="input">

                <option :value="null" disabled>
                  Pilih agama
                </option>

                <option
                  v-for="ag in agama"
                  :key="ag.id"
                  :value="ag.id"
                >
                  {{ ag.nama }}
                </option>

              </select>

            </div>

            <div>
              <label class="label">NIK</label>
              <input v-model="form.NIK" class="input" />
            </div>

            <div>
              <label class="label">Umur</label>

              <input
                type="text"
                :value="form.umur !== null ? form.umur + ' Tahun' : ''"
                readonly
                class="input bg-slate-100 cursor-not-allowed"
              />

            </div>

            <div>
              <label class="label">Tanggal Lahir</label>

              <input
                type="date"
                v-model="form.tanggal_lahir"
                class="input"
              />

            </div>

            <div>
              <label class="label">Tempat Lahir</label>
              <input v-model="form.tempat_lahir" class="input" />
            </div>

            <div>
              <label class="label">No HP Orang Tua</label>
              <input v-model="form.no_hp_orang_tua" class="input" />
            </div>

            <div>
              <label class="label">Status</label>

              <select v-model="form.status_aktif" class="input">

                <option :value="true">
                  Aktif
                </option>

                <option :value="false">
                  Nonaktif
                </option>

              </select>

            </div>

            <div class="md:col-span-2">

              <label class="label">
                Alamat
              </label>

              <textarea
                v-model="form.alamat"
                rows="3"
                class="input resize-none h-auto py-3"
              ></textarea>

            </div>

          </div>

        </div>

        <!-- FOOTER -->
        <div class="px-8 py-5 border-t border-slate-100 flex justify-end gap-3">

          <button
            @click="dialog = false"
            class="
              h-11
              px-5
              rounded-2xl
              bg-slate-100
              hover:bg-slate-200
              text-slate-700
              font-semibold
              transition
            "
          >
            Batal
          </button>

          <button
            @click="save"
            class="
              h-11
              px-6
              rounded-2xl
              bg-indigo-600
              hover:bg-indigo-700
              text-white
              font-semibold
              shadow-lg
              shadow-indigo-500/20
              transition-all
              duration-300
            "
          >
            Simpan Data
          </button>

        </div>

      </div>

    </div>

  </transition>

</div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import api from '../axios'
import { useToast } from 'vue-toastification'

function hitungUmur(tanggal) {

  if (!tanggal) return null

  const today = new Date()
  const birth = new Date(tanggal)

  let umur = today.getFullYear() - birth.getFullYear()

  const m = today.getMonth() - birth.getMonth()

  if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) {
    umur--
  }

  return umur
}

const toast = useToast()

const data = ref([])
const orangTua = ref([])
const jenisKelamin = ref([])
const agama = ref([])

const search = ref('')
const dialog = ref(false)
const isEdit = ref(false)

const formDefault = {
  nama_anak: '',
  id_orang_tua: null,
  jenis_kelamin_id: null,
  agama_id: null,
  NIK: '',
  alamat: '',
  umur: null,
  tanggal_lahir: '',
  tempat_lahir: '',
  no_hp_orang_tua: '',
  status_aktif: true
}

const form = ref({ ...formDefault })

onMounted(loadData)

async function loadData() {

  const anakRes = await api.get('/profile-anak')
  const userRes = await api.get('/user-profile')

  const allData = anakRes.data.data ?? anakRes.data
  const profiles = userRes.data.data ?? userRes.data

  jenisKelamin.value = (await api.get('/jenis-kelamin')).data
  agama.value = (await api.get('/agama')).data

  data.value = allData.filter(item => {

    const parent = profiles.find(
      p => p.id === item.id_orang_tua
    )

    if (parent && Number(parent.jenis_user_id) === 8) {
      item.orangTua = parent
      return true
    }

    return false
  })

  orangTua.value = profiles.filter(
    p => Number(p.jenis_user_id) === 8
  )
}

const filteredData = computed(() =>
  !search.value
    ? data.value
    : data.value.filter(d =>
        d.nama_anak
          .toLowerCase()
          .includes(search.value.toLowerCase())
      )
)

const openAdd = () => {

  form.value = { ...formDefault }

  isEdit.value = false

  dialog.value = true
}

const openEdit = row => {

  form.value = { ...row }

  isEdit.value = true

  dialog.value = true
}

const save = async () => {

  try {

    if (isEdit.value) {

      await api.put(
        `/profile-anak/${form.value.id}`,
        form.value
      )

    } else {

      await api.post(
        '/profile-anak',
        form.value
      )
    }

    toast.success('Data berhasil disimpan')

    dialog.value = false

    loadData()

  } catch (err) {

    toast.error('Gagal menyimpan data')

    console.error(err)
  }
}

const deleteRow = async row => {

  await api.delete(`/profile-anak/${row.id}`)

  toast.success('Data berhasil dihapus')

  loadData()
}

watch(
  () => form.value.tanggal_lahir,
  val => {

    const hasil = hitungUmur(val)

    form.value.umur = hasil
  }
)
</script>

<style scoped>

.fade-enter-active,
.fade-leave-active {
  transition: all .25s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: scale(.98);
}

.input {
  @apply
    w-full
    h-12
    rounded-2xl
    border
    border-slate-200
    bg-slate-50
    px-4
    text-slate-700
    outline-none
    transition-all
    focus:bg-white
    focus:ring-4
    focus:ring-indigo-100
    focus:border-indigo-500;
}

.label {
  @apply
    block
    mb-2
    text-sm
    font-semibold
    text-slate-700;
}

</style>