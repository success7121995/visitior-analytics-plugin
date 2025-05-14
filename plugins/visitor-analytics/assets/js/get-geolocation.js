/**
 * Get Geo Location
 * 
 * @package visitor-analytics
 */

class GetGeoLocation {
    constructor() {
        this.sendGeoLocationDataToServer();
    }

    /**
     * Send Geo Location Data to Server
     */
    async sendGeoLocationDataToServer() {
        try {
            // Get user agent, landing page, referrer, visit time
            const user_agent = navigator.userAgent;
            const landing_page = window.location.href;
            const referrer = document.referrer || null;
            const visit_time = new Date().toISOString();
            const device = this.getDevice(user_agent);
            const browser = this.getBrowser(user_agent);

            // Get IP
            const ipRes = await fetch('https://api.ipify.org?format=json');
            if (!ipRes.ok) return;
            const { ip } = await ipRes.json();
            if (!ip) return;

            // Get geo
            const geoRes = await fetch(`https://ipapi.co/${ip}/json/`);
            if (!geoRes.ok) return;
            const geo = await geoRes.json();
            if (!geo || geo.error) return;

            // Prepare data
            const data = {
                landing_page,
                user_agent,
                device,
                browser,
                visit_time,
                referrer,
                ip,
                network: geo.network || null,
                version: geo.version || null,
                city: geo.city || null,
                region: geo.region || null,
                region_code: geo.region_code || null,
                country_name: geo.country_name || null,
                country_code: geo.country_code || null,
                postal: geo.postal || null,
                latitude: geo.latitude || null,
                longitude: geo.longitude || null,
                languages: geo.languages || null,
                timezone: geo.timezone || null,
                utc_offset: geo.utc_offset || null,
                country_calling_code: geo.country_calling_code || null,
                country_area: geo.country_area || null,
                asn: geo.asn || null,
                org: geo.org || null
            };

            // Send to server
            const formData = new FormData();
            formData.append('action', 'get_geo_location_data');
            formData.append('data', JSON.stringify(data));
            await fetch(visitor_analytics_get_geolocation.ajaxurl, {
                method: 'POST',
                body: formData
            });
        } catch (e) {
            // fail silently
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

    getDevice(userAgent) {
        if (/android|bb\d+|meego.+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(userAgent)) {
            return 'Mobile';
        } else if (/ipad|tablet|playbook|silk/i.test(userAgent)) {
            return 'Tablet';
        } else {
            return 'Desktop';
        }
    }

    getBrowser(userAgent) {
        if (/MSIE/i.test(userAgent)) {
            return 'Internet Explorer';
        } else if (/Firefox/i.test(userAgent)) {
            return 'Firefox';
        } else if (/Chrome/i.test(userAgent)) {
            return 'Chrome';
        } else if (/Safari/i.test(userAgent)) {
            return 'Safari';
        } else if (/Opera/i.test(userAgent)) {
            return 'Opera';
        } else if (/Edge/i.test(userAgent)) {
            return 'Edge';
        }
        return '';
    }
}

// 自動 tracking geolocation
new GetGeoLocation();