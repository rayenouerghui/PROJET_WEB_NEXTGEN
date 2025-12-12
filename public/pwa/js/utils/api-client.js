/**
 * API Client - Communicates with backend PHP APIs
 * Cleaned version - removed unused methods
 */

class APIClient {
    constructor() {
        this.baseURL = '/PROJET_WEB_NEXTGEN-main/public/api/pwa';
    }

    async getUserDeliveries(userId) {
        const url = `${this.baseURL}/orders.php?user_id=${userId}`;
        console.log('📡 API Request:', url);

        try {
            const response = await fetch(url);
            console.log('📡 Response status:', response.status);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            console.log('📡 API Response:', data);

            if (!data.success) {
                throw new Error(data.error || 'API returned success: false');
            }

            return data.deliveries || [];

        } catch (error) {
            console.error('❌ Failed to fetch deliveries:', error);
            throw error;
        }
    }
}
