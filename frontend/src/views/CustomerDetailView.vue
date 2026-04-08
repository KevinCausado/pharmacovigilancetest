<template>
  <div class="page-container">
    <div class="page-header">
      <button @click="router.back()" class="btn-back">← Back</button>
      <h1 class="page-title">Customer Details</h1>
    </div>

    <div v-if="loading" class="card loading-state">Loading customer...</div>

    <div v-else-if="customer">
      <div class="card profile-card">
        <div class="profile-avatar">{{ customer.name.charAt(0) }}</div>
        <div class="profile-info">
          <h2 class="profile-name">{{ customer.name }}</h2>
          <div class="profile-meta">
            <span>✉ {{ customer.email }}</span>
            <span v-if="customer.phone">📞 {{ customer.phone }}</span>
          </div>
        </div>
      </div>

      <div class="card">
        <h3 class="section-title">Order History</h3>
        <div v-if="customer.orders?.length === 0" class="empty-state">No orders found.</div>
        <table v-else class="data-table">
          <thead>
            <tr>
              <th>Order ID</th>
              <th>Date</th>
              <th>Status</th>
              <th>Medications</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="order in customer.orders" :key="order.id">
              <td class="order-id">{{ String(order.id).padStart(7, '0') }}</td>
              <td>{{ formatDate(order.purchase_date) }}</td>
              <td><span class="badge badge-success">{{ order.status }}</span></td>
              <td>
                <span v-for="item in order.items" :key="item.id" class="lot-badge">
                  {{ item.medication?.lot_number }}
                </span>
              </td>
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
const customer = ref(null)
const loading = ref(true)

onMounted(async () => {
  try {
    const res = await api.get(`/customers/${route.params.id}`)
    customer.value = res.data
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
.card { background: white; border-radius: 10px; padding: 24px; margin-bottom: 20px; box-shadow: 0 1px 6px rgba(0,0,0,0.07); }
.section-title { font-size: 15px; font-weight: 600; color: #1a2744; margin-bottom: 14px; }
.profile-card { display: flex; align-items: center; gap: 20px; }
.profile-avatar { width: 60px; height: 60px; background: #1a2744; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: 700; flex-shrink: 0; }
.profile-name { font-size: 20px; font-weight: 700; margin-bottom: 6px; }
.profile-meta { display: flex; gap: 20px; color: #666; font-size: 14px; }
.data-table { width: 100%; border-collapse: collapse; font-size: 14px; }
.data-table th { background: #f8f9fb; text-align: left; padding: 10px 14px; font-size: 13px; font-weight: 600; color: #555; border-bottom: 2px solid #eee; }
.data-table td { padding: 12px 14px; border-bottom: 1px solid #f0f0f0; }
.order-id { font-weight: 600; color: #1a2744; font-family: monospace; }
.badge { padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: capitalize; }
.badge-success { background: #c6f6d5; color: #276749; }
.lot-badge { background: #ebf4ff; color: #2b6cb0; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; margin-right: 4px; }
.empty-state { text-align: center; padding: 40px; color: #888; }
.loading-state { text-align: center; padding: 40px; color: #888; }
</style>
