<template>
  <div class="activity-log-container">
    <!-- Stats Cards -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon bg-blue">
          <i class="fas fa-list"></i>
        </div>
        <div class="stat-content">
          <p class="stat-label">Total Logs</p>
          <p class="stat-value">{{ formatNumber(stats.total) }}</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon bg-green">
          <i class="fas fa-calendar-day"></i>
        </div>
        <div class="stat-content">
          <p class="stat-label">Today</p>
          <p class="stat-value">{{ formatNumber(stats.today) }}</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon bg-purple">
          <i class="fas fa-calendar-week"></i>
        </div>
        <div class="stat-content">
          <p class="stat-label">This Week</p>
          <p class="stat-value">{{ formatNumber(stats.this_week) }}</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon bg-orange">
          <i class="fas fa-calendar-alt"></i>
        </div>
        <div class="stat-content">
          <p class="stat-label">This Month</p>
          <p class="stat-value">{{ formatNumber(stats.this_month) }}</p>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="filters-card">
      <div class="filters-grid">
        <div class="filter-group">
          <label>Search</label>
          <input
            v-model="filters.search"
            type="text"
            placeholder="Search description..."
            @input="debouncedSearch"
            class="filter-input"
          />
        </div>
        <div class="filter-group">
          <label>Type</label>
          <select v-model="filters.type" @change="applyFilters" class="filter-input">
            <option value="">All Types</option>
            <option v-for="type in types" :key="type" :value="type">
              {{ capitalize(type) }}
            </option>
          </select>
        </div>
        <div class="filter-group">
          <label>Date From</label>
          <input
            v-model="filters.date_from"
            type="date"
            @change="applyFilters"
            class="filter-input"
          />
        </div>
        <div class="filter-group">
          <label>Date To</label>
          <input
            v-model="filters.date_to"
            type="date"
            @change="applyFilters"
            class="filter-input"
          />
        </div>
      </div>
      <div class="filters-actions">
        <button @click="resetFilters" class="btn-secondary">
          <i class="fas fa-redo"></i> Reset Filters
        </button>
        <div class="per-page-selector">
          <label>Per Page:</label>
          <select v-model.number="perPage" @change="applyFilters" class="filter-input">
            <option :value="10">10</option>
            <option :value="15">15</option>
            <option :value="25">25</option>
            <option :value="50">50</option>
            <option :value="100">100</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Activity Logs Table -->
    <div class="table-card">
      <div class="table-container">
        <table class="activity-table">
          <thead>
            <tr>
              <th>Type</th>
              <th>Description</th>
              <th>User</th>
              <th>IP Address</th>
              <th>Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody v-if="!loading && logs.length > 0">
            <tr v-for="log in logs" :key="log.id">
              <td>
                <span :class="['badge', `badge-${log.type_badge_color}`]">
                  {{ capitalize(log.type) }}
                </span>
              </td>
              <td>{{ log.description }}</td>
              <td>{{ log.causer ? log.causer.name : 'System' }}</td>
              <td>{{ log.ip_address || '-' }}</td>
              <td>{{ log.formatted_created_at }}</td>
              <td>
                <button @click="showDetail(log)" class="btn-view">
                  <i class="fas fa-eye"></i> View
                </button>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Loading State -->
        <div v-if="loading" class="state-container">
          <i class="fas fa-spinner fa-spin fa-3x text-blue"></i>
          <p>Loading activity logs...</p>
        </div>

        <!-- Empty State -->
        <div v-if="!loading && logs.length === 0" class="state-container">
          <i class="fas fa-inbox fa-5x text-gray"></i>
          <p>No activity logs found</p>
        </div>
      </div>

      <!-- Pagination -->
      <div class="pagination-container">
        <div class="pagination-info">
          Showing {{ pagination.from || 0 }} to {{ pagination.to || 0 }} of {{ pagination.total || 0 }} results
        </div>
        <div class="pagination-buttons">
          <button
            @click="changePage(pagination.current_page - 1)"
            :disabled="!pagination.prev_page_url"
            class="btn-pagination"
          >
            Previous
          </button>
          <button
            v-for="page in visiblePages"
            :key="page"
            @click="changePage(page)"
            :class="['btn-pagination', { active: page === pagination.current_page }]"
          >
            {{ page }}
          </button>
          <button
            @click="changePage(pagination.current_page + 1)"
            :disabled="!pagination.next_page_url"
            class="btn-pagination"
          >
            Next
          </button>
        </div>
      </div>
    </div>

    <!-- Detail Modal -->
    <Teleport to="body">
      <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
        <div class="modal-content">
          <div class="modal-header">
            <h3>Activity Log Details</h3>
            <button @click="closeModal" class="modal-close">
              <i class="fas fa-times"></i>
            </button>
          </div>
          <div class="modal-body" v-if="selectedLog">
            <div class="detail-group">
              <label>Type</label>
              <span :class="['badge', `badge-${selectedLog.type_badge_color}`]">
                {{ capitalize(selectedLog.type) }}
              </span>
            </div>
            <div class="detail-group">
              <label>Description</label>
              <p>{{ selectedLog.description }}</p>
            </div>
            <div class="detail-group">
              <label>User</label>
              <p>{{ selectedLog.causer ? selectedLog.causer.name : 'System' }}</p>
            </div>
            <div v-if="selectedLog.subject" class="detail-group">
              <label>Subject</label>
              <p>{{ selectedLog.subject.name }}</p>
            </div>
            <div v-if="selectedLog.properties && Object.keys(selectedLog.properties).length > 0" class="detail-group">
              <label>Properties</label>
              <pre class="properties-pre">{{ prettyJson(selectedLog.properties) }}</pre>
            </div>
            <div class="detail-group">
              <label>IP Address</label>
              <p>{{ selectedLog.ip_address || '-' }}</p>
            </div>
            <div class="detail-group">
              <label>Date & Time</label>
              <p>{{ selectedLog.formatted_created_at }}</p>
            </div>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';

// Props
const props = defineProps({
  apiBaseUrl: {
    type: String,
    default: '/api/activity-logs'
  }
});

// State
const logs = ref([]);
const stats = reactive({
  total: 0,
  today: 0,
  this_week: 0,
  this_month: 0
});
const types = ref([]);
const filters = reactive({
  search: '',
  type: '',
  date_from: '',
  date_to: ''
});
const pagination = reactive({});
const currentPage = ref(1);
const perPage = ref(15);
const loading = ref(false);
const showModal = ref(false);
const selectedLog = ref(null);
const searchTimeout = ref(null);

// Computed
const visiblePages = computed(() => {
  const current = pagination.current_page || 1;
  const last = pagination.last_page || 1;
  const pages = [];
  const start = Math.max(1, current - 2);
  const end = Math.min(last, current + 2);
  
  for (let i = start; i <= end; i++) {
    pages.push(i);
  }
  
  return pages;
});

// Methods
const loadStats = async () => {
  try {
    const response = await fetch(`${props.apiBaseUrl}/stats`);
    const data = await response.json();
    Object.assign(stats, data);
  } catch (error) {
    console.error('Error loading stats:', error);
  }
};

const loadTypes = async () => {
  try {
    const response = await fetch(`${props.apiBaseUrl}/types`);
    types.value = await response.json();
  } catch (error) {
    console.error('Error loading types:', error);
  }
};

const loadLogs = async () => {
  loading.value = true;
  
  try {
    const params = new URLSearchParams({
      page: currentPage.value,
      per_page: perPage.value,
      ...filters
    });

    const response = await fetch(`${props.apiBaseUrl}?${params}`);
    const data = await response.json();
    
    logs.value = data.data;
    Object.assign(pagination, {
      current_page: data.current_page,
      last_page: data.last_page,
      from: data.from,
      to: data.to,
      total: data.total,
      prev_page_url: data.prev_page_url,
      next_page_url: data.next_page_url
    });
  } catch (error) {
    console.error('Error loading logs:', error);
  } finally {
    loading.value = false;
  }
};

const debouncedSearch = () => {
  clearTimeout(searchTimeout.value);
  searchTimeout.value = setTimeout(() => {
    applyFilters();
  }, 500);
};

const applyFilters = () => {
  currentPage.value = 1;
  loadLogs();
};

const resetFilters = () => {
  filters.search = '';
  filters.type = '';
  filters.date_from = '';
  filters.date_to = '';
  currentPage.value = 1;
  loadLogs();
};

const changePage = (page) => {
  currentPage.value = page;
  loadLogs();
};

const showDetail = (log) => {
  selectedLog.value = log;
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  selectedLog.value = null;
};

// Utility functions
const capitalize = (value) => {
  if (!value) return '';
  return value.charAt(0).toUpperCase() + value.slice(1);
};

const formatNumber = (value) => {
  return value.toLocaleString();
};

const prettyJson = (value) => {
  return JSON.stringify(value, null, 2);
};

// Lifecycle
onMounted(() => {
  loadStats();
  loadTypes();
  loadLogs();
});
</script>

<style scoped>
@import './activity-log.css';
</style>

