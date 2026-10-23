<script setup>
import { onMounted, ref } from 'vue'
import { api } from '../api'

const props = defineProps({
  modifiable: { type: Boolean, default: false },
  plats: { type: Array, default: () => [] },
})

const emit = defineEmits(['choisir'])

const platDuJour = ref(null)
const idSelectionne = ref('')
const message = ref('')
const enCours = ref(false)
const historiqueVisible = ref(false)
const historique = ref([])

async function charger() {
  try {
    platDuJour.value = await api.platDuJour.lire()
  } catch (e) {
    message.value = e.message
  }
}

async function definir() {
  if (!idSelectionne.value) return
  
  enCours.value = true
  message.value = ''
  try {
    platDuJour.value = await api.platDuJour.definir(parseInt(idSelectionne.value))
    message.value = ''
  } catch (e) {
    message.value = e.message
  } finally {
    enCours.value = false
  }
}

async function afficherHistorique() {
  try {
    historique.value = await api.platDuJour.historique()
    historiqueVisible.value = true
  } catch (e) {
    message.value = e.message
  }
}

function choisir() {
  if (platDuJour.value) {
    emit('choisir', platDuJour.value)
  }
}

onMounted(charger)
</script>

<template>
  <div class="plat-du-jour">
    <h2>Plat du jour</h2>
    
    <p v-if="message" class="alerte alerte-erreur">{{ message }}</p>
    
    <div v-if="platDuJour" class="plat-du-jour-contenu">
      <div class="plat-du-jour-info">
        <span class="plat-du-jour-nom">{{ platDuJour.nom }}</span>
        <span v-if="platDuJour.description" class="plat-du-jour-description">{{ platDuJour.description }}</span>
      </div>
      
      <button v-if="!modifiable" class="bouton" @click="choisir">Ajouter au panier</button>
      
      <div v-if="modifiable" class="plat-du-jour-selection">
        <select v-model.number="idSelectionne">
          <option value="">Choisir un plat…</option>
          <option v-for="plat in plats.filter(p => p.disponible)" :key="plat.id" :value="plat.id">
            {{ plat.nom }}
          </option>
        </select>
        <button class="bouton" @click="definir" :disabled="enCours || !idSelectionne">
          Choisir
        </button>
      </div>
      
      <button class="lien" @click="afficherHistorique">Voir les 7 derniers jours</button>
      
      <div v-if="historiqueVisible" class="plat-du-jour-historique">
        <ul>
          <li v-for="entree in historique" :key="entree.date_jour">
            {{ new Date(entree.date_jour).toLocaleDateString('fr-FR') }} : {{ entree.plat?.nom || 'N/A' }}
          </li>
        </ul>
      </div>
    </div>
    
    <p v-else class="plat-du-jour-vide">
      Aucun plat du jour choisi pour aujourd'hui.
      <span v-if="modifiable"> Utilisez le formulaire pour en choisir un.</span>
    </p>
  </div>
</template>
