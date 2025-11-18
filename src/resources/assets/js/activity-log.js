/**
 * Activity Log JavaScript
 * Handles fetching and displaying activity logs
 */

class ActivityLogManager {
    constructor() {
        this.currentPage = 1;
        this.perPage = 15;
        this.filters = {};
        this.apiBaseUrl = '/api/activity-logs';
        
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.loadStats();
        this.loadTypes();
        this.loadLogs();
    }

    setupEventListeners() {
        // Search
        const searchInput = document.getElementById('search');
        let searchTimeout;
        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.filters.search = e.target.value;
                this.currentPage = 1;
                this.loadLogs();
            }, 500);
        });

        // Type filter
        document.getElementById('type-filter').addEventListener('change', (e) => {
            this.filters.type = e.target.value;
            this.currentPage = 1;
            this.loadLogs();
        });

        // Date filters
        document.getElementById('date-from').addEventListener('change', (e) => {
            this.filters.date_from = e.target.value;
            this.currentPage = 1;
            this.loadLogs();
        });

        document.getElementById('date-to').addEventListener('change', (e) => {
            this.filters.date_to = e.target.value;
            this.currentPage = 1;
            this.loadLogs();
        });

        // Per page
        document.getElementById('per-page').addEventListener('change', (e) => {
            this.perPage = parseInt(e.target.value);
            this.currentPage = 1;
            this.loadLogs();
        });

        // Reset filters
        document.getElementById('reset-filters').addEventListener('click', () => {
            this.resetFilters();
        });

        // Modal close
        document.getElementById('close-modal').addEventListener('click', () => {
            this.closeModal();
        });

        // Close modal on outside click
        document.getElementById('detail-modal').addEventListener('click', (e) => {
            if (e.target.id === 'detail-modal') {
                this.closeModal();
            }
        });
    }

    async loadStats() {
        try {
            const response = await fetch(`${this.apiBaseUrl}/stats`);
            const stats = await response.json();
            
            document.getElementById('stat-total').textContent = stats.total.toLocaleString();
            document.getElementById('stat-today').textContent = stats.today.toLocaleString();
            document.getElementById('stat-week').textContent = stats.this_week.toLocaleString();
            document.getElementById('stat-month').textContent = stats.this_month.toLocaleString();
        } catch (error) {
            console.error('Error loading stats:', error);
        }
    }

    async loadTypes() {
        try {
            const response = await fetch(`${this.apiBaseUrl}/types`);
            const types = await response.json();
            
            const typeFilter = document.getElementById('type-filter');
            types.forEach(type => {
                const option = document.createElement('option');
                option.value = type;
                option.textContent = this.capitalizeFirst(type);
                typeFilter.appendChild(option);
            });
        } catch (error) {
            console.error('Error loading types:', error);
        }
    }

    async loadLogs() {
        this.showLoading();
        
        try {
            const params = new URLSearchParams({
                page: this.currentPage,
                per_page: this.perPage,
                ...this.filters
            });

            const response = await fetch(`${this.apiBaseUrl}?${params}`);
            const data = await response.json();
            
            this.renderLogs(data.data);
            this.renderPagination(data);
            this.hideLoading();
            
            if (data.data.length === 0) {
                this.showEmptyState();
            } else {
                this.hideEmptyState();
            }
        } catch (error) {
            console.error('Error loading logs:', error);
            this.hideLoading();
            this.showEmptyState();
        }
    }

    renderLogs(logs) {
        const tbody = document.getElementById('logs-tbody');
        tbody.innerHTML = '';

        logs.forEach(log => {
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50';
            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-${log.type_badge_color}-100 text-${log.type_badge_color}-800">
                        ${this.capitalizeFirst(log.type || 'default')}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm text-gray-900">${this.escapeHtml(log.description)}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">${log.causer ? this.escapeHtml(log.causer.name) : 'System'}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    ${log.ip_address || '-'}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    ${log.formatted_created_at}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <button onclick="activityLogManager.showDetail(${log.id})" 
                        class="text-blue-600 hover:text-blue-900">
                        <i class="fas fa-eye"></i> View
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    renderPagination(data) {
        document.getElementById('showing-from').textContent = data.from || 0;
        document.getElementById('showing-to').textContent = data.to || 0;
        document.getElementById('showing-total').textContent = data.total || 0;

        const paginationButtons = document.getElementById('pagination-buttons');
        paginationButtons.innerHTML = '';

        // Previous button
        const prevButton = this.createPaginationButton('Previous', data.current_page - 1, !data.prev_page_url);
        paginationButtons.appendChild(prevButton);

        // Page numbers
        const startPage = Math.max(1, data.current_page - 2);
        const endPage = Math.min(data.last_page, data.current_page + 2);

        for (let i = startPage; i <= endPage; i++) {
            const pageButton = this.createPaginationButton(i, i, false, i === data.current_page);
            paginationButtons.appendChild(pageButton);
        }

        // Next button
        const nextButton = this.createPaginationButton('Next', data.current_page + 1, !data.next_page_url);
        paginationButtons.appendChild(nextButton);
    }

    createPaginationButton(text, page, disabled = false, active = false) {
        const button = document.createElement('button');
        button.textContent = text;
        button.className = `px-3 py-1 rounded ${
            active 
                ? 'bg-blue-500 text-white' 
                : disabled 
                    ? 'bg-gray-100 text-gray-400 cursor-not-allowed' 
                    : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300'
        }`;
        button.disabled = disabled;
        
        if (!disabled) {
            button.addEventListener('click', () => {
                this.currentPage = page;
                this.loadLogs();
            });
        }
        
        return button;
    }

    async showDetail(id) {
        try {
            const response = await fetch(`${this.apiBaseUrl}/${id}`);
            const log = await response.json();
            
            const modalContent = document.getElementById('modal-content');
            modalContent.innerHTML = `
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Type</label>
                        <span class="mt-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-${log.type_badge_color}-100 text-${log.type_badge_color}-800">
                            ${this.capitalizeFirst(log.type || 'default')}
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <p class="mt-1 text-sm text-gray-900">${this.escapeHtml(log.description)}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">User</label>
                        <p class="mt-1 text-sm text-gray-900">${log.causer ? this.escapeHtml(log.causer.name) : 'System'}</p>
                    </div>
                    ${log.subject ? `
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Subject</label>
                        <p class="mt-1 text-sm text-gray-900">${this.escapeHtml(log.subject.name)}</p>
                    </div>
                    ` : ''}
                    ${log.properties && Object.keys(log.properties).length > 0 ? `
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Properties</label>
                        <pre class="mt-1 text-xs bg-gray-50 p-3 rounded overflow-x-auto">${JSON.stringify(log.properties, null, 2)}</pre>
                    </div>
                    ` : ''}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">IP Address</label>
                        <p class="mt-1 text-sm text-gray-900">${log.ip_address || '-'}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Date & Time</label>
                        <p class="mt-1 text-sm text-gray-900">${log.formatted_created_at}</p>
                    </div>
                </div>
            `;
            
            document.getElementById('detail-modal').classList.remove('hidden');
        } catch (error) {
            console.error('Error loading log detail:', error);
        }
    }

    closeModal() {
        document.getElementById('detail-modal').classList.add('hidden');
    }

    resetFilters() {
        this.filters = {};
        this.currentPage = 1;
        document.getElementById('search').value = '';
        document.getElementById('type-filter').value = '';
        document.getElementById('date-from').value = '';
        document.getElementById('date-to').value = '';
        this.loadLogs();
    }

    showLoading() {
        document.getElementById('loading').classList.remove('hidden');
        document.getElementById('logs-tbody').innerHTML = '';
    }

    hideLoading() {
        document.getElementById('loading').classList.add('hidden');
    }

    showEmptyState() {
        document.getElementById('empty-state').classList.remove('hidden');
    }

    hideEmptyState() {
        document.getElementById('empty-state').classList.add('hidden');
    }

    capitalizeFirst(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }
}

// Initialize when DOM is ready
let activityLogManager;
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        activityLogManager = new ActivityLogManager();
    });
} else {
    activityLogManager = new ActivityLogManager();
}

