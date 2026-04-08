<template>
  <div class="page-container">
    <div class="page-header">
      <button @click="router.back()" class="btn-back">← Back</button>
      <h1 class="page-title">Order Details</h1>
    </div>

    <div v-if="loading" class="card loading-state">Loading order...</div>

    <div v-else-if="order" class="card">
      <div class="detail-grid">
        <div class="detail-section">
          <h3 class="section-title">Order Information</h3>
          <div class="detail-row">
            <span class="detail-label">Order ID</span>
            <span class="detail-value">{{ String(order.id).padStart(7, '0') }}</span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Purchase Date</span>
            <span class="detail-value">{{ formatDate(order.purchase_date) }}</span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Status</span>
            <span class="badge badge-success">{{ order.status }}</span>
          </div>
        </div>

        <div class="detail-section">
          <h3 class="section-title">Customer</h3>
          <div class="detail-row">
            <span class="detail-label">Name</span>
            <span class="detail-value">{{ order.customer?.name }}</span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Email</span>
            <span class="detail-value">{{ order.customer?.email }}</span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Phone</span>
            <span class="detail-value">{{ order.customer?.phone || '—' }}</span>
          </div>
          <button @click="router.push(`/customers/${order.customer.id}`)" class="btn-link">
            View full profile →
          </button>
        </div>
      </div>

      <div class="medications-section">
        <h3 class="section-title">Medications</h3>
        <table class="data-table">
          <thead>
            <tr>
              <th>Medication</th>
              <th>Lot Number</th>
              <th>Quantity</th>
              <th>Expiration</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in order.items" :key="item.id">
              <td>{{ item.medication?.name }}</td>
              <td><span class="lot-badge">{{ item.medication?.lot_number }}</span></td>
              <td>{{ item.quantity }}</td>
              <td>{{ item.medication?.expiration_date || '—' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../services/api'

const route = useRoute()
const router = useRouter()
const order = ref(null)
const loading = ref(true)

onMounted(async () => {
  try {
    const res = await api.get(`/orders/${route.params.id}`)
    order.value = res.data
  } finally {
    loading.value = false
  }
})

function formatDate(d) {
  return new Date(d).toLocaleDateString('en-US', { month: '2-digit', day: '2-digit', year: 'numeric' })
}
</script>

<style scoped>
.page-container { max-width: 900px; margin: 32px auto; padding: 0 20px; }
.page-header { display: flex; align-items: center; gap: 16px; margin-bottom: 20px; }
.page-title { font-size: 22px; font-weight: 700; color: #1a2744; }
.btn-back { background: white; border: 1px solid #d1d5db; padding: 8px 16px; border-radius: 7px; cursor: pointer; font-family: inherit; font-size: 14px; }
.card { background: white; border-radius: 10px; padding: 24px; box-shadow: 0 1px 6px rgba(0,0,0,0.07); }
.section-title { font-size: 15px; font-weight: 600; color: #1a2744; margin-bottom: 14px; }
.detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 32px; margin-bottom: 28px; }
.detail-section {}
.detail-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f5f5f5; font-size: 14px; }
.detail-label { color: #888; }
.detail-value { font-weight: 500; }
.badge { padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: capitalize; }
.badge-success { background: #c6f6d5; color: #276749; }
.btn-link { background: none; border: none; color: #2b6cb0; cursor: pointer; font-size: 13px; padding: 8px 0; font-family: inherit; }
.medications-section { border-top: 1px solid #eee; padding-top: 20px; }
.data-table { width: 100%; border-collapse: collapse; font-size: 14px; }
.data-table th { background: #f8f9fb; text-align: left; padding: 10px 14px; font-size: 13px; font-weight: 600; color: #555; border-bottom: 2px solid #eee; }
.data-table td { padding: 12px 14px; border-bottom: 1px solid #f0f0f0; }
.lot-badge { background: #ebf4ff; color: #2b6cb0; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
.loading-state { text-align: center; padding: 40px; color: #888; }
</style>
