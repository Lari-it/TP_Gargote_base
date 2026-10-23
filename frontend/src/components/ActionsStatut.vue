<script setup>
import { api } from '../api'

const props = defineProps({
  commande: { type: Object, required: true },
})

const emit = defineEmits(['change'])

async function changerStatut(nouveauStatut) {
  try {
    await api.commandes.changerStatut(props.commande.id, nouveauStatut)
    emit('change')
  } catch (e) {
    alert(e.message)
  }
}
</script>

<template>
  <div class="actions-statut">
    <button
      v-for="statut in commande.transitions_possibles"
      :key="statut"
      class="bouton bouton-discret"
      @click="changerStatut(statut)"
    >
      {{ statut }}
    </button>
  </div>
</template>
