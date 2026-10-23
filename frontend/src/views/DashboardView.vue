<script setup>
import { onMounted, ref } from 'vue'
import { api } from '../api'
import { ariary } from '../utils/format'

const stats = ref(null)

onMounted(async () => {
  stats.value = await api.stats.obtenir()
})
</script>

<template>
  <div class="panneau">
    <h2>Tableau de bord</h2>
    
    <div v-if="stats" class="dashboard">
      <div class="kpi">
        <h3>Commandes aujourd'hui</h3>
        <p class="kpi-valeur">{{ stats.commandes_aujourdhui }}</p>
      </div>
      
      <div class="kpi">
        <h3>CA aujourd'hui</h3>
        <p class="kpi-valeur">{{ ariary(stats.total_aujourdhui) }}</p>
      </div>
      
      <div class="kpi">
        <h3>Plats populaires</h3>
        <ul class="liste-simple">
          <li v-for="plat in stats.plats_populaires" :key="plat.nom">
            {{ plat.nom }} ({{ plat.total_vendu }})
          </li>
        </ul>
      </div>
      
      <div class="kpi">
        <h3>Stock bas</h3>
        <ul class="liste-simple">
          <li v-for="item in stats.stock_bas" :key="item.nom" class="alerte">
            {{ item.nom }} : {{ item.stock }} {{ item.unite }}
          </li>
        </ul>
      </div>
    </div>
    
    <p v-else class="vide">Chargement...</p>
  </div>
</template>
