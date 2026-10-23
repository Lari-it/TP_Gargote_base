<script setup>
import { onMounted, ref } from 'vue'
import { api } from '../api'

const stock = ref([])

onMounted(async () => {
  stock.value = await api.stock.lister()
})
</script>

<template>
  <div class="panneau">
    <h2>Stock actuel</h2>
    <table v-if="stock.length > 0" class="tableau">
      <thead>
        <tr><th>Ingrédient</th><th>Stock</th><th>Unité</th></tr>
      </thead>
      <tbody>
        <tr v-for="item in stock" :key="item.id" :class="{ 'stock-bas': item.stock < 10 }">
          <td>{{ item.nom }}</td>
          <td class="nombre">{{ item.stock }}</td>
          <td>{{ item.unite }}</td>
        </tr>
      </tbody>
    </table>
    <p v-else class="vide">Aucun ingrédient enregistré.</p>
  </div>
</template>
