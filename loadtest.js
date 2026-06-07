import http from 'k6/http';
import { check, sleep } from 'k6';

export const options = {
    vus: 10, // Jumlah user virtual yang akan dijalankan
    duration: '30s', // durasi tes
};

export default function () { 
    const res = http.get('http://localhost:8000/api/product-category');

    check(res, {
        'status code 200': (r) => r.status === 200,
        'response time < 300ms': (r) => r.timings.duration < 500,
    });

    // sleep(1); // delay biar ga terlalu ngebombardir server 
}