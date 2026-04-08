<template>
  <div class="page-container">
    <div class="page-header">
      <h1 class="page-title">Alert Audit Log</h1>
    </div>

    <div class="card">
      <div class="log-header">
        <h3 class="section-title">
          Sent Alerts
          <span v-if="pagination.total" class="results-count">{{ pagination.total }} total</span>
        </h3>
      </div>
      <div v-if="loading" class="loading-state">Loading alerts...</div>
      <div v-else-if="alerts.length === 0" class="empty-state">No alerts have been sent yet.</div>
      <table v-else class="data-table">
        <thead>
          <tr>
            <th>Date & Time</th>
            <th>Customer</th>
            <th>Email</th>
            <th>Lot Number</th>
            <th>Order ID</th>
            <th>Sent By</th>
            <th>Type</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="alert in alerts" :key="alert.id">
            <td>{{ formatDateTime(alert.sent_at) }}</td>
            <td>{{ alert.customer?.name }}</td>
            <td>{{ alert.customer?.email }}</td>
            <td><span class="lot-badge">{{ alert.lot_number }}</span></td>
            <td class="order-id">{{ String(alert.order_id).padStart(7, '0') }}</td>
            <td>{{ alert.user?.username }}</td>
            <td><span class="badge badge-email">{{ alert.notification_type }}</span></td>
          </tr>
        </tbody>
      </table>

      <div v-if="pagination.last_page > 1" class="pagination">
        <button @click="fetchAlerts(pagination.current_page - 1)" :disabled="pagination.current_page === 1" class="page-btn">← Prev</button>
        <span class="page-info">Page {{ pagination.current_page }} of {{ pagination.last_page }}</span>
        <button @click="fetchAlerts(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page" class="page-btn">Next →</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../services/api'

const alerts = ref([])
const pagination = ref({})
const loading = ref(true)

onMounted(() => fetchAlerts(1))

async function fetchAlerts(page = 1) {
  loading.value = true
  try {
    const res = await api.get('/alerts', { params: { page } })
    alerts.value = res.data.data
    pagination.value = { current_page: res.data.current_page, last_page: res.data.last_page, total: res.data.total }
  } finally {
    loading.value = false
  }
}

function formatDateTime(d) {
  return new Date(d).toLocaleString('en-US', { month: '2-digit', day: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' })
}
</script>

<style scoped>
.page-container { max-width: 1100px; margin: 32px auto; padding: 0 20px; }
.page-header { margin-bottom: 20px; }
.page-title { font-size: 22px; font-weight: 700; color: #1a2744; }
.card { background: white; border-radius: 10px; padding: 24px; box-shadow: 0 1px 6px rgba(0,0,0,0.07); }
.data-table { width: 100%; border-collapse: collapse; font-size: 14px; }
.data-table th { background: #f8f9fb; text-align: left; padding: 10px 14px; font-size: 13px; font-weight: 600; color: #555; border-bottom: 2px solid #eee; }
.data-table td { padding: 12px 14px; border-bottom: 1px solid #f0f0f0; }
.order-id { font-weight: 600; color: #1a2744; font-family: monospace; }
.lot-badge { background: #ebf4ff; color: #2b6cb0; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
.badge { padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
.badge-email { background: #e9d8fd; color: #553c9a; }
.empty-state { text-align: center; padding: 40px; color: #888; }
.log-header { margin-bottom: 16px; }
.section-title { font-size: 15px; font-weight: 600; color: #1a2744; }
.results-count { font-size: 13px; font-weight: 400; color: #888; margin-left: 8px; }
.loading-state { text-align: center; padding: 40px; color: #888; }
.pagination { display: flex; align-items: center; justify-content: center; gap: 16px; padding-top: 16px; }
.page-btn { background: white; border: 1px solid #d1d5db; padding: 7px 16px; border-radius: 6px; cursor: pointer; font-family: inherit; }
.page-btn:disabled { opacity: 0.4; cursor: not-allowed; }
.page-info { font-size: 14px; color: #555; }
</style>
