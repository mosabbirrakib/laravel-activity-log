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
          <p class="stat-value">{{ stats.total | formatNumber }}</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon bg-green">
          <i class="fas fa-calendar-day"></i>
        </div>
        <div class="stat-content">
          <p class="stat-label">Today</p>
          <p class="stat-value">{{ stats.today | formatNumber }}</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon bg-purple">
          <i class="fas fa-calendar-week"></i>
        </div>
        <div class="stat-content">
          <p class="stat-label">This Week</p>
          <p class="stat-value">{{ stats.this_week | formatNumber }}</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon bg-orange">
          <i class="fas fa-calendar-alt"></i>
        </div>
        <div class="stat-content">
          <p class="stat-label">This Month</p>
          <p class="stat-value">{{ stats.this_month | formatNumber }}</p>
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
              {{ type | capitalize }}
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
                  {{ log.type | capitalize }}
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
              {{ selectedLog.type | capitalize }}
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
            <pre class="properties-pre">{{ selectedLog.properties | prettyJson }}</pre>
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
  </div>
</template>

<script>
export default {
  name: 'ActivityLogVue2',
  
  props: {
    apiBaseUrl: {
      type: String,
      default: '/api/activity-logs'
    }
  },

  data() {
    return {
      logs: [],
      stats: {
        total: 0,
        today: 0,
        this_week: 0,
        this_month: 0
      },
      types: [],
      filters: {
        search: '',
        type: '',
        date_from: '',
        date_to: ''
      },
      pagination: {},
      currentPage: 1,
      perPage: 15,
      loading: false,
      showModal: false,
      selectedLog: null,
      searchTimeout: null
    };
  },

  computed: {
    visiblePages() {
      const current = this.pagination.current_page || 1;
      const last = this.pagination.last_page || 1;
      const pages = [];
      const start = Math.max(1, current - 2);
      const end = Math.min(last, current + 2);
      
      for (let i = start; i <= end; i++) {
        pages.push(i);
      }
      
      return pages;
    }
  },

  filters: {
    capitalize(value) {
      if (!value) return '';
      return value.charAt(0).toUpperCase() + value.slice(1);
    },
    formatNumber(value) {
      return value.toLocaleString();
    },
    prettyJson(value) {
      return JSON.stringify(value, null, 2);
    }
  },

  mounted() {
    this.loadStats();
    this.loadTypes();
    this.loadLogs();
  },

  methods: {
    async loadStats() {
      try {
        const response = await fetch(`${this.apiBaseUrl}/stats`);
        this.stats = await response.json();
      } catch (error) {
        console.error('Error loading stats:', error);
      }
    },

    async loadTypes() {
      try {
        const response = await fetch(`${this.apiBaseUrl}/types`);
        this.types = await response.json();
      } catch (error) {
        console.error('Error loading types:', error);
      }
    },

    async loadLogs() {
      this.loading = true;
      
      try {
        const params = new URLSearchParams({
          page: this.currentPage,
          per_page: this.perPage,
          ...this.filters
        });

        const response = await fetch(`${this.apiBaseUrl}?${params}`);
        const data = await response.json();
        
        this.logs = data.data;
        this.pagination = {
          current_page: data.current_page,
          last_page: data.last_page,
          from: data.from,
          to: data.to,
          total: data.total,
          prev_page_url: data.prev_page_url,
          next_page_url: data.next_page_url
        };
      } catch (error) {
        console.error('Error loading logs:', error);
      } finally {
        this.loading = false;
      }
    },

    debouncedSearch() {
      clearTimeout(this.searchTimeout);
      this.searchTimeout = setTimeout(() => {
        this.applyFilters();
      }, 500);
    },

    applyFilters() {
      this.currentPage = 1;
      this.loadLogs();
    },

    resetFilters() {
      this.filters = {
        search: '',
        type: '',
        date_from: '',
        date_to: ''
      };
      this.currentPage = 1;
      this.loadLogs();
    },

    changePage(page) {
      this.currentPage = page;
      this.loadLogs();
    },

    showDetail(log) {
      this.selectedLog = log;
      this.showModal = true;
    },

    closeModal() {
      this.showModal = false;
      this.selectedLog = null;
    }
  }
};
</script>

<style scoped>
/* Add your styles here or import from external CSS */
</style>

