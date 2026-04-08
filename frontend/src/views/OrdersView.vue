<template>
  <div class="page-container">
    <div class="page-header">
      <h1 class="page-title">≡ Order Search</h1>
    </div>

    <!-- Search Form -->
    <div class="card">
      <h3 class="section-title">Medication Search</h3>
      <div class="search-form">
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Lot Number <span class="required">*</span></label>
            <input
              v-model="search.lot"
              type="text"
              class="form-input"
              placeholder="e.g. 951357"
              :class="{ 'input-error': searchError }"
            />
            <span v-if="searchError" class="error-text">{{ searchError }}</span>
          </div>
          <div class="form-group">
            <label class="form-label">Start Date</label>
            <input v-model="search.start_date" type="date" class="form-input" />
          </div>
          <div class="form-group">
            <label class="form-label">End Date</label>
            <input v-model="search.end_date" type="date" class="form-input" />
          </div>
          <div class="form-group form-group-btn">
            <button @click="fetchOrders" class="btn-search" :disabled="loading">
              {{ loading ? 'Searching...' : 'Search' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Results Table -->
    <div class="card" v-if="searched">
      <div class="table-header">
        <div class="results-title-row">
          <h3 class="section-title">
            Order Results
            <span v-if="pagination.total" class="results-count">{{ pagination.total }} result(s)</span>
          </h3>
        </div>
        <div class="table-actions" v-if="orders.length > 0">
          <button @click="exportCSV" class="btn-secondary">Export CSV</button>
          <button @click="openBulkAlert" class="btn-danger" :disabled="selectedOrders.length === 0">
            Alert Selected ({{ selectedOrders.length }})
          </button>
        </div>
      </div>

      <div v-if="loading" class="loading-state">Searching orders...</div>

      <div v-else-if="orders.length === 0" class="empty-state">
        No orders found for lot <strong>{{ search.lot }}</strong> in the selected date range.
      </div>

      <table v-else class="data-table">
        <thead>
          <tr>
            <th><input type="checkbox" @change="toggleAll" :checked="allSelected" /></th>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="order in orders" :key="order.id">
            <td>
              <input type="checkbox" :value="order.id" v-model="selectedOrders" />
            </td>
            <td class="order-id">{{ String(order.id).padStart(7, '0') }}</td>
            <td>{{ order.customer?.name }}</td>
            <td>{{ formatDate(order.purchase_date) }}</td>
            <td class="actions-cell">
              <button @click="viewOrder(order.id)" class="btn-action btn-view">
                👁 View Order
              </button>
              <button @click="openAlertModal(order)" class="btn-action btn-alert">
                🔔 Alert Buyer
              </button>
              <button @click="viewCustomer(order.customer.id)" class="btn-action btn-customer">
                👤 View Buyer
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination -->
      <div v-if="pagination.last_page > 1" class="pagination">
        <button @click="changePage(pagination.current_page - 1)" :disabled="pagination.current_page === 1" class="page-btn">← Prev</button>
        <span class="page-info">Page {{ pagination.current_page }} of {{ pagination.last_page }}</span>
        <button @click="changePage(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page" class="page-btn">Next →</button>
      </div>
    </div>

    <!-- Alert Modal -->
    <div v-if="alertModal.show" class="modal-overlay" @click.self="closeAlertModal">
      <div class="modal">
        <div class="modal-header">
          <h3>Send Alert to Customer</h3>
          <button @click="closeAlertModal" class="modal-close">✕</button>
        </div>
        <div class="modal-body">
          <div class="alert-warning">
            <p>
              Warning: Important recall notice for medication<br>
              Lot #<strong>{{ search.lot }}</strong>. Please contact the pharmacy<br>
              immediately.
            </p>
          </div>
        </div>
        <div class="modal-footer">
          <button @click="sendAlert" class="btn-primary" :disabled="alertModal.sending">
            {{ alertModal.sending ? 'Sending...' : 'Send Email' }}
          </button>
          <button @click="closeAlertModal" class="btn-secondary">Cancel</button>
        </div>
      </div>
    </div>

    <!-- Success message -->
    <div v-if="successMessage" class="toast-success">
      {{ successMessage }}
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { useRouter } from 'vue-router'
import api from '../services/api'

const router = useRouter()

const search = reactive({
  lot: '951357',
  start_date: '',
  end_date: '',
})

const orders = ref([])
const pagination = ref({})
const loading = ref(false)
const searched = ref(false)
const searchError = ref('')
const selectedOrders = ref([])
const successMessage = ref('')

const alertModal = reactive({
  show: false,
  orderIds: [],
  sending: false,
})

const allSelected = computed(() =>
  orders.value.length > 0 && selectedOrders.value.length === orders.value.length
)

async function fetchOrders(page = 1) {
  if (!search.lot) {
    searchError.value = 'Lot number is required'
    return
  }
  searchError.value = ''
  loading.value = true
  searched.value = true
  selectedOrders.value = []

  try {
    const params = { lot: search.lot, page }
    if (search.start_date) params.start_date = search.start_date
    if (search.end_date) params.end_date = search.end_date

    const res = await api.get('/orders', { params })
    orders.value = res.data.data
    pagination.value = {
      current_page: res.data.current_page,
      last_page: res.data.last_page,
      total: res.data.total,
    }
  } catch (err) {
    orders.value = []
  } finally {
    loading.value = false
  }
}

function changePage(page) {
  fetchOrders(page)
}

function toggleAll(e) {
  selectedOrders.value = e.target.checked ? orders.value.map(o => o.id) : []
}

function viewOrder(id) {
  router.push(`/orders/${id}`)
}

function viewCustomer(id) {
  router.push(`/customers/${id}`)
}

function openAlertModal(order) {
  alertModal.orderIds = [order.id]
  alertModal.show = true
}

function openBulkAlert() {
  alertModal.orderIds = [...selectedOrders.value]
  alertModal.show = true
}

function closeAlertModal() {
  alertModal.show = false
  alertModal.orderIds = []
}

async function sendAlert() {
  alertModal.sending = true
  const count = alertModal.orderIds.length
  try {
    await api.post('/alerts/send', {
      order_ids: alertModal.orderIds,
      lot_number: search.lot,
    })
    closeAlertModal()
    showSuccess(`Alert sent to ${count} customer(s)`)
    selectedOrders.value = []
  } catch (err) {
    alert('Failed to send alert. Please try again.')
  } finally {
    alertModal.sending = false
  }
}

function showSuccess(msg) {
  successMessage.value = msg
  setTimeout(() => { successMessage.value = '' }, 3000)
}

function formatDate(dateStr) {
  return new Date(dateStr).toLocaleDateString('en-US', {
    month: '2-digit', day: '2-digit', year: 'numeric'
  })
}

function exportCSV() {
  const headers = ['Order ID', 'Customer', 'Email', 'Phone', 'Purchase Date']
  const rows = orders.value.map(o => [
    String(o.id).padStart(7, '0'),
    o.customer?.name,
    o.customer?.email,
    o.customer?.phone,
    formatDate(o.purchase_date),
  ])
  const csv = [headers, ...rows].map(r => r.join(',')).join('\n')
  const blob = new Blob([csv], { type: 'text/csv' })
  const url = URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = url
  a.download = `orders-lot-${search.lot}.csv`
  a.click()
}
</script>

<style scoped>
.page-container {
  max-width: 1100px;
  margin: 32px auto;
  padding: 0 20px;
  position: relative;
}

.page-header { margin-bottom: 20px; }
.page-title { font-size: 22px; font-weight: 700; color: #1a2744; }

.card {
  background: white;
  border-radius: 10px;
  padding: 24px;
  margin-bottom: 20px;
  box-shadow: 0 1px 6px rgba(0,0,0,0.07);
}

.section-title { font-size: 15px; font-weight: 600; color: #1a2744; margin-bottom: 16px; }

.search-form .form-row {
  display: flex;
  gap: 16px;
  align-items: flex-end;
  flex-wrap: wrap;
}

.form-group { display: flex; flex-direction: column; gap: 6px; flex: 1; min-width: 160px; }
.form-group-btn { flex: 0; min-width: auto; }
.form-label { font-size: 13px; font-weight: 500; color: #555; }
.required { color: #e53e3e; }

.form-input {
  padding: 9px 12px;
  border: 1px solid #d1d5db;
  border-radius: 7px;
  font-size: 14px;
  outline: none;
  font-family: inherit;
  transition: border-color 0.2s;
}
.form-input:focus { border-color: #1a2744; }
.input-error { border-color: #e53e3e; }
.error-text { color: #e53e3e; font-size: 12px; }

.btn-search {
  background: #1a2744;
  color: white;
  border: none;
  padding: 10px 24px;
  border-radius: 7px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  white-space: nowrap;
  font-family: inherit;
  transition: background 0.2s;
}
.btn-search:hover:not(:disabled) { background: #243660; }
.btn-search:disabled { opacity: 0.7; cursor: not-allowed; }

.table-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 10px; }
.results-title-row { margin-bottom: 4px; }
.results-count { font-size: 13px; font-weight: 400; color: #888; margin-left: 8px; }
.table-actions { display: flex; gap: 10px; }

.data-table { width: 100%; border-collapse: collapse; font-size: 14px; }
.data-table th {
  background: #f8f9fb;
  text-align: left;
  padding: 10px 14px;
  font-size: 13px;
  font-weight: 600;
  color: #555;
  border-bottom: 2px solid #eee;
}
.data-table td { padding: 12px 14px; border-bottom: 1px solid #f0f0f0; }
.data-table tr:hover td { background: #fafbff; }
.order-id { font-weight: 600; color: #1a2744; font-family: monospace; }

.actions-cell { display: flex; gap: 6px; flex-wrap: wrap; }

.btn-action {
  padding: 5px 12px;
  border-radius: 6px;
  border: none;
  cursor: pointer;
  font-size: 12px;
  font-weight: 500;
  font-family: inherit;
  transition: opacity 0.2s;
  white-space: nowrap;
}
.btn-action:hover { opacity: 0.85; }
.btn-view     { background: #1a2744; color: white; }
.btn-alert    { background: #e53e3e; color: white; }
.btn-customer { background: #2b6cb0; color: white; }

.btn-primary  { background: #1a2744; color: white; border: none; padding: 9px 20px; border-radius: 7px; cursor: pointer; font-size: 14px; font-weight: 600; font-family: inherit; }
.btn-secondary{ background: white; color: #333; border: 1px solid #d1d5db; padding: 9px 20px; border-radius: 7px; cursor: pointer; font-size: 14px; font-family: inherit; }
.btn-danger   { background: #e53e3e; color: white; border: none; padding: 9px 20px; border-radius: 7px; cursor: pointer; font-size: 14px; font-weight: 600; font-family: inherit; }
.btn-danger:disabled { opacity: 0.5; cursor: not-allowed; }

.empty-state { text-align: center; padding: 40px; color: #888; }
.loading-state { text-align: center; padding: 40px; color: #888; }

.pagination { display: flex; align-items: center; justify-content: center; gap: 16px; padding-top: 16px; }
.page-btn { background: white; border: 1px solid #d1d5db; padding: 7px 16px; border-radius: 6px; cursor: pointer; font-family: inherit; }
.page-btn:disabled { opacity: 0.4; cursor: not-allowed; }
.page-info { font-size: 14px; color: #555; }

/* Modal */
.modal-overlay {
  position: fixed; inset: 0;
  background: rgba(0,0,0,0.45);
  display: flex; align-items: center; justify-content: center;
  z-index: 100;
}
.modal {
  background: white;
  border-radius: 10px;
  width: 420px;
  max-width: 90vw;
  box-shadow: 0 8px 40px rgba(0,0,0,0.18);
}
.modal-header {
  display: flex; justify-content: space-between; align-items: center;
  padding: 18px 24px;
  border-bottom: 1px solid #eee;
}
.modal-header h3 { font-size: 16px; font-weight: 600; color: #1a2744; }
.modal-close { background: none; border: none; font-size: 18px; cursor: pointer; color: #888; }
.modal-body { padding: 24px; }
.modal-footer { display: flex; gap: 10px; padding: 16px 24px; border-top: 1px solid #eee; justify-content: flex-end; }

.alert-warning {
  background: #fff5f5;
  border: 1px solid #fed7d7;
  border-radius: 8px;
  padding: 16px;
  color: #742a2a;
  font-size: 14px;
  line-height: 1.6;
}

/* Toast */
.toast-success {
  position: fixed;
  bottom: 24px; right: 24px;
  background: #276749;
  color: white;
  padding: 12px 20px;
  border-radius: 8px;
  font-size: 14px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  z-index: 200;
}
</style>
