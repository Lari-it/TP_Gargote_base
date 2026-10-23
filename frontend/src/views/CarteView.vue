<script setup>
import { onMounted, ref, computed } from 'vue'
import { api } from '../api'
import PlatList from '../components/PlatList.vue'
import PlatForm from '../components/PlatForm.vue'
import PlatDuJour from '../components/PlatDuJour.vue'

const plats = ref([])
const categories = ref([])
const stock = ref([])
const platSelectionne = ref(null)
const message = ref(null)

const portions = computed(() => {
  const result = {}
  stock.value.forEach(item => {
    result[item.id] = item.stock
  })
  return result
})

async function charger() {
  try {
    ;[plats.value, categories.value, stock.value] = await Promise.all([
      api.plats.lister(),
      api.categories.lister(),
      api.stock.lister(),
    ])
  } catch (e) {
    message.value = { type: 'erreur', texte: 'Impossible de charger la carte : ' + e.message }
  }
}

async function surEnregistrement(plat) {
  message.value = { type: 'succes', texte: `« ${plat.nom} » est enregistré.` }
  platSelectionne.value = null
  await charger()
}

async function supprimer(plat) {
  if (!confirm(`Supprimer « ${plat.nom} » de la carte ?`)) return
  try {
    await api.plats.supprimer(plat.id)
    message.value = { type: 'succes', texte: `« ${plat.nom} » est supprimé.` }
    await charger()
  } catch (e) {
    message.value = { type: 'erreur', texte: e.message }
  }
}

async function basculer(plat) {
  try {
    await api.plats.modifier(plat.id, { disponible: !plat.disponible })
    message.value = { type: 'succes', texte: `« ${plat.nom} » est maintenant ${plat.disponible ? 'épuisé' : 'disponible'}.` }
    await charger()
  } catch (e) {
    message.value = { type: 'erreur', texte: e.message }
  }
}

onMounted(charger)
</script>

<template>
  <p v-if="message" class="alerte" :class="`alerte-${message.type}`">{{ message.texte }}</p>

  <PlatDuJour :modifiable="true" :plats="plats" />

  <div class="grille">
    <PlatList :plats="plats" :portions="portions" @modifier="platSelectionne = $event" @supprimer="supprimer" @basculer="basculer" />

    <PlatForm
      :plat="platSelectionne"
      :categories="categories"
      @enregistre="surEnregistrement"
      @annule="platSelectionne = null"
    />
  </div>
</template>
