<script setup>
import { onMounted, onUnmounted, ref } from 'vue'
import { api } from '../api'
import { ariary, dateHeure } from '../utils/format'
import CommandeDetail from '../components/CommandeDetail.vue'
import StatutBadge from '../components/StatutBadge.vue'

const commandes = ref([])
const detail = ref(null)
const filtreStatut = ref('')
const filtreDate = ref('')
let intervalId = null

async function charger() {
  const params = new URLSearchParams()
  if (filtreStatut.value) params.append('statut', filtreStatut.value)
  if (filtreDate.value) params.append('date', filtreDate.value)
  
  const queryString = params.toString()
  commandes.value = await api.commandes.lister(queryString ? `?${queryString}` : '')
}

async function afficher(id) {
  detail.value = await api.commandes.detail(id)
}

async function onStatutChange() {
  await charger()
  if (detail.value) {
    detail.value = await api.commandes.detail(detail.value.id)
  }
}

onMounted(() => {
  charger()
  intervalId = setInterval(charger, 30000)
})

onUnmounted(() => {
  if (intervalId) clearInterval(intervalId)
})
</script>

<template>
  <div class="grille">
    <div class="panneau">
      <h2>Commandes</h2>
      
      <div class="filtres">
        <label class="champ champ-court">
          <span>Statut</span>
          <select v-model="filtreStatut" @change="charger">
            <option value="">Tous</option>
            <option value="en_cours">En cours</option>
            <option value="en_preparation">En préparation</option>
            <option value="servie">Servie</option>
            <option value="payee">Payée</option>
          </select>
        </label>
        <label class="champ champ-court">
          <span>Date</span>
          <input v-model="filtreDate" type="date" @change="charger" />
        </label>
      </div>

      <p v-if="commandes.length === 0" class="vide">Aucune commande enregistrée.</p>

      <table v-else class="tableau tableau-cliquable">
        <thead>
          <tr><th>N°</th><th>Heure</th><th>Statut</th><th class="nombre">Table</th><th class="nombre">Plats</th><th class="nombre">Total</th></tr>
        </thead>
        <tbody>
          <tr
            v-for="c in commandes"
            :key="c.id"
            :class="{ selectionnee: detail && detail.id === c.id }"
            tabindex="0"
            @click="afficher(c.id)"
            @keydown.enter="afficher(c.id)"
          >
            <td>{{ c.id }}</td>
            <td>{{ dateHeure(c.date_commande) }}</td>
            <td><StatutBadge :statut="c.statut" /></td>
            <td class="nombre">{{ c.numero_table }}</td>
            <td class="nombre">{{ c.nb_lignes }}</td>
            <td class="nombre">{{ ariary(c.total) }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <CommandeDetail v-if="detail" :commande="detail" @change="onStatutChange" />
    <p v-else class="panneau vide">Choisissez une commande pour voir son détail.</p>
  </div>
</template>
