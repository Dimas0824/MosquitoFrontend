import http from 'k6/http';
import { check, group, sleep } from 'k6';
import { Rate } from 'k6/metrics';

// Custom metrics
const errorRate = new Rate('errors');

// Test configuration
export const options = {
    stages: [
        { duration: '30s', target: 5 },   // Ramp-up ke 5 device
        { duration: '1m', target: 10 },   // Ramp-up ke 10 device
        { duration: '2m', target: 10 },   // Maintain 10 device
        { duration: '30s', target: 0 },   // Ramp-down
    ],
    thresholds: {
        http_req_duration: ['p(95)<2000'], // 95% request harus < 2s
        http_req_failed: ['rate<0.1'],      // Error rate < 10%
        errors: ['rate<0.1'],
    },
};

const BASE_URL = 'https://dimspersonal.my.id';

// Simulasi data device
const DEVICES = [
    { device_id: 'DEVICE001', password: '123456' },
    { device_id: 'DEVICE002', password: '123456' },
    { device_id: 'DEVICE003', password: '123456' },
    { device_id: 'DEVICE004', password: '123456' },
    { device_id: 'DEVICE005', password: '123456' },
    { device_id: 'DEVICE006', password: '123456' },
    { device_id: 'DEVICE007', password: '123456' },
    { device_id: 'DEVICE008', password: '123456' },
    { device_id: 'DEVICE009', password: '123456' },
    { device_id: 'DEVICE010', password: '123456' },
];

// Fungsi helper untuk mendapatkan CSRF token
function getCsrfToken(htmlResponse) {
    const tokenMatch = htmlResponse.match(/name="_token"\s+value="([^"]+)"/);
    return tokenMatch ? tokenMatch[1] : null;
}

// Main test scenario
export default function () {
    const deviceIndex = __VU % DEVICES.length;
    const device = DEVICES[deviceIndex];

    let sessionCookie = '';
    let csrfToken = '';

    group('1. Login Flow', () => {
        // GET login page
        let res = http.get(`${BASE_URL}/login`);

        const loginCheck = check(res, {
            'login page loaded': (r) => r.status === 200,
            'login page has form': (r) => r.body.includes('device_id') || r.body.includes('password'),
        });
        errorRate.add(!loginCheck);

        // Extract CSRF token and session cookie
        csrfToken = getCsrfToken(res.body);
        const cookies = res.cookies;

        if (cookies['laravel_session']) {
            sessionCookie = cookies['laravel_session'][0].value;
        } else if (cookies['XSRF-TOKEN']) {
            sessionCookie = cookies['XSRF-TOKEN'][0].value;
        }

        sleep(1);

        // POST login
        const loginPayload = {
            device_id: device.device_id,
            password: device.password,
            _token: csrfToken || '',
        };

        res = http.post(`${BASE_URL}/login`, loginPayload, {
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'Cookie': sessionCookie ? `laravel_session=${sessionCookie}` : '',
            },
            redirects: 0, // Don't follow redirects automatically
        });

        const authCheck = check(res, {
            'login successful': (r) => r.status === 302 || r.status === 200,
            'redirected to dashboard': (r) =>
                r.headers['Location']?.includes('dashboard') || r.status === 200,
        });
        errorRate.add(!authCheck);

        // Update session cookie after login
        if (res.cookies['laravel_session']) {
            sessionCookie = res.cookies['laravel_session'][0].value;
        }

        sleep(2);
    });

    group('2. Dashboard Access', () => {
        const res = http.get(`${BASE_URL}/dashboard`, {
            headers: {
                'Cookie': sessionCookie ? `laravel_session=${sessionCookie}` : '',
            },
        });

        const dashboardCheck = check(res, {
            'dashboard loaded': (r) => r.status === 200,
            'dashboard has content': (r) => r.body.length > 1000,
        });
        errorRate.add(!dashboardCheck);

        sleep(2);
    });

    group('3. API Endpoints', () => {
        // Get inference results
        let res = http.get(`${BASE_URL}/api/inference/results`, {
            headers: {
                'Cookie': sessionCookie ? `laravel_session=${sessionCookie}` : '',
                'Accept': 'application/json',
            },
        });

        let apiCheck = check(res, {
            'inference API responds': (r) => r.status === 200,
            'inference returns JSON': (r) => {
                try {
                    JSON.parse(r.body);
                    return true;
                } catch (e) {
                    return false;
                }
            },
        });
        errorRate.add(!apiCheck);

        sleep(1);

        // Get detection history
        res = http.get(`${BASE_URL}/api/detections/history`, {
            headers: {
                'Cookie': sessionCookie ? `laravel_session=${sessionCookie}` : '',
                'Accept': 'application/json',
            },
        });

        apiCheck = check(res, {
            'history API responds': (r) => r.status === 200,
            'history returns data': (r) => r.body.length > 0,
        });
        errorRate.add(!apiCheck);

        sleep(2);
    });

    group('4. Actuator Control (Optional)', () => {
        // Simulate actuator activation (uncomment if you want to test this)
        /*
        const res = http.post(`${BASE_URL}/actuator/activate`, null, {
          headers: {
            'Cookie': sessionCookie ? `laravel_session=${sessionCookie}` : '',
            'X-CSRF-TOKEN': csrfToken || '',
            'Accept': 'application/json',
          },
        });

        const actuatorCheck = check(res, {
          'actuator responds': (r) => r.status === 200 || r.status === 422,
        });
        errorRate.add(!actuatorCheck);
        */

        sleep(1);
    });

    // Random think time
    sleep(Math.random() * 3 + 2);
}

// Setup function - runs once per VU
export function setup() {
    console.log('Starting load test for Mosquito Detection System');
    console.log(`Testing with ${DEVICES.length} devices`);
    console.log(`Base URL: ${BASE_URL}`);
}

// Teardown function - runs once after all VUs complete
export function teardown(data) {
    console.log('Load test completed');
}
