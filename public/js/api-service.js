class ApiService {
    constructor() {
        this.baseURL = '/api/v1';
        this.token = document.querySelector('meta[name="api-token"]')?.getAttribute('content');
    }

    async request(url, options = {}) {
        const config = {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                ...options.headers,
            },
            ...options,
        };

        if (this.token) {
            config.headers['Authorization'] = `Bearer ${this.token}`;
        }

        try {
            const response = await fetch(`${this.baseURL}${url}`, config);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    }

    // Tickets API
    async getTickets(params = {}) {
        const queryString = new URLSearchParams(params).toString();
        return this.request(`/tickets?${queryString}`);
    }

    async getTicket(id) {
        return this.request(`/tickets/${id}`);
    }

    async createTicket(data) {
        const formData = new FormData();
        
        // Handle regular fields
        Object.keys(data).forEach(key => {
            if (key !== 'attachments' && data[key] !== null) {
                formData.append(key, data[key]);
            }
        });

        // Handle file attachments
        if (data.attachments && data.attachments.length > 0) {
            data.attachments.forEach((file, index) => {
                formData.append('attachments[]', file);
            });
        }

        return this.request('/tickets', {
            method: 'POST',
            headers: {
                // Remove Content-Type to let browser set it with boundary for FormData
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            },
            body: formData,
        });
    }

    async updateTicket(id, data) {
        const formData = new FormData();
        formData.append('_method', 'PUT');
        
        Object.keys(data).forEach(key => {
            if (data[key] !== null) {
                formData.append(key, data[key]);
            }
        });

        return this.request(`/tickets/${id}`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            },
            body: formData,
        });
    }

    async deleteTicket(id) {
        return this.request(`/tickets/${id}`, {
            method: 'DELETE',
        });
    }

    async bulkDeleteTickets(ids) {
        return this.request('/tickets/bulk', {
            method: 'DELETE',
            body: JSON.stringify({ ids }),
        });
    }

    // Comments API
    async createComment(data) {
        return this.request('/comments', {
            method: 'POST',
            body: JSON.stringify(data),
        });
    }

    async updateComment(id, data) {
        return this.request(`/comments/${id}`, {
            method: 'PUT',
            body: JSON.stringify(data),
        });
    }

    async deleteComment(id) {
        return this.request(`/comments/${id}`, {
            method: 'DELETE',
        });
    }

    // Dropdown data
    async getStatuses() {
        return this.request('/dropdown/statuses');
    }

    async getPriorities() {
        return this.request('/dropdown/priorities');
    }

    async getCategories() {
        return this.request('/dropdown/categories');
    }

    async getUsers() {
        return this.request('/dropdown/users');
    }
}

// Global instance
window.apiService = new ApiService();