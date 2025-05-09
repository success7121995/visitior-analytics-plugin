/**
 * Get Geo Location
 * 
 * @package visitor-analytics
 */

class GetGeoLocation {
    constructor() {
        this.preferences = this.getCookiePreferences();

        // If no preferences, return
        if (!this.preferences) {
            return;
        }

        this.visitorId = this.getVisitorId();
        
        if (this.visitorId) {
            this.sendGeoLocationDataToServer();
        }
    }

    /**
     * Get the cookie preferences
     * @returns {Object} The cookie preferences
     */
    getCookiePreferences = () => {
        const cookieName = 'wpva-visitor-analytics-cookie-preferences';
        const cookies = document.cookie.split(';');
        
        for (let cookie of cookies) {
            const [name, value] = cookie.trim().split('=');
            if (name === cookieName) {
                try {
                    return JSON.parse(decodeURIComponent(value));
                } catch (e) {
                    console.error('Error parsing cookie preferences:', e);
                    return null;
                }
            }
        }
        return null;
    }

    /**
     * Get Visitor ID from cookie
     * @returns {string|null} Visitor ID
     */
    getVisitorId() {
        const cookies = document.cookie.split(';');
        
        for (let cookie of cookies) {
            const [name, value] = cookie.trim().split('=');
            if (name === 'visitor_id') {
                return value;
            }
        }
        
        return null;
    }

    /**
     * Send Geo Location Data to Server
     */
    async sendGeoLocationDataToServer() {
        try {
            
            
            // Get IP Address
            const ipAddress = this.preferences.ip_address === "on" ? await this.getIpAddress() : null;

            if (!ipAddress) {
                console.error('Error:', 'No IP Address Found');
                return;
            }

            // Get Geo Location 
            const geoLocation = await this.getGeoLocation(ipAddress);

            if (!geoLocation) {
                console.error('Error:', 'No Geo Location Found');
                return;
            }

            // Get Geo Location Data
            const data = {};
            const keys = [
                'ip_address',
                'network',
                'version',
                'city',
                'region',
                'region_code',
                'country_name',
                'country_code',
                'postal',
                'latitude',
                'longitude',
                'languages',
                'timezone',
                'utc_offset',
                'country_calling_code',
                'country_area', 'asn', 'org'
            ];

            // Check if the preference is on and add the data to the data object
            for (const key of keys) {
                const geoKey = key === 'ip_address' ? 'ip' : key; // Map 'ip_address' to 'ip'
                data[geoKey] = this.preferences[key] === "on" ? geoLocation[geoKey] : null;
            }
            
            const formData = new FormData();
            formData.append('action', 'get_geo_location_data');
            formData.append('data', JSON.stringify(data));
            formData.append('visitor_id', this.visitorId);

            // Send Geo Location Data to Server
            const res = await fetch(visitor_analytics_get_geolocation.ajaxurl, {
                method: 'POST',
                body: formData,
            });

            if (!res.ok) {
                console.error('Error:', `HTTP Error! status: ${res.status}`);
                return;
            }

            const response = await res.json();

            if (!response.success) {
                console.error('Error:', response.data);
            }
            
        } catch (err) {
            console.error('Error:', err.message);
        }
    }

    /**
     * Get IP Address
     * @returns {string} IP Address
     */
    async getIpAddress() {
        try {
            const res = await fetch('https://api.ipify.org?format=json');

            if (!res.ok) {
                console.error('Error:', res.statusText);
                return;
            }

            const { ip } = await res.json();
            return ip;
        } catch (err) {
            console.error('Error:', err.message);
        }
    }

    /**
     * Get Geo Location
     * @param {string} ip
     * @returns {Object} Geo Location
     */
    async getGeoLocation(ip) {
        try {
            const res = await fetch(`https://ipapi.co/${ip}/json/`);

            if (!res.ok) {
                console.error('Error:', res.statusText);
                return;
            }

            const geoData = await res.json();

            return geoData;
        } catch (err) {
            console.error('Error:', err.message);
        }
    }
}