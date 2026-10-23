<script setup>
import { ref } from 'vue'
import CarteView from './views/CarteView.vue'
import CommandeView from './views/CommandeView.vue'
import HistoriqueView from './views/HistoriqueView.vue'
import StockView from './views/StockView.vue'
import DashboardView from './views/DashboardView.vue'

const ongletActif = ref('carte')

const onglets = [
  { id: 'carte', label: 'Carte' },
  { id: 'commande', label: 'Commande' },
  { id: 'historique', label: 'Historique' },
  { id: 'stock', label: 'Stock' },
  { id: 'dashboard', label: 'Tableau de bord' },
]
</script>

<template>
  <div class="app">
    <header>
      <h1>Le Gargote</h1>
      <p>Antananarivo</p>
    </header>

    <nav class="onglets">
      <button
        v-for="o in onglets"
        :key="o.id"
        class="onglet"
        :class="{ actif: ongletActif === o.id }"
        @click="ongletActif = o.id"
      >
        {{ o.label }}
      </button>
    </nav>

    <main>
      <CarteView v-if="ongletActif === 'carte'" />
      <CommandeView v-else-if="ongletActif === 'commande'" />
      <HistoriqueView v-else-if="ongletActif === 'historique'" />
      <StockView v-else-if="ongletActif === 'stock'" />
      <DashboardView v-else-if="ongletActif === 'dashboard'" />
    </main>
  </div>
</template>
