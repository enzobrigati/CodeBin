import axios from 'axios'

const PROD_ENV = false

let baseUrl = 'http://127.0.0.1:8000/api'

if(PROD_ENV) {
    baseUrl = 'https://codebin.avenanceagency.com/api'
}
export const axiosInstance = axios.create({
    baseURL: baseUrl,
})